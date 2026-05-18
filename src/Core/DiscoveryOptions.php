<?php

declare(strict_types=1);

namespace Structora\Core;

final class DiscoveryOptions
{
    public function __construct(
        public readonly string $source = '',
        public readonly bool $renderingEnabled = false,
        public readonly bool $interpretationEnabled = false,
        public readonly array $metadata = [],
    ) {
    }

    public static function fromArray(array $options): self
    {
        return new self(
            source: (string)($options['source'] ?? ''),
            renderingEnabled: (bool)($options['rendering_enabled'] ?? false),
            interpretationEnabled: (bool)($options['interpretation_enabled'] ?? false),
            metadata: is_array($options['metadata'] ?? null) ? $options['metadata'] : [],
        );
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'rendering_enabled' => $this->renderingEnabled,
            'interpretation_enabled' => $this->interpretationEnabled,
            'metadata' => $this->metadata,
        ];
    }
}
