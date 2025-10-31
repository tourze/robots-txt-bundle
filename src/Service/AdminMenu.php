<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtDirectiveEntity;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtEntryEntity;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtRuleEntity;

/**
 * Robots.txt管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('系统配置')) {
            $item->addChild('系统配置');
        }

        $systemMenu = $item->getChild('系统配置');
        if (null === $systemMenu) {
            return;
        }

        // 添加 Robots.txt 管理子菜单
        if (null === $systemMenu->getChild('Robots.txt管理')) {
            $systemMenu->addChild('Robots.txt管理')
                ->setAttribute('icon', 'fas fa-robot')
            ;
        }

        $robotsMenu = $systemMenu->getChild('Robots.txt管理');
        if (null === $robotsMenu) {
            return;
        }

        $robotsMenu->addChild('条目管理')
            ->setUri($this->linkGenerator->getCurdListPage(RobotsTxtEntryEntity::class))
            ->setAttribute('icon', 'fas fa-file-alt')
        ;

        $robotsMenu->addChild('规则管理')
            ->setUri($this->linkGenerator->getCurdListPage(RobotsTxtRuleEntity::class))
            ->setAttribute('icon', 'fas fa-cogs')
        ;

        $robotsMenu->addChild('指令管理')
            ->setUri($this->linkGenerator->getCurdListPage(RobotsTxtDirectiveEntity::class))
            ->setAttribute('icon', 'fas fa-list')
        ;
    }
}
