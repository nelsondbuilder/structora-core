<?php

declare(strict_types=1);

namespace Structora\Rendering;

final class RenderResult
{
    public function __construct(
        public readonly bool $status,
        public readonly RenderedDocument $document,
        public readonly RenderedMetadata $metadata,
        public readonly array $errors = [],
    ) {
    }

    public static function skipped(string $source = '', string $strategy = 'none'): self
    {
        return new self(
            status: true,
            document: new RenderedDocument(source: $source),
            metadata: new RenderedMetadata(
                rendered: false,
                strategy: $strategy,
                details: [
                    'read_only' => true,
                    'reason' => 'Rendering not requested.',
                ],
            ),
        );
    }

    public function toArray(bool $includeHtml = false): array
    {
        $payload = [
            'status' => $this->status,
            'rendered' => $this->metadata->rendered,
            'strategy' => $this->metadata->strategy,
            'duration_ms' => $this->metadata->durationMs,
            'source' => $this->document->source,
            'document_length' => strlen($this->document->html),
            'metadata' => $this->metadata->toArray(),
            'errors' => $this->errors,
        ];

        if ($includeHtml) {
            $payload['html'] = $this->document->html;
        }

        return $payload;
    }
}
