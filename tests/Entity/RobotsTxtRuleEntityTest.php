<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtDirectiveEntity;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtRuleEntity;

/**
 * RobotsTxtRuleEntity实体测试
 * @internal
 */
#[CoversClass(RobotsTxtRuleEntity::class)]
class RobotsTxtRuleEntityTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new RobotsTxtRuleEntity();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['userAgent', '*'],
            ['priority', 10],
        ];
    }

    public function testConstruct(): void
    {
        $entity = new RobotsTxtRuleEntity();

        self::assertNull($entity->getId());
        self::assertLessThanOrEqual(new \DateTimeImmutable(), $entity->getCreatedAt());
        self::assertNull($entity->getUpdatedAt());
        self::assertSame(0, $entity->getPriority());
        self::assertCount(0, $entity->getDirectives());
    }

    public function testSettersAndGetters(): void
    {
        $entity = new RobotsTxtRuleEntity();

        $entity->setUserAgent('*');
        self::assertSame('*', $entity->getUserAgent());

        $entity->setPriority(10);
        self::assertSame(10, $entity->getPriority());

        self::assertLessThanOrEqual(new \DateTimeImmutable(), $entity->getUpdatedAt());
    }

    public function testDirectivesManagement(): void
    {
        $rule = new RobotsTxtRuleEntity();
        $directive = new RobotsTxtDirectiveEntity();

        $rule->addDirective($directive);
        self::assertCount(1, $rule->getDirectives());

        $rule->removeDirective($directive);
        self::assertCount(0, $rule->getDirectives());
    }

    public function testToString(): void
    {
        $entity = new RobotsTxtRuleEntity();
        $entity->setUserAgent('*');
        $entity->setPriority(5);

        self::assertSame('User-agent: * (优先级: 5)', (string) $entity);
    }
}
