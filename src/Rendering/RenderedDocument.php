<?php

declare(strict_types=1);

namespace Structora\Rendering;

final class RenderedDocument
{
    public function __construct(
        public readonly string $html = '',
        public readonly string $source = '',
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'html' => $this->html,
            'source' => $this->source,
            'metadata' => $this->metadata,
        ];
    }
}
