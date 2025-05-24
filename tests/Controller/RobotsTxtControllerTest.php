<?php

namespace Tourze\RobotsTxtBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Tourze\RobotsTxtBundle\Controller\RobotsTxtController;
use Tourze\RobotsTxtBundle\Service\RobotsTxtService;

class RobotsTxtControllerTest extends TestCase
{
    public function test_invoke_withContent(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn("User-agent: *\nDisallow: /admin/");
        $service->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("User-agent: *\nDisallow: /admin/", $response->getContent());
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function test_invoke_withEmptyContent(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn('');
        $service->method('isEmpty')->willReturn(true);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('', $response->getContent());
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function test_invoke_withWhitespaceOnlyContent(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn("   \n\t  ");
        $service->method('isEmpty')->willReturn(true); // Service should handle whitespace trimming
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_invoke_setsCorrectCacheHeaders(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn("User-agent: *\nDisallow:");
        $service->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertEquals(3600, $response->getMaxAge());
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertStringContainsString('s-maxage=3600', $response->headers->get('Cache-Control'));
    }

    public function test_invoke_setsCorrectContentType(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn("User-agent: *");
        $service->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function test_invoke_callsServiceMethods(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->expects($this->once())->method('generate')->willReturn('test content');
        $service->expects($this->once())->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        $controller();
    }

    public function test_invoke_withLargeContent(): void
    {
        $largeContentParts = [];
        for ($i = 1; $i <= 100; $i++) {
            $largeContentParts[] = "User-agent: Bot{$i}\nDisallow: /path{$i}/";
        }
        $largeContent = implode("\n", $largeContentParts);
        
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn($largeContent);
        $service->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($largeContent, $response->getContent());
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function test_invoke_withSpecialCharacters(): void
    {
        $content = "# Generated at 2024-01-01 @ 12:00:00\n" .
                  "User-agent: *\n" .
                  "Disallow: /admin/*?query=test&param=value\n" .
                  "Sitemap: https://example.com/sitemap.xml#section";
        
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn($content);
        $service->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
    }

    public function test_invoke_multipleCallsReturnConsistentResponses(): void
    {
        $content = "User-agent: *\nDisallow: /test/";
        
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn($content);
        $service->method('isEmpty')->willReturn(false);
        
        $controller = new RobotsTxtController($service);
        
        $response1 = $controller();
        $response2 = $controller();
        
        $this->assertEquals($response1->getStatusCode(), $response2->getStatusCode());
        $this->assertEquals($response1->getContent(), $response2->getContent());
        $this->assertEquals($response1->headers->get('Content-Type'), $response2->headers->get('Content-Type'));
        $this->assertEquals($response1->getMaxAge(), $response2->getMaxAge());
    }

    public function test_invoke_cacheHeadersSetEvenForEmptyContent(): void
    {
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn('');
        $service->method('isEmpty')->willReturn(true);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        // Cache headers should be set even for 404 responses
        $this->assertEquals(3600, $response->getMaxAge());
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertStringContainsString('s-maxage=3600', $response->headers->get('Cache-Control'));
    }

    public function test_invoke_respectsServiceEmptyCheck(): void
    {
        // Test case where content exists but service says it's empty
        $service = $this->createMock(RobotsTxtService::class);
        $service->method('generate')->willReturn('# Only comments');
        $service->method('isEmpty')->willReturn(true);
        
        $controller = new RobotsTxtController($service);
        $response = $controller();
        
        // Should return 404 even though there's content, because service says it's empty
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('# Only comments', $response->getContent());
    }
} 