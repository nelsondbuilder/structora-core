<?php

declare(strict_types=1);

namespace Structora\DOM;

final class ParsedLink
{
    public function __construct(
        public readonly string $href,
        public readonly string $text = '',
        public readonly string $title = '',
        public readonly string $rel = '',
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'href' => $this->href,
            'text' => $this->text,
            'title' => $this->title,
            'rel' => $this->rel,
            'metadata' => $this->metadata,
        ];
    }
}
