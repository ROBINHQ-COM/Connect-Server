<?php


namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Robin\Api\Logger\RobinLogger;
use Symfony\Component\HttpFoundation\HeaderBag;

class WebhookMiddleware
{
    /**
     * @var RobinLogger
     */
    private $logger;

    /**
     * @param RobinLogger $logger
     */
    public function __construct(RobinLogger $logger)
    {

        $this->logger = $logger;
    }

    public function handle(Request $request, \Closure $next)
    {
        if ($request->getMethod() === "POST") {
            if ($this->validSignature($request) || $this->validShopId($request)) {
                return $next($request);
            }
        }

        $this->logger->hooksError($request->getContent(), $request);
        return new Response("", Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function validSignature(Request $request)
    {
        $signature = $request->headers->get("X-Signature");
        $md5 = md5($request->getContent() . env("SEOSHOP_API_SECRET"));

        return $signature !== null && $signature === $md5;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function validShopId(Request $request)
    {
        return $request->headers->get('X-Shop-Id') === env("SEOSHOP_SHOP_ID");
    }
}