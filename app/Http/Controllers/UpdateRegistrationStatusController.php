<?php

namespace App\Http\Controllers;

use App\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpdateRegistrationStatusController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Atualização da situação de matrículas em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::UPDATE_REGISTRATION_STATUS);

        return view('registration.update-registration-status.index');
    }
}
