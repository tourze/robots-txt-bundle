<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\RobotsTxtBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu($rootItem);

        // 验证菜单结构
        $systemMenu = $rootItem->getChild('系统配置');
        self::assertNotNull($systemMenu);

        $robotsMenu = $systemMenu->getChild('Robots.txt管理');
        self::assertNotNull($robotsMenu);

        self::assertNotNull($robotsMenu->getChild('条目管理'));
        self::assertNotNull($robotsMenu->getChild('规则管理'));
        self::assertNotNull($robotsMenu->getChild('指令管理'));
    }
}
