<?php

namespace Tourze\RobotsTxtBundle\Tests\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;

/**
 * @internal
 */
#[CoversClass(RobotsTxtEntry::class)]
final class RobotsTxtEntryTest extends TestCase
{
    public function testConstructorWithDefaultValues(): void
    {
        $entry = new RobotsTxtEntry();

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([], $entry->comments);
    }

    public function testWithRuleAddsSingleRule(): void
    {
        $entry = new RobotsTxtEntry();
        $rule = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/private/')]);
        $newEntry = $entry->withRule($rule);

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([$rule], $newEntry->rules);
    }

    public function testToStringWithEmptyEntry(): void
    {
        $entry = new RobotsTxtEntry();
        $result = $entry->toString();

        $this->assertEquals('', $result);
    }

    public function testMergeWithEmptyEntries(): void
    {
        $entry1 = new RobotsTxtEntry();
        $entry2 = new RobotsTxtEntry();
        $merged = $entry1->merge($entry2);

        $this->assertEquals([], $merged->rules);
        $this->assertEquals([], $merged->sitemaps);
        $this->assertEquals([], $merged->comments);
    }

    public function testWithComment(): void
    {
        $entry = new RobotsTxtEntry();
        $comment = 'Test comment';
        $newEntry = $entry->withComment($comment);

        $this->assertEquals([], $entry->comments);
        $this->assertEquals([$comment], $newEntry->comments);
    }

    public function testWithRules(): void
    {
        $entry = new RobotsTxtEntry();
        $rules = [
            RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')]),
            RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::crawlDelay(5)]),
        ];
        $newEntry = $entry->withRules($rules);

        $this->assertEquals([], $entry->rules);
        $this->assertEquals($rules, $newEntry->rules);
    }

    public function testWithSitemap(): void
    {
        $entry = new RobotsTxtEntry();
        $sitemapUrl = 'https://example.com/sitemap.xml';
        $newEntry = $entry->withSitemap($sitemapUrl);

        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([$sitemapUrl], $newEntry->sitemaps);
    }

    public function testWithSitemaps(): void
    {
        $entry = new RobotsTxtEntry();
        $sitemaps = [
            'https://example.com/sitemap1.xml',
            'https://example.com/sitemap2.xml',
        ];
        $newEntry = $entry->withSitemaps($sitemaps);

        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals($sitemaps, $newEntry->sitemaps);
    }
}
