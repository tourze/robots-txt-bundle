<?php

namespace Tourze\RobotsTxtBundle\Tests\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;

/**
 * @internal
 */
#[CoversClass(RobotsTxtDirective::class)]
final class RobotsTxtDirectiveTest extends TestCase
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

    public function testDisallowWithEmptyPath(): void
    {
        $directive = RobotsTxtDirective::disallow('');

        $this->assertEquals('Disallow', $directive->directive);
        $this->assertEquals('', $directive->value);
        $this->assertEquals('Disallow: ', $directive->toString());
    }

    public function testAllowWithEmptyPath(): void
    {
        $directive = RobotsTxtDirective::allow('');

        $this->assertEquals('Allow', $directive->directive);
        $this->assertEquals('', $directive->value);
        $this->assertEquals('Allow: ', $directive->toString());
    }

    public function testDisallowWithSpecialCharacters(): void
    {
        $directive = RobotsTxtDirective::disallow('/admin/*?query=value&test=123');

        $this->assertEquals('Disallow', $directive->directive);
        $this->assertEquals('/admin/*?query=value&test=123', $directive->value);
        $this->assertEquals('Disallow: /admin/*?query=value&test=123', $directive->toString());
    }

    public function testCrawlDelayWithZero(): void
    {
        $directive = RobotsTxtDirective::crawlDelay(0);

        $this->assertEquals('Crawl-delay', $directive->directive);
        $this->assertEquals('0', $directive->value);
        $this->assertEquals('Crawl-delay: 0', $directive->toString());
    }

    public function testCrawlDelayWithLargeValue(): void
    {
        $directive = RobotsTxtDirective::crawlDelay(86400);

        $this->assertEquals('Crawl-delay', $directive->directive);
        $this->assertEquals('86400', $directive->value);
        $this->assertEquals('Crawl-delay: 86400', $directive->toString());
    }

    public function testSitemapWithComplexUrl(): void
    {
        $directive = RobotsTxtDirective::sitemap('https://example.com/sitemap.xml?version=1&lang=en');

        $this->assertEquals('Sitemap', $directive->directive);
        $this->assertEquals('https://example.com/sitemap.xml?version=1&lang=en', $directive->value);
        $this->assertEquals('Sitemap: https://example.com/sitemap.xml?version=1&lang=en', $directive->toString());
    }

    public function testCustomDirectiveWithEmptyValue(): void
    {
        $directive = new RobotsTxtDirective('Request-rate', '');

        $this->assertEquals('Request-rate', $directive->directive);
        $this->assertEquals('', $directive->value);
        $this->assertEquals('Request-rate: ', $directive->toString());
    }

    public function testCustomDirectiveWithSpacesInDirective(): void
    {
        $directive = new RobotsTxtDirective('Host', 'example.com');

        $this->assertEquals('Host', $directive->directive);
        $this->assertEquals('example.com', $directive->value);
        $this->assertEquals('Host: example.com', $directive->toString());
    }
}
