<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()', false);
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none', false);
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin', false);
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin', false);
        $viteOrigin = '';
        $viteWs = '';
        if (app()->isLocal() && file_exists(public_path('hot'))) {
            $hotUrl = trim((string) @file_get_contents(public_path('hot')));
            $parts = parse_url($hotUrl);
            if (! empty($parts['scheme']) && ! empty($parts['host'])) {
                $port = isset($parts['port']) ? ':' . $parts['port'] : '';
                $viteOrigin = ' ' . $parts['scheme'] . '://' . $parts['host'] . $port;
                $wsScheme = $parts['scheme'] === 'https' ? 'wss' : 'ws';
                $viteWs = ' ' . $wsScheme . '://' . $parts['host'] . $port;
            }
        }

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; img-src 'self' data: blob: https:; font-src 'self' data:{$viteOrigin}; style-src 'self' 'unsafe-inline'{$viteOrigin}; script-src 'self' 'unsafe-inline' 'unsafe-eval'{$viteOrigin}; connect-src 'self' https:{$viteOrigin}{$viteWs}; frame-ancestors 'self'; base-uri 'self'; form-action 'self'",
            false
        );
        $response->headers->remove('X-Powered-By');

        if (app()->isProduction() && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains', false);
        }

        return $response;
    }
}
