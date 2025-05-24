<?php

namespace Tourze\RobotsTxtBundle\Tests\Model\RobotsTxtEntry;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;

class RobotsTxtEntryBasicTest extends TestCase
{
    public function test_constructor_withDefaultValues(): void
    {
        $entry = new RobotsTxtEntry();

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([], $entry->comments);
    }

    public function test_constructor_withAllParameters(): void
    {
        $rules = [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])];
        $sitemaps = ['https://example.com/sitemap.xml'];
        $comments = ['Generated robots.txt'];

        $entry = new RobotsTxtEntry($rules, $sitemaps, $comments);

        $this->assertEquals($rules, $entry->rules);
        $this->assertEquals($sitemaps, $entry->sitemaps);
        $this->assertEquals($comments, $entry->comments);
    }

    public function test_withRule_addsSingleRule(): void
    {
        $entry = new RobotsTxtEntry();
        $rule = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/private/')]);
        $newEntry = $entry->withRule($rule);

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([$rule], $newEntry->rules);
        $this->assertEquals([], $newEntry->sitemaps);
        $this->assertEquals([], $newEntry->comments);
    }

    public function test_withRules_addsMultipleRules(): void
    {
        $initialRule = RobotsTxtRule::forAllAgents([RobotsTxtDirective::allow('/public/')]);
        $entry = new RobotsTxtEntry([$initialRule]);

        $newRules = [
            RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::crawlDelay(10)]),
            RobotsTxtRule::forAgent('Bingbot', [RobotsTxtDirective::disallow('/private/')])
        ];
        $newEntry = $entry->withRules($newRules);

        $expectedRules = [$initialRule, ...$newRules];
        $this->assertEquals($expectedRules, $newEntry->rules);
        $this->assertCount(3, $newEntry->rules);
    }

    public function test_withSitemap_addsSingleSitemap(): void
    {
        $entry = new RobotsTxtEntry();
        $sitemapUrl = 'https://example.com/sitemap.xml';
        $newEntry = $entry->withSitemap($sitemapUrl);

        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([$sitemapUrl], $newEntry->sitemaps);
    }

    public function test_withSitemaps_addsMultipleSitemaps(): void
    {
        $initialSitemap = 'https://example.com/sitemap1.xml';
        $entry = new RobotsTxtEntry([], [$initialSitemap]);

        $newSitemaps = [
            'https://example.com/sitemap2.xml',
            'https://example.com/sitemap3.xml'
        ];
        $newEntry = $entry->withSitemaps($newSitemaps);

        $expectedSitemaps = [$initialSitemap, ...$newSitemaps];
        $this->assertEquals($expectedSitemaps, $newEntry->sitemaps);
        $this->assertCount(3, $newEntry->sitemaps);
    }

    public function test_withComment_addsSingleComment(): void
    {
        $entry = new RobotsTxtEntry();
        $comment = 'This is a test comment';
        $newEntry = $entry->withComment($comment);

        $this->assertEquals([], $entry->comments);
        $this->assertEquals([$comment], $newEntry->comments);
    }

    public function test_withComment_addsMultipleComments(): void
    {
        $entry = new RobotsTxtEntry();
        $firstComment = 'First comment';
        $secondComment = 'Second comment';

        $entryWithFirst = $entry->withComment($firstComment);
        $entryWithBoth = $entryWithFirst->withComment($secondComment);

        $this->assertEquals([], $entry->comments);
        $this->assertEquals([$firstComment], $entryWithFirst->comments);
        $this->assertEquals([$firstComment, $secondComment], $entryWithBoth->comments);
    }

    public function test_immutability_withRule(): void
    {
        $originalRule = RobotsTxtRule::forAllAgents([RobotsTxtDirective::allow('/api/')]);
        $entry = new RobotsTxtEntry([$originalRule]);
        
        $newRule = RobotsTxtRule::forAgent('TestBot', [RobotsTxtDirective::disallow('/test/')]);
        $newEntry = $entry->withRule($newRule);

        // Original entry should remain unchanged
        $this->assertCount(1, $entry->rules);
        $this->assertEquals($originalRule, $entry->rules[0]);

        // New entry should have both rules
        $this->assertCount(2, $newEntry->rules);
        $this->assertEquals($originalRule, $newEntry->rules[0]);
        $this->assertEquals($newRule, $newEntry->rules[1]);
    }

    public function test_immutability_withSitemap(): void
    {
        $originalSitemap = 'https://example.com/original.xml';
        $entry = new RobotsTxtEntry([], [$originalSitemap]);
        
        $newSitemap = 'https://example.com/new.xml';
        $newEntry = $entry->withSitemap($newSitemap);

        // Original entry should remain unchanged
        $this->assertEquals([$originalSitemap], $entry->sitemaps);

        // New entry should have both sitemaps
        $this->assertEquals([$originalSitemap, $newSitemap], $newEntry->sitemaps);
    }

    public function test_immutability_withComment(): void
    {
        $originalComment = 'Original comment';
        $entry = new RobotsTxtEntry([], [], [$originalComment]);
        
        $newComment = 'New comment';
        $newEntry = $entry->withComment($newComment);

        // Original entry should remain unchanged
        $this->assertEquals([$originalComment], $entry->comments);

        // New entry should have both comments
        $this->assertEquals([$originalComment, $newComment], $newEntry->comments);
    }

    public function test_withRules_withEmptyArray(): void
    {
        $entry = new RobotsTxtEntry();
        $newEntry = $entry->withRules([]);

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([], $newEntry->rules);
    }

    public function test_withSitemaps_withEmptyArray(): void
    {
        $entry = new RobotsTxtEntry();
        $newEntry = $entry->withSitemaps([]);

        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([], $newEntry->sitemaps);
    }
} 