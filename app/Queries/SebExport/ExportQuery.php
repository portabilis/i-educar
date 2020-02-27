<?php

namespace App\Queries\SebExport;

use App\Models\LegacyRegistration;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Builder;

class ExportQuery
{
    private $query;

    public function query(array $filters) : Builder
    {
        $this->query = LegacyRegistration::query();
        $this->select();
        $this->join();
        $this->filter($filters);

        return $this->query;
    }

    private function select() : void
    {
        $this->query->select(
            [
                'matricula.ano',
                'educacenso_cod_escola.cod_escola_inep as inep_escola',
                'fisica.cpf',
                'fisica.nome_social',
                'matricula.cod_matricula',
                'turma.etapa_educacenso',
                'aluno.emancipado',
                'fisica_mae.cpf as cpf_mae',
                'fisica_pai.cpf as cpf_pai',
                'fisica_responsavel.cpf as cpf_responsavel',
            ]
        );
    }

    private function join() : void
    {
        $this->query->join('pmieducar.aluno', 'aluno.cod_aluno', 'matricula.ref_cod_aluno');
        $this->query->join('cadastro.fisica', 'fisica.idpes', 'aluno.ref_idpes');
        $this->query->join('pmieducar.escola', 'escola.cod_escola', 'matricula.ref_ref_cod_escola');
        $this->query->join('modules.educacenso_cod_escola', 'educacenso_cod_escola.cod_escola', 'escola.cod_escola');
        $this->query->join(
            'pmieducar.matricula_turma',
            function ($query) {
                $query->whereColumn('matricula_turma.ref_cod_matricula', 'matricula.cod_matricula');
                $query->where('matricula_turma.ativo', 1);
            }
        );
        $this->query->join('pmieducar.turma', 'turma.cod_turma', 'matricula_turma.ref_cod_turma');
        $this->query->leftJoin('cadastro.fisica as fisica_mae', 'fisica_mae.idpes', 'fisica.idpes_mae');
        $this->query->leftJoin('cadastro.fisica as fisica_pai', 'fisica_pai.idpes', 'fisica.idpes_pai');
        $this->query->leftJoin('cadastro.fisica as fisica_responsavel', 'fisica_responsavel.idpes', 'fisica.idpes_responsavel');
    }

    private function filter(array $filters) : void
    {
        $this->query->where('escola.ref_cod_instituicao', $filters['ref_cod_instituicao']);
        $this->query->where('matricula.ano', $filters['ano']);
        $this->query->whereNotNull('turma.etapa_educacenso');
        $this->query->where('matricula.aprovado', App_Model_MatriculaSituacao::EM_ANDAMENTO);

        if (!empty($filters['ref_cod_escola'])) {
            $this->query->where('escola.cod_escola', $filters['ref_cod_escola']);
        }
    }
}
