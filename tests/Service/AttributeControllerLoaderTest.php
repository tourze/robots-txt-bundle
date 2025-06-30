<?php

namespace Tourze\RobotsTxtBundle\Test\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RobotsTxtBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new AttributeControllerLoader();
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
            if ($route->getPath() === '/robots.txt') {
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