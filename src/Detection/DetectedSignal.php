<?php

declare(strict_types=1);

namespace Structora\Detection;

final class DetectedSignal
{
    public function __construct(
        public readonly string $type,
        public readonly string $label = '',
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label,
            'metadata' => $this->metadata,
        ];
    }
}
