<?php

declare(strict_types=1);

namespace Structora\Workflow;

use Structora\Detection\DetectedSignal;
use Structora\DOM\ParsedDocument;

interface WorkflowMapperInterface
{
    /**
     * @param DetectedSignal[] $signals
     */
    public function map(ParsedDocument $document, array $signals = [], array $options = []): WorkflowMap;
}
