<?php


namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookMiddleware
{

    public function handle(Request $request, \Closure $next)
    {
        if ($request->getMethod() === "POST") {
            $payload = $request->getContent();

            $headers = $request->headers;
            $signature = $headers->get("X-Signature");
            Log::info(json_encode($payload));
            if ($signature === null || $signature != md5($payload . env("SEOSHOP_API_SECRET"))) {
                Log::info("Request X-Signature does not match payload API SECRET hash");
                return new Response("", Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}