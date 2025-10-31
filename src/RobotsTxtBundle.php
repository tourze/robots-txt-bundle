<?php

namespace Tourze\RobotsTxtBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;

class RobotsTxtBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            RoutingAutoLoaderBundle::class => ['all' => true],
        ];
    }
}
