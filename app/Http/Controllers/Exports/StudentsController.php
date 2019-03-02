<?php

namespace App\Http\Controllers\Exports;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentsExport as Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentsController extends Controller
{
    public function export(Request $request)
    {
        $params = $this->translateParams($request->all());
        $export = new StudentsExport($params);

        return Excel::download($export, 'alunos.xlsx');
    }

    protected function translateParams($params)
    {
        $paramMap = [
            'cod_aluno' => 'id',
            'cod_inep' => 'inep_code',
            'aluno_estado_id' => 'registry_code',
            'nome_aluno' => 'student_name',
            'data_nascimento' => 'birthdate',
            'nome_pai' => 'father_name',
            'nome_mae' => 'mother_name',
            'nome_responsavel' => 'guardian_name',
            'ano' => 'year',
            'ref_cod_escola' => 'school_id',
            'ref_cod_curso' => 'course_id',
            'ref_cod_serie' => 'level_id',
        ];

        $newParams = [];

        foreach ($params as $k => $v) {
            if (!in_array($k, array_keys($paramMap))) {
                continue;
            }

            $newParams[$paramMap[$k]] = $v;
        }

        return $newParams;
    }
}
