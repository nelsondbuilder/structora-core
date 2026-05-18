<?php

declare(strict_types=1);

namespace Structora\DOM;

final class ParsedHeading
{
    public function __construct(
        public readonly int $level,
        public readonly string $text,
        public readonly string $id = '',
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'level' => $this->level,
            'text' => $this->text,
            'id' => $this->id,
            'metadata' => $this->metadata,
        ];
    }
}
