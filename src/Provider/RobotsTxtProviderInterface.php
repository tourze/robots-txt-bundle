<?php

namespace Tourze\RobotsTxtBundle\Provider;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;

/**
 * robots.txt内容提供者接口
 *
 * 实现此接口的服务可以为robots.txt文件提供内容
 */
#[AutoconfigureTag(name: 'robots_txt.provider')]
interface RobotsTxtProviderInterface
{
    /**
     * 提供robots.txt条目
     *
     * @return RobotsTxtEntry robots.txt条目
     */
    public function provide(): RobotsTxtEntry;

    /**
     * 获取提供者优先级
     *
     * 数字越大优先级越高，优先级高的提供者的规则会覆盖优先级低的
     *
     * @return int 优先级
     */
    public function getPriority(): int;

    /**
     * 检查提供者是否支持当前环境
     *
     * 可以根据环境变量、配置等条件决定是否启用此提供者
     *
     * @return bool 是否支持
     */
    public function supports(): bool;
}
