<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryResult;
use Structora\Export\MarkdownExporter;

final class MarkdownExporterTest extends TestCase
{
    public function testMarkdownExporterProducesStableStructure(): void
    {
        $result = new DiscoveryResult(
            status: true,
            source: 'synthetic',
            summary: [],
            title: 'Synthetic',
            signalSummary: ['types' => ['search_form']],
            workflowSummary: ['workflow_types' => ['search_flow']],
            generatedAt: '2026-05-19T00:00:00+00:00',
        );

        $export = (new MarkdownExporter())->export($result);

        self::assertSame('markdown', $export->format);
        self::assertStringContainsString('# Structora Discovery Summary', $export->content);
        self::assertStringContainsString('| Source | `synthetic` |', $export->content);
        self::assertStringContainsString('- `search_form`', $export->content);
        self::assertStringContainsString('- `search_flow`', $export->content);
        self::assertTrue($export->metadata['preserves_metadata']);
    }

    public function testMarkdownExporterHandlesEmptySignalsAndWorkflow(): void
    {
        $export = (new MarkdownExporter())->export(DiscoveryResult::empty('empty'));

        self::assertStringContainsString('- none', $export->content);
        self::assertTrue($export->metadata['read_only'] ?? true);
    }
}
