<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Core\DiscoveryEngine;
use Structora\Core\DiscoveryOptions;
use Structora\Core\DiscoveryResult;
use Structora\Interpretation\InterpretationProviderInterface;

final class DiscoveryEngineIntegrationTest extends TestCase
{
    public function testParserIntegrationAddsParsedStructureToDiscoveryResult(): void
    {
        $engine = new DiscoveryEngine();
        $html = $this->fixture('synthetic-login-form.html');

        $result = $engine->discover($html, DiscoveryOptions::fromArray([
            'source' => 'synthetic-login-form',
        ]));

        self::assertInstanceOf(DiscoveryResult::class, $result);
        self::assertSame('Synthetic Access Form', $result->title);
        self::assertCount(1, $result->forms);
        self::assertSame('login-form', $result->forms[0]['id']);
        self::assertSame('email', $result->forms[0]['fields'][0]['name']);
        self::assertSame('/synthetic/help', $result->links[0]['href']);
        self::assertSame('Developer Access', $result->headings[0]['text']);
        self::assertSame(1, $result->signalSummary['count']);
        self::assertContains('auth_like_form', $result->signalSummary['types']);
        self::assertContains('auth_flow', $result->workflowSummary['workflow_types']);
        self::assertContains('form_flow', $result->workflowSummary['workflow_types']);
    }

    public function testMalformedHtmlDoesNotFatalAndStillReturnsCounts(): void
    {
        $engine = new DiscoveryEngine();

        $result = $engine->discover(
            '<html><head><title>Malformed</title><body><h1>Open<form><input name="q"><button>Go',
            DiscoveryOptions::fromArray(['source' => 'malformed'])
        );

        self::assertTrue($result->status);
        self::assertSame('Malformed', $result->title);
        self::assertSame(1, $result->summary['form_count']);
        self::assertSame(1, $result->summary['field_count']);
        self::assertSame(1, $result->summary['button_count']);
        self::assertArrayHasKey('signal_count', $result->summary);
        self::assertArrayHasKey('workflow_count', $result->summary);
        self::assertTrue($result->metadata['read_only']);
    }

    public function testSummaryCountsAndParserMetadataAreIncluded(): void
    {
        $engine = new DiscoveryEngine();

        $result = $engine->discover($this->fixture('synthetic-multi-step-page.html'));

        self::assertSame(2, $result->summary['form_count']);
        self::assertSame(3, $result->summary['field_count']);
        self::assertSame(2, $result->summary['button_count']);
        self::assertSame(1, $result->summary['link_count']);
        self::assertSame(3, $result->summary['heading_count']);
        self::assertSame([
            'forms' => 2,
            'fields' => 3,
            'buttons' => 2,
            'links' => 1,
            'headings' => 3,
        ], $result->metadata['extraction_counts']);
        self::assertArrayHasKey('parser', $result->metadata);
        self::assertFalse($result->metadata['execution_required']);
    }

    public function testInterpretationRemainsOptional(): void
    {
        $provider = new IntegrationCountingInterpretationProvider();
        $engine = new DiscoveryEngine($provider);

        $disabled = $engine->discover($this->fixture('synthetic-search-page.html'));
        self::assertSame(0, $provider->calls);
        self::assertSame([], $disabled->interpretation);

        $enabled = $engine->discover(
            $this->fixture('synthetic-search-page.html'),
            DiscoveryOptions::fromArray(['interpretation_enabled' => true])
        );

        self::assertSame(1, $provider->calls);
        self::assertSame('integration-counting', $enabled->interpretation['provider']);
        self::assertSame(1, $enabled->interpretation['form_count']);
    }

    private function fixture(string $name): string
    {
        $html = file_get_contents(dirname(__DIR__, 2) . '/examples/fixtures/' . $name);

        return $html ?: '';
    }
}

final class IntegrationCountingInterpretationProvider implements InterpretationProviderInterface
{
    public int $calls = 0;

    public function interpret(array $discoveryResult): array
    {
        ++$this->calls;

        return [
            'enabled' => true,
            'provider' => 'integration-counting',
            'form_count' => count($discoveryResult['forms']),
            'read_only' => true,
        ];
    }
}
