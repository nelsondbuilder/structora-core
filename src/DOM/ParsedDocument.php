<?php

declare(strict_types=1);

namespace Structora\DOM;

final class ParsedDocument
{
    public function __construct(
        public readonly string $source = '',
        public readonly array $nodes = [],
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'nodes' => $this->nodes,
            'metadata' => $this->metadata,
        ];
    }
}
