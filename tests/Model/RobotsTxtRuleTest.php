<?php

namespace Tourze\RobotsTxtBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;

class RobotsTxtRuleTest extends TestCase
{
    public function test_constructor_withBasicParameters(): void
    {
        $directives = [RobotsTxtDirective::disallow('/admin/')];
        $rule = new RobotsTxtRule('Googlebot', $directives, 100);

        $this->assertEquals('Googlebot', $rule->userAgent);
        $this->assertEquals($directives, $rule->directives);
        $this->assertEquals(100, $rule->priority);
    }

    public function test_constructor_withDefaultValues(): void
    {
        $rule = new RobotsTxtRule('*');

        $this->assertEquals('*', $rule->userAgent);
        $this->assertEquals([], $rule->directives);
        $this->assertEquals(0, $rule->priority);
    }

    public function test_forAllAgents_withDirectives(): void
    {
        $directives = [
            RobotsTxtDirective::disallow('/admin/'),
            RobotsTxtDirective::disallow('/private/')
        ];
        $rule = RobotsTxtRule::forAllAgents($directives, 50);

        $this->assertEquals('*', $rule->userAgent);
        $this->assertEquals($directives, $rule->directives);
        $this->assertEquals(50, $rule->priority);
    }

    public function test_forAllAgents_withDefaultPriority(): void
    {
        $directives = [RobotsTxtDirective::allow('/public/')];
        $rule = RobotsTxtRule::forAllAgents($directives);

        $this->assertEquals('*', $rule->userAgent);
        $this->assertEquals($directives, $rule->directives);
        $this->assertEquals(0, $rule->priority);
    }

    public function test_forAgent_withSpecificAgent(): void
    {
        $directives = [RobotsTxtDirective::crawlDelay(10)];
        $rule = RobotsTxtRule::forAgent('Bingbot', $directives, 75);

        $this->assertEquals('Bingbot', $rule->userAgent);
        $this->assertEquals($directives, $rule->directives);
        $this->assertEquals(75, $rule->priority);
    }

    public function test_withDirective_addsSingleDirective(): void
    {
        $rule = new RobotsTxtRule('*');
        $directive = RobotsTxtDirective::disallow('/test/');
        $newRule = $rule->withDirective($directive);

        $this->assertEquals([], $rule->directives);
        $this->assertEquals([$directive], $newRule->directives);
        $this->assertEquals('*', $newRule->userAgent);
        $this->assertEquals(0, $newRule->priority);
    }

    public function test_withDirectives_addsMultipleDirectives(): void
    {
        $initialDirectives = [RobotsTxtDirective::allow('/api/')];
        $rule = new RobotsTxtRule('*', $initialDirectives);
        
        $newDirectives = [
            RobotsTxtDirective::disallow('/admin/'),
            RobotsTxtDirective::crawlDelay(5)
        ];
        $newRule = $rule->withDirectives($newDirectives);

        $expectedDirectives = [...$initialDirectives, ...$newDirectives];
        $this->assertEquals($expectedDirectives, $newRule->directives);
        $this->assertEquals('*', $newRule->userAgent);
    }

    public function test_toString_withDirectives(): void
    {
        $directives = [
            RobotsTxtDirective::disallow('/admin/'),
            RobotsTxtDirective::allow('/api/'),
            RobotsTxtDirective::crawlDelay(10)
        ];
        $rule = new RobotsTxtRule('Googlebot', $directives);

        $expected = "User-agent: Googlebot\n" .
                   "Disallow: /admin/\n" .
                   "Allow: /api/\n" .
                   "Crawl-delay: 10";

        $this->assertEquals($expected, $rule->toString());
        $this->assertEquals($expected, (string) $rule);
    }

    public function test_toString_withEmptyDirectives(): void
    {
        $rule = new RobotsTxtRule('*');

        $this->assertEquals('', $rule->toString());
        $this->assertEquals('', (string) $rule);
    }

    public function test_toString_withAllAgents(): void
    {
        $directives = [RobotsTxtDirective::disallow('/private/')];
        $rule = RobotsTxtRule::forAllAgents($directives);

        $expected = "User-agent: *\n" .
                   "Disallow: /private/";

        $this->assertEquals($expected, $rule->toString());
    }

    public function test_withDirective_maintainsImmutability(): void
    {
        $originalRule = new RobotsTxtRule('TestBot', [RobotsTxtDirective::allow('/test/')], 25);
        $newDirective = RobotsTxtDirective::disallow('/new/');
        $newRule = $originalRule->withDirective($newDirective);

        // Original rule should remain unchanged
        $this->assertCount(1, $originalRule->directives);
        $this->assertEquals('TestBot', $originalRule->userAgent);
        $this->assertEquals(25, $originalRule->priority);

        // New rule should have additional directive
        $this->assertCount(2, $newRule->directives);
        $this->assertEquals('TestBot', $newRule->userAgent);
        $this->assertEquals(25, $newRule->priority);
        $this->assertEquals($newDirective, $newRule->directives[1]);
    }

    public function test_withDirectives_maintainsImmutability(): void
    {
        $originalDirectives = [RobotsTxtDirective::allow('/original/')];
        $originalRule = new RobotsTxtRule('Bot', $originalDirectives, 10);
        
        $additionalDirectives = [
            RobotsTxtDirective::disallow('/new1/'),
            RobotsTxtDirective::disallow('/new2/')
        ];
        $newRule = $originalRule->withDirectives($additionalDirectives);

        // Original rule should remain unchanged
        $this->assertEquals($originalDirectives, $originalRule->directives);
        $this->assertCount(1, $originalRule->directives);

        // New rule should have all directives
        $this->assertCount(3, $newRule->directives);
        $this->assertEquals($originalDirectives[0], $newRule->directives[0]);
        $this->assertEquals($additionalDirectives[0], $newRule->directives[1]);
        $this->assertEquals($additionalDirectives[1], $newRule->directives[2]);
    }
} 