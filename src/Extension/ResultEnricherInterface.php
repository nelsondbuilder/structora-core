<?php

declare(strict_types=1);

namespace Structora\Extension;

use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;

interface ResultEnricherInterface
{
    public function enrich(DiscoveryResult $result, DiscoveryOptions $options): DiscoveryResult;
}
