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
final class RobotsTxtServiceCollectionTest extends TestCase
{
    public function testCollectEntriesWithNoProviders(): void
    {
        $service = new RobotsTxtService([]);
        $entry = $service->collectEntries();

        $this->assertInstanceOf(RobotsTxtEntry::class, $entry);
        $this->assertEquals([], $entry->rules);
        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([], $entry->comments);
    }

    public function testCollectEntriesWithSingleProvider(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                ['https://example.com/sitemap.xml'],
                ['Test comment']
            ),
            100,
            true
        );

        $service = new RobotsTxtService([$provider]);
        $entry = $service->collectEntries();

        $this->assertCount(1, $entry->rules);
        $this->assertCount(1, $entry->sitemaps);
        $this->assertCount(1, $entry->comments);
        $this->assertEquals('Test comment', $entry->comments[0]);
    }

    public function testCollectEntriesWithMultipleProviders(): void
    {
        $provider1 = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                ['https://example.com/sitemap1.xml'],
                ['Provider 1']
            ),
            50,
            true
        );

        $provider2 = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::allow('/api/')])],
                ['https://example.com/sitemap2.xml'],
                ['Provider 2']
            ),
            100,
            true
        );

        $service = new RobotsTxtService([$provider1, $provider2]);
        $entry = $service->collectEntries();

        $this->assertCount(2, $entry->rules);
        $this->assertCount(2, $entry->sitemaps);
        $this->assertCount(2, $entry->comments);
    }

    public function testCollectEntriesWithUnsupportedProvider(): void
    {
        $supportedProvider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                ['Supported']
            ),
            50,
            true
        );

        $unsupportedProvider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAgent('TestBot', [RobotsTxtDirective::allow('/test/')])],
                [],
                ['Unsupported']
            ),
            100,
            false // This provider doesn't support current environment
        );

        $service = new RobotsTxtService([$supportedProvider, $unsupportedProvider]);
        $entry = $service->collectEntries();

        // Only supported provider should be included
        $this->assertCount(1, $entry->rules);
        $this->assertCount(1, $entry->comments);
        $this->assertEquals('Supported', $entry->comments[0]);
    }

    public function testCollectEntriesWithPrioritySorting(): void
    {
        $lowPriorityProvider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['Low priority']),
            10,
            true
        );

        $highPriorityProvider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['High priority']),
            100,
            true
        );

        $mediumPriorityProvider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['Medium priority']),
            50,
            true
        );

        $service = new RobotsTxtService([$lowPriorityProvider, $highPriorityProvider, $mediumPriorityProvider]);
        $entry = $service->collectEntries();

        // Should be merged in priority order: high, medium, low
        $this->assertEquals([
            'High priority',
            'Medium priority',
            'Low priority',
        ], $entry->comments);
    }

    public function testGetProvidersReturnsAllProviders(): void
    {
        $provider1 = $this->createMockProvider(new RobotsTxtEntry(), 50, true);
        $provider2 = $this->createMockProvider(new RobotsTxtEntry(), 100, false);

        $service = new RobotsTxtService([$provider1, $provider2]);
        $providers = $service->getProviders();

        $this->assertCount(2, $providers);
        $this->assertSame($provider1, $providers[0]);
        $this->assertSame($provider2, $providers[1]);
    }

    public function testGetProvidersWithEmptyIterable(): void
    {
        $service = new RobotsTxtService([]);
        $providers = $service->getProviders();

        $this->assertEquals([], $providers);
    }

    public function testCollectEntriesWithEmptyProviderEntry(): void
    {
        $provider = $this->createMockProvider(new RobotsTxtEntry(), 50, true);

        $service = new RobotsTxtService([$provider]);
        $entry = $service->collectEntries();

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([], $entry->comments);
    }

    public function testCollectEntriesWithMixedSupportStatus(): void
    {
        $providers = [];

        // Create multiple providers with different support status
        for ($i = 1; $i <= 5; ++$i) {
            $supports = 1 === $i % 2; // Odd numbers support, even don't
            $providers[] = $this->createMockProvider(
                new RobotsTxtEntry([], [], ["Provider {$i}"]),
                $i * 10,
                $supports
            );
        }

        $service = new RobotsTxtService($providers);
        $entry = $service->collectEntries();

        // Only providers 1, 3, 5 should be included (odd numbers)
        $this->assertCount(3, $entry->comments);
        $this->assertEquals(['Provider 5', 'Provider 3', 'Provider 1'], $entry->comments);
    }

    public function testGenerate(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                ['https://example.com/sitemap.xml'],
                ['Generated by test']
            ),
            50,
            true
        );

        $service = new RobotsTxtService([$provider]);
        $result = $service->generate();

        $expected = "# Generated by test\n\nUser-agent: *\nDisallow: /admin/\n\nSitemap: https://example.com/sitemap.xml";
        $this->assertEquals($expected, $result);
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
