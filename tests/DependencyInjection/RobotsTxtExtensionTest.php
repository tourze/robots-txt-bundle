<?php

namespace Tourze\RobotsTxtBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\RobotsTxtBundle\DependencyInjection\RobotsTxtExtension;

class RobotsTxtExtensionTest extends TestCase
{
    private RobotsTxtExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new RobotsTxtExtension();
        $this->container = new ContainerBuilder();
    }

    public function test_load_loadsServicesYaml(): void
    {
        $this->extension->load([], $this->container);
        
        // Check that services are loaded by verifying some key services exist
        $this->assertTrue($this->container->hasDefinition('Tourze\RobotsTxtBundle\Service\RobotsTxtService'));
        $this->assertTrue($this->container->hasDefinition('Tourze\RobotsTxtBundle\Controller\RobotsTxtController'));
        $this->assertTrue($this->container->hasDefinition('Tourze\RobotsTxtBundle\Provider\DefaultRobotsTxtProvider'));
    }

    public function test_load_withEmptyConfigs(): void
    {
        $this->extension->load([], $this->container);
        
        $this->assertGreaterThan(0, count($this->container->getDefinitions()));
    }

    public function test_load_withNonEmptyConfigs(): void
    {
        $configs = [
            ['some_config' => 'value']
        ];
        
        $this->extension->load($configs, $this->container);
        
        $this->assertGreaterThan(0, count($this->container->getDefinitions()));
    }

    public function test_load_setsCorrectAutowiring(): void
    {
        $this->extension->load([], $this->container);
        
        $definitions = $this->container->getDefinitions();
        
        // Check that autowiring is enabled for bundle services
        foreach ($definitions as $id => $definition) {
            if (str_starts_with($id, 'Tourze\RobotsTxtBundle\\')) {
                $this->assertTrue($definition->isAutowired(), "Service $id should be autowired");
            }
        }
    }

    public function test_load_setsCorrectAutoconfiguration(): void
    {
        $this->extension->load([], $this->container);
        
        $definitions = $this->container->getDefinitions();
        
        // Check that autoconfiguration is enabled for bundle services
        foreach ($definitions as $id => $definition) {
            if (str_starts_with($id, 'Tourze\RobotsTxtBundle\\')) {
                $this->assertTrue($definition->isAutoconfigured(), "Service $id should be autoconfigured");
            }
        }
    }

    public function test_load_registersProviderInterface(): void
    {
        $this->extension->load([], $this->container);
        
        // Check that provider services are loaded
        $this->assertTrue($this->container->hasDefinition('Tourze\RobotsTxtBundle\Provider\DefaultRobotsTxtProvider'));
        
        // Check that the provider directory is configured for autowiring
        $definitions = $this->container->getDefinitions();
        $providerDefinitions = array_filter(array_keys($definitions), function($id) {
            return str_starts_with($id, 'Tourze\RobotsTxtBundle\Provider\\');
        });
        
        $this->assertGreaterThan(0, count($providerDefinitions));
    }

    public function test_load_multipleCalls(): void
    {
        $this->extension->load([], $this->container);
        $firstCount = count($this->container->getDefinitions());
        
        // Loading again should not duplicate services
        $this->extension->load([], $this->container);
        $secondCount = count($this->container->getDefinitions());
        
        $this->assertEquals($firstCount, $secondCount);
    }

    public function test_extension_inheritsFromCorrectClass(): void
    {
        $this->assertInstanceOf(
            \Symfony\Component\DependencyInjection\Extension\Extension::class,
            $this->extension
        );
    }

    public function test_load_doesNotThrowException(): void
    {
        $this->expectNotToPerformAssertions();
        
        $this->extension->load([], $this->container);
        $this->extension->load([['key' => 'value']], $this->container);
        $this->extension->load([[], ['another' => 'config']], $this->container);
    }
} 