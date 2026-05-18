<?php

declare(strict_types=1);

namespace Structora\Rendering\Adapters;

use Structora\Rendering\RenderedDocument;
use Structora\Rendering\RenderedMetadata;
use Structora\Rendering\RendererInterface;
use Structora\Rendering\RenderResult;

final class PlaywrightRenderer implements RendererInterface
{
    public function __construct(
        private readonly bool $enabled = false,
    ) {
    }

    public function render(string $source, array $options = []): RenderResult
    {
        if (!$this->enabled) {
            return new RenderResult(
                status: false,
                document: new RenderedDocument(source: (string)($options['source'] ?? '')),
                metadata: new RenderedMetadata(
                    rendered: false,
                    strategy: 'playwright_disabled',
                    details: [
                        'read_only' => true,
                        'enabled' => false,
                        'reason' => 'Playwright rendering is an optional adapter scaffold and is disabled by default.',
                    ],
                ),
                errors: ['Playwright renderer is disabled by default.'],
            );
        }

        return new RenderResult(
            status: false,
            document: new RenderedDocument(source: (string)($options['source'] ?? '')),
            metadata: new RenderedMetadata(
                rendered: false,
                strategy: 'playwright_scaffold',
                details: [
                    'read_only' => true,
                    'actions_allowed' => [],
                    'intended_steps' => [
                        'open page',
                        'wait for DOM readiness',
                        'extract rendered HTML snapshot',
                    ],
                    'implementation_required' => true,
                ],
            ),
            errors: ['Playwright adapter scaffold does not include a runtime implementation.'],
        );
    }
}
