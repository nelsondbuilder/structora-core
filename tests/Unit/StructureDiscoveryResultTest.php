<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryResult;

final class StructureDiscoveryResultTest extends TestCase
{
    public function testStructuredDiscoveryResultSerializesParsedStructure(): void
    {
        $result = new DiscoveryResult(
            status: true,
            source: 'synthetic',
            summary: ['form_count' => 1],
            title: 'Synthetic Page',
            forms: [['id' => 'search-form']],
            links: [['href' => '/synthetic/docs']],
            headings: [['level' => 1, 'text' => 'Synthetic']],
            metadata: [
                'read_only' => true,
                'parser' => 'SyntheticParser',
            ],
        );

        $payload = $result->toArray();

        self::assertSame('Synthetic Page', $payload['title']);
        self::assertSame([['id' => 'search-form']], $payload['forms']);
        self::assertSame([['href' => '/synthetic/docs']], $payload['links']);
        self::assertSame([['level' => 1, 'text' => 'Synthetic']], $payload['headings']);
        self::assertSame('SyntheticParser', $payload['metadata']['parser']);
    }

    public function testDiscoverFileCliReadsLocalFilesOnly(): void
    {
        $root = dirname(__DIR__, 2);
        $command = escapeshellarg(PHP_BINARY)
            . ' '
            . escapeshellarg($root . '/bin/structora')
            . ' discover-file '
            . escapeshellarg($root . '/examples/fixtures/synthetic-login-form.html');

        exec($command, $output, $exitCode);

        self::assertSame(0, $exitCode);
        $payload = json_decode(implode(PHP_EOL, $output), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('Synthetic Access Form', $payload['title']);
        self::assertSame(1, $payload['summary']['form_count']);
        self::assertTrue($payload['metadata']['read_only']);
    }

    public function testDiscoverFileCliRejectsRemoteUrls(): void
    {
        $root = dirname(__DIR__, 2);
        $command = escapeshellarg(PHP_BINARY)
            . ' '
            . escapeshellarg($root . '/bin/structora')
            . ' discover-file https://example.test/page.html 2>&1';

        exec($command, $output, $exitCode);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('Remote URLs are not supported', implode(PHP_EOL, $output));
    }
}
