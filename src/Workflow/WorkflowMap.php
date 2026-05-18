<?php

declare(strict_types=1);

namespace Structora\Workflow;

final class WorkflowMap
{
    public function __construct(
        public readonly array $steps = [],
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'steps' => $this->steps,
            'metadata' => $this->metadata,
        ];
    }
}
