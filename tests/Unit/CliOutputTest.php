<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryResult;

final class CliOutputTest extends TestCase
{
    public function testVersionCommandReturnsValidJson(): void
    {
        $payload = $this->runJsonCommand('version');

        self::assertSame('structora/core', $payload['name']);
        self::assertSame(DiscoveryResult::SCHEMA_VERSION, $payload['schema_version']);
        self::assertTrue($payload['read_only']);
    }

    public function testSummaryFileCommandReturnsSummaryPayload(): void
    {
        $payload = $this->runJsonCommand('summary-file examples/fixtures/synthetic-auth-flow.html');

        self::assertSame(DiscoveryResult::SCHEMA_VERSION, $payload['schema_version']);
        self::assertSame('Synthetic Authentication Flow', $payload['title']);
        self::assertSame(1, $payload['summary']['signal_count']);
        self::assertSame(2, $payload['summary']['workflow_count']);
        self::assertContains('auth_flow', $payload['summary']['workflow_types']);
        self::assertArrayHasKey('auth_flow', $payload['workflow_confidence']);
        self::assertTrue($payload['read_only']);
        self::assertTrue($payload['non_executable']);
    }

    public function testInspectFileCommandReturnsValidJsonWithStableKeys(): void
    {
        $payload = $this->runJsonCommand('inspect-file examples/fixtures/synthetic-search-flow.html');

        self::assertArrayHasKey('schema_version', $payload);
        self::assertArrayHasKey('metadata', $payload);
        self::assertArrayHasKey('workflow_summary', $payload);
        self::assertContains('search_flow', $payload['workflow_summary']['workflow_types']);
    }

    private function runJsonCommand(string $arguments): array
    {
        $root = dirname(__DIR__, 2);
        $command = escapeshellarg(PHP_BINARY)
            . ' '
            . escapeshellarg($root . '/bin/structora')
            . ' '
            . $arguments;

        exec($command, $output, $exitCode);

        self::assertSame(0, $exitCode, implode(PHP_EOL, $output));

        return json_decode(implode(PHP_EOL, $output), true, flags: JSON_THROW_ON_ERROR);
    }
}
