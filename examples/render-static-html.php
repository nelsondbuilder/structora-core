<?php

declare(strict_types=1);

use Structora\Rendering\StaticHtmlRenderer;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = file_get_contents(__DIR__ . '/fixtures/rendered-search-page.html') ?: '';
$result = (new StaticHtmlRenderer())->render($html, [
    'source' => 'rendered-search-page',
]);

echo json_encode($result->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
