<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 完整的robots.txt条目的可持久化实体
 */
#[ORM\Entity]
#[ORM\Table(name: 'robots_txt_entry', options: ['comment' => 'Robots.txt条目表'])]
class RobotsTxtEntryEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '条目名称'])]
    #[Assert\NotBlank(message: '条目名称不能为空')]
    #[Assert\Length(max: 255, maxMessage: '条目名称长度不能超过255个字符')]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '条目描述'])]
    #[Assert\Length(max: 1000, maxMessage: '条目描述长度不能超过1000个字符')]
    private ?string $description = null;

    /**
     * @var Collection<int, RobotsTxtRuleEntity>
     */
    #[ORM\OneToMany(
        mappedBy: 'entry',
        targetEntity: RobotsTxtRuleEntity::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $rules;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '站点地图URL列表'])]
    #[Assert\Type(type: 'array', message: '站点地图必须是数组')]
    private array $sitemaps = [];

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '注释列表'])]
    #[Assert\Type(type: 'array', message: '注释必须是数组')]
    private array $comments = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否激活'])]
    #[Assert\Type(type: 'bool', message: '激活状态必须是布尔值')]
    private bool $active = true;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, RobotsTxtRuleEntity>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(RobotsTxtRuleEntity $rule): self
    {
        if (!$this->rules->contains($rule)) {
            $this->rules->add($rule);
            $rule->setEntry($this);
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function removeRule(RobotsTxtRuleEntity $rule): self
    {
        if ($this->rules->removeElement($rule)) {
            if ($rule->getEntry() === $this) {
                $rule->setEntry(null);
            }
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getSitemaps(): array
    {
        return $this->sitemaps;
    }

    /**
     * @param array<string> $sitemaps
     */
    public function setSitemaps(array $sitemaps): void
    {
        $this->sitemaps = $sitemaps;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function addSitemap(string $sitemap): self
    {
        if (!in_array($sitemap, $this->sitemaps, true)) {
            $this->sitemaps[] = $sitemap;
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function removeSitemap(string $sitemap): self
    {
        $key = array_search($sitemap, $this->sitemaps, true);
        if (false !== $key) {
            unset($this->sitemaps[$key]);
            $this->sitemaps = array_values($this->sitemaps);
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param array<string> $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function addComment(string $comment): self
    {
        if (!in_array($comment, $this->comments, true)) {
            $this->comments[] = $comment;
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function removeComment(string $comment): self
    {
        $key = array_search($comment, $this->comments, true);
        if (false !== $key) {
            unset($this->comments[$key]);
            $this->comments = array_values($this->comments);
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
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

    public function __toString(): string
    {
        return $this->name . ($this->active ? '' : ' (未激活)');
    }
}
