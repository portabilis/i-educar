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
        $turma_id = $request->query('ref_cod_turma');

        $collection = $this->responsavelTurmaRepository->list($request->allWithTranslatedKeys());
        if(!empty($turma_id)){
            $uniqueCollection = $collection->unique('id')->where('school_class_id', $turma_id)->where('ativo', 1)->where('ativo_turma', 1);
        }else{
            $uniqueCollection = $collection->unique('id')->where('ativo', 1)->where('ativo_turma', 1);
        }
        
        $export = new ResponsavelTurmaExport($uniqueCollection);

        return Excel::download($export, 'responsaveis_turma.xlsx');
    }
}
  