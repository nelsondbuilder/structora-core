<?php

declare(strict_types=1);

namespace Structora\Workflow;

use Structora\Detection\DetectedSignal;
use Structora\Detection\SignalCollection;
use Structora\DOM\ParsedDocument;

final class WorkflowMapper implements WorkflowMapperInterface
{
    public function map(ParsedDocument $document, SignalCollection $signals, array $options = []): WorkflowMap
    {
        $states = [];
        $signalByType = $this->signalsByType($signals);

        $this->mapSignalState($signalByType, 'search_form', 'search_flow', $states);
        $this->mapSignalState($signalByType, 'auth_like_form', 'auth_flow', $states);
        $this->mapSignalState($signalByType, 'multi_step_indicator', 'multi_step_flow', $states);
        $this->mapSignalState($signalByType, 'challenge_indicator', 'challenge_flow', $states);
        $this->mapSignalState($signalByType, 'confirmation_indicator', 'confirmation_flow', $states);
        $this->mapSignalState($signalByType, 'navigation_heavy_page', 'navigation_hub', $states);

        if (count($document->forms) > 0) {
            $states[] = $this->state('form_flow', min(0.9, 0.55 + (count($document->forms) / 10)), [
                'form_count' => count($document->forms),
                'field_count' => (int)($document->summary['field_count'] ?? 0),
                'button_count' => (int)($document->summary['button_count'] ?? 0),
            ]);
        }

        if ($states === []) {
            $states[] = $this->state('informational_page', 0.62, [
                'heading_count' => count($document->headings),
                'link_count' => count($document->links),
                'title_present' => $document->title !== '',
            ]);
        }

        return new WorkflowMap(
            states: $states,
            metadata: [
                'read_only' => true,
                'observational_only' => true,
                'non_executable' => true,
                'mapper' => self::class,
            ],
        );
    }

    /**
     * @param array<string, DetectedSignal> $signalByType
     * @param WorkflowState[] $states
     */
    private function mapSignalState(array $signalByType, string $signalType, string $workflowType, array &$states): void
    {
        if (!isset($signalByType[$signalType])) {
            return;
        }

        $signal = $signalByType[$signalType];
        $states[] = $this->state($workflowType, $signal->confidence, [
            'source_signal' => $signal->type,
            'signal_confidence' => $signal->confidence,
            'signal_evidence' => $signal->evidence,
        ]);
    }

    private function state(string $type, float $confidence, array $evidence): WorkflowState
    {
        return new WorkflowState(
            type: $type,
            confidence: round($confidence, 2),
            evidence: $evidence,
            metadata: [
                'read_only' => true,
                'observational_only' => true,
                'non_executable' => true,
            ],
        );
    }

    /**
     * @return array<string, DetectedSignal>
     */
    private function signalsByType(SignalCollection $signals): array
    {
        $indexed = [];
        foreach ($signals->signals as $signal) {
            $indexed[$signal->type] = $signal;
        }

        return $indexed;
    }
}
