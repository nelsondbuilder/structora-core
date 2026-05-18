<?php

declare(strict_types=1);

namespace Structora\DOM;

interface StructureParserInterface
{
    public function parse(string $document, array $options = []): ParsedDocument;
}
