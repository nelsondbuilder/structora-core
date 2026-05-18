<?php

declare(strict_types=1);

namespace Structora\Rendering;

final class RenderedMetadata
{
    public function __construct(
        public readonly bool $rendered = false,
        public readonly string $strategy = 'none',
        public readonly int $durationMs = 0,
        public readonly array $details = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'rendered' => $this->rendered,
            'strategy' => $this->strategy,
            'duration_ms' => $this->durationMs,
            'details' => $this->details,
            'read_only' => true,
            'non_executable' => true,
        ];
    }
}
