<?php

declare(strict_types=1);

namespace Structora\Rendering;

final class StaticHtmlRenderer implements RendererInterface
{
    public function render(string $source, array $options = []): RenderResult
    {
        $startedAt = microtime(true);
        $html = $this->normalize($source);
        $durationMs = max(0, (int)round((microtime(true) - $startedAt) * 1000));

        return new RenderResult(
            status: true,
            document: new RenderedDocument(
                html: $html,
                source: (string)($options['source'] ?? ''),
                metadata: [
                    'read_only' => true,
                    'local_only' => true,
                    'normalized' => true,
                ],
            ),
            metadata: new RenderedMetadata(
                rendered: true,
                strategy: 'static_html',
                durationMs: $durationMs,
                details: [
                    'read_only' => true,
                    'local_only' => true,
                    'actions_performed' => [],
                    'mutation_performed' => false,
                ],
            ),
        );
    }

    private function normalize(string $html): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $html);
        $normalized = preg_replace('/></', ">\n<", trim($normalized));

        return is_string($normalized) ? $normalized : trim($html);
    }
}
