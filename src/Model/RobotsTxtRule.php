<?php

namespace Tourze\RobotsTxtBundle\Model;

/**
 * 表示一组针对特定User-agent的robots.txt规则
 */
class RobotsTxtRule
{
    /**
     * @param string               $userAgent  用户代理标识符
     * @param RobotsTxtDirective[] $directives 指令列表
     * @param int                  $priority   优先级，数字越大优先级越高
     */
    public function __construct(
        public readonly string $userAgent,
        public readonly array $directives = [],
        public readonly int $priority = 0,
    ) {
    }

    /**
     * 创建针对所有爬虫的规则
     *
     * @param RobotsTxtDirective[] $directives
     */
    public static function forAllAgents(array $directives, int $priority = 0): self
    {
        return new self('*', $directives, $priority);
    }

    /**
     * 创建针对特定爬虫的规则
     *
     * @param RobotsTxtDirective[] $directives
     */
    public static function forAgent(string $userAgent, array $directives, int $priority = 0): self
    {
        return new self($userAgent, $directives, $priority);
    }

    /**
     * 添加指令
     */
    public function withDirective(RobotsTxtDirective $directive): self
    {
        return new self(
            $this->userAgent,
            [...$this->directives, $directive],
            $this->priority
        );
    }

    /**
     * 添加多个指令
     *
     * @param RobotsTxtDirective[] $directives
     */
    public function withDirectives(array $directives): self
    {
        return new self(
            $this->userAgent,
            [...$this->directives, ...$directives],
            $this->priority
        );
    }

    /**
     * 转换为robots.txt格式字符串
     */
    public function toString(): string
    {
        if (0 === count($this->directives)) {
            return '';
        }

        $lines = ['User-agent: ' . $this->userAgent];

        foreach ($this->directives as $directive) {
            $lines[] = $directive->toString();
        }

        return implode("\n", $lines);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
