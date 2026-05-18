<?php

declare(strict_types=1);

namespace Structora\DOM;

final class ParsedField
{
    public function __construct(
        public readonly string $tag,
        public readonly string $type = '',
        public readonly string $name = '',
        public readonly string $id = '',
        public readonly string $label = '',
        public readonly string $value = '',
        public readonly string $placeholder = '',
        public readonly bool $required = false,
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'tag' => $this->tag,
            'type' => $this->type,
            'name' => $this->name,
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
            'placeholder' => $this->placeholder,
            'required' => $this->required,
            'metadata' => $this->metadata,
        ];
    }
}
