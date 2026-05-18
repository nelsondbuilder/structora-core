<?php

declare(strict_types=1);

namespace Structora\Core;

final class ReleaseMetadata
{
    public const VERSION = '0.1.0-alpha';
    public const TAG = 'v0.1.0-alpha';
    public const SCHEMA_VERSION = '0.1.0-alpha';
    public const RELEASE_STAGE = 'alpha';

    public static function toArray(): array
    {
        return [
            'name' => 'structora/core',
            'version' => self::VERSION,
            'tag' => self::TAG,
            'schema_version' => self::SCHEMA_VERSION,
            'release_stage' => self::RELEASE_STAGE,
            'read_only' => true,
            'non_executable' => true,
        ];
    }
}
