<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryResult;
use Structora\Export\JsonExporter;
use Structora\Export\SummaryExporter;

final class JsonExporterTest extends TestCase
{
    public function testJsonExporterProducesDeterministicJsonAndPreservesMetadata(): void
    {
        $result = $this->fixtureResult();
        $export = (new JsonExporter())->export($result);

        $payload = json_decode($export->content, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('json', $export->format);
        self::assertSame('0.1.0-alpha', $payload['schema_version']);
        self::assertSame('fixed-source', $payload['source']);
        self::assertTrue($payload['metadata']['read_only']);
        self::assertTrue($export->metadata['preserves_metadata']);
        self::assertSame($export->content, (new JsonExporter())->export($result)->content);
    }

    public function testSummaryExporterHandlesEmptyState(): void
    {
        $export = (new SummaryExporter())->export(DiscoveryResult::empty('empty'));

        self::assertSame('summary', $export->format);
        self::assertStringContainsString('Structora Discovery Summary', $export->content);
        self::assertStringContainsString('Signals: 0 []', $export->content);
        self::assertStringContainsString('Read-only: true', $export->content);
    }

    private function fixtureResult(): DiscoveryResult
    {
        return new DiscoveryResult(
            status: true,
            source: 'fixed-source',
            summary: ['form_count' => 1],
            title: 'Fixed',
            metadata: ['read_only' => true],
            schemaVersion: DiscoveryResult::SCHEMA_VERSION,
            generatedAt: '2026-05-19T00:00:00+00:00',
        );
    }
}
