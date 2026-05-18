<?php

declare(strict_types=1);

namespace Structora\Core;

final class DiscoveryResult
{
    public const SCHEMA_VERSION = '0.1.0-alpha';

    public function __construct(
        public readonly bool $status,
        public readonly string $source,
        public readonly array $summary,
        public readonly string $title = '',
        public readonly array $metadata = [],
        public readonly array $rendering = [],
        public readonly array $forms = [],
        public readonly array $links = [],
        public readonly array $headings = [],
        public readonly array $signals = [],
        public readonly array $signalSummary = [],
        public readonly array $workflow = [],
        public readonly array $workflowSummary = [],
        public readonly array $interpretation = [],
        public readonly array $extensionsApplied = [],
        public readonly array $exportMetadata = [],
        public readonly array $enrichmentMetadata = [],
        public readonly string $schemaVersion = self::SCHEMA_VERSION,
        public readonly string $generatedAt = '',
    ) {
    }

    public static function empty(string $source = ''): self
    {
        return new self(
            status: true,
            source: $source,
            summary: [
                'engine' => 'structora-core',
                'mode' => 'read_only',
                'message' => 'Discovery engine scaffold initialized.',
            ],
            title: '',
            metadata: [
                'read_only' => true,
                'execution_required' => false,
                'engine' => self::engineMetadata(),
            ],
        );
    }

    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'generated_at' => $this->generatedAt !== '' ? $this->generatedAt : gmdate(DATE_ATOM),
            'status' => $this->status,
            'source' => $this->source,
            'title' => $this->title,
            'summary' => $this->normalizeSummary($this->summary),
            'metadata' => $this->normalizeMetadata($this->metadata),
            'rendering' => $this->normalizeRendering($this->rendering),
            'forms' => array_values($this->forms),
            'links' => array_values($this->links),
            'headings' => array_values($this->headings),
            'signals' => array_values($this->signals),
            'signal_summary' => $this->normalizeSignalSummary($this->signalSummary),
            'workflow' => array_values($this->workflow),
            'workflow_summary' => $this->normalizeWorkflowSummary($this->workflowSummary),
            'interpretation' => $this->interpretation,
            'extensions_applied' => array_values($this->extensionsApplied),
            'export_metadata' => $this->normalizeExportMetadata($this->exportMetadata),
            'enrichment_metadata' => $this->normalizeEnrichmentMetadata($this->enrichmentMetadata),
        ];
    }

    public function with(array $changes): self
    {
        return new self(
            status: $changes['status'] ?? $this->status,
            source: $changes['source'] ?? $this->source,
            summary: $changes['summary'] ?? $this->summary,
            title: $changes['title'] ?? $this->title,
            metadata: $changes['metadata'] ?? $this->metadata,
            rendering: $changes['rendering'] ?? $this->rendering,
            forms: $changes['forms'] ?? $this->forms,
            links: $changes['links'] ?? $this->links,
            headings: $changes['headings'] ?? $this->headings,
            signals: $changes['signals'] ?? $this->signals,
            signalSummary: $changes['signalSummary'] ?? $this->signalSummary,
            workflow: $changes['workflow'] ?? $this->workflow,
            workflowSummary: $changes['workflowSummary'] ?? $this->workflowSummary,
            interpretation: $changes['interpretation'] ?? $this->interpretation,
            extensionsApplied: $changes['extensionsApplied'] ?? $this->extensionsApplied,
            exportMetadata: $changes['exportMetadata'] ?? $this->exportMetadata,
            enrichmentMetadata: $changes['enrichmentMetadata'] ?? $this->enrichmentMetadata,
            schemaVersion: $changes['schemaVersion'] ?? $this->schemaVersion,
            generatedAt: $changes['generatedAt'] ?? $this->generatedAt,
        );
    }

    public static function engineMetadata(): array
    {
        return [
            'name' => 'structora-core',
            'schema_version' => self::SCHEMA_VERSION,
            'mode' => 'read_only',
            'read_only' => true,
            'non_executable' => true,
        ];
    }

    private function normalizeSummary(array $summary): array
    {
        return array_merge([
            'engine' => 'structora-core',
            'mode' => 'read_only',
            'message' => '',
            'title_present' => false,
            'form_count' => 0,
            'field_count' => 0,
            'button_count' => 0,
            'link_count' => 0,
            'heading_count' => 0,
            'signal_count' => 0,
            'signal_types' => [],
            'workflow_count' => 0,
            'workflow_types' => [],
            'rendered' => false,
            'render_strategy' => 'none',
        ], $summary);
    }

    private function normalizeMetadata(array $metadata): array
    {
        return array_merge([
            'read_only' => true,
            'execution_required' => false,
            'network_access' => false,
            'filesystem_writes' => false,
            'engine' => self::engineMetadata(),
            'parser' => '',
            'detector' => '',
            'workflow_mapper' => '',
            'input_metadata' => [],
            'renderer_configured' => false,
            'rendering_enabled' => false,
            'interpretation_enabled' => false,
            'extraction_counts' => [
                'forms' => 0,
                'fields' => 0,
                'buttons' => 0,
                'links' => 0,
                'headings' => 0,
            ],
        ], $metadata);
    }

    private function normalizeRendering(array $rendering): array
    {
        return array_merge([
            'rendered' => false,
            'strategy' => 'none',
            'duration_ms' => 0,
            'document_length' => 0,
            'metadata' => [
                'rendered' => false,
                'strategy' => 'none',
                'duration_ms' => 0,
                'details' => [],
                'read_only' => true,
                'non_executable' => true,
            ],
            'errors' => [],
        ], $rendering);
    }

    private function normalizeSignalSummary(array $summary): array
    {
        return array_merge([
            'count' => 0,
            'types' => [],
            'confidence' => [],
            'read_only' => true,
            'non_executable' => true,
        ], $summary);
    }

    private function normalizeWorkflowSummary(array $summary): array
    {
        return array_merge([
            'workflow_count' => 0,
            'workflow_types' => [],
            'confidence_summary' => [],
            'read_only' => true,
            'non_executable' => true,
        ], $summary);
    }

    private function normalizeExportMetadata(array $metadata): array
    {
        return array_merge([
            'exportable' => true,
            'formats' => ['json', 'summary', 'markdown'],
            'read_only' => true,
            'non_executable' => true,
        ], $metadata);
    }

    private function normalizeEnrichmentMetadata(array $metadata): array
    {
        return array_merge([
            'applied_count' => count($this->extensionsApplied),
            'read_only' => true,
            'non_destructive' => true,
            'extensions' => [],
        ], $metadata);
    }
}
