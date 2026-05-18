<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Rendering\NullRenderer;
use Structora\Rendering\RenderResult;
use Structora\Rendering\StaticHtmlRenderer;

final class StaticHtmlRendererTest extends TestCase
{
    public function testStaticHtmlRendererReturnsStableRenderResult(): void
    {
        $html = "<html>\r\n<body><h1>Rendered</h1></body></html>";
        $result = (new StaticHtmlRenderer())->render($html, ['source' => 'inline']);
        $payload = $result->toArray();

        self::assertInstanceOf(RenderResult::class, $result);
        self::assertTrue($payload['status']);
        self::assertTrue($payload['rendered']);
        self::assertSame('static_html', $payload['strategy']);
        self::assertSame('inline', $payload['source']);
        self::assertGreaterThan(0, $payload['document_length']);
        self::assertSame([], $payload['errors']);
    }

    public function testRenderedHtmlIsNormalizedWithoutActions(): void
    {
        $result = (new StaticHtmlRenderer())->render("<html><body><h1>Rendered</h1></body></html>");

        self::assertStringContainsString(">\n<", $result->document->html);
        self::assertSame([], $result->metadata->details['actions_performed']);
        self::assertFalse($result->metadata->details['mutation_performed']);
        self::assertTrue($result->metadata->details['read_only']);
    }

    public function testNullRendererUsesSafeDefaults(): void
    {
        $result = (new NullRenderer())->render('<html></html>', ['source' => 'ignored']);
        $payload = $result->toArray();

        self::assertTrue($payload['status']);
        self::assertFalse($payload['rendered']);
        self::assertSame('none', $payload['strategy']);
        self::assertSame(0, $payload['document_length']);
        self::assertTrue($payload['metadata']['read_only']);
        self::assertTrue($payload['metadata']['non_executable']);
    }

    public function testRenderTimingMetadataIsPresent(): void
    {
        $payload = (new StaticHtmlRenderer())->render('<html></html>')->toArray();

        self::assertArrayHasKey('duration_ms', $payload);
        self::assertGreaterThanOrEqual(0, $payload['duration_ms']);
        self::assertArrayHasKey('duration_ms', $payload['metadata']);
    }
}
