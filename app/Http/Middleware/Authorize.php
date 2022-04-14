<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Middleware\Authorize as AuthorizeMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Authorize extends AuthorizeMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string  $ability
     * @param mixed   ...$models
     *
     * @throws AuthorizationException
     *
     * @return Response
     */
    public function handle($request, Closure $next, $ability, ...$models)
    {
        $arguments = $this->getGateArguments($request, $models);

        if (Str::contains($ability, ':')) {
            [$ability, $arguments] = explode(':', $ability);
        }

        try {
            $this->gate->authorize($ability, $arguments);
        } catch (AuthorizationException $a) {
            return back()->withErrors(['Error' => ['Você não tem permissão para acessar este recurso']]);
        }

        return $next($request);
    }
}
