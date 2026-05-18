<?php

declare(strict_types=1);

namespace Structora\Detection;

use Structora\DOM\ParsedDocument;

interface DetectorInterface
{
    /**
     * @return DetectedSignal[]
     */
    public function detect(ParsedDocument $document, array $options = []): array;
}
