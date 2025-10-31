<?php

namespace Tourze\RobotsTxtBundle\Tests\Service\RobotsTxtService;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;
use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;
use Tourze\RobotsTxtBundle\Service\RobotsTxtService;

/**
 * @internal
 */
#[CoversClass(RobotsTxtService::class)]
final class RobotsTxtServiceGenerationTest extends TestCase
{
    public function testGenerateWithNoProviders(): void
    {
        $service = new RobotsTxtService([]);
        $result = $service->generate();

        $this->assertEquals('', $result);
    }

    public function testGenerateWithSingleProvider(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                ['https://example.com/sitemap.xml'],
                ['Generated robots.txt']
            ),
            100,
            true
        );

        $service = new RobotsTxtService([$provider]);
        $result = $service->generate();

        $expected = "# Generated robots.txt\n" .
                   "\n" .
                   "User-agent: *\n" .
                   "Disallow: /admin/\n" .
                   "\n" .
                   'Sitemap: https://example.com/sitemap.xml';

        $this->assertEquals($expected, $result);
    }

    public function testGenerateWithMultipleProviders(): void
    {
        $provider1 = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                ['Provider 1']
            ),
            50,
            true
        );

        $provider2 = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::allow('/api/')])],
                ['https://example.com/sitemap.xml'],
                ['Provider 2']
            ),
            100,
            true
        );

        $service = new RobotsTxtService([$provider1, $provider2]);
        $result = $service->generate();

        $expected = "# Provider 2\n" .
                   "# Provider 1\n" .
                   "\n" .
                   "User-agent: Googlebot\n" .
                   "Allow: /api/\n" .
                   "\n" .
                   "User-agent: *\n" .
                   "Disallow: /admin/\n" .
                   "\n" .
                   'Sitemap: https://example.com/sitemap.xml';

        $this->assertEquals($expected, $result);
    }

    public function testIsEmptyWithNoProviders(): void
    {
        $service = new RobotsTxtService([]);

        $this->assertTrue($service->isEmpty());
    }

    public function testIsEmptyWithEmptyProvider(): void
    {
        $provider = $this->createMockProvider(new RobotsTxtEntry(), 50, true);
        $service = new RobotsTxtService([$provider]);

        $this->assertTrue($service->isEmpty());
    }

    public function testIsEmptyWithContentfulProvider(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                []
            ),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);

        $this->assertFalse($service->isEmpty());
    }

    public function testIsEmptyWithCommentsOnly(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['Just a comment']),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);

        $this->assertFalse($service->isEmpty());
    }

    public function testIsEmptyWithSitemapsOnly(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry([], ['https://example.com/sitemap.xml'], []),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);

        $this->assertFalse($service->isEmpty());
    }

    public function testIsEmptyWithUnsupportedProviders(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                []
            ),
            50,
            false // Provider doesn't support current environment
        );
        $service = new RobotsTxtService([$provider]);

        $this->assertTrue($service->isEmpty());
    }

    public function testGenerateWithComplexRules(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [
                    RobotsTxtRule::forAllAgents([
                        RobotsTxtDirective::disallow('/admin/'),
                        RobotsTxtDirective::disallow('/private/'),
                    ], 10),
                    RobotsTxtRule::forAgent('Googlebot', [
                        RobotsTxtDirective::allow('/api/'),
                        RobotsTxtDirective::crawlDelay(5),
                    ], 100),
                    RobotsTxtRule::forAgent('Bingbot', [
                        RobotsTxtDirective::disallow('/test/'),
                    ], 50),
                ],
                [
                    'https://example.com/sitemap.xml',
                    'https://example.com/news-sitemap.xml',
                ],
                ['Complex robots.txt', 'Generated by test']
            ),
            50,
            true
        );

        $service = new RobotsTxtService([$provider]);
        $result = $service->generate();

        $this->assertStringContainsString('# Complex robots.txt', $result);
        $this->assertStringContainsString('# Generated by test', $result);
        $this->assertStringContainsString('User-agent: Googlebot', $result);
        $this->assertStringContainsString('User-agent: Bingbot', $result);
        $this->assertStringContainsString('User-agent: *', $result);
        $this->assertStringContainsString('Allow: /api/', $result);
        $this->assertStringContainsString('Crawl-delay: 5', $result);
        $this->assertStringContainsString('Disallow: /admin/', $result);
        $this->assertStringContainsString('Sitemap: https://example.com/sitemap.xml', $result);
        $this->assertStringContainsString('Sitemap: https://example.com/news-sitemap.xml', $result);
    }

    public function testGenerateWithWhitespaceOnlyContent(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['   ', "\t", "\n"]),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);

        // Whitespace-only comments should still make isEmpty return false
        $this->assertFalse($service->isEmpty());
        $result = $service->generate();
        $this->assertNotEmpty($result);
    }

    public function testGenerateCallsProvidersMethodsCorrectly(): void
    {
        $callTracker = ['supports' => 0, 'getPriority' => 0, 'provide' => 0];

        $provider = new class($callTracker) implements RobotsTxtProviderInterface {
            /** @param array<string, int> $callTracker */
            public function __construct(private array $callTracker)
            {
            }

            public function supports(): bool
            {
                ++$this->callTracker['supports'];

                return true;
            }

            public function getPriority(): int
            {
                ++$this->callTracker['getPriority'];

                return 50;
            }

            public function provide(): RobotsTxtEntry
            {
                ++$this->callTracker['provide'];

                return new RobotsTxtEntry();
            }

            /** @return array<string, int> */
            public function getCallTracker(): array
            {
                return $this->callTracker;
            }
        };

        $service = new RobotsTxtService([$provider]);
        $result = $service->generate();

        $this->assertEquals('', $result);
        $finalCallCounts = $provider->getCallTracker();
        $this->assertEquals(1, $finalCallCounts['supports']);
        $this->assertGreaterThanOrEqual(1, $finalCallCounts['getPriority']);
        $this->assertEquals(1, $finalCallCounts['provide']);
    }

    public function testIsEmptyCallsProvidersMethodsCorrectly(): void
    {
        $callTracker = ['supports' => 0, 'getPriority' => 0, 'provide' => 0];

        $provider = new class($callTracker) implements RobotsTxtProviderInterface {
            /** @param array<string, int> $callTracker */
            public function __construct(private array $callTracker)
            {
            }

            public function supports(): bool
            {
                ++$this->callTracker['supports'];

                return true;
            }

            public function getPriority(): int
            {
                ++$this->callTracker['getPriority'];

                return 50;
            }

            public function provide(): RobotsTxtEntry
            {
                ++$this->callTracker['provide'];

                return new RobotsTxtEntry();
            }

            /** @return array<string, int> */
            public function getCallTracker(): array
            {
                return $this->callTracker;
            }
        };

        $service = new RobotsTxtService([$provider]);
        $result = $service->isEmpty();

        $this->assertTrue($result);
        $finalCallCounts = $provider->getCallTracker();
        $this->assertEquals(1, $finalCallCounts['supports']);
        $this->assertGreaterThanOrEqual(1, $finalCallCounts['getPriority']);
        $this->assertEquals(1, $finalCallCounts['provide']);
    }

    public function testGenerateWithLargeNumberOfProviders(): void
    {
        $providers = [];
        for ($i = 1; $i <= 10; ++$i) {
            $providers[] = $this->createMockProvider(
                new RobotsTxtEntry([], [], ["Provider {$i}"]),
                $i * 10,
                true
            );
        }

        $service = new RobotsTxtService($providers);
        $result = $service->generate();

        // Should contain all comments in priority order (highest first)
        for ($i = 10; $i >= 1; --$i) {
            $this->assertStringContainsString("Provider {$i}", $result);
        }
    }

    public function testCollectEntries(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                ['https://example.com/sitemap.xml'],
                ['Test entry']
            ),
            50,
            true
        );

        $service = new RobotsTxtService([$provider]);
        $entry = $service->collectEntries();

        $this->assertInstanceOf(RobotsTxtEntry::class, $entry);
        $this->assertCount(1, $entry->rules);
        $this->assertCount(1, $entry->sitemaps);
        $this->assertCount(1, $entry->comments);
        $this->assertEquals('Test entry', $entry->comments[0]);
    }

    private function createMockProvider(
        RobotsTxtEntry $entry,
        int $priority,
        bool $supports,
    ): RobotsTxtProviderInterface {
        return new class($entry, $priority, $supports) implements RobotsTxtProviderInterface {
            public function __construct(
                private readonly RobotsTxtEntry $entry,
                private readonly int $priority,
                private readonly bool $supports,
            ) {
            }

            public function provide(): RobotsTxtEntry
            {
                return $this->entry;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function supports(): bool
            {
                return $this->supports;
            }
        };
    }
}
