<?php

declare(strict_types=1);

namespace Structora\Workflow;

final class WorkflowCollection
{
    /**
     * @param WorkflowState[] $states
     */
    public function __construct(
        public readonly array $states = [],
    ) {
    }

    /**
     * @param WorkflowState[] $states
     */
    public static function fromStates(array $states): self
    {
        return new self($states);
    }

    public function toArray(): array
    {
        return array_map(
            static fn (WorkflowState $state): array => $state->toArray(),
            $this->states,
        );
    }

    public function summary(): array
    {
        $types = array_map(
            static fn (WorkflowState $state): string => $state->type,
            $this->states,
        );

        $confidenceSummary = [];
        foreach ($this->states as $state) {
            $confidenceSummary[$state->type] = $state->confidence;
        }

        return [
            'workflow_count' => count($this->states),
            'workflow_types' => $types,
            'confidence_summary' => $confidenceSummary,
            'read_only' => true,
            'non_executable' => true,
        ];
    }
}
