<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConnectTenantDatabase
{
    /**
     * @var Closure
     */
    private static $resolver;

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
        $connections = config('database.connections');

        $tenant = $this->getTenant($request);

        if (isset($connections[$tenant])) {
            DB::setDefaultConnection($tenant);
        }

        return $next($request);
    }

    /**
     * Return tenant default connection name.
     *
     * @param Request $request
     *
     * @return string
     */
    public function getTenant(Request $request)
    {
        $resolver = self::$resolver;

        if (empty($resolver)) {
            $resolver = $this->getDefaultTenantResolver();
        }

        return $resolver($request);
    }

    /**
     * Return default tenant resolver.
     *
     * @return Closure
     */
    public function getDefaultTenantResolver()
    {
        return function (Request $request) {
            $host = str_replace('-', '', $request->getHost());

            return Str::replaceFirst('.' . config('app.default_host'), '', $host);
        };
    }

    /**
     * Set default tenant resolver.
     *
     * @param Closure $resolver
     *
     * @return bool
     */
    public static function setTenantResolver(Closure $resolver)
    {
        static::$resolver = $resolver;

        return true;
    }
}
