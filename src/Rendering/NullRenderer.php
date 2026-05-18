<?php

declare(strict_types=1);

namespace Structora\Rendering;

final class NullRenderer implements RendererInterface
{
    public function render(string $source, array $options = []): RenderResult
    {
        return RenderResult::skipped(
            source: (string)($options['source'] ?? ''),
            strategy: 'none',
        );
    }
}
