<?php

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyGrade;
use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use App\Models\LegacyStudent;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $ref_cod_matricula;

    public $ref_cod_tipo_ocorrencia_disciplinar;

    public $sequencial;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $observacao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Ocorrência disciplinar - Detalhe';

        $this->sequencial = $_GET['sequencial'];
        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_tipo_ocorrencia_disciplinar = $_GET['ref_cod_tipo_ocorrencia_disciplinar'];

        $registro = LegacyRegistrationDisciplinaryOccurrenceType::query()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('ref_cod_tipo_ocorrencia_disciplinar', $this->ref_cod_tipo_ocorrencia_disciplinar)
            ->where('sequencial', $this->sequencial)
            ->where('ativo', 1)
            ->orderBy('cod_ocorrencia_disciplinar', 'DESC')
            ->first()
            ?->getAttributes();

        if (!$registro) {
            $this->redirectNotFound(
                url: "educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}",
                registro: 'ocorrência disciplinar',
                feminino: true,
                dica: 'Confira os registros listados abaixo.',
            );
        }

        $obj_ref_cod_matricula = new clsPmieducarMatricula($registro['ref_cod_matricula']);
        $det_ref_cod_matricula = $obj_ref_cod_matricula->detalhe();
        $registro['ref_ref_cod_serie'] = LegacyGrade::query()->whereKey($det_ref_cod_matricula['ref_ref_cod_serie'])->value('nm_serie');

        $det_ref_cod_tipo_ocorrencia_disciplinar = LegacyDisciplinaryOccurrenceType::find($registro['ref_cod_tipo_ocorrencia_disciplinar'])?->getAttributes();
        $registro['nm_tipo'] = $det_ref_cod_tipo_ocorrencia_disciplinar['nm_tipo'];

        if ($registro['ref_cod_matricula']) {
            $this->addDetalhe(['Matrícula', "{$registro['ref_cod_matricula']}"]);
        }

        $obj_ref_cod_matricula = (new clsPmieducarMatricula)->lista($this->ref_cod_matricula);
        if (is_array($obj_ref_cod_matricula)) {
            $obj_ref_cod_matricula = array_shift($obj_ref_cod_matricula);
        }

        $detalhe_aluno = $obj_ref_cod_matricula;

        $nm_aluno = LegacyStudent::query()
            ->active()
            ->whereStudent($detalhe_aluno['ref_cod_aluno'])
            ->joinPerson()
            ->value('pessoa.nome');

        $this->addDetalhe(['Nome do Aluno', $nm_aluno]);

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(['Série', "{$registro['ref_ref_cod_serie']}"]);
        }

        if ($registro['sequencial']) {
            $this->addDetalhe(['N&uacute;mero da Ocorrência', "{$registro['sequencial']}"]);
        }
        if ($registro['data_cadastro']) {
            if ($hora = dataFromPgToBr("{$registro['data_cadastro']}", 'H:i')) {
                $this->addDetalhe(['Hora Ocorrência', $hora]);
            }
            $this->addDetalhe(['Data Ocorrência', dataFromPgToBr("{$registro['data_cadastro']}", 'd/m/Y')]);
        }
        if ($registro['ref_cod_tipo_ocorrencia_disciplinar']) {
            $this->addDetalhe(['Tipo Ocorrência', "{$registro['nm_tipo']}"]);
        }
        if ($registro['observacao']) {
            $this->addDetalhe(['Observação', nl2br("{$registro['observacao']}")]);
        }

        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            $this->url_novo = "educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}";
            $this->url_editar = "educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}";
        }

        $this->url_cancelar = "educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['ref_cod_matricula']}";
        $this->largura = '100%';

        // Início > Escola > Alunos > Nome > Matrícula > Ocorrências disciplinares > Ocorrência disciplinar
        $registration = LegacyRegistration::find($registro['ref_cod_matricula']);
        $this->breadcrumb('Ocorrência disciplinar', [
            url('intranet/educar_index.php') => 'Escola',
            url('intranet/educar_aluno_lst.php') => 'Alunos',
            url("intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url("intranet/educar_matricula_det.php?cod_matricula={$registro['ref_cod_matricula']}") => 'Matrícula',
            url("intranet/educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['ref_cod_matricula']}") => 'Ocorrências disciplinares',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Ocorrência Disciplinar';
        $this->processoAp = '578';
    }
};
