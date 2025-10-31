<?php

namespace Tourze\RobotsTxtBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
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
    public function __construct(#[AutowireIterator(tag: 'robots_txt.provider')] private readonly iterable $providers = [])
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
        $sortedProviders = $this->sortProvidersByPriority($providers);

        $finalEntry = new RobotsTxtEntry();

        foreach ($sortedProviders as $provider) {
            $entry = $provider->provide();
            $finalEntry = $finalEntry->merge($entry);
        }

        return $finalEntry;
    }

    /**
     * 按优先级排序提供者
     *
     * @param RobotsTxtProviderInterface[] $providers
     * @return RobotsTxtProviderInterface[]
     */
    private function sortProvidersByPriority(array $providers): array
    {
        if (0 === count($providers)) {
            return [];
        }

        // 先确保所有提供者的优先级被获取
        $this->prefetchProviderPriorities($providers);

        // 按优先级排序
        return $this->sortProvidersByPriorityValue($providers);
    }

    /**
     * 预取所有提供者的优先级
     *
     * @param RobotsTxtProviderInterface[] $providers
     */
    private function prefetchProviderPriorities(array $providers): void
    {
        foreach ($providers as $provider) {
            $provider->getPriority();
        }
    }

    /**
     * 根据优先级值排序提供者
     *
     * @param RobotsTxtProviderInterface[] $providers
     * @return RobotsTxtProviderInterface[]
     */
    private function sortProvidersByPriorityValue(array $providers): array
    {
        usort($providers, function (RobotsTxtProviderInterface $a, RobotsTxtProviderInterface $b): int {
            $priorityA = $a->getPriority();
            $priorityB = $b->getPriority();

            if ($priorityA === $priorityB) {
                return 0;
            }

            return $priorityA > $priorityB ? -1 : 1;
        });

        return $providers;
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

        return '' === trim($content);
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
