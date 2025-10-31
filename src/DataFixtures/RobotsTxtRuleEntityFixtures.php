<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtEntryEntity;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtRuleEntity;

/**
 * Robots.txt规则测试数据
 */
class RobotsTxtRuleEntityFixtures extends Fixture implements DependentFixtureInterface
{
    public const RULE_ALL_AGENTS = 'robots-txt-rule-all-agents';
    public const RULE_SEARCH_ENGINE = 'robots-txt-rule-search-engine';
    public const RULE_BOT_SPECIFIC = 'robots-txt-rule-bot-specific';

    public function load(ObjectManager $manager): void
    {
        // 通用规则 - 所有用户代理
        $allAgentsRule = new RobotsTxtRuleEntity();
        $allAgentsRule->setUserAgent('*');
        $allAgentsRule->setPriority(0);
        $allAgentsRule->setEntry($this->getReference(RobotsTxtEntryEntityFixtures::ENTRY_DEFAULT, RobotsTxtEntryEntity::class));

        $manager->persist($allAgentsRule);
        $this->addReference(self::RULE_ALL_AGENTS, $allAgentsRule);

        // 搜索引擎规则
        $searchEngineRule = new RobotsTxtRuleEntity();
        $searchEngineRule->setUserAgent('Googlebot');
        $searchEngineRule->setPriority(10);
        $searchEngineRule->setEntry($this->getReference(RobotsTxtEntryEntityFixtures::ENTRY_SEO_OPTIMIZED, RobotsTxtEntryEntity::class));

        $manager->persist($searchEngineRule);
        $this->addReference(self::RULE_SEARCH_ENGINE, $searchEngineRule);

        // 特定机器人规则
        $botSpecificRule = new RobotsTxtRuleEntity();
        $botSpecificRule->setUserAgent('BadBot');
        $botSpecificRule->setPriority(100);
        $botSpecificRule->setEntry($this->getReference(RobotsTxtEntryEntityFixtures::ENTRY_RESTRICTED, RobotsTxtEntryEntity::class));

        $manager->persist($botSpecificRule);
        $this->addReference(self::RULE_BOT_SPECIFIC, $botSpecificRule);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RobotsTxtEntryEntityFixtures::class,
        ];
    }
}
