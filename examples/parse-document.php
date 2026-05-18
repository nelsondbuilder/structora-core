<?php

declare(strict_types=1);

use Structora\DOM\StructureParser;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/synthetic-multi-step-page.html');
$parser = new StructureParser();
$document = $parser->parse($html ?: '', [
    'source' => 'synthetic-multi-step-page',
]);

echo json_encode([
    'summary' => $document->summary,
    'forms' => array_map(
        static fn ($form): array => $form->toArray(),
        $document->forms,
    ),
    'headings' => array_map(
        static fn ($heading): array => $heading->toArray(),
        $document->headings,
    ),
    'links' => array_map(
        static fn ($link): array => $link->toArray(),
        $document->links,
    ),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
