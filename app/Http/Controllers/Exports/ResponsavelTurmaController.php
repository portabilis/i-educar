<?php

namespace App\Http\Controllers\Exports;

use App\Exports\ResponsavelTurmaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResponsavelTurmaExport as Request;
use iEducar\Support\Repositories\ResponsavelTurmaRepository;
use Maatwebsite\Excel\Facades\Excel;
 
class ResponsavelTurmaController extends Controller
{
    protected $responsavelTurmaRepository;

    public function __construct(ResponsavelTurmaRepository $responsavelTurmaRepository)
    {
        $this->responsavelTurmaRepository = $responsavelTurmaRepository;
    }

    public function export(Request $request)
    {
        $collection = $this->responsavelTurmaRepository->list($request->allWithTranslatedKeys());
        $export = new ResponsavelTurmaExport($collection);

        return Excel::download($export, 'responsaveis_turma.xlsx');
    }
}
 