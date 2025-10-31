<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtEntryEntity;

/**
 * Robots.txt条目测试数据
 */
class RobotsTxtEntryEntityFixtures extends Fixture
{
    public const ENTRY_DEFAULT = 'robots-txt-entry-default';
    public const ENTRY_SEO_OPTIMIZED = 'robots-txt-entry-seo-optimized';
    public const ENTRY_RESTRICTED = 'robots-txt-entry-restricted';

    public function load(ObjectManager $manager): void
    {
        // 默认条目
        $defaultEntry = new RobotsTxtEntryEntity();
        $defaultEntry->setName('默认配置');
        $defaultEntry->setDescription('网站的默认robots.txt配置');
        $defaultEntry->setSitemaps(['https://robots-txt-demo.netlify.app/sitemap.xml']);
        $defaultEntry->setComments(['This is a default robots.txt configuration', 'Generated automatically']);
        $defaultEntry->setActive(true);

        $manager->persist($defaultEntry);
        $this->addReference(self::ENTRY_DEFAULT, $defaultEntry);

        // SEO优化条目
        $seoEntry = new RobotsTxtEntryEntity();
        $seoEntry->setName('SEO优化配置');
        $seoEntry->setDescription('为搜索引擎优化的robots.txt配置');
        $seoEntry->setSitemaps([
            'https://robots-txt-demo.netlify.app/sitemap.xml',
            'https://robots-txt-demo.netlify.app/product-sitemap.xml',
            'https://robots-txt-demo.netlify.app/blog-sitemap.xml',
        ]);
        $seoEntry->setComments(['SEO optimized configuration', 'Allows search engines to crawl important content']);
        $seoEntry->setActive(true);

        $manager->persist($seoEntry);
        $this->addReference(self::ENTRY_SEO_OPTIMIZED, $seoEntry);

        // 受限条目
        $restrictedEntry = new RobotsTxtEntryEntity();
        $restrictedEntry->setName('受限访问配置');
        $restrictedEntry->setDescription('限制特定机器人访问的配置');
        $restrictedEntry->setSitemaps([]);
        $restrictedEntry->setComments(['Restricted access configuration', 'Blocks specific bots']);
        $restrictedEntry->setActive(false);

        $manager->persist($restrictedEntry);
        $this->addReference(self::ENTRY_RESTRICTED, $restrictedEntry);

        $manager->flush();
    }
}
