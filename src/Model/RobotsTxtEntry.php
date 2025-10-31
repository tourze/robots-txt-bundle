<?php

namespace Tourze\RobotsTxtBundle\Model;

/**
 * 完整的robots.txt条目
 */
class RobotsTxtEntry
{
    /**
     * @param RobotsTxtRule[] $rules    规则列表
     * @param string[]        $sitemaps 站点地图URL列表
     * @param string[]        $comments 注释列表
     */
    public function __construct(
        public readonly array $rules = [],
        public readonly array $sitemaps = [],
        public readonly array $comments = [],
    ) {
    }

    /**
     * 添加规则
     */
    public function withRule(RobotsTxtRule $rule): self
    {
        return new self(
            [...$this->rules, $rule],
            $this->sitemaps,
            $this->comments
        );
    }

    /**
     * 添加多个规则
     *
     * @param RobotsTxtRule[] $rules
     */
    public function withRules(array $rules): self
    {
        return new self(
            [...$this->rules, ...$rules],
            $this->sitemaps,
            $this->comments
        );
    }

    /**
     * 添加站点地图
     */
    public function withSitemap(string $sitemapUrl): self
    {
        return new self(
            $this->rules,
            [...$this->sitemaps, $sitemapUrl],
            $this->comments
        );
    }

    /**
     * 添加多个站点地图
     *
     * @param string[] $sitemaps
     */
    public function withSitemaps(array $sitemaps): self
    {
        return new self(
            $this->rules,
            [...$this->sitemaps, ...$sitemaps],
            $this->comments
        );
    }

    /**
     * 添加注释
     */
    public function withComment(string $comment): self
    {
        return new self(
            $this->rules,
            $this->sitemaps,
            [...$this->comments, $comment]
        );
    }

    /**
     * 合并另一个条目
     */
    public function merge(RobotsTxtEntry $other): self
    {
        return new self(
            [...$this->rules, ...$other->rules],
            [...$this->sitemaps, ...$other->sitemaps],
            [...$this->comments, ...$other->comments]
        );
    }

    /**
     * 转换为robots.txt格式字符串
     */
    public function toString(): string
    {
        $lines = [];

        $lines = array_merge($lines, $this->getCommentsLines());
        $lines = array_merge($lines, $this->getRulesLines());
        $lines = array_merge($lines, $this->getSitemapsLines());

        return implode("\n", $lines);
    }

    /**
     * @return string[]
     */
    private function getCommentsLines(): array
    {
        $lines = [];
        foreach ($this->comments as $comment) {
            $lines[] = '# ' . $comment;
        }

        if (count($this->comments) > 0 && (count($this->rules) > 0 || count($this->sitemaps) > 0)) {
            $lines[] = '';
        }

        return $lines;
    }

    /**
     * @return string[]
     */
    private function getRulesLines(): array
    {
        $lines = [];
        $groupedRules = $this->groupRulesByUserAgent();

        $ruleIndex = 0;
        foreach ($groupedRules as $userAgent => $directives) {
            if ($ruleIndex > 0) {
                $lines[] = '';
            }

            $lines[] = 'User-agent: ' . $userAgent;
            foreach ($directives as $directive) {
                $lines[] = $directive->toString();
            }

            ++$ruleIndex;
        }

        return $lines;
    }

    /**
     * @return array<string, RobotsTxtDirective[]>
     */
    private function groupRulesByUserAgent(): array
    {
        $sortedRules = $this->rules;
        usort($sortedRules, fn ($a, $b) => $b->priority <=> $a->priority);

        $groupedRules = [];
        foreach ($sortedRules as $rule) {
            if (!isset($groupedRules[$rule->userAgent])) {
                $groupedRules[$rule->userAgent] = [];
            }
            $groupedRules[$rule->userAgent] = array_merge(
                $groupedRules[$rule->userAgent],
                $rule->directives
            );
        }

        return $groupedRules;
    }

    /**
     * @return string[]
     */
    private function getSitemapsLines(): array
    {
        $lines = [];
        if (count($this->sitemaps) > 0) {
            // Add separator if we have previous content
            if (count($this->comments) > 0 || count($this->rules) > 0) {
                $lines[] = '';
            }

            foreach ($this->sitemaps as $sitemap) {
                $lines[] = 'Sitemap: ' . $sitemap;
            }
        }

        return $lines;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
