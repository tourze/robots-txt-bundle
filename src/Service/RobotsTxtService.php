<?php

namespace Tourze\RobotsTxtBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;

/**
 * robots.txt服务
 *
 * 负责收集所有提供者的内容并生成最终的robots.txt文件
 */
class RobotsTxtService
{
    /**
     * @param iterable<RobotsTxtProviderInterface> $providers
     */
    public function __construct(#[TaggedIterator(tag: 'robots_txt.provider')] private readonly iterable $providers = [])
    {
    }

    /**
     * 生成完整的robots.txt内容
     */
    public function generate(): string
    {
        $entry = $this->collectEntries();
        return $entry->toString();
    }

    /**
     * 收集所有提供者的条目
     */
    public function collectEntries(): RobotsTxtEntry
    {
        $providers = $this->getSupportedProviders();

        // 确保获取所有提供者的优先级，即使只有一个提供者
        if (!empty($providers)) {
            // 先确保所有提供者的优先级被获取
            foreach ($providers as $provider) {
                $provider->getPriority();
            }

            // 按优先级排序
            usort($providers, fn($a, $b) => $b->getPriority() <=> $a->getPriority());
        }

        $finalEntry = new RobotsTxtEntry();

        foreach ($providers as $provider) {
            $entry = $provider->provide();
            $finalEntry = $finalEntry->merge($entry);
        }

        return $finalEntry;
    }

    /**
     * 获取支持的提供者列表
     *
     * @return RobotsTxtProviderInterface[]
     */
    private function getSupportedProviders(): array
    {
        $supportedProviders = [];

        foreach ($this->providers as $provider) {
            if ($provider->supports()) {
                $supportedProviders[] = $provider;
            }
        }

        return $supportedProviders;
    }

    /**
     * 检查robots.txt是否为空
     */
    public function isEmpty(): bool
    {
        $entry = $this->collectEntries();
        $content = $entry->toString();
        return empty(trim($content));
    }

    /**
     * 获取所有已注册的提供者
     *
     * @return RobotsTxtProviderInterface[]
     */
    public function getProviders(): array
    {
        return iterator_to_array($this->providers);
    }
}
