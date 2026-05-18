<?php

declare(strict_types=1);

namespace Structora\Extension\Examples;

use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Extension\ResultEnricherInterface;

final class WorkflowSummaryExtension implements ResultEnricherInterface
{
    public function enrich(DiscoveryResult $result, DiscoveryOptions $options): DiscoveryResult
    {
        $metadata = $result->metadata;
        $metadata['extension_workflow_summary'] = [
            'workflow_count' => count($result->workflow),
            'workflow_types' => $result->workflowSummary['workflow_types'] ?? [],
            'read_only' => true,
        ];

        return $result->with(['metadata' => $metadata]);
    }
}
