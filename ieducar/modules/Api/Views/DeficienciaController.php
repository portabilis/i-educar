<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class DeficienciaController extends ApiCoreController
{
    protected function searchOptions()
    {
        return ['namespace' => 'cadastro', 'labelAttr' => 'nm_deficiencia', 'idAttr' => 'cod_deficiencia'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getDeficiencias()
    {
        $modified = $this->getRequest()->modified ?: '';
        $schools = $this->getRequest()->escola ?? [];

        if (is_string($schools)) {
            $schools = explode(',', $schools);
        }

        $query = DB::table('cadastro.deficiencia')->selectRaw(
            'cod_deficiencia as id, nm_deficiencia as nome, desconsidera_regra_diferenciada, updated_at, null as deleted_at'
        )->when($modified, function ($query) use ($modified) {
            $query->where('updated_at', '>=', $modified);
        });

        $queryExcluded = DB::table('cadastro.deficiencia_excluidos')->selectRaw(
            'cod_deficiencia as id, nm_deficiencia as nome, desconsidera_regra_diferenciada, updated_at, deleted_at'
        )->when($modified, function ($query) use ($modified) {
            $query->where('updated_at', '>=', $modified);
        });

        $deficiencias = $query->unionAll($queryExcluded)->orderBy('id')->get()->map(function ($deficiencia) use ($schools) {

            $deficiencia = (array) $deficiencia;

            $alunos = DB::table('cadastro.fisica_deficiencia')
                ->join('pmieducar.aluno', 'fisica_deficiencia.ref_idpes', '=', 'aluno.ref_idpes')
                ->join('pmieducar.matricula', 'matricula.ref_cod_aluno', '=', 'aluno.cod_aluno')
                ->where('ref_cod_deficiencia', $deficiencia['id'])
                ->whereIn('matricula.ref_ref_cod_escola', $schools)
                ->orderBy('aluno.ref_idpes')
                ->pluck('aluno.cod_aluno')
                ->toArray();

            $deficiencia['alunos'] = $alunos;

            return $deficiencia;
        });

        return [
            'deficiencias' => $deficiencias
        ];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'deficiencia-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'deficiencias')) {
            $this->appendResponse($this->getDeficiencias());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
