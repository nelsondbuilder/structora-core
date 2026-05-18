<?php

declare(strict_types=1);

namespace Structora\Workflow;

final class WorkflowState
{
    public function __construct(
        public readonly string $type,
        public readonly float $confidence,
        public readonly array $evidence = [],
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'confidence' => $this->confidence,
            'evidence' => $this->evidence,
            'metadata' => $this->metadata,
        ];
    }
}
