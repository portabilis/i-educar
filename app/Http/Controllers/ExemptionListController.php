<?php

namespace App\Http\Controllers;

class ExemptionListController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->breadcrumb('Consulta de dispensas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function index()
    {

    }
}
