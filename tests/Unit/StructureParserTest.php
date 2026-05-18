<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\DOM\ParsedDocument;
use Structora\DOM\StructureParser;
use Structora\DOM\StructureParserInterface;

final class StructureParserTest extends TestCase
{
    public function testItImplementsThePublicParserContract(): void
    {
        self::assertInstanceOf(StructureParserInterface::class, new StructureParser());
    }

    public function testItExtractsTitleFormsFieldsButtonsHeadingsAndLinks(): void
    {
        $document = $this->parseFixture('synthetic-login-form.html');

        self::assertSame('Synthetic Access Form', $document->title);
        self::assertCount(1, $document->forms);
        self::assertCount(2, $document->forms[0]->fields);
        self::assertCount(1, $document->forms[0]->buttons);
        self::assertSame('email', $document->forms[0]->fields[0]->name);
        self::assertSame('Email address', $document->forms[0]->fields[0]->label);
        self::assertTrue($document->forms[0]->fields[0]->required);
        self::assertSame('Continue', $document->forms[0]->buttons[0]->value);
        self::assertSame('Developer Access', $document->headings[0]->text);
        self::assertSame('/synthetic/help', $document->links[0]->href);
    }

    public function testItExtractsMultipleFormsAndHeadings(): void
    {
        $document = $this->parseFixture('synthetic-multi-step-page.html');

        self::assertSame('Synthetic Multi-Step Workflow', $document->title);
        self::assertCount(2, $document->forms);
        self::assertSame('contact-step', $document->forms[0]->id);
        self::assertSame('preferences-step', $document->forms[1]->id);
        self::assertSame(3, $document->summary['heading_count']);
        self::assertSame('Step 2: Preferences', $document->headings[2]->text);
    }

    public function testItToleratesMalformedHtml(): void
    {
        $parser = new StructureParser();

        $document = $parser->parse('<html><head><title>Broken Fixture</title><body><h1>Open<form><input name="q"><button>Go');

        self::assertInstanceOf(ParsedDocument::class, $document);
        self::assertSame('Broken Fixture', $document->title);
        self::assertCount(1, $document->forms);
        self::assertCount(1, $document->forms[0]->fields);
        self::assertCount(1, $document->forms[0]->buttons);
        self::assertGreaterThanOrEqual(0, $document->metadata['libxml_error_count']);
    }

    public function testItGeneratesReadOnlyMetadataAndSummary(): void
    {
        $document = $this->parseFixture('synthetic-search-page.html');

        self::assertTrue($document->metadata['read_only']);
        self::assertFalse($document->metadata['network_access']);
        self::assertFalse($document->metadata['filesystem_writes']);
        self::assertSame(1, $document->summary['form_count']);
        self::assertSame(2, $document->summary['field_count']);
        self::assertSame(1, $document->summary['button_count']);
        self::assertSame(2, $document->summary['link_count']);
        self::assertSame(2, $document->summary['heading_count']);
    }

    private function parseFixture(string $fixture): ParsedDocument
    {
        $html = file_get_contents(dirname(__DIR__, 2) . '/examples/fixtures/' . $fixture);

        return (new StructureParser())->parse($html ?: '', [
            'source' => $fixture,
        ]);
    }
}
