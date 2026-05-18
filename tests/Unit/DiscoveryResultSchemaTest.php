<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryResult;

final class DiscoveryResultSchemaTest extends TestCase
{
    public function testDiscoveryResultExposesStableSchemaKeys(): void
    {
        $payload = (new DiscoveryEngine())->discover('<html><body><h1>Schema</h1></body></html>')->toArray();

        self::assertSame([
            'schema_version',
            'generated_at',
            'status',
            'source',
            'title',
            'summary',
            'metadata',
            'forms',
            'links',
            'headings',
            'signals',
            'signal_summary',
            'workflow',
            'workflow_summary',
            'interpretation',
        ], array_keys($payload));
    }

    public function testSchemaVersionAndGeneratedAtArePresent(): void
    {
        $payload = (new DiscoveryEngine())->discover('<html></html>')->toArray();

        self::assertSame(DiscoveryResult::SCHEMA_VERSION, $payload['schema_version']);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T/', $payload['generated_at']);
    }

    public function testEmptySectionsNormalizeToArraysAndStableMetadata(): void
    {
        $payload = DiscoveryResult::empty('empty-source')->toArray();

        self::assertSame([], $payload['forms']);
        self::assertSame([], $payload['links']);
        self::assertSame([], $payload['headings']);
        self::assertSame([], $payload['signals']);
        self::assertSame([], $payload['workflow']);
        self::assertTrue($payload['metadata']['read_only']);
        self::assertFalse($payload['metadata']['execution_required']);
        self::assertFalse($payload['metadata']['network_access']);
        self::assertFalse($payload['metadata']['filesystem_writes']);
        self::assertSame('structora-core', $payload['metadata']['engine']['name']);
        self::assertSame(DiscoveryResult::SCHEMA_VERSION, $payload['metadata']['engine']['schema_version']);
    }
}
