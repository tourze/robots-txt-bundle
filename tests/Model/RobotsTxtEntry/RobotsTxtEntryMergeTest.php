<?php

namespace Tourze\RobotsTxtBundle\Tests\Model\RobotsTxtEntry;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;

class RobotsTxtEntryMergeTest extends TestCase
{
    public function test_merge_withEmptyEntries(): void
    {
        $entry1 = new RobotsTxtEntry();
        $entry2 = new RobotsTxtEntry();
        $merged = $entry1->merge($entry2);

        $this->assertEquals([], $merged->rules);
        $this->assertEquals([], $merged->sitemaps);
        $this->assertEquals([], $merged->comments);
    }

    public function test_merge_withRulesOnly(): void
    {
        $rule1 = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')]);
        $rule2 = RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::allow('/api/')]);

        $entry1 = new RobotsTxtEntry([$rule1]);
        $entry2 = new RobotsTxtEntry([$rule2]);
        $merged = $entry1->merge($entry2);

        $this->assertEquals([$rule1, $rule2], $merged->rules);
        $this->assertEquals([], $merged->sitemaps);
        $this->assertEquals([], $merged->comments);
    }

    public function test_merge_withSitemapsOnly(): void
    {
        $sitemap1 = 'https://example.com/sitemap1.xml';
        $sitemap2 = 'https://example.com/sitemap2.xml';
        
        $entry1 = new RobotsTxtEntry([], [$sitemap1]);
        $entry2 = new RobotsTxtEntry([], [$sitemap2]);
        $merged = $entry1->merge($entry2);

        $this->assertEquals([], $merged->rules);
        $this->assertEquals([$sitemap1, $sitemap2], $merged->sitemaps);
        $this->assertEquals([], $merged->comments);
    }

    public function test_merge_withCommentsOnly(): void
    {
        $comment1 = 'First comment';
        $comment2 = 'Second comment';
        
        $entry1 = new RobotsTxtEntry([], [], [$comment1]);
        $entry2 = new RobotsTxtEntry([], [], [$comment2]);
        $merged = $entry1->merge($entry2);

        $this->assertEquals([], $merged->rules);
        $this->assertEquals([], $merged->sitemaps);
        $this->assertEquals([$comment1, $comment2], $merged->comments);
    }

    public function test_merge_withAllComponents(): void
    {
        $rule1 = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/private/')]);
        $rule2 = RobotsTxtRule::forAgent('Bingbot', [RobotsTxtDirective::crawlDelay(5)]);
        
        $sitemap1 = 'https://example.com/sitemap1.xml';
        $sitemap2 = 'https://example.com/sitemap2.xml';
        
        $comment1 = 'Generated by system';
        $comment2 = 'Last updated today';
        
        $entry1 = new RobotsTxtEntry([$rule1], [$sitemap1], [$comment1]);
        $entry2 = new RobotsTxtEntry([$rule2], [$sitemap2], [$comment2]);
        $merged = $entry1->merge($entry2);

        $this->assertEquals([$rule1, $rule2], $merged->rules);
        $this->assertEquals([$sitemap1, $sitemap2], $merged->sitemaps);
        $this->assertEquals([$comment1, $comment2], $merged->comments);
    }

    public function test_merge_withFirstEntryEmpty(): void
    {
        $rule = RobotsTxtRule::forAgent('TestBot', [RobotsTxtDirective::allow('/test/')]);
        $sitemap = 'https://example.com/sitemap.xml';
        $comment = 'Test comment';
        
        $entry1 = new RobotsTxtEntry();
        $entry2 = new RobotsTxtEntry([$rule], [$sitemap], [$comment]);
        $merged = $entry1->merge($entry2);

        $this->assertEquals([$rule], $merged->rules);
        $this->assertEquals([$sitemap], $merged->sitemaps);
        $this->assertEquals([$comment], $merged->comments);
    }

    public function test_merge_withSecondEntryEmpty(): void
    {
        $rule = RobotsTxtRule::forAgent('TestBot', [RobotsTxtDirective::allow('/test/')]);
        $sitemap = 'https://example.com/sitemap.xml';
        $comment = 'Test comment';
        
        $entry1 = new RobotsTxtEntry([$rule], [$sitemap], [$comment]);
        $entry2 = new RobotsTxtEntry();
        $merged = $entry1->merge($entry2);

        $this->assertEquals([$rule], $merged->rules);
        $this->assertEquals([$sitemap], $merged->sitemaps);
        $this->assertEquals([$comment], $merged->comments);
    }

    public function test_merge_withDuplicateItems(): void
    {
        $rule = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')]);
        $sitemap = 'https://example.com/sitemap.xml';
        $comment = 'Duplicate comment';
        
        $entry1 = new RobotsTxtEntry([$rule], [$sitemap], [$comment]);
        $entry2 = new RobotsTxtEntry([$rule], [$sitemap], [$comment]);
        $merged = $entry1->merge($entry2);

        // Should preserve duplicates as the merge is a simple concatenation
        $this->assertEquals([$rule, $rule], $merged->rules);
        $this->assertEquals([$sitemap, $sitemap], $merged->sitemaps);
        $this->assertEquals([$comment, $comment], $merged->comments);
    }

    public function test_merge_preservesOriginalEntries(): void
    {
        $rule1 = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')]);
        $rule2 = RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::allow('/api/')]);
        
        $entry1 = new RobotsTxtEntry([$rule1]);
        $entry2 = new RobotsTxtEntry([$rule2]);
        $merged = $entry1->merge($entry2);

        // Original entries should remain unchanged
        $this->assertEquals([$rule1], $entry1->rules);
        $this->assertEquals([$rule2], $entry2->rules);
        
        // Merged entry should have both rules
        $this->assertEquals([$rule1, $rule2], $merged->rules);
    }

    public function test_merge_withMultipleRulesAndComponents(): void
    {
        $rules1 = [
            RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')]),
            RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::crawlDelay(1)])
        ];
        $rules2 = [
            RobotsTxtRule::forAgent('Bingbot', [RobotsTxtDirective::allow('/api/')]),
            RobotsTxtRule::forAgent('DuckDuckBot', [RobotsTxtDirective::disallow('/private/')])
        ];
        
        $sitemaps1 = ['https://example.com/sitemap1.xml', 'https://example.com/sitemap2.xml'];
        $sitemaps2 = ['https://example.com/sitemap3.xml'];
        
        $comments1 = ['First entry comment', 'Generated at startup'];
        $comments2 = ['Second entry comment'];
        
        $entry1 = new RobotsTxtEntry($rules1, $sitemaps1, $comments1);
        $entry2 = new RobotsTxtEntry($rules2, $sitemaps2, $comments2);
        $merged = $entry1->merge($entry2);

        $expectedRules = [...$rules1, ...$rules2];
        $expectedSitemaps = [...$sitemaps1, ...$sitemaps2];
        $expectedComments = [...$comments1, ...$comments2];

        $this->assertEquals($expectedRules, $merged->rules);
        $this->assertEquals($expectedSitemaps, $merged->sitemaps);
        $this->assertEquals($expectedComments, $merged->comments);
        
        $this->assertCount(4, $merged->rules);
        $this->assertCount(3, $merged->sitemaps);
        $this->assertCount(3, $merged->comments);
    }

    public function test_merge_chainedMerging(): void
    {
        $entry1 = new RobotsTxtEntry(
            [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
            ['https://example.com/sitemap1.xml'],
            ['Entry 1']
        );
        
        $entry2 = new RobotsTxtEntry(
            [RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::allow('/api/')])],
            ['https://example.com/sitemap2.xml'],
            ['Entry 2']
        );
        
        $entry3 = new RobotsTxtEntry(
            [RobotsTxtRule::forAgent('Bingbot', [RobotsTxtDirective::crawlDelay(10)])],
            ['https://example.com/sitemap3.xml'],
            ['Entry 3']
        );
        
        $merged = $entry1->merge($entry2)->merge($entry3);
        
        $this->assertCount(3, $merged->rules);
        $this->assertCount(3, $merged->sitemaps);
        $this->assertCount(3, $merged->comments);
        
        $this->assertEquals('Entry 1', $merged->comments[0]);
        $this->assertEquals('Entry 2', $merged->comments[1]);
        $this->assertEquals('Entry 3', $merged->comments[2]);
    }
}
