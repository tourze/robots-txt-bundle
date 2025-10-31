<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtEntryEntity;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtRuleEntity;

/**
 * RobotsTxtEntryEntity实体测试
 * @internal
 */
#[CoversClass(RobotsTxtEntryEntity::class)]
class RobotsTxtEntryEntityTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new RobotsTxtEntryEntity();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['name', 'Test Entry'],
            ['description', 'Test Description'],
            ['active', false],
            ['sitemaps', ['https://example.com/sitemap.xml']],
            ['comments', ['Test comment']],
        ];
    }

    public function testConstruct(): void
    {
        $entity = new RobotsTxtEntryEntity();

        self::assertNull($entity->getId());
        self::assertLessThanOrEqual(new \DateTimeImmutable(), $entity->getCreatedAt());
        self::assertNull($entity->getUpdatedAt());
        self::assertTrue($entity->isActive());
        self::assertCount(0, $entity->getRules());
        self::assertSame([], $entity->getSitemaps());
        self::assertSame([], $entity->getComments());
    }

    public function testSettersAndGetters(): void
    {
        $entity = new RobotsTxtEntryEntity();

        $entity->setName('Test Entry');
        self::assertSame('Test Entry', $entity->getName());

        $entity->setDescription('Test Description');
        self::assertSame('Test Description', $entity->getDescription());

        $entity->setActive(false);
        self::assertFalse($entity->isActive());

        self::assertLessThanOrEqual(new \DateTimeImmutable(), $entity->getUpdatedAt());
    }

    public function testRulesManagement(): void
    {
        $entry = new RobotsTxtEntryEntity();
        $rule = new RobotsTxtRuleEntity();

        $entry->addRule($rule);
        self::assertCount(1, $entry->getRules());

        $entry->removeRule($rule);
        self::assertCount(0, $entry->getRules());
    }

    public function testSitemapsManagement(): void
    {
        $entity = new RobotsTxtEntryEntity();

        $entity->addSitemap('https://example.com/sitemap.xml');
        self::assertContains('https://example.com/sitemap.xml', $entity->getSitemaps());

        $entity->removeSitemap('https://example.com/sitemap.xml');
        self::assertNotContains('https://example.com/sitemap.xml', $entity->getSitemaps());
    }

    public function testCommentsManagement(): void
    {
        $entity = new RobotsTxtEntryEntity();

        $entity->addComment('Test comment');
        self::assertContains('Test comment', $entity->getComments());

        $entity->removeComment('Test comment');
        self::assertNotContains('Test comment', $entity->getComments());
    }

    public function testToString(): void
    {
        $entity = new RobotsTxtEntryEntity();
        $entity->setName('Test Entry');

        self::assertSame('Test Entry', (string) $entity);

        $entity->setActive(false);
        self::assertSame('Test Entry (未激活)', (string) $entity);
    }
}
