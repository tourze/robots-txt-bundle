<?php

namespace Tourze\RobotsTxtBundle\Model;

/**
 * 表示单个robots.txt指令
 */
class RobotsTxtDirective
{
    public function __construct(
        public readonly string $directive,
        public readonly string $value,
    ) {
    }

    /**
     * 创建Disallow指令
     */
    public static function disallow(string $path): self
    {
        return new self('Disallow', $path);
    }

    /**
     * 创建Allow指令
     */
    public static function allow(string $path): self
    {
        return new self('Allow', $path);
    }

    /**
     * 创建Crawl-delay指令
     */
    public static function crawlDelay(int $seconds): self
    {
        return new self('Crawl-delay', (string) $seconds);
    }

    /**
     * 创建Sitemap指令
     */
    public static function sitemap(string $url): self
    {
        return new self('Sitemap', $url);
    }

    /**
     * 转换为robots.txt格式字符串
     */
    public function toString(): string
    {
        return $this->directive . ': ' . $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
