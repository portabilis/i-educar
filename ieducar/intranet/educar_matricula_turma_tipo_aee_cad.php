<?php

use iEducar\Modules\Educacenso\Model\TipoAtendimentoAluno;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

return new class extends clsCadastro {
    public $cod_matricula;
    public $ref_cod_aluno;
    public $tipo_atendimento;

    public function Inicializar()
    {
        $this->cod_matricula = $this->getQueryString('ref_cod_matricula');
        $this->ref_cod_aluno = $this->getQueryString('ref_cod_aluno');

        $this->validaPermissao();
        $this->validaParametros();

        return 'Editar';
    }

    public function Gerar()
    {
        $this->campoOculto('cod_matricula', $this->cod_matricula);
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

        $this->nome_url_cancelar = 'Voltar';
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        $this->breadcrumb('Tipo do AEE do aluno', [
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_index.php' => 'Escola',
        ]);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoRotulo('nm_aluno', 'Aluno', $this->nm_aluno);
        }

        $enturmacoes = $this->getEnturmacoesAee();

        foreach ($enturmacoes as $enturmacao) {
            $tipoAtendimento = explode(',', str_replace(['{', '}'], '', $enturmacao['tipo_atendimento']));

            $helperOptions = ['objectName' => "{$enturmacao['ref_cod_turma']}_{$enturmacao['sequencial']}_tipoatendimento"];
            $options = [
                'label' => "Tipo de atendimento educacional especializado do aluno na turma {$enturmacao['nm_turma']}: ",
                'options' => [
                    'values' => $tipoAtendimento,
                    'all_values' => TipoAtendimentoAluno::getDescriptiveValues(),
                ],
                'required' => false,
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
        }
    }

    public function Editar()
    {
        $this->validaPermissao();
        $this->validaParametros();

        $enturmacoes = $this->getEnturmacoesAee();

        $arrayTipoAtendimento = [];
        foreach ($enturmacoes as $enturmacao) {
            $arrayTipoAtendimento[] = [
                'value' => request($enturmacao['ref_cod_turma'] . '_' . $enturmacao['sequencial'] . '_tipoatendimento'),
                'turma' => $enturmacao['ref_cod_turma'],
                'sequencial' => $enturmacao['sequencial'],
            ];
        }

        foreach ($arrayTipoAtendimento as $data) {
            $obj = new clsPmieducarMatriculaTurma($this->cod_matricula, $data['turma'], $this->pessoa_logada);
            $tipoAtendimento = $data['value'] ? implode(',', $data['value']) : null;
            $obj->sequencial = $data['sequencial'];
            $obj->tipo_atendimento = $tipoAtendimento;
            $obj->edita();
        }

        $this->mensagem = 'Tipo do AEE do aluno atualizado com sucesso.<br>';
        $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->cod_matricula}");
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula) {
            $this->simpleRedirect("educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }
    }

    private function getEnturmacoesAee()
    {
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            $this->cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            false,
            null,
            null,
            null,
            false,
            false,
            false,
            null,
            null,
            false,
            null,
            false,
            false,
            false
        );

        $arrayEnturmacoes = [];
        foreach ($enturmacoes as $enturmacao) {
            $turma         = new clsPmieducarTurma($enturmacao['ref_cod_turma']);
            $turma         = $turma->detalhe();

            if ($turma['tipo_atendimento'] == TipoAtendimentoTurma::AEE) {
                $arrayEnturmacoes[] = $enturmacao;
            }
        }

        return $arrayEnturmacoes;
    }

    public function Formular()
    {
        $this->title = 'Tipo do AEE do aluno';
        $this->processoAp = '578';
    }
};
