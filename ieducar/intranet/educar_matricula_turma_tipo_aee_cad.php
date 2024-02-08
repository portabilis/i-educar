<?php

use iEducar\Modules\Educacenso\Model\TipoAtendimentoAluno;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

return new class extends clsCadastro
{
    public $cod_matricula;

    public $ref_cod_aluno;

    public $tipo_atendimento;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 688);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Inicializar()
    {
        $this->cod_matricula = $this->getQueryString(name: 'ref_cod_matricula');
        $this->ref_cod_aluno = $this->getQueryString(name: 'ref_cod_aluno');

        $this->validaPermissao();
        $this->validaParametros();

        return 'Editar';
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'cod_matricula', valor: $this->cod_matricula);
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        $this->nome_url_cancelar = 'Voltar';
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        $this->breadcrumb(currentPage: 'Tipo do AEE do aluno', breadcrumbs: [
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_index.php' => 'Escola',
        ]);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista(int_cod_aluno: $this->ref_cod_aluno, int_ativo: 1);
        if (is_array(value: $lst_aluno)) {
            $det_aluno = array_shift(array: $lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno);
        }

        $enturmacoes = $this->getEnturmacoesAee();

        foreach ($enturmacoes as $enturmacao) {
            $tipoAtendimento = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $enturmacao['tipo_atendimento']));

            $helperOptions = ['objectName' => "{$enturmacao['ref_cod_turma']}_{$enturmacao['sequencial']}_tipoatendimento"];
            $options = [
                'label' => "Tipo de atendimento educacional especializado do aluno na turma {$enturmacao['nm_turma']}: ",
                'options' => [
                    'values' => $tipoAtendimento,
                    'all_values' => TipoAtendimentoAluno::getDescriptiveValues(),
                ],
                'required' => false,
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);
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
                'value' => request(key: $enturmacao['ref_cod_turma'] . '_' . $enturmacao['sequencial'] . '_tipoatendimento'),
                'turma' => $enturmacao['ref_cod_turma'],
                'sequencial' => $enturmacao['sequencial'],
            ];
        }

        foreach ($arrayTipoAtendimento as $data) {
            $obj = new clsPmieducarMatriculaTurma(ref_cod_matricula: $this->cod_matricula, ref_cod_turma: $data['turma'], ref_usuario_exc: $this->pessoa_logada);
            $tipoAtendimento = $data['value'] ? implode(separator: ',', array: $data['value']) : null;
            $obj->sequencial = $data['sequencial'];
            $obj->tipo_atendimento = $tipoAtendimento;
            $obj->edita();
        }

        $this->mensagem = 'Tipo do AEE do aluno atualizado com sucesso.<br>';
        $this->simpleRedirect(url: "educar_matricula_det.php?cod_matricula={$this->cod_matricula}");
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula) {
            $this->simpleRedirect(url: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }
    }

    private function getEnturmacoesAee()
    {
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            int_ref_cod_matricula: $this->cod_matricula,
            int_ativo: 1
        );

        $arrayEnturmacoes = [];
        foreach ($enturmacoes as $enturmacao) {
            $turma = new clsPmieducarTurma(cod_turma: $enturmacao['ref_cod_turma']);
            $turma = $turma->detalhe();

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
