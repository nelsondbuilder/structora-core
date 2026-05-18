<?php

declare(strict_types=1);

namespace Structora\Interpretation;

final class NullInterpretationProvider implements InterpretationProviderInterface
{
    public function interpret(array $discoveryResult): array
    {
        return [
            'enabled' => false,
            'provider' => 'none',
            'message' => 'No interpretation provider configured.',
            'read_only' => true,
            'execution_required' => false,
        ];
    }
}
