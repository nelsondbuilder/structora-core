<?php

declare(strict_types=1);

namespace Structora\DOM;

final class ParsedDocument
{
    /**
     * @param ParsedForm[] $forms
     * @param ParsedLink[] $links
     * @param ParsedHeading[] $headings
     */
    public function __construct(
        public readonly string $source = '',
        public readonly string $title = '',
        public readonly array $forms = [],
        public readonly array $links = [],
        public readonly array $headings = [],
        public readonly array $metadata = [],
        public readonly array $summary = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'title' => $this->title,
            'forms' => array_map(
                static fn (ParsedForm $form): array => $form->toArray(),
                $this->forms,
            ),
            'links' => array_map(
                static fn (ParsedLink $link): array => $link->toArray(),
                $this->links,
            ),
            'headings' => array_map(
                static fn (ParsedHeading $heading): array => $heading->toArray(),
                $this->headings,
            ),
            'metadata' => $this->metadata,
            'summary' => $this->summary,
        ];
    }
}
