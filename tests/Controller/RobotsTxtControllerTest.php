<?php

namespace Tourze\RobotsTxtBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use Tourze\RobotsTxtBundle\Controller\RobotsTxtController;

/**
 * @internal
 */
#[CoversClass(RobotsTxtController::class)]
#[RunTestsInSeparateProcesses]
final class RobotsTxtControllerTest extends AbstractWebTestCase
{
    public function testGetRobotsTxtWithContent(): void
    {
        $client = self::createClient();
        $client->request('GET', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertEquals(3600, $response->getMaxAge());
    }

    public function testGetRobotsTxtUnauthenticated(): void
    {
        $client = self::createClient();
        $client->request('GET', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPostMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPutMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testDeleteMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPatchMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PATCH', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testHeadMethod(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertEquals('', $response->getContent());
    }

    public function testOptionsMethod(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/robots.txt');

        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [200, 405]);
    }

    public function testResponseCacheHeaders(): void
    {
        $client = self::createClient();
        $client->request('GET', '/robots.txt');

        $response = $client->getResponse();
        $this->assertTrue($response->headers->hasCacheControlDirective('public'));
        $this->assertEquals(3600, $response->getMaxAge());
        $this->assertStringContainsString('s-maxage=3600', $response->headers->get('Cache-Control') ?? '');
    }

    public function testResponseContentType(): void
    {
        $client = self::createClient();
        $client->request('GET', '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/robots.txt');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }
}
