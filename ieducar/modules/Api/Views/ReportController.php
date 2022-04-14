<?php

use iEducar\Reports\Contracts\TeacherReportCard;

class ReportController extends ApiCoreController
{

    // validations
    protected function canGetBoletim()
    {
        return (
            $this->validatesId('escola') &&
            $this->validatesId('matricula')
        );
    }

    protected function canGetBoletimProfessor()
    {
        return (
            $this->validatesId('instituicao') &&
            $this->validatesPresenceOf('ano') &&
            $this->validatesId('escola') &&
            $this->validatesId('serie') &&
            $this->validatesId('turma') &&
            $this->validatesPresenceOf('componente_curricular_id')
        );
    }

    // load
    protected function loadDadosForMatricula($matriculaId)
    {
        $sql = '
            SELECT cod_matricula AS id,
                   ref_cod_aluno AS aluno_id,
                   matricula.ano,
                   escola.ref_cod_instituicao AS instituicao_id,
                   matricula.ref_ref_cod_escola AS escola_id,
                   matricula.ref_cod_curso AS curso_id,
                   matricula.ref_ref_cod_serie AS serie_id,
                   matricula_turma.ref_cod_turma AS turma_id
              FROM pmieducar.matricula_turma,
                   pmieducar.matricula,
                   pmieducar.escola
             WHERE escola.cod_escola = matricula.ref_ref_cod_escola
               AND ref_cod_matricula = cod_matricula
               AND ref_cod_matricula = $1
               AND matricula.ativo = 1
               AND (matricula_turma.ativo = 1 OR matricula_turma.transferido = TRUE)
          ORDER BY matricula_turma.sequencial
             LIMIT 1
        ';

        $dadosMatricula = $this->fetchPreparedQuery($sql, $matriculaId, false, 'first-row');

        $attrs = [
            'id',
            'aluno_id',
            'ano',
            'instituicao_id',
            'escola_id',
            'curso_id',
            'serie_id',
            'turma_id'
        ];

        return Portabilis_Array_Utils::filter($dadosMatricula, $attrs);
    }

    // api
    protected function getBoletim()
    {
        if ($this->canGetBoletim()) {
            $dadosMatricula = $this->loadDadosForMatricula($this->getRequest()->matricula_id);

            $boletimReport = new BoletimReport();

            $boletimReport->addArg('matricula', (int)$dadosMatricula['id']);
            $boletimReport->addArg('ano', (int)$dadosMatricula['ano']);
            $boletimReport->addArg('instituicao', (int)$dadosMatricula['instituicao_id']);
            $boletimReport->addArg('escola', (int)$dadosMatricula['escola_id']);
            $boletimReport->addArg('curso', (int)$dadosMatricula['curso_id']);
            $boletimReport->addArg('serie', (int)$dadosMatricula['serie_id']);
            $boletimReport->addArg('turma', (int)$dadosMatricula['turma_id']);
            $boletimReport->addArg('situacao_matricula', 10);
            $boletimReport->addArg('situacao', (int)$dadosMatricula['situacao'] ?? 0);
            $boletimReport->addArg('SUBREPORT_DIR', base_path() . config('legacy.report.source_path'));

            if ($this->getRequest()->etapa) {
                $boletimReport->addArg('etapa', (int)$this->getRequest()->etapa);
            }

            $encoding = 'base64';
            $dumpsOptions = ['options' => ['encoding' => $encoding]];
            $encoded = $boletimReport->dumps($dumpsOptions);

            return [
                'matricula_id' => $this->getRequest()->matricula_id,
                'encoding' => $encoding,
                'encoded' => base64_encode($encoded)
            ];
        }
    }

    protected function getBoletimProfessor()
    {
        if ($this->canGetBoletimProfessor()) {
            $boletimProfessorReport = app(TeacherReportCard::class);

            $boletimProfessorReport->addArg('ano', (int)$this->getRequest()->ano);
            $boletimProfessorReport->addArg('instituicao', (int)$this->getRequest()->instituicao_id);
            $boletimProfessorReport->addArg('escola', (int)$this->getRequest()->escola_id);
            $boletimProfessorReport->addArg('curso', (int)$this->getRequest()->curso_id);
            $boletimProfessorReport->addArg('serie', (int)$this->getRequest()->serie_id);
            $boletimProfessorReport->addArg('turma', (int)$this->getRequest()->turma_id);
            $boletimProfessorReport->addArg('professor', $this->getRequest()->professor);
            $boletimProfessorReport->addArg('disciplina', (int)$this->getRequest()->componente_curricular_id);
            $boletimProfessorReport->addArg('orientacao', 2);
            $boletimProfessorReport->addArg('situacao', (int) $this->getRequest()->situacao ?? 0);

            $configuracoes = new clsPmieducarConfiguracoesGerais();
            $configuracoes = $configuracoes->detalhe();

            $modelo = $configuracoes['modelo_boletim_professor'];

            $boletimProfessorReport->addArg('modelo', $modelo);
            $boletimProfessorReport->addArg('linha', 0);
            $boletimProfessorReport->addArg('SUBREPORT_DIR', base_path() . config('legacy.report.source_path'));

            $encoding = 'base64';

            $dumpsOptions = [
                'options' => [
                    'encoding' => $encoding
                ]
            ];

            $encoded = $boletimProfessorReport->dumps($dumpsOptions);

            return [
                'encoding' => $encoding,
                'encoded' => base64_encode($encoded)
            ];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'boletim')) {
            $this->appendResponse($this->getBoletim());
        } elseif ($this->isRequestFor('get', 'boletim-professor')) {
            $this->appendResponse($this->getBoletimProfessor());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
