<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryResult;
use Structora\Core\ReleaseMetadata;

final class ReleaseMetadataTest extends TestCase
{
    public function testSchemaVersionAlignsWithReleaseMetadata(): void
    {
        self::assertSame('0.1.0-alpha', ReleaseMetadata::VERSION);
        self::assertSame('v0.1.0-alpha', ReleaseMetadata::TAG);
        self::assertSame(ReleaseMetadata::SCHEMA_VERSION, DiscoveryResult::SCHEMA_VERSION);
    }

    public function testEngineMetadataIncludesReleaseVersion(): void
    {
        $metadata = DiscoveryResult::engineMetadata();

        self::assertSame(ReleaseMetadata::VERSION, $metadata['version']);
        self::assertSame(ReleaseMetadata::TAG, $metadata['tag']);
        self::assertSame(ReleaseMetadata::RELEASE_STAGE, $metadata['release_stage']);
        self::assertTrue($metadata['read_only']);
    }
}
