# RobotsTxt Bundle (中文文档)

一个功能完整的 Symfony Bundle，用于动态生成和管理 `robots.txt` 文件。

## 核心架构

### 数据模型

- **RobotsTxtDirective**: 单个指令（如 `Disallow: /admin/`）
- **RobotsTxtRule**: 针对特定 User-agent 的规则组
- **RobotsTxtEntry**: 完整的 robots.txt 条目

### 提供者系统

- **RobotsTxtProviderInterface**: 核心接口，定义内容提供者
- **DefaultRobotsTxtProvider**: 默认规则提供者

### 服务层

- **RobotsTxtService**: 核心服务，收集和合并所有提供者的内容
- **RobotsTxtController**: 控制器，响应 `/robots.txt` 请求

## 特点

- **简洁架构**: 使用现代 PHP 8 属性，无需复杂配置
- **自动发现**: 提供者自动注册，无需手动配置
- **高性能**: 优化的缓存策略和最小化配置

## 快速开始

1. 安装 Bundle
2. 创建自定义提供者
3. 实现 `RobotsTxtProviderInterface`
4. 访问 `/robots.txt`

## 示例输出

```
# Generated by RobotsTxtBundle

User-agent: *
Disallow: /admin/
Disallow: /private/

User-agent: Googlebot
Allow: /api/public/
Crawl-delay: 1

Sitemap: https://example.com/sitemap.xml
```

## 核心特性

- 支持环境特定配置
- 提供者优先级控制
- 动态规则生成
- 性能缓存优化
- 完全符合 Google 规范
- 基于 PHP 8 属性的现代架构

详细文档请参见 [README.md](README.md)。
