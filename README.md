# RobotsTxt Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/robots-txt-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/robots-txt-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/robots-txt-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/robots-txt-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/robots-txt-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/robots-txt-bundle)
[![License](https://img.shields.io/packagist/l/tourze/robots-txt-bundle.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/robots-txt-bundle/ci.yml?style=flat-square)](
https://github.com/tourze/robots-txt-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/robots-txt-bundle?style=flat-square)](
https://codecov.io/gh/tourze/robots-txt-bundle)

A Symfony Bundle for managing `robots.txt` files with a multi-provider architecture, 
allowing flexible assembly and generation of robots.txt content.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Requirements](#requirements)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Creating Custom Providers](#creating-custom-providers)
- [API Reference](#api-reference)
  - [RobotsTxtDirective](#robotstxtdirective)
  - [RobotsTxtRule](#robotstxtrule)
  - [RobotsTxtEntry](#robotstxtentry)
- [Advanced Usage](#advanced-usage)
  - [Dynamic Rules](#dynamic-rules)
  - [Conditional Providers](#conditional-providers)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

- 🤖 **Complete robots.txt support**: Support for all standard directives 
  (User-agent, Allow, Disallow, Crawl-delay, Sitemap, etc.)
- 🔌 **Multi-provider architecture**: Collect rules from multiple sources by 
  implementing `RobotsTxtProviderInterface`
- 🎯 **Priority control**: Support provider priority to flexibly control rule 
  merging order
- 🌍 **Environment support**: Provide different robots.txt content based on 
  different environments
- ⚡ **Performance optimization**: Built-in caching strategy to reduce server load
- 📖 **Standards compliant**: Follows 
  [Google robots.txt specification](https://developers.google.com/search/docs/crawling-indexing/robots/robots_txt)
- 🔧 **Auto-configuration**: Providers are automatically registered using 
  Symfony's autoconfiguration

## Installation

```bash
composer require tourze/robots-txt-bundle
```

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher

## Configuration

The Bundle works out of the box with minimal configuration. The `/robots.txt` 
route is automatically registered and will collect content from all registered 
providers.

### Custom Configuration

If you need to customize the Bundle behavior, create a configuration file:

```yaml
# config/packages/robots_txt.yaml
robots_txt:
    cache_enabled: true
    cache_ttl: 3600
```

## Usage

### Basic Usage

The Bundle automatically registers a `/robots.txt` route that returns the generated robots.txt content.

### Creating Custom Providers

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

        // Add comments
        $entry = $entry->withComment('Custom robots.txt rules');

        // Disallow all crawlers from accessing admin areas
        $adminRule = RobotsTxtRule::forAllAgents([
            RobotsTxtDirective::disallow('/admin/'),
            RobotsTxtDirective::disallow('/private/'),
        ]);
        $entry = $entry->withRule($adminRule);

        // Set special rules for Googlebot
        $googlebotRule = RobotsTxtRule::forAgent('Googlebot', [
            RobotsTxtDirective::allow('/api/public/'),
            RobotsTxtDirective::crawlDelay(1),
        ]);
        $entry = $entry->withRule($googlebotRule);

        // Add sitemap
        $entry = $entry->withSitemap('https://example.com/sitemap.xml');

        return $entry;
    }

    public function getPriority(): int
    {
        return 100; // High priority
    }

    public function supports(): bool
    {
        // Only enable in production environment
        return ($_ENV['APP_ENV'] ?? 'prod') === 'prod';
    }
}
```

Providers are automatically registered without additional configuration.

## API Reference

### RobotsTxtDirective

Create individual robots.txt directives:

```php
// Disallow directive
RobotsTxtDirective::disallow('/admin/');

// Allow directive
RobotsTxtDirective::allow('/public/');

// Crawl-delay directive
RobotsTxtDirective::crawlDelay(10);

// Sitemap directive
RobotsTxtDirective::sitemap('https://example.com/sitemap.xml');

// Custom directive
new RobotsTxtDirective('Custom-directive', 'value');
```

### RobotsTxtRule

Create rule groups for specific User-agents:

```php
// For all crawlers
RobotsTxtRule::forAllAgents([
    RobotsTxtDirective::disallow('/admin/'),
]);

// For specific crawlers
RobotsTxtRule::forAgent('Googlebot', [
    RobotsTxtDirective::allow('/api/'),
    RobotsTxtDirective::crawlDelay(1),
], 100); // Priority
```

### RobotsTxtEntry

Complete robots.txt entry:

```php
$entry = new RobotsTxtEntry();
$entry = $entry->withComment('Generated robots.txt')
               ->withRule($rule)
               ->withSitemap('https://example.com/sitemap.xml');
```

## Advanced Usage

### Dynamic Rules

```php
class DynamicRobotsTxtProvider implements RobotsTxtProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function provide(): RobotsTxtEntry
    {
        $entry = new RobotsTxtEntry();
        
        // Dynamically generate rules based on user data
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

### Conditional Providers

```php
class ConditionalRobotsTxtProvider implements RobotsTxtProviderInterface
{
    public function supports(): bool
    {
        // Only enable under specific conditions
        return $_ENV['ENABLE_SEO'] === 'true' && 
               $_ENV['APP_ENV'] === 'prod';
    }
}
```

## Testing

```bash
./vendor/bin/phpunit packages/robots-txt-bundle/tests
```

## Contributing

Please see [CONTRIBUTING.md](../../CONTRIBUTING.md) for details on how to contribute to this project.

## License

MIT License - Please see [LICENSE](LICENSE) file for more information.
