<?php

namespace Tourze\RobotsTxtBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;
use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;
use Tourze\RobotsTxtBundle\Service\RobotsTxtService;

class RobotsTxtServiceTest extends TestCase
{
    public function test_generate_withNoProviders(): void
    {
        $service = new RobotsTxtService([]);
        $result = $service->generate();
        
        $this->assertEquals('', $result);
    }

    public function test_collectEntries_withSingleProvider(): void
    {
        $provider = $this->createMock(RobotsTxtProviderInterface::class);
        $provider->method('supports')->willReturn(true);
        $provider->method('getPriority')->willReturn(100);
        
        $entry = new RobotsTxtEntry(
            [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])]
        );
        $provider->method('provide')->willReturn($entry);
        
        $service = new RobotsTxtService([$provider]);
        $result = $service->collectEntries();
        
        $this->assertCount(1, $result->rules);
        $this->assertEquals($entry->rules[0], $result->rules[0]);
    }

    public function test_isEmpty_withEmptyContent(): void
    {
        $service = new RobotsTxtService([]);
        
        $this->assertTrue($service->isEmpty());
    }

    public function test_getProviders_returnsAllProviders(): void
    {
        $provider1 = $this->createMock(RobotsTxtProviderInterface::class);
        $provider2 = $this->createMock(RobotsTxtProviderInterface::class);
        
        $service = new RobotsTxtService([$provider1, $provider2]);
        $providers = $service->getProviders();
        
        $this->assertCount(2, $providers);
        $this->assertContains($provider1, $providers);
        $this->assertContains($provider2, $providers);
    }
}