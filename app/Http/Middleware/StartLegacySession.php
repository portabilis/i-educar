<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StartLegacySession
{
    /**
     * @return void
     */
    private function startLegacySession()
    {
        try {
            session_start();
        } catch (Throwable $throwable) {
            // Pega erro caso a sessão já tenha sido iniciada.
        }
    }

    /**
     * @return void
     */
    private function writeLegacySession()
    {
        try {
            session_write_close();
        } catch (Throwable $throwable) {
            // Pega erro caso a sessão já tenha sido iniciada.
        }
    }

    /**
     * @return void
     */
    private function mergeLegacySession()
    {
        session($_SESSION);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->startLegacySession();
        $this->mergeLegacySession();

        return $next($request);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->mergeLegacySession();
        $this->writeLegacySession();
    }
}
