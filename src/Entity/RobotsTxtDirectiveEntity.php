<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 表示单个robots.txt指令的可持久化实体
 */
#[ORM\Entity]
#[ORM\Table(name: 'robots_txt_directive', options: ['comment' => 'Robots.txt指令表'])]
class RobotsTxtDirectiveEntity
{
    /** @phpstan-ignore property.unusedType */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '指令类型'])]
    #[Assert\NotBlank(message: '指令类型不能为空')]
    #[Assert\Choice(choices: ['Disallow', 'Allow', 'Crawl-delay', 'Sitemap'], message: '无效的指令类型')]
    private string $directive;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '指令值'])]
    #[Assert\NotBlank(message: '指令值不能为空')]
    private string $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: RobotsTxtRuleEntity::class, inversedBy: 'directives')]
    #[ORM\JoinColumn(nullable: true)]
    private ?RobotsTxtRuleEntity $rule = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirective(): string
    {
        return $this->directive;
    }

    public function setDirective(string $directive): void
    {
        $this->directive = $directive;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getRule(): ?RobotsTxtRuleEntity
    {
        return $this->rule;
    }

    public function setRule(?RobotsTxtRuleEntity $rule): void
    {
        $this->rule = $rule;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->directive . ': ' . $this->value;
    }
}
