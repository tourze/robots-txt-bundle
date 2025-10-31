<?php

namespace Tourze\RobotsTxtBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\RobotsTxtBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
    }

    public function testSupportsReturnsFalse(): void
    {
        $this->assertFalse($this->loader->supports('resource'));
        $this->assertFalse($this->loader->supports('resource', 'type'));
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $collection = $this->loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $this->assertGreaterThan(0, $collection->count());

        // 验证路由包含 robots.txt 路由
        $routes = $collection->all();
        $hasRobotsTxtRoute = false;
        foreach ($routes as $route) {
            if ('/robots.txt' === $route->getPath()) {
                $hasRobotsTxtRoute = true;
                break;
            }
        }
        $this->assertTrue($hasRobotsTxtRoute, 'RouteCollection should contain /robots.txt route');
    }

    public function testLoadCallsAutoload(): void
    {
        $collection = $this->loader->load('resource');

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $this->assertGreaterThan(0, $collection->count());
    }
}
