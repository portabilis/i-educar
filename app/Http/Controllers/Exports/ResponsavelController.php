<?php

namespace App\Http\Controllers\Exports;

use App\Exports\ResponsavelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResponsavelExport as Request;
use iEducar\Support\Repositories\ResponsavelRepository;
use Maatwebsite\Excel\Facades\Excel;
 
class ResponsavelController extends Controller
{
    protected $responsavelRepository;

    public function __construct(ResponsavelRepository $responsavelRepository)
    {
        $this->responsavelRepository = $responsavelRepository;
    }

    public function export(Request $request)
    {
        $collection = $this->responsavelRepository->list($request->allWithTranslatedKeys());
        $export = new ResponsavelExport($collection);

        return Excel::download($export, 'responsavel.xlsx');
    }
}
 