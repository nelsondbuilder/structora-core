<?php

declare(strict_types=1);

namespace Structora\Workflow;

use Structora\Detection\SignalCollection;
use Structora\DOM\ParsedDocument;

interface WorkflowMapperInterface
{
    public function map(ParsedDocument $document, SignalCollection $signals, array $options = []): WorkflowMap;
}
