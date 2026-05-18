<?php

declare(strict_types=1);

namespace Structora\DOM;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

final class StructureParser implements StructureParserInterface
{
    public function parse(string $document, array $options = []): ParsedDocument
    {
        $source = (string)($options['source'] ?? '');
        $errorsBefore = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $normalizedDocument = $this->normalizeDocument($document);
        $loaded = $dom->loadHTML($normalizedDocument, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $parserErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($errorsBefore);

        if (!$loaded) {
            return $this->emptyDocument($source, $document, count($parserErrors));
        }

        $xpath = new DOMXPath($dom);
        $forms = $this->extractForms($xpath);
        $links = $this->extractLinks($xpath);
        $headings = $this->extractHeadings($xpath);
        $title = $this->nodeText($xpath->query('//title')->item(0));

        $summary = [
            'title_present' => $title !== '',
            'form_count' => count($forms),
            'field_count' => array_sum(array_map(
                static fn (ParsedForm $form): int => count($form->fields),
                $forms,
            )),
            'button_count' => array_sum(array_map(
                static fn (ParsedForm $form): int => count($form->buttons),
                $forms,
            )),
            'link_count' => count($links),
            'heading_count' => count($headings),
        ];

        return new ParsedDocument(
            source: $source,
            title: $title,
            forms: $forms,
            links: $links,
            headings: $headings,
            metadata: [
                'read_only' => true,
                'parser' => self::class,
                'document_length' => strlen($document),
                'libxml_error_count' => count($parserErrors),
                'network_access' => false,
                'filesystem_writes' => false,
            ],
            summary: $summary,
        );
    }

    private function normalizeDocument(string $document): string
    {
        return '<!doctype html><meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $document;
    }

    private function emptyDocument(string $source, string $document, int $parserErrorCount): ParsedDocument
    {
        return new ParsedDocument(
            source: $source,
            metadata: [
                'read_only' => true,
                'parser' => self::class,
                'document_length' => strlen($document),
                'libxml_error_count' => $parserErrorCount,
                'network_access' => false,
                'filesystem_writes' => false,
            ],
            summary: [
                'title_present' => false,
                'form_count' => 0,
                'field_count' => 0,
                'button_count' => 0,
                'link_count' => 0,
                'heading_count' => 0,
            ],
        );
    }

    /**
     * @return ParsedForm[]
     */
    private function extractForms(DOMXPath $xpath): array
    {
        $forms = [];

        foreach ($xpath->query('//form') as $index => $formNode) {
            if (!$formNode instanceof DOMElement) {
                continue;
            }

            $fields = [];
            foreach ($xpath->query('.//input | .//select | .//textarea', $formNode) as $fieldNode) {
                if ($fieldNode instanceof DOMElement) {
                    $fields[] = $this->parseField($fieldNode, $xpath);
                }
            }

            $buttons = [];
            foreach ($xpath->query('.//button | .//input[@type="button" or @type="submit" or @type="reset"]', $formNode) as $buttonNode) {
                if ($buttonNode instanceof DOMElement) {
                    $buttons[] = $this->parseField($buttonNode, $xpath);
                }
            }

            $forms[] = new ParsedForm(
                method: strtolower($this->attribute($formNode, 'method') ?: 'get'),
                action: $this->attribute($formNode, 'action'),
                id: $this->attribute($formNode, 'id'),
                name: $this->attribute($formNode, 'name'),
                fields: $fields,
                buttons: $buttons,
                metadata: [
                    'index' => $index,
                    'read_only' => true,
                ],
            );
        }

        return $forms;
    }

    private function parseField(DOMElement $node, DOMXPath $xpath): ParsedField
    {
        $tag = strtolower($node->tagName);
        $type = strtolower($this->attribute($node, 'type') ?: $tag);

        return new ParsedField(
            tag: $tag,
            type: $type,
            name: $this->attribute($node, 'name'),
            id: $this->attribute($node, 'id'),
            label: $this->labelFor($node, $xpath),
            value: $tag === 'button' ? $this->nodeText($node) : $this->attribute($node, 'value'),
            placeholder: $this->attribute($node, 'placeholder'),
            required: $node->hasAttribute('required'),
            metadata: [
                'read_only' => true,
            ],
        );
    }

    private function labelFor(DOMElement $node, DOMXPath $xpath): string
    {
        $id = $this->attribute($node, 'id');
        if ($id !== '') {
            $label = $xpath->query('//label[@for=' . $this->xpathLiteral($id) . ']')->item(0);
            if ($label instanceof DOMNode) {
                return $this->nodeText($label);
            }
        }

        $parent = $node->parentNode;
        while ($parent instanceof DOMElement) {
            if (strtolower($parent->tagName) === 'label') {
                return $this->nodeText($parent);
            }

            $parent = $parent->parentNode;
        }

        return '';
    }

    /**
     * @return ParsedLink[]
     */
    private function extractLinks(DOMXPath $xpath): array
    {
        $links = [];

        foreach ($xpath->query('//a[@href]') as $index => $linkNode) {
            if (!$linkNode instanceof DOMElement) {
                continue;
            }

            $links[] = new ParsedLink(
                href: $this->attribute($linkNode, 'href'),
                text: $this->nodeText($linkNode),
                title: $this->attribute($linkNode, 'title'),
                rel: $this->attribute($linkNode, 'rel'),
                metadata: [
                    'index' => $index,
                    'read_only' => true,
                ],
            );
        }

        return $links;
    }

    /**
     * @return ParsedHeading[]
     */
    private function extractHeadings(DOMXPath $xpath): array
    {
        $headings = [];

        foreach ($xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6') as $index => $headingNode) {
            if (!$headingNode instanceof DOMElement) {
                continue;
            }

            $headings[] = new ParsedHeading(
                level: (int)substr(strtolower($headingNode->tagName), 1),
                text: $this->nodeText($headingNode),
                id: $this->attribute($headingNode, 'id'),
                metadata: [
                    'index' => $index,
                    'read_only' => true,
                ],
            );
        }

        return $headings;
    }

    private function attribute(DOMElement $node, string $name): string
    {
        return trim($node->getAttribute($name));
    }

    private function nodeText(?DOMNode $node): string
    {
        if (!$node instanceof DOMNode) {
            return '';
        }

        return trim((string)preg_replace('/\s+/', ' ', $node->textContent));
    }

    private function xpathLiteral(string $value): string
    {
        if (!str_contains($value, '"')) {
            return '"' . $value . '"';
        }

        if (!str_contains($value, "'")) {
            return "'" . $value . "'";
        }

        $parts = explode('"', $value);

        return 'concat("' . implode('", \'"\', "', $parts) . '")';
    }
}
