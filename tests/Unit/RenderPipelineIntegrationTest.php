<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Rendering\StaticHtmlRenderer;

final class RenderPipelineIntegrationTest extends TestCase
{
    public function testDiscoveryUsesRendererWhenRenderingIsEnabled(): void
    {
        $html = $this->fixture('rendered-search-page.html');
        $engine = (new DiscoveryEngine())->withRenderer(new StaticHtmlRenderer());

        $payload = $engine->discover($html, DiscoveryOptions::fromArray([
            'source' => 'rendered-search-page',
            'rendering_enabled' => true,
        ]))->toArray();

        self::assertTrue($payload['rendering']['rendered']);
        self::assertSame('static_html', $payload['rendering']['strategy']);
        self::assertTrue($payload['summary']['rendered']);
        self::assertSame('static_html', $payload['summary']['render_strategy']);
        self::assertSame(1, $payload['summary']['form_count']);
        self::assertContains('search_flow', $payload['workflow_summary']['workflow_types']);
    }

    public function testDiscoverySkipsRendererWhenRenderingIsDisabled(): void
    {
        $html = $this->fixture('rendered-search-page.html');
        $engine = (new DiscoveryEngine())->withRenderer(new StaticHtmlRenderer());

        $payload = $engine->discover($html, DiscoveryOptions::fromArray([
            'rendering_enabled' => false,
        ]))->toArray();

        self::assertFalse($payload['rendering']['rendered']);
        self::assertSame('not_requested', $payload['rendering']['strategy']);
        self::assertFalse($payload['summary']['rendered']);
        self::assertTrue($payload['metadata']['renderer_configured']);
    }

    public function testRenderingMetadataIsReadOnlyAndNonExecutable(): void
    {
        $payload = (new StaticHtmlRenderer())->render($this->fixture('rendered-navigation-page.html'))->toArray();

        self::assertTrue($payload['metadata']['read_only']);
        self::assertTrue($payload['metadata']['non_executable']);
        self::assertSame([], $payload['metadata']['details']['actions_performed']);
    }

    private function fixture(string $fixture): string
    {
        $html = file_get_contents(dirname(__DIR__, 2) . '/examples/fixtures/' . $fixture);

        return $html ?: '';
    }
}
