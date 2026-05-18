<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryResult;

final class DiscoveryResultTest extends TestCase
{
    public function testEmptyReturnsReadOnlyMetadata(): void
    {
        $result = DiscoveryResult::empty('phase-1-check');

        self::assertTrue($result->status);
        self::assertSame('phase-1-check', $result->source);
        self::assertSame('read_only', $result->summary['mode']);
        self::assertTrue($result->metadata['read_only']);
        self::assertFalse($result->metadata['execution_required']);
    }
}
