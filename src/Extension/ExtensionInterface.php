<?php

declare(strict_types=1);

namespace Structora\Extension;

use Structora\Core\DiscoveryEngine;

interface ExtensionInterface
{
    public function boot(DiscoveryEngine $engine): void;
}
