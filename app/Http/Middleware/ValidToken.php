<?php

namespace App\Http\Middleware;

use App\Models\LegacyInstitution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidToken
{
    public function __construct(
        public LegacyInstitution $institution,
    ) {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configs = $this->institution?->generalConfiguration;

        if ($request->headers->get('token') !== trim($configs->token_novo_educacao)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
