<?php

declare(strict_types=1);

namespace Structora\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Structora\Detection\PassiveSignalDetector;
use Structora\DOM\StructureParser;

final class PassiveSignalDetectorTest extends TestCase
{
    public function testItDetectsAuthLikeForms(): void
    {
        $signals = $this->detectFixture('synthetic-login-form.html');

        $signal = $this->signalOfType($signals, 'auth_like_form');
        self::assertNotNull($signal);
        self::assertGreaterThanOrEqual(0.8, $signal['confidence']);
        self::assertContains('password', $signal['evidence']['field_types']);
        self::assertTrue($signal['metadata']['observational_only']);
    }

    public function testItDetectsSearchForms(): void
    {
        $signals = $this->detectFixture('synthetic-search-page.html');

        $signal = $this->signalOfType($signals, 'search_form');
        self::assertNotNull($signal);
        self::assertContains('search', $signal['evidence']['matched_terms']);
        self::assertTrue($signal['metadata']['non_executable']);
    }

    public function testItDetectsMultiStepAndProgressIndicators(): void
    {
        $signals = $this->detectFixture('synthetic-multi-step-page.html');

        self::assertNotNull($this->signalOfType($signals, 'multi_step_indicator'));
        self::assertNotNull($this->signalOfType($signals, 'progress_indicator'));
        self::assertNotNull($this->signalOfType($signals, 'form_density'));
    }

    public function testItDetectsConfirmationIndicators(): void
    {
        $signals = $this->detectFixture('synthetic-confirmation-page.html');

        $signal = $this->signalOfType($signals, 'confirmation_indicator');
        self::assertNotNull($signal);
        self::assertContains('confirmation', $signal['evidence']['matched_terms']);
    }

    public function testItDetectsChallengeIndicators(): void
    {
        $signals = $this->detectFixture('synthetic-challenge-page.html');

        $signal = $this->signalOfType($signals, 'challenge_indicator');
        self::assertNotNull($signal);
        self::assertContains('captcha', $signal['evidence']['matched_terms']);
    }

    public function testItDetectsNavigationHeavyPages(): void
    {
        $signals = $this->detectFixture('synthetic-navigation-heavy-page.html');

        $signal = $this->signalOfType($signals, 'navigation_heavy_page');
        self::assertNotNull($signal);
        self::assertSame(8, $signal['evidence']['link_count']);
    }

    public function testSignalsExposeConfidenceEvidenceAndMetadata(): void
    {
        $signals = $this->detectFixture('synthetic-login-form.html');
        $signal = $signals[0];

        self::assertArrayHasKey('type', $signal);
        self::assertArrayHasKey('confidence', $signal);
        self::assertArrayHasKey('evidence', $signal);
        self::assertArrayHasKey('metadata', $signal);
        self::assertTrue($signal['metadata']['read_only']);
    }

    public function testMalformedHtmlIsTolerated(): void
    {
        $document = (new StructureParser())->parse('<title>Broken</title><h1>Step 1<form><input type="password"><button>Next');
        $signals = array_map(
            static fn ($signal): array => $signal->toArray(),
            (new PassiveSignalDetector())->detect($document),
        );

        self::assertNotNull($this->signalOfType($signals, 'auth_like_form'));
        self::assertNotNull($this->signalOfType($signals, 'progress_indicator'));
    }

    private function detectFixture(string $fixture): array
    {
        $html = file_get_contents(dirname(__DIR__, 2) . '/examples/fixtures/' . $fixture);
        $document = (new StructureParser())->parse($html ?: '', [
            'source' => $fixture,
        ]);

        return array_map(
            static fn ($signal): array => $signal->toArray(),
            (new PassiveSignalDetector())->detect($document),
        );
    }

    private function signalOfType(array $signals, string $type): ?array
    {
        foreach ($signals as $signal) {
            if ($signal['type'] === $type) {
                return $signal;
            }
        }

        return null;
    }
}
