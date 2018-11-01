<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SetLayoutVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->setLoggedUser();
        $this->setLegacyConfig();

        return $next($request);
    }

    private function setLoggedUser()
    {
        $personId = session('id_pessoa');
        $person = DB::selectOne('SELECT nome, email FROM cadastro.pessoa WHERE idpes = :personId', ['personId' => $personId]);

        $loggedUser = new \stdClass();
        $loggedUser->personId = $personId;
        $loggedUser->name = $person->nome;
        $loggedUser->email = $person->email;

        View::share('loggedUser', $loggedUser);
    }

    private function setLegacyConfig()
    {
        View::share('config', config('legacy'));
    }
}
