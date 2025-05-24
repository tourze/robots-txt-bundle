<?php

namespace Tourze\RobotsTxtBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\RobotsTxtBundle\RobotsTxtBundle;

class RobotsTxtBundleTest extends TestCase
{
    public function test_bundle_extendsSymfonyBundle(): void
    {
        $bundle = new RobotsTxtBundle();
        
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function test_bundle_canBeInstantiated(): void
    {
        $bundle = new RobotsTxtBundle();
        
        $this->assertInstanceOf(RobotsTxtBundle::class, $bundle);
    }

    public function test_bundle_hasCorrectName(): void
    {
        $bundle = new RobotsTxtBundle();
        
        $this->assertEquals('RobotsTxtBundle', $bundle->getName());
    }

    public function test_bundle_hasCorrectNamespace(): void
    {
        $bundle = new RobotsTxtBundle();
        
        $this->assertEquals('Tourze\RobotsTxtBundle', $bundle->getNamespace());
    }

    public function test_bundle_hasCorrectPath(): void
    {
        $bundle = new RobotsTxtBundle();
        $path = $bundle->getPath();
        
        $this->assertStringEndsWith('robots-txt-bundle/src', $path);
        $this->assertTrue(is_dir($path));
    }

    public function test_bundle_multipleInstances(): void
    {
        $bundle1 = new RobotsTxtBundle();
        $bundle2 = new RobotsTxtBundle();
        
        $this->assertEquals($bundle1->getName(), $bundle2->getName());
        $this->assertEquals($bundle1->getNamespace(), $bundle2->getNamespace());
        $this->assertEquals($bundle1->getPath(), $bundle2->getPath());
    }

    public function test_bundle_implementsCorrectInterface(): void
    {
        $bundle = new RobotsTxtBundle();
        
        $this->assertInstanceOf(
            \Symfony\Component\HttpKernel\Bundle\BundleInterface::class,
            $bundle
        );
    }

    public function test_bundle_pathExists(): void
    {
        $bundle = new RobotsTxtBundle();
        $path = $bundle->getPath();
        
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path . '/RobotsTxtBundle.php');
    }
} 