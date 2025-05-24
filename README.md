# RobotsTxt Bundle

一个用于管理 `robots.txt` 文件的 Symfony Bundle，支持多提供者架构，可以灵活地组装和生成 robots.txt 内容。

## 特性

- 🤖 **完整的 robots.txt 支持**：支持所有标准指令（User-agent、Allow、Disallow、Crawl-delay、Sitemap等）
- 🔌 **多提供者架构**：通过实现 `RobotsTxtProviderInterface` 可以从多个来源收集规则
- 🎯 **优先级控制**：支持提供者优先级，灵活控制规则合并顺序
- 🌍 **环境支持**：可以根据不同环境提供不同的 robots.txt 内容
- ⚡ **性能优化**：内置缓存策略，减少服务器负载
- 📖 **符合规范**：遵循 [Google robots.txt 规范](https://developers.google.com/search/docs/crawling-indexing/robots/robots_txt)

## 安装

```bash
composer require tourze/robots-txt-bundle
```

## 使用方法

### 基本用法

Bundle 会自动注册 `/robots.txt` 路由，返回生成的 robots.txt 内容。

### 创建自定义提供者

```php
<?php

namespace App\Provider;

use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;

class CustomRobotsTxtProvider implements RobotsTxtProviderInterface
{
    public function provide(): RobotsTxtEntry
    {
        $entry = new RobotsTxtEntry();

        // 添加注释
        $entry = $entry->withComment('Custom robots.txt rules');

        // 禁止所有爬虫访问管理区域
        $adminRule = RobotsTxtRule::forAllAgents([
            RobotsTxtDirective::disallow('/admin/'),
            RobotsTxtDirective::disallow('/private/'),
        ]);
        $entry = $entry->withRule($adminRule);

        // 为 Googlebot 设置特殊规则
        $googlebotRule = RobotsTxtRule::forAgent('Googlebot', [
            RobotsTxtDirective::allow('/api/public/'),
            RobotsTxtDirective::crawlDelay(1),
        ]);
        $entry = $entry->withRule($googlebotRule);

        // 添加站点地图
        $entry = $entry->withSitemap('https://example.com/sitemap.xml');

        return $entry;
    }

    public function getPriority(): int
    {
        return 100; // 高优先级
    }

    public function supports(): bool
    {
        // 只在生产环境启用
        return ($_ENV['APP_ENV'] ?? 'prod') === 'prod';
    }
}
```

提供者会自动被注册，无需额外配置。

## API 参考

### RobotsTxtDirective

创建单个 robots.txt 指令：

```php
// Disallow 指令
RobotsTxtDirective::disallow('/admin/');

// Allow 指令
RobotsTxtDirective::allow('/public/');

// Crawl-delay 指令
RobotsTxtDirective::crawlDelay(10);

// Sitemap 指令
RobotsTxtDirective::sitemap('https://example.com/sitemap.xml');

// 自定义指令
new RobotsTxtDirective('Custom-directive', 'value');
```

### RobotsTxtRule

创建针对特定 User-agent 的规则组：

```php
// 针对所有爬虫
RobotsTxtRule::forAllAgents([
    RobotsTxtDirective::disallow('/admin/'),
]);

// 针对特定爬虫
RobotsTxtRule::forAgent('Googlebot', [
    RobotsTxtDirective::allow('/api/'),
    RobotsTxtDirective::crawlDelay(1),
], 100); // 优先级
```

### RobotsTxtEntry

完整的 robots.txt 条目：

```php
$entry = new RobotsTxtEntry();
$entry = $entry->withComment('Generated robots.txt')
               ->withRule($rule)
               ->withSitemap('https://example.com/sitemap.xml');
```

## 高级用法

### 动态规则

```php
class DynamicRobotsTxtProvider implements RobotsTxtProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function provide(): RobotsTxtEntry
    {
        $entry = new RobotsTxtEntry();
        
        // 根据用户数据动态生成规则
        $users = $this->userRepository->findPublicUsers();
        foreach ($users as $user) {
            $entry = $entry->withRule(
                RobotsTxtRule::forAllAgents([
                    RobotsTxtDirective::allow("/users/{$user->getId()}/")
                ])
            );
        }

        return $entry;
    }
}
```

### 条件性提供者

```php
class ConditionalRobotsTxtProvider implements RobotsTxtProviderInterface
{
    public function supports(): bool
    {
        // 只在特定条件下启用
        return $_ENV['ENABLE_SEO'] === 'true' && 
               $_ENV['APP_ENV'] === 'prod';
    }
}
```

## 测试

```bash
./vendor/bin/phpunit tests
```

## 许可证

MIT License - 查看 [LICENSE](LICENSE) 文件了解详情。
