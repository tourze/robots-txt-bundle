<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtDirectiveEntity;

/**
 * RobotsTxtDirectiveEntity实体测试
 * @internal
 */
#[CoversClass(RobotsTxtDirectiveEntity::class)]
class RobotsTxtDirectiveEntityTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new RobotsTxtDirectiveEntity();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['directive', 'Disallow'],
            ['value', '/admin'],
        ];
    }

    public function testConstruct(): void
    {
        $entity = new RobotsTxtDirectiveEntity();

        self::assertNull($entity->getId());
        self::assertLessThanOrEqual(new \DateTimeImmutable(), $entity->getCreatedAt());
        self::assertNull($entity->getUpdatedAt());
    }

    public function testSettersAndGetters(): void
    {
        $entity = new RobotsTxtDirectiveEntity();

        $entity->setDirective('Disallow');
        self::assertSame('Disallow', $entity->getDirective());

        $entity->setValue('/admin');
        self::assertSame('/admin', $entity->getValue());

        self::assertLessThanOrEqual(new \DateTimeImmutable(), $entity->getUpdatedAt());
    }

    public function testToString(): void
    {
        $entity = new RobotsTxtDirectiveEntity();
        $entity->setDirective('Disallow');
        $entity->setValue('/admin');

        self::assertSame('Disallow: /admin', (string) $entity);
    }
}
