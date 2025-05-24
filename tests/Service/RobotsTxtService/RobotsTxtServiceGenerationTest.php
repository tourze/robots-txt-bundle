<?php

namespace Tourze\RobotsTxtBundle\Tests\Service\RobotsTxtService;

use PHPUnit\Framework\TestCase;
use Tourze\RobotsTxtBundle\Model\RobotsTxtDirective;
use Tourze\RobotsTxtBundle\Model\RobotsTxtEntry;
use Tourze\RobotsTxtBundle\Model\RobotsTxtRule;
use Tourze\RobotsTxtBundle\Provider\RobotsTxtProviderInterface;
use Tourze\RobotsTxtBundle\Service\RobotsTxtService;

class RobotsTxtServiceGenerationTest extends TestCase
{
    public function test_generate_withNoProviders(): void
    {
        $service = new RobotsTxtService([]);
        $result = $service->generate();
        
        $this->assertEquals('', $result);
    }

    public function test_generate_withSingleProvider(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                ['https://example.com/sitemap.xml'],
                ['Generated robots.txt']
            ),
            100,
            true
        );
        
        $service = new RobotsTxtService([$provider]);
        $result = $service->generate();
        
        $expected = "# Generated robots.txt\n" .
                   "\n" .
                   "User-agent: *\n" .
                   "Disallow: /admin/\n" .
                   "\n" .
                   "Sitemap: https://example.com/sitemap.xml";
        
        $this->assertEquals($expected, $result);
    }

    public function test_generate_withMultipleProviders(): void
    {
        $provider1 = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                ['Provider 1']
            ),
            50,
            true
        );
        
        $provider2 = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAgent('Googlebot', [RobotsTxtDirective::allow('/api/')])],
                ['https://example.com/sitemap.xml'],
                ['Provider 2']
            ),
            100,
            true
        );
        
        $service = new RobotsTxtService([$provider1, $provider2]);
        $result = $service->generate();
        
        $expected = "# Provider 2\n" .
                   "# Provider 1\n" .
                   "\n" .
                   "User-agent: Googlebot\n" .
                   "Allow: /api/\n" .
                   "\n" .
                   "User-agent: *\n" .
                   "Disallow: /admin/\n" .
                   "\n" .
                   "Sitemap: https://example.com/sitemap.xml";
        
        $this->assertEquals($expected, $result);
    }

    public function test_isEmpty_withNoProviders(): void
    {
        $service = new RobotsTxtService([]);
        
        $this->assertTrue($service->isEmpty());
    }

    public function test_isEmpty_withEmptyProvider(): void
    {
        $provider = $this->createMockProvider(new RobotsTxtEntry(), 50, true);
        $service = new RobotsTxtService([$provider]);
        
        $this->assertTrue($service->isEmpty());
    }

    public function test_isEmpty_withContentfulProvider(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                []
            ),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);
        
        $this->assertFalse($service->isEmpty());
    }

    public function test_isEmpty_withCommentsOnly(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['Just a comment']),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);
        
        $this->assertFalse($service->isEmpty());
    }

    public function test_isEmpty_withSitemapsOnly(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry([], ['https://example.com/sitemap.xml'], []),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);
        
        $this->assertFalse($service->isEmpty());
    }

    public function test_isEmpty_withUnsupportedProviders(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [RobotsTxtRule::forAllAgents([RobotsTxtDirective::disallow('/admin/')])],
                [],
                []
            ),
            50,
            false // Provider doesn't support current environment
        );
        $service = new RobotsTxtService([$provider]);
        
        $this->assertTrue($service->isEmpty());
    }

    public function test_generate_withComplexRules(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry(
                [
                    RobotsTxtRule::forAllAgents([
                        RobotsTxtDirective::disallow('/admin/'),
                        RobotsTxtDirective::disallow('/private/')
                    ], 10),
                    RobotsTxtRule::forAgent('Googlebot', [
                        RobotsTxtDirective::allow('/api/'),
                        RobotsTxtDirective::crawlDelay(5)
                    ], 100),
                    RobotsTxtRule::forAgent('Bingbot', [
                        RobotsTxtDirective::disallow('/test/')
                    ], 50)
                ],
                [
                    'https://example.com/sitemap.xml',
                    'https://example.com/news-sitemap.xml'
                ],
                ['Complex robots.txt', 'Generated by test']
            ),
            50,
            true
        );
        
        $service = new RobotsTxtService([$provider]);
        $result = $service->generate();
        
        $this->assertStringContainsString('# Complex robots.txt', $result);
        $this->assertStringContainsString('# Generated by test', $result);
        $this->assertStringContainsString('User-agent: Googlebot', $result);
        $this->assertStringContainsString('User-agent: Bingbot', $result);
        $this->assertStringContainsString('User-agent: *', $result);
        $this->assertStringContainsString('Allow: /api/', $result);
        $this->assertStringContainsString('Crawl-delay: 5', $result);
        $this->assertStringContainsString('Disallow: /admin/', $result);
        $this->assertStringContainsString('Sitemap: https://example.com/sitemap.xml', $result);
        $this->assertStringContainsString('Sitemap: https://example.com/news-sitemap.xml', $result);
    }

    public function test_generate_withWhitespaceOnlyContent(): void
    {
        $provider = $this->createMockProvider(
            new RobotsTxtEntry([], [], ['   ', "\t", "\n"]),
            50,
            true
        );
        $service = new RobotsTxtService([$provider]);
        
        // Whitespace-only comments should still make isEmpty return false
        $this->assertFalse($service->isEmpty());
        $result = $service->generate();
        $this->assertNotEmpty($result);
    }

    public function test_generate_callsProvidersMethodsCorrectly(): void
    {
        $provider = $this->createMock(RobotsTxtProviderInterface::class);
        $provider->expects($this->once())->method('supports')->willReturn(true);
        $provider->expects($this->atLeastOnce())->method('getPriority')->willReturn(50);
        $provider->expects($this->once())->method('provide')->willReturn(new RobotsTxtEntry());
        
        $service = new RobotsTxtService([$provider]);
        $service->generate();
    }

    public function test_isEmpty_callsProvidersMethodsCorrectly(): void
    {
        $provider = $this->createMock(RobotsTxtProviderInterface::class);
        $provider->expects($this->once())->method('supports')->willReturn(true);
        $provider->expects($this->atLeastOnce())->method('getPriority')->willReturn(50);
        $provider->expects($this->once())->method('provide')->willReturn(new RobotsTxtEntry());
        
        $service = new RobotsTxtService([$provider]);
        $service->isEmpty();
    }

    public function test_generate_withLargeNumberOfProviders(): void
    {
        $providers = [];
        for ($i = 1; $i <= 10; $i++) {
            $providers[] = $this->createMockProvider(
                new RobotsTxtEntry([], [], ["Provider $i"]),
                $i * 10,
                true
            );
        }
        
        $service = new RobotsTxtService($providers);
        $result = $service->generate();
        
        // Should contain all comments in priority order (highest first)
        for ($i = 10; $i >= 1; $i--) {
            $this->assertStringContainsString("Provider $i", $result);
        }
    }

    private function createMockProvider(
        RobotsTxtEntry $entry,
        int $priority,
        bool $supports
    ): RobotsTxtProviderInterface {
        $provider = $this->createMock(RobotsTxtProviderInterface::class);
        $provider->method('provide')->willReturn($entry);
        $provider->method('getPriority')->willReturn($priority);
        $provider->method('supports')->willReturn($supports);
        
        return $provider;
    }
} 