<?php

declare(strict_types=1);

namespace Structora\Export;

final class ExportResult
{
    public function __construct(
        public readonly string $format,
        public readonly string $content,
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'format' => $this->format,
            'content' => $this->content,
            'metadata' => array_merge([
                'read_only' => true,
                'non_executable' => true,
                'bytes' => strlen($this->content),
            ], $this->metadata),
        ];
    }
}
