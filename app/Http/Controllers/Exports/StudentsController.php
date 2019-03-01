<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function export(Request $request)
    {
        $params = $request->all();
        $params = $this->translateParams($params);

        $query = Student::select();

        if ($params['cod_aluno'] ?? null) {
            $query->where('id', $params['cod_aluno']);
        }

        if ($params['aluno_estado_id'] ?? null) {
            $query->where('registry_code', $params['aluno_estado_id']);
        }

        $students = $query->get()->all();

        //...
    }

    protected function translateParams(array $params): array
    {
        $newParams = [];

        foreach ($params as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            switch ($k) {
                case 'cod_aluno':
                case 'cod_inep':
                case 'ref_cod_escola':
                case 'ref_cod_curso':
                case 'ref_cod_serie':
                    $newParams[$k] = (int) $v;

                    break;
                case 'aluno_estado_id':
                    if (preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{1}$/', $v) === 1) {
                        $newParams[$k] = $v;
                    }

                    break;
                case 'nome_aluno':
                case 'nome_pai':
                case 'nome_mae':
                case 'nome_responsavel':
                    $newParams[$k] = trim($v);

                    break;
                case 'data_nascimento':
                    if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $v) === 1) {
                        $newParams[$k] = $v;
                    }

                    break;
                case 'ano':
                    if (preg_match('/^[0-9]{4}$/', $v) === 1) {
                        $newParams[$k] = (int) $v;
                    }

                    break;
            }
        }

        return $newParams;
    }
}
