<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Return configurations for institution.
     *
     * TODO
     * Move this logic to middleware.
     *
     * @return object
     */
    private function getConfig()
    {
        return DB::table('pmieducar.configuracoes_gerais as cg')
            ->select('cg.*')
            ->join('pmieducar.instituicao as i', 'cod_instituicao', '=', 'ref_cod_instituicao')
            ->where('i.ativo', 1)
            ->first();
    }

    /**
     * @inheritdoc
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email', [
            'config' => $this->getConfig(),
        ]);
    }

}
