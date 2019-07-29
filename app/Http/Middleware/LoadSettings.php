<?php

namespace App\Http\Middleware;

use Closure;
use App\Setting;
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

        return $next($request);
    }
}
