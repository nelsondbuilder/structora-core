<?php

declare(strict_types=1);

namespace Structora\Interpretation;

interface InterpretationProviderInterface
{
    public function interpret(array $discoveryResult): array;
}
