<?php

namespace Tourze\RobotsTxtBundle\Tests\Provider;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;

/**
 * @internal
 */
#[CoversClass(RobotsTxtProviderInterface::class)]
#[RunTestsInSeparateProcesses]
final class RobotsTxtProviderInterfaceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 接口测试不需要特殊设置
    }

    public function testInterfaceCanBeImplemented(): void
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

    public function testInterfaceMethodsExist(): void
    {
        $reflection = new \ReflectionClass(RobotsTxtProviderInterface::class);

        $this->assertTrue($reflection->hasMethod('provide'));
        $this->assertTrue($reflection->hasMethod('getPriority'));
        $this->assertTrue($reflection->hasMethod('supports'));
    }
}
