<?php

declare(strict_types=1);

namespace Structora\Core;

use Structora\DOM\ParsedDocument;
use Structora\Detection\DetectorInterface;
use Structora\Detection\PassiveSignalDetector;
use Structora\Detection\SignalCollection;
use Structora\DOM\StructureParser;
use Structora\DOM\StructureParserInterface;
use Structora\Extension\ExtensionInterface;
use Structora\Extension\ResultEnricherInterface;
use Structora\Interpretation\InterpretationProviderInterface;
use Structora\Interpretation\NullInterpretationProvider;
use Structora\Rendering\RendererInterface;
use Structora\Workflow\WorkflowCollection;
use Structora\Workflow\WorkflowMapper;
use Structora\Workflow\WorkflowMapperInterface;

final class DiscoveryEngine
{
    private InterpretationProviderInterface $interpretationProvider;
    private StructureParserInterface $structureParser;
    private DetectorInterface $signalDetector;
    private WorkflowMapperInterface $workflowMapper;
    private ?RendererInterface $renderer = null;

    /** @var ExtensionInterface[] */
    private array $extensions = [];

    /** @var ResultEnricherInterface[] */
    private array $enrichers = [];

    public function __construct(
        ?InterpretationProviderInterface $interpretationProvider = null,
        ?StructureParserInterface $structureParser = null,
        ?DetectorInterface $signalDetector = null,
        ?WorkflowMapperInterface $workflowMapper = null,
    ) {
        $this->interpretationProvider = $interpretationProvider ?? new NullInterpretationProvider();
        $this->structureParser = $structureParser ?? new StructureParser();
        $this->signalDetector = $signalDetector ?? new PassiveSignalDetector();
        $this->workflowMapper = $workflowMapper ?? new WorkflowMapper();
    }

    public function withRenderer(RendererInterface $renderer): self
    {
        $clone = clone $this;
        $clone->renderer = $renderer;

        return $clone;
    }

    public function withExtension(ExtensionInterface $extension): self
    {
        $clone = clone $this;
        $clone->extensions[] = $extension;

        return $clone;
    }

    public function withResultEnricher(ResultEnricherInterface $enricher): self
    {
        $clone = clone $this;
        $clone->enrichers[] = $enricher;

        return $clone;
    }

    public function discover(string $document, ?DiscoveryOptions $options = null): DiscoveryResult
    {
        $options ??= new DiscoveryOptions();

        $parsedDocument = $this->structureParser->parse($document, [
            'source' => $options->source,
            'metadata' => $options->metadata,
        ]);

        $result = $this->buildResult($parsedDocument, $options);

        foreach ($this->extensions as $extension) {
            $extension->boot($this);
        }

        foreach ($this->enrichers as $enricher) {
            $result = $enricher->enrich($result, $options);
        }

        if ($options->interpretationEnabled) {
            $interpretation = $this->interpretationProvider->interpret($result->toArray());

            return new DiscoveryResult(
                status: $result->status,
                source: $result->source,
                summary: $result->summary,
                title: $result->title,
                metadata: $result->metadata,
                forms: $result->forms,
                links: $result->links,
                headings: $result->headings,
                signals: $result->signals,
                signalSummary: $result->signalSummary,
                workflow: $result->workflow,
                workflowSummary: $result->workflowSummary,
                interpretation: $interpretation,
                schemaVersion: $result->schemaVersion,
                generatedAt: $result->generatedAt,
            );
        }

        return $result;
    }

    private function buildResult(ParsedDocument $parsedDocument, DiscoveryOptions $options): DiscoveryResult
    {
        $parsed = $parsedDocument->toArray();
        $summary = array_merge([
            'engine' => 'structora-core',
            'mode' => 'read_only',
            'message' => 'Discovery completed from provided HTML input.',
        ], $parsedDocument->summary);
        $signalCollection = SignalCollection::fromSignals($this->signalDetector->detect($parsedDocument, [
            'source' => $options->source,
            'metadata' => $options->metadata,
        ]));
        $signalSummary = $signalCollection->summary();
        $summary['signal_count'] = $signalSummary['count'];
        $summary['signal_types'] = $signalSummary['types'];
        $workflowMap = $this->workflowMapper->map($parsedDocument, $signalCollection, [
            'source' => $options->source,
            'metadata' => $options->metadata,
        ]);
        $workflowCollection = WorkflowCollection::fromStates($workflowMap->states);
        $workflowSummary = $workflowCollection->summary();
        $summary['workflow_count'] = $workflowSummary['workflow_count'];
        $summary['workflow_types'] = $workflowSummary['workflow_types'];

        return new DiscoveryResult(
            status: true,
            source: $options->source !== '' ? $options->source : $parsedDocument->source,
            summary: $summary,
            title: $parsedDocument->title,
            metadata: array_merge($parsedDocument->metadata, [
                'read_only' => true,
                'execution_required' => false,
                'network_access' => false,
                'filesystem_writes' => false,
                'engine' => array_merge(DiscoveryResult::engineMetadata(), [
                    'class' => self::class,
                ]),
                'parser' => $parsedDocument->metadata['parser'] ?? $this->structureParser::class,
                'detector' => $this->signalDetector::class,
                'workflow_mapper' => $this->workflowMapper::class,
                'input_metadata' => $options->metadata,
                'renderer_configured' => $this->renderer instanceof RendererInterface,
                'interpretation_enabled' => $options->interpretationEnabled,
                'extraction_counts' => [
                    'forms' => $summary['form_count'] ?? 0,
                    'fields' => $summary['field_count'] ?? 0,
                    'buttons' => $summary['button_count'] ?? 0,
                    'links' => $summary['link_count'] ?? 0,
                    'headings' => $summary['heading_count'] ?? 0,
                ],
            ]),
            forms: $parsed['forms'],
            links: $parsed['links'],
            headings: $parsed['headings'],
            signals: $signalCollection->toArray(),
            signalSummary: $signalSummary,
            workflow: $workflowCollection->toArray(),
            workflowSummary: $workflowSummary,
            schemaVersion: DiscoveryResult::SCHEMA_VERSION,
            generatedAt: gmdate(DATE_ATOM),
        );
    }
}
