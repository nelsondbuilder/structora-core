<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Extension\Examples\MetadataEnrichmentExtension;
use Structora\Extension\Examples\SignalSummaryExtension;
use Structora\Extension\Examples\WorkflowSummaryExtension;
use Structora\Extension\ExtensionPipeline;

final class ExtensionPipelineTest extends TestCase
{
    public function testPipelineRunsExtensionsInOrderAndRecordsMetadata(): void
    {
        $pipeline = (new ExtensionPipeline())
            ->register(new SignalSummaryExtension())
            ->register(new WorkflowSummaryExtension())
            ->register(new MetadataEnrichmentExtension());

        $result = $pipeline->run($this->fixtureResult(), DiscoveryOptions::fromArray([
            'metadata' => ['example' => true],
        ]));

        self::assertSame([
            SignalSummaryExtension::class,
            WorkflowSummaryExtension::class,
            MetadataEnrichmentExtension::class,
        ], $result->extensionsApplied);
        self::assertSame(3, $result->enrichmentMetadata['applied_count']);
        self::assertTrue($result->enrichmentMetadata['read_only']);
        self::assertSame('synthetic', $result->metadata['extension_metadata']['source_label']);
    }

    public function testPipelinePreservesImmutability(): void
    {
        $original = $this->fixtureResult();
        $pipeline = (new ExtensionPipeline())->register(new SignalSummaryExtension());

        $enriched = $pipeline->run($original, new DiscoveryOptions());

        self::assertNotSame($original, $enriched);
        self::assertSame([], $original->extensionsApplied);
        self::assertSame([SignalSummaryExtension::class], $enriched->extensionsApplied);
    }

    public function testEmptyPipelineAddsStableEnrichmentMetadata(): void
    {
        $result = (new ExtensionPipeline())->run($this->fixtureResult(), new DiscoveryOptions());

        self::assertSame([], $result->extensionsApplied);
        self::assertSame(0, $result->enrichmentMetadata['applied_count']);
        self::assertSame([], $result->enrichmentMetadata['extensions']);
    }

    public function testConstructorRegistersExtensions(): void
    {
        $pipeline = new ExtensionPipeline([
            new SignalSummaryExtension(),
            new WorkflowSummaryExtension(),
        ]);

        $result = $pipeline->run($this->fixtureResult(), new DiscoveryOptions());

        self::assertSame([
            SignalSummaryExtension::class,
            WorkflowSummaryExtension::class,
        ], $result->extensionsApplied);
    }

    private function fixtureResult(): DiscoveryResult
    {
        return new DiscoveryResult(
            status: true,
            source: 'synthetic',
            summary: [],
            metadata: ['read_only' => true],
            signals: [['type' => 'search_form']],
            signalSummary: ['types' => ['search_form']],
            workflow: [['type' => 'search_flow']],
            workflowSummary: ['workflow_types' => ['search_flow']],
            generatedAt: '2026-05-19T00:00:00+00:00',
        );
    }
}
