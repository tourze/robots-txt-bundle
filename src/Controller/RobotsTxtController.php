<?php

namespace Tourze\RobotsTxtBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\RobotsTxtBundle\Service\RobotsTxtService;

/**
 * robots.txt控制器
 */
class RobotsTxtController
{
    public function __construct(private readonly RobotsTxtService $robotsTxtService)
    {
    }

    /**
     * 返回robots.txt内容
     */
    #[Route(path: '/robots.txt', name: 'robots_txt', methods: ['GET'])]
    public function __invoke(): Response
    {
        $content = $this->robotsTxtService->generate();

        $response = new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);

        // 如果内容为空，返回404
        if ($this->robotsTxtService->isEmpty()) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        // 设置缓存策略
        $response->setMaxAge(3600); // 1小时缓存
        $response->setSharedMaxAge(3600);
        $response->setPublic();

        return $response;
    }
}
