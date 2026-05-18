<?php

declare(strict_types=1);

namespace Structora\Extension;

use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;

final class ExtensionPipeline
{
    /** @var ResultEnricherInterface[] */
    private array $extensions = [];

    /**
     * @param ResultEnricherInterface[] $extensions
     */
    public function __construct(array $extensions = [])
    {
        foreach ($extensions as $extension) {
            $this->extensions[] = $extension;
        }
    }

    public function register(ResultEnricherInterface $extension): self
    {
        $clone = clone $this;
        $clone->extensions[] = $extension;

        return $clone;
    }

    public function run(DiscoveryResult $result, DiscoveryOptions $options): DiscoveryResult
    {
        $current = $result;
        $applied = $current->extensionsApplied;
        $metadata = $current->enrichmentMetadata;
        $extensionMetadata = $metadata['extensions'] ?? [];

        foreach ($this->extensions as $index => $extension) {
            $before = $current;
            $current = $extension->enrich($current, $options);
            $name = $extension::class;
            $applied[] = $name;
            $extensionMetadata[] = [
                'name' => $name,
                'order' => $index,
                'read_only' => true,
                'non_destructive' => $before !== $current,
            ];
        }

        return $current->with([
            'extensionsApplied' => $applied,
            'enrichmentMetadata' => array_merge($metadata, [
                'applied_count' => count($applied),
                'read_only' => true,
                'non_destructive' => true,
                'extensions' => $extensionMetadata,
            ]),
        ]);
    }
}
