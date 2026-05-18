<?php

declare(strict_types=1);

namespace Structora\Detection;

final class SignalCollection
{
    /**
     * @param DetectedSignal[] $signals
     */
    public function __construct(
        public readonly array $signals = [],
    ) {
    }

    /**
     * @param DetectedSignal[] $signals
     */
    public static function fromSignals(array $signals): self
    {
        return new self($signals);
    }

    public function toArray(): array
    {
        return array_map(
            static fn (DetectedSignal $signal): array => $signal->toArray(),
            $this->signals,
        );
    }

    public function summary(): array
    {
        $types = array_map(
            static fn (DetectedSignal $signal): string => $signal->type,
            $this->signals,
        );

        $confidenceByType = [];
        foreach ($this->signals as $signal) {
            $confidenceByType[$signal->type] = $signal->confidence;
        }

        return [
            'count' => count($this->signals),
            'types' => $types,
            'confidence' => $confidenceByType,
            'read_only' => true,
            'non_executable' => true,
        ];
    }
}
