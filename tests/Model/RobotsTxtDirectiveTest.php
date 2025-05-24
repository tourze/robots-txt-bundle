<?php

namespace Tourze\RobotsTxtBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;

class RobotsTxtDirectiveTest extends TestCase
{
    public function testDisallowDirective(): void
    {
        $directive = RobotsTxtDirective::disallow('/admin/');
        
        $this->assertEquals('Disallow', $directive->directive);
        $this->assertEquals('/admin/', $directive->value);
        $this->assertEquals('Disallow: /admin/', $directive->toString());
    }

    public function testAllowDirective(): void
    {
        $directive = RobotsTxtDirective::allow('/public/');
        
        $this->assertEquals('Allow', $directive->directive);
        $this->assertEquals('/public/', $directive->value);
        $this->assertEquals('Allow: /public/', $directive->toString());
    }

    public function testCrawlDelayDirective(): void
    {
        $directive = RobotsTxtDirective::crawlDelay(10);
        
        $this->assertEquals('Crawl-delay', $directive->directive);
        $this->assertEquals('10', $directive->value);
        $this->assertEquals('Crawl-delay: 10', $directive->toString());
    }

    public function testSitemapDirective(): void
    {
        $directive = RobotsTxtDirective::sitemap('https://example.com/sitemap.xml');
        
        $this->assertEquals('Sitemap', $directive->directive);
        $this->assertEquals('https://example.com/sitemap.xml', $directive->value);
        $this->assertEquals('Sitemap: https://example.com/sitemap.xml', $directive->toString());
    }

    public function testCustomDirective(): void
    {
        $directive = new RobotsTxtDirective('Custom', 'value');
        
        $this->assertEquals('Custom', $directive->directive);
        $this->assertEquals('value', $directive->value);
        $this->assertEquals('Custom: value', $directive->toString());
    }

    public function testToString(): void
    {
        $directive = RobotsTxtDirective::disallow('/test/');
        
        $this->assertEquals('Disallow: /test/', (string) $directive);
    }
} 