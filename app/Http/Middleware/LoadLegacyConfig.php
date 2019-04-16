<?php

namespace App\Http\Middleware;

use Closure;
use iEducar\Support\Config\LegacyConfig;
use Illuminate\Support\Facades\DB;

class LoadLegacyConfig
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $legacyConfigPath = base_path() . '/' . config('legacy.path') . '/configuration/';

        $configObject = new LegacyConfig($legacyConfigPath, config('legacy.env'), request()->getHttpHost());

        $laravelLegacyConfig = config()->get('legacy');

        $data = array_merge(
            $laravelLegacyConfig, $configObject->getArrayConfig(), $this->getConfig()
        );

        config()->set(['legacy' => $data]);

        return $next($request);
    }

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

        return ['config' => $config];
    }
}
