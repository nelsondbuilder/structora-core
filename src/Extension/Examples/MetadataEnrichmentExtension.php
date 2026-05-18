<?php

declare(strict_types=1);

namespace Structora\Extension\Examples;

use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Extension\ResultEnricherInterface;

final class MetadataEnrichmentExtension implements ResultEnricherInterface
{
    public function enrich(DiscoveryResult $result, DiscoveryOptions $options): DiscoveryResult
    {
        $metadata = $result->metadata;
        $metadata['extension_metadata'] = [
            'source_label' => $result->source,
            'input_metadata_keys' => array_keys($options->metadata),
            'read_only' => true,
            'non_executable' => true,
        ];

        return $result->with(['metadata' => $metadata]);
    }
}
