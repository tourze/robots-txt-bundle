<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\RobotsTxtBundle\RobotsTxtBundle;

/**
 * @internal
 */
#[CoversClass(RobotsTxtBundle::class)]
#[RunTestsInSeparateProcesses]
final class RobotsTxtBundleTest extends AbstractBundleTestCase
{
}
