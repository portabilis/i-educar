<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventIframe
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Impede carregamento em iframes de forma clássica
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Alternativa moderna e mais flexível (usada por navegadores modernos)
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

        return $response;
    }
}
