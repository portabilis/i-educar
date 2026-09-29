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
        $expected = trim((string) $this->institution?->generalConfiguration?->token_novo_educacao);
        $received = $request->headers->get('token');

        if ($expected === '' || !is_string($received) || !hash_equals($expected, $received)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
