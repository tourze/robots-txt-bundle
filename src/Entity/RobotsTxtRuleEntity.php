<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 表示一组针对特定User-agent的robots.txt规则的可持久化实体
 */
#[ORM\Entity]
#[ORM\Table(name: 'robots_txt_rule', options: ['comment' => 'Robots.txt规则表'])]
class RobotsTxtRuleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    /** @phpstan-ignore property.unusedType */
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '用户代理标识符'])]
    #[Assert\NotBlank(message: '用户代理不能为空')]
    #[Assert\Length(max: 255, maxMessage: '用户代理长度不能超过255个字符')]
    private string $userAgent;

    /**
     * @var Collection<int, RobotsTxtDirectiveEntity>
     */
    #[ORM\OneToMany(
        mappedBy: 'rule',
        targetEntity: RobotsTxtDirectiveEntity::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $directives;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '优先级'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: '优先级不能为负数')]
    private int $priority = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: RobotsTxtEntryEntity::class, inversedBy: 'rules')]
    #[ORM\JoinColumn(nullable: true)]
    private ?RobotsTxtEntryEntity $entry = null;

    public function __construct()
    {
        $this->directives = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, RobotsTxtDirectiveEntity>
     */
    public function getDirectives(): Collection
    {
        return $this->directives;
    }

    public function addDirective(RobotsTxtDirectiveEntity $directive): self
    {
        if (!$this->directives->contains($directive)) {
            $this->directives->add($directive);
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function removeDirective(RobotsTxtDirectiveEntity $directive): self
    {
        $this->directives->removeElement($directive);
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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

    public function getEntry(): ?RobotsTxtEntryEntity
    {
        return $this->entry;
    }

    public function setEntry(?RobotsTxtEntryEntity $entry): void
    {
        $this->entry = $entry;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return 'User-agent: ' . $this->userAgent . ' (优先级: ' . $this->priority . ')';
    }
}
