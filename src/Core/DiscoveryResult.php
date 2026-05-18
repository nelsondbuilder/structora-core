<?php

declare(strict_types=1);

namespace Structora\Core;

final class DiscoveryResult
{
    public function __construct(
        public readonly bool $status,
        public readonly string $source,
        public readonly array $summary,
        public readonly string $title = '',
        public readonly array $forms = [],
        public readonly array $links = [],
        public readonly array $headings = [],
        public readonly array $signals = [],
        public readonly array $workflow = [],
        public readonly array $interpretation = [],
        public readonly array $metadata = [],
    ) {
    }

    public static function empty(string $source = ''): self
    {
        return new self(
            status: true,
            source: $source,
            summary: [
                'engine' => 'structora-core',
                'mode' => 'read_only',
                'message' => 'Discovery engine scaffold initialized.',
            ],
            metadata: [
                'read_only' => true,
                'execution_required' => false,
            ],
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'source' => $this->source,
            'summary' => $this->summary,
            'title' => $this->title,
            'forms' => $this->forms,
            'links' => $this->links,
            'headings' => $this->headings,
            'signals' => $this->signals,
            'workflow' => $this->workflow,
            'interpretation' => $this->interpretation,
            'metadata' => $this->metadata,
        ];
    }
}
