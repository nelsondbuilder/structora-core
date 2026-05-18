<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Interpretation\NullInterpretationProvider;

final class NullInterpretationProviderTest extends TestCase
{
    public function testInterpretReturnsDisabledReadOnlyPayload(): void
    {
        $provider = new NullInterpretationProvider();

        $interpretation = $provider->interpret([]);

        self::assertFalse($interpretation['enabled']);
        self::assertSame('none', $interpretation['provider']);
        self::assertTrue($interpretation['read_only']);
        self::assertFalse($interpretation['execution_required']);
    }
}
