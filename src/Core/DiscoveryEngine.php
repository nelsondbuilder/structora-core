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

final class DiscoveryEngine
{
    private InterpretationProviderInterface $interpretationProvider;
    private StructureParserInterface $structureParser;
    private DetectorInterface $signalDetector;
    private ?RendererInterface $renderer = null;

    /** @var ExtensionInterface[] */
    private array $extensions = [];

    /** @var ResultEnricherInterface[] */
    private array $enrichers = [];

    public function __construct(
        ?InterpretationProviderInterface $interpretationProvider = null,
        ?StructureParserInterface $structureParser = null,
        ?DetectorInterface $signalDetector = null,
    ) {
        $this->interpretationProvider = $interpretationProvider ?? new NullInterpretationProvider();
        $this->structureParser = $structureParser ?? new StructureParser();
        $this->signalDetector = $signalDetector ?? new PassiveSignalDetector();
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
                forms: $result->forms,
                links: $result->links,
                headings: $result->headings,
                signals: $result->signals,
                signalSummary: $result->signalSummary,
                workflow: $result->workflow,
                interpretation: $interpretation,
                metadata: $result->metadata,
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

        return new DiscoveryResult(
            status: true,
            source: $options->source !== '' ? $options->source : $parsedDocument->source,
            summary: $summary,
            title: $parsedDocument->title,
            forms: $parsed['forms'],
            links: $parsed['links'],
            headings: $parsed['headings'],
            signals: $signalCollection->toArray(),
            signalSummary: $signalSummary,
            metadata: array_merge($parsedDocument->metadata, [
                'read_only' => true,
                'execution_required' => false,
                'engine' => self::class,
                'parser' => $parsedDocument->metadata['parser'] ?? $this->structureParser::class,
                'detector' => $this->signalDetector::class,
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
        );
    }
}
