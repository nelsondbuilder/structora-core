<?php

declare(strict_types=1);

namespace Structora\Core;

use Structora\Extension\ExtensionInterface;
use Structora\Extension\ResultEnricherInterface;
use Structora\Interpretation\InterpretationProviderInterface;
use Structora\Interpretation\NullInterpretationProvider;
use Structora\Rendering\RendererInterface;

final class DiscoveryEngine
{
    private InterpretationProviderInterface $interpretationProvider;
    private ?RendererInterface $renderer = null;

    /** @var ExtensionInterface[] */
    private array $extensions = [];

    /** @var ResultEnricherInterface[] */
    private array $enrichers = [];

    public function __construct(?InterpretationProviderInterface $interpretationProvider = null)
    {
        $this->interpretationProvider = $interpretationProvider ?? new NullInterpretationProvider();
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

        $result = DiscoveryResult::empty($options->source);

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
                signals: $result->signals,
                workflow: $result->workflow,
                interpretation: $interpretation,
                metadata: $result->metadata,
            );
        }

        return $result;
    }
}
