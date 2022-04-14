<?php

namespace App\Http\Middleware;

use App\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class LoadSettings
{
    /**
     * Return configurations for institution.
     *
     * @return array
     */
    private function getConfig()
    {
        $config = (array) DB::table('pmieducar.configuracoes_gerais as cg')
            ->select('cg.*')
            ->join('pmieducar.instituicao as i', 'cod_instituicao', '=', 'ref_cod_instituicao')
            ->where('i.ativo', 1)
            ->first();

        return ['legacy.config' => $config];
    }

    /**
     * Return database configuration.
     *
     * @return array
     */
    private function getDatabaseConfig()
    {
        $config = DB::connection()->getConfig();

        return [
            'legacy.app.database.hostname' => $config['host'],
            'legacy.app.database.port' => $config['port'],
            'legacy.app.database.dbname' => $config['database'],
            'legacy.app.database.username' => $config['username'],
            'legacy.app.database.password' => $config['password'],
        ];
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
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        Config::set($settings);
        Config::set($this->getConfig());
        Config::set($this->getDatabaseConfig());

        return $next($request);
    }
}
