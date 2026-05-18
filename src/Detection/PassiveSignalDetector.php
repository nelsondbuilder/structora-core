<?php

declare(strict_types=1);

namespace Structora\Detection;

use Structora\DOM\ParsedDocument;

final class PassiveSignalDetector implements DetectorInterface
{
    /**
     * @return DetectedSignal[]
     */
    public function detect(ParsedDocument $document, array $options = []): array
    {
        $signals = [];
        $textIndex = $this->textIndex($document);

        $this->detectAuthLikeForms($document, $signals);
        $this->detectSearchForms($document, $signals);
        $this->detectMultiStepIndicators($document, $textIndex, $signals);
        $this->detectNavigationHeavyPage($document, $signals);
        $this->detectDensitySignals($document, $signals);
        $this->detectChallengeIndicators($document, $textIndex, $signals);
        $this->detectConfirmationIndicators($document, $textIndex, $signals);
        $this->detectProgressIndicators($document, $textIndex, $signals);

        return $signals;
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectAuthLikeForms(ParsedDocument $document, array &$signals): void
    {
        foreach ($document->forms as $index => $form) {
            $fields = $this->formFields($form);
            $fieldTypes = array_column($fields, 'type');
            $fieldNames = strtolower(implode(' ', array_filter(array_column($fields, 'name'))));
            $labels = strtolower(implode(' ', array_filter(array_column($fields, 'label'))));

            if (in_array('password', $fieldTypes, true)) {
                $signals[] = $this->signal('auth_like_form', 0.95, [
                    'form_index' => $index,
                    'form_id' => $form->id,
                    'field_types' => array_values(array_unique($fieldTypes)),
                    'matched_terms' => $this->matchedTerms($fieldNames . ' ' . $labels, ['email', 'username', 'password', 'login', 'sign in']),
                ]);
            }
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectSearchForms(ParsedDocument $document, array &$signals): void
    {
        foreach ($document->forms as $index => $form) {
            $fields = $this->formFields($form);
            $formText = strtolower($form->id . ' ' . $form->name . ' ' . $form->action . ' ' . $this->fieldsText($fields));

            if (str_contains($formText, 'search') || in_array('search', array_column($fields, 'type'), true)) {
                $signals[] = $this->signal('search_form', 0.9, [
                    'form_index' => $index,
                    'form_id' => $form->id,
                    'action' => $form->action,
                    'matched_terms' => $this->matchedTerms($formText, ['search', 'query', 'find']),
                ]);
            }
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectMultiStepIndicators(ParsedDocument $document, string $textIndex, array &$signals): void
    {
        $matches = $this->matchedTerms($textIndex, ['step 1', 'step 2', 'multi-step', 'workflow']);
        if (count($document->forms) > 1 || $matches !== []) {
            $signals[] = $this->signal('multi_step_indicator', count($document->forms) > 1 ? 0.86 : 0.72, [
                'form_count' => count($document->forms),
                'matched_terms' => $matches,
            ]);
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectNavigationHeavyPage(ParsedDocument $document, array &$signals): void
    {
        $linkCount = count($document->links);
        if ($linkCount >= 6) {
            $signals[] = $this->signal('navigation_heavy_page', min(0.95, 0.6 + ($linkCount / 30)), [
                'link_count' => $linkCount,
                'sample_links' => array_slice(array_map(
                    static fn ($link): string => $link->text !== '' ? $link->text : $link->href,
                    $document->links,
                ), 0, 5),
            ]);
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectDensitySignals(ParsedDocument $document, array &$signals): void
    {
        $formCount = count($document->forms);
        $fieldCount = (int)($document->summary['field_count'] ?? 0);
        $buttonCount = (int)($document->summary['button_count'] ?? 0);

        if ($formCount >= 2) {
            $signals[] = $this->signal('form_density', min(0.95, 0.55 + ($formCount / 10)), [
                'form_count' => $formCount,
            ]);
        }

        if ($fieldCount >= 3) {
            $signals[] = $this->signal('input_density', min(0.95, 0.5 + ($fieldCount / 20)), [
                'field_count' => $fieldCount,
            ]);
        }

        if ($buttonCount >= 2) {
            $signals[] = $this->signal('button_density', min(0.95, 0.5 + ($buttonCount / 15)), [
                'button_count' => $buttonCount,
            ]);
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectChallengeIndicators(ParsedDocument $document, string $textIndex, array &$signals): void
    {
        $matches = $this->matchedTerms($textIndex, ['captcha', 'challenge', 'verification', 'verify you are human', 'security check']);
        if ($matches !== []) {
            $signals[] = $this->signal('challenge_indicator', 0.88, [
                'matched_terms' => $matches,
                'title' => $document->title,
            ]);
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectConfirmationIndicators(ParsedDocument $document, string $textIndex, array &$signals): void
    {
        $matches = $this->matchedTerms($textIndex, ['confirmation', 'confirmed', 'complete', 'success', 'thank you', 'receipt']);
        if ($matches !== []) {
            $signals[] = $this->signal('confirmation_indicator', 0.84, [
                'matched_terms' => $matches,
                'title' => $document->title,
            ]);
        }
    }

    /**
     * @param DetectedSignal[] $signals
     */
    private function detectProgressIndicators(ParsedDocument $document, string $textIndex, array &$signals): void
    {
        $matches = $this->matchedTerms($textIndex, ['progress', 'step 1', 'step 2', 'next step']);
        if ($matches !== []) {
            $signals[] = $this->signal('progress_indicator', 0.78, [
                'matched_terms' => $matches,
                'heading_count' => count($document->headings),
            ]);
        }
    }

    private function signal(string $type, float $confidence, array $evidence): DetectedSignal
    {
        return new DetectedSignal(
            type: $type,
            confidence: round($confidence, 2),
            evidence: $evidence,
            metadata: [
                'read_only' => true,
                'observational_only' => true,
                'non_executable' => true,
            ],
        );
    }

    private function textIndex(ParsedDocument $document): string
    {
        $parts = [$document->title];

        foreach ($document->headings as $heading) {
            $parts[] = $heading->text;
        }

        foreach ($document->links as $link) {
            $parts[] = $link->text;
            $parts[] = $link->href;
        }

        foreach ($document->forms as $form) {
            $parts[] = $form->id;
            $parts[] = $form->name;
            $parts[] = $form->action;

            foreach ($this->formFields($form) as $field) {
                $parts[] = (string)($field['type'] ?? '');
                $parts[] = (string)($field['name'] ?? '');
                $parts[] = (string)($field['label'] ?? '');
                $parts[] = (string)($field['value'] ?? '');
                $parts[] = (string)($field['placeholder'] ?? '');
            }
        }

        return strtolower(implode(' ', array_filter($parts)));
    }

    private function fieldsText(array $fields): string
    {
        $parts = [];
        foreach ($fields as $field) {
            $parts[] = (string)($field['type'] ?? '');
            $parts[] = (string)($field['name'] ?? '');
            $parts[] = (string)($field['label'] ?? '');
            $parts[] = (string)($field['value'] ?? '');
            $parts[] = (string)($field['placeholder'] ?? '');
        }

        return strtolower(implode(' ', array_filter($parts)));
    }

    private function formFields(object $form): array
    {
        return array_map(
            static fn ($field): array => $field->toArray(),
            array_merge($form->fields, $form->buttons),
        );
    }

    private function matchedTerms(string $text, array $terms): array
    {
        $matches = [];
        foreach ($terms as $term) {
            if (str_contains($text, $term)) {
                $matches[] = $term;
            }
        }

        return array_values(array_unique($matches));
    }
}
