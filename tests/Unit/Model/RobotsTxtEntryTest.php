<?php

namespace Tourze\RobotsTxtBundle\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;

class RobotsTxtEntryTest extends TestCase
{
    public function test_constructor_withDefaultValues(): void
    {
        $entry = new RobotsTxtEntry();

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([], $entry->sitemaps);
        $this->assertEquals([], $entry->comments);
    }

    public function test_withRule_addsSingleRule(): void
    {
        $entry = new RobotsTxtEntry();
        $rule = RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/private/')]);
        $newEntry = $entry->withRule($rule);

        $this->assertEquals([], $entry->rules);
        $this->assertEquals([$rule], $newEntry->rules);
    }

    public function test_toString_withEmptyEntry(): void
    {
        $entry = new RobotsTxtEntry();
        $result = $entry->toString();
        
        $this->assertEquals('', $result);
    }

    public function test_merge_withEmptyEntries(): void
    {
        $entry1 = new RobotsTxtEntry();
        $entry2 = new RobotsTxtEntry();
        $merged = $entry1->merge($entry2);

        $this->assertEquals([], $merged->rules);
        $this->assertEquals([], $merged->sitemaps);
        $this->assertEquals([], $merged->comments);
    }
}