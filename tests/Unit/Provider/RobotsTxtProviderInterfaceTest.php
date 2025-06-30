<?php

namespace Tourze\RobotsTxtBundle\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;

class RobotsTxtProviderInterfaceTest extends TestCase
{
    public function test_interface_can_be_implemented(): void
    {
        $provider = new class implements RobotsTxtProviderInterface {
            public function provide(): RobotsTxtEntry
            {
                return new RobotsTxtEntry();
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(): bool
            {
                return true;
            }
        };

        $this->assertInstanceOf(RobotsTxtProviderInterface::class, $provider);
        $this->assertInstanceOf(RobotsTxtEntry::class, $provider->provide());
        $this->assertEquals(0, $provider->getPriority());
        $this->assertTrue($provider->supports());
    }

    public function test_interface_methods_exist(): void
    {
        $reflection = new \ReflectionClass(RobotsTxtProviderInterface::class);

        $this->assertTrue($reflection->hasMethod('provide'));
        $this->assertTrue($reflection->hasMethod('getPriority'));
        $this->assertTrue($reflection->hasMethod('supports'));
    }
}