<?php

declare(strict_types=1);

namespace Structora\Extension\Examples;

use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Extension\ResultEnricherInterface;

final class SignalSummaryExtension implements ResultEnricherInterface
{
    public function enrich(DiscoveryResult $result, DiscoveryOptions $options): DiscoveryResult
    {
        $metadata = $result->metadata;
        $metadata['extension_signal_summary'] = [
            'signal_count' => count($result->signals),
            'signal_types' => $result->signalSummary['types'] ?? [],
            'read_only' => true,
        ];

        return $result->with(['metadata' => $metadata]);
    }
}
