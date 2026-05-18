<?php

declare(strict_types=1);

namespace Structora\Export;

use Structora\Core\DiscoveryResult;

interface ExporterInterface
{
    public function export(DiscoveryResult $result, array $options = []): ExportResult;
}
