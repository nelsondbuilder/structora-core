<?php

declare(strict_types=1);

namespace Structora\DOM;

final class ParsedForm
{
    /**
     * @param ParsedField[] $fields
     * @param ParsedField[] $buttons
     */
    public function __construct(
        public readonly string $method = 'get',
        public readonly string $action = '',
        public readonly string $id = '',
        public readonly string $name = '',
        public readonly array $fields = [],
        public readonly array $buttons = [],
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'action' => $this->action,
            'id' => $this->id,
            'name' => $this->name,
            'fields' => array_map(
                static fn (ParsedField $field): array => $field->toArray(),
                $this->fields,
            ),
            'buttons' => array_map(
                static fn (ParsedField $button): array => $button->toArray(),
                $this->buttons,
            ),
            'metadata' => $this->metadata,
        ];
    }
}
