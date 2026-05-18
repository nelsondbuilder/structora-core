<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Detection\PassiveSignalDetector;
use Structora\Detection\SignalCollection;
use Structora\DOM\StructureParser;
use Structora\Workflow\WorkflowMapper;

final class WorkflowMapperTest extends TestCase
{
    public function testItDetectsAuthFlow(): void
    {
        $workflow = $this->mapFixture('synthetic-auth-flow.html');

        self::assertNotNull($this->stateOfType($workflow, 'auth_flow'));
        self::assertNotNull($this->stateOfType($workflow, 'form_flow'));
    }

    public function testItDetectsSearchFlow(): void
    {
        $workflow = $this->mapFixture('synthetic-search-flow.html');

        $state = $this->stateOfType($workflow, 'search_flow');
        self::assertNotNull($state);
        self::assertSame('search_form', $state['evidence']['source_signal']);
    }

    public function testItDetectsMultiStepFlow(): void
    {
        $workflow = $this->mapFixture('synthetic-checkout-like-flow.html');

        self::assertNotNull($this->stateOfType($workflow, 'multi_step_flow'));
        self::assertNotNull($this->stateOfType($workflow, 'form_flow'));
    }

    public function testItDetectsChallengeFlow(): void
    {
        $workflow = $this->mapFixture('synthetic-challenge-page.html');

        $state = $this->stateOfType($workflow, 'challenge_flow');
        self::assertNotNull($state);
        self::assertSame('challenge_indicator', $state['evidence']['source_signal']);
    }

    public function testItDetectsConfirmationFlow(): void
    {
        $workflow = $this->mapFixture('synthetic-confirmation-flow.html');

        $state = $this->stateOfType($workflow, 'confirmation_flow');
        self::assertNotNull($state);
        self::assertSame('confirmation_indicator', $state['evidence']['source_signal']);
    }

    public function testItDetectsNavigationHub(): void
    {
        $workflow = $this->mapFixture('synthetic-navigation-heavy-page.html');

        $state = $this->stateOfType($workflow, 'navigation_hub');
        self::assertNotNull($state);
        self::assertSame('navigation_heavy_page', $state['evidence']['source_signal']);
    }

    public function testItFallsBackToInformationalPage(): void
    {
        $workflow = $this->mapHtml('<html><head><title>Info</title></head><body><h1>Information</h1><p>Read-only page.</p></body></html>');

        self::assertNotNull($this->stateOfType($workflow, 'informational_page'));
    }

    public function testStatesExposeConfidenceEvidenceAndMetadata(): void
    {
        $workflow = $this->mapFixture('synthetic-search-flow.html');
        $state = $workflow[0];

        self::assertArrayHasKey('type', $state);
        self::assertArrayHasKey('confidence', $state);
        self::assertArrayHasKey('evidence', $state);
        self::assertArrayHasKey('metadata', $state);
        self::assertTrue($state['metadata']['read_only']);
        self::assertTrue($state['metadata']['non_executable']);
    }

    public function testMalformedHtmlIsTolerated(): void
    {
        $workflow = $this->mapHtml('<title>Broken</title><h1>Verification Challenge<form><input type="password"><button>Verify');

        self::assertNotNull($this->stateOfType($workflow, 'auth_flow'));
        self::assertNotNull($this->stateOfType($workflow, 'challenge_flow'));
    }

    private function mapFixture(string $fixture): array
    {
        $html = file_get_contents(dirname(__DIR__, 2) . '/examples/fixtures/' . $fixture);

        return $this->mapHtml($html ?: '');
    }

    private function mapHtml(string $html): array
    {
        $document = (new StructureParser())->parse($html);
        $signals = SignalCollection::fromSignals((new PassiveSignalDetector())->detect($document));
        $workflowMap = (new WorkflowMapper())->map($document, $signals);

        return $workflowMap->toArray()['states'];
    }

    private function stateOfType(array $states, string $type): ?array
    {
        foreach ($states as $state) {
            if ($state['type'] === $type) {
                return $state;
            }
        }

        return null;
    }
}
