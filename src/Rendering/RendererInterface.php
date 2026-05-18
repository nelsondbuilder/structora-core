<?php

declare(strict_types=1);

namespace Structora\Rendering;

interface RendererInterface
{
    public function render(string $source, array $options = []): array;
}
