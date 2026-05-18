<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Interpretation\InterpretationProviderInterface;

final class DiscoveryEngineTest extends TestCase
{
    public function testDiscoverReturnsValidDiscoveryResult(): void
    {
        $engine = new DiscoveryEngine();

        $result = $engine->discover(
            '<html><body><main>Phase 1</main></body></html>',
            DiscoveryOptions::fromArray(['source' => 'phase-1-check'])
        );

        self::assertInstanceOf(DiscoveryResult::class, $result);
        self::assertTrue($result->status);
        self::assertSame('phase-1-check', $result->source);
        self::assertTrue($result->metadata['read_only']);
    }

    public function testInterpretationRunsOnlyWhenEnabled(): void
    {
        $provider = new CountingInterpretationProvider();
        $engine = new DiscoveryEngine($provider);

        $withoutInterpretation = $engine->discover(
            '<html></html>',
            DiscoveryOptions::fromArray(['interpretation_enabled' => false])
        );

        self::assertSame(0, $provider->calls);
        self::assertSame([], $withoutInterpretation->interpretation);

        $withInterpretation = $engine->discover(
            '<html></html>',
            DiscoveryOptions::fromArray(['interpretation_enabled' => true])
        );

        self::assertSame(1, $provider->calls);
        self::assertSame([
            'enabled' => true,
            'provider' => 'counting',
            'read_only' => true,
        ], $withInterpretation->interpretation);
    }
}

final class CountingInterpretationProvider implements InterpretationProviderInterface
{
    public int $calls = 0;

    public function interpret(array $discoveryResult): array
    {
        ++$this->calls;

        return [
            'enabled' => true,
            'provider' => 'counting',
            'read_only' => true,
        ];
    }
}
