<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtDirectiveEntity;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtRuleEntity;

/**
 * Robots.txt指令测试数据
 */
class RobotsTxtDirectiveEntityFixtures extends Fixture implements DependentFixtureInterface
{
    public const DIRECTIVE_DISALLOW_ALL = 'robots-txt-directive-disallow-all';
    public const DIRECTIVE_ALLOW_ASSETS = 'robots-txt-directive-allow-assets';
    public const DIRECTIVE_CRAWL_DELAY = 'robots-txt-directive-crawl-delay';
    public const DIRECTIVE_SITEMAP = 'robots-txt-directive-sitemap';

    public function load(ObjectManager $manager): void
    {
        // Disallow 指令
        $disallowDirective = new RobotsTxtDirectiveEntity();
        $disallowDirective->setDirective('Disallow');
        $disallowDirective->setValue('/');
        $disallowDirective->setRule($this->getReference(RobotsTxtRuleEntityFixtures::RULE_ALL_AGENTS, RobotsTxtRuleEntity::class));

        $manager->persist($disallowDirective);
        $this->addReference(self::DIRECTIVE_DISALLOW_ALL, $disallowDirective);

        // Allow 指令
        $allowDirective = new RobotsTxtDirectiveEntity();
        $allowDirective->setDirective('Allow');
        $allowDirective->setValue('/assets/');
        $allowDirective->setRule($this->getReference(RobotsTxtRuleEntityFixtures::RULE_SEARCH_ENGINE, RobotsTxtRuleEntity::class));

        $manager->persist($allowDirective);
        $this->addReference(self::DIRECTIVE_ALLOW_ASSETS, $allowDirective);

        // Crawl-delay 指令
        $crawlDelayDirective = new RobotsTxtDirectiveEntity();
        $crawlDelayDirective->setDirective('Crawl-delay');
        $crawlDelayDirective->setValue('10');
        $crawlDelayDirective->setRule($this->getReference(RobotsTxtRuleEntityFixtures::RULE_ALL_AGENTS, RobotsTxtRuleEntity::class));

        $manager->persist($crawlDelayDirective);
        $this->addReference(self::DIRECTIVE_CRAWL_DELAY, $crawlDelayDirective);

        // Sitemap 指令（不关联规则）
        $sitemapDirective = new RobotsTxtDirectiveEntity();
        $sitemapDirective->setDirective('Sitemap');
        $sitemapDirective->setValue('https://robots-txt-demo.netlify.app/sitemap.xml');

        $manager->persist($sitemapDirective);
        $this->addReference(self::DIRECTIVE_SITEMAP, $sitemapDirective);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RobotsTxtRuleEntityFixtures::class,
        ];
    }
}
