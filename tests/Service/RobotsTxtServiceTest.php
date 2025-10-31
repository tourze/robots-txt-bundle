<?php

namespace Tourze\RobotsTxtBundle\Tests\Service;

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
final class RobotsTxtServiceTest extends TestCase
{
    public function testGenerateWithNoProviders(): void
    {
        $service = new RobotsTxtService([]);
        $result = $service->generate();

        $this->assertEquals('', $result);
    }

    public function testCollectEntriesWithSingleProvider(): void
    {
        $entry = new RobotsTxtEntry(
            [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])]
        );

        $provider = new class($entry) implements RobotsTxtProviderInterface {
            public function __construct(private readonly RobotsTxtEntry $entry)
            {
            }

            public function supports(): bool
            {
                return true;
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function provide(): RobotsTxtEntry
            {
                return $this->entry;
            }
        };

        $service = new RobotsTxtService([$provider]);
        $result = $service->collectEntries();

        $this->assertCount(1, $result->rules);
        $this->assertEquals($entry->rules[0], $result->rules[0]);
    }

    public function testIsEmptyWithEmptyContent(): void
    {
        $service = new RobotsTxtService([]);

        $this->assertTrue($service->isEmpty());
    }

    public function testGetProvidersReturnsAllProviders(): void
    {
        $provider1 = new class implements RobotsTxtProviderInterface {
            public function supports(): bool
            {
                return true;
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function provide(): RobotsTxtEntry
            {
                return new RobotsTxtEntry();
            }
        };

        $provider2 = new class implements RobotsTxtProviderInterface {
            public function supports(): bool
            {
                return true;
            }

            public function getPriority(): int
            {
                return 50;
            }

            public function provide(): RobotsTxtEntry
            {
                return new RobotsTxtEntry();
            }
        };

        $service = new RobotsTxtService([$provider1, $provider2]);
        $providers = $service->getProviders();

        $this->assertCount(2, $providers);
        $this->assertContains($provider1, $providers);
        $this->assertContains($provider2, $providers);
    }
}
