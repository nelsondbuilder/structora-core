<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryOptions;

final class DiscoveryOptionsTest extends TestCase
{
    public function testFromArrayMapsPublicOptions(): void
    {
        $options = DiscoveryOptions::fromArray([
            'source' => 'phase-1-check',
            'rendering_enabled' => true,
            'interpretation_enabled' => true,
            'metadata' => [
                'read_only' => true,
            ],
        ]);

        self::assertSame('phase-1-check', $options->source);
        self::assertTrue($options->renderingEnabled);
        self::assertTrue($options->interpretationEnabled);
        self::assertSame(['read_only' => true], $options->metadata);
    }

    public function testFromArrayUsesSafeDefaults(): void
    {
        $options = DiscoveryOptions::fromArray([
            'metadata' => 'ignored',
        ]);

        self::assertSame('', $options->source);
        self::assertFalse($options->renderingEnabled);
        self::assertFalse($options->interpretationEnabled);
        self::assertSame([], $options->metadata);
    }
}
