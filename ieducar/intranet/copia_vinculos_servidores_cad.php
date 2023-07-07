<?php

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;

return new class() extends clsCadastro
{
    public $pessoa_logada;

    public $cod_turma;

    public $retorno;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ano;

    public $nome_url_sucesso = 'Copiar Vínculos';

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma = $_GET['cod_turma'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 586, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_turma_lst.php');

        $schoolClass = LegacySchoolClass::find(request()->get('cod_turma'));

        $this->cod_turma = $schoolClass->getKey();
        $this->ref_cod_instituicao = $schoolClass->ref_cod_instituicao;
        $this->ref_cod_escola = $schoolClass->ref_ref_cod_escola;
        $this->ref_cod_curso = $schoolClass->ref_cod_curso;
        $this->ref_cod_serie = $schoolClass->ref_ref_cod_serie;
        $this->ano = $schoolClass->ano;

        $this->url_cancelar = 'educar_turma_det.php?cod_turma=' . $this->cod_turma;

        $nomeMenu = 'Copiar Vínculos dos Servidores da';

        $this->breadcrumb(currentPage: $nomeMenu . ' turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->retorno = $retorno;

        return $retorno;
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(helperNames: [
            'instituicao',
            'escola',
            'curso',
            'serie',
        ], inputOptions: [
            'disabled' => true,
        ]);

        $opcoes = LegacySchoolClass::query()
            ->where(column: 'ref_cod_instituicao', operator: $this->ref_cod_instituicao)
            ->where(column: 'ref_ref_cod_escola', operator: $this->ref_cod_escola)
            ->where(column: 'ref_cod_curso', operator: $this->ref_cod_curso)
            ->where(column: 'ref_ref_cod_serie', operator: $this->ref_cod_serie)
            ->where(column: 'ativo', operator: 1)
            ->where(column: 'ano', operator: ($this->ano - 1))
            ->orderBy(column: 'nm_turma')
            ->get('cod_turma', 'nm_turma', 'ano')
            ->pluck(value: 'name', key: 'id')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(
            nome: 'ref_cod_turma_origem',
            campo: 'Turma origem dos vínculos',
            valor: $opcoes,
        );

        $this->inputsHelper()->dynamic(
            helperNames: 'turma',
            inputOptions: [
                'label' => 'Turma destino',
                'disabled' => true,
                'value' => $this->cod_turma,
            ]
        );
    }

    public function Novo()
    {
        $schoolClassDestiny = LegacySchoolClass::find(request()->get('cod_turma'), ['cod_turma', 'ref_cod_instituicao', 'ano']);
        $doesntExist = LegacySchoolClassTeacher::query()
            ->where('ano', $schoolClassDestiny->ano)
            ->where('turma_id', $schoolClassDestiny->getKey())
            ->doesntExist();

        if ($doesntExist) {
            $schoolClassOrigem = LegacySchoolClass::find(request()->get('ref_cod_turma_origem'), ['cod_turma', 'ref_cod_instituicao', 'ano']);

            $vinculosAnteriores = LegacySchoolClassTeacher::query()
                ->where('ano', $schoolClassOrigem->ano)
                ->where('turma_id', $schoolClassOrigem->getKey())
                ->get();

            $vinculosAnteriores->each(function (LegacySchoolClassTeacher $vinculo) use ($schoolClassDestiny) {
                $vinculo->replicate()
                    ->fill([
                        'ano' => $schoolClassDestiny->ano,
                        'turma_id' => $schoolClassDestiny->getKey(),
                    ])
                    ->save();
            });

            $this->mensagem = '<b>A cópia de vínculos dos servidores entre turmas foi efetuada.<br/>Você pode verificar os vínculos copiados no cadastro dos servidores.</br><br>';
            $this->simpleRedirect('educar_turma_det.php?cod_turma='.$schoolClassDestiny->getKey());
        }

        $this->mensagem = 'A turma atual já possui vínculos com servidores.<br>';
        $this->simpleRedirect('educar_turma_det.php?cod_turma='.$schoolClassDestiny->getKey());
    }

    public function Formular()
    {
        $this->title = 'Turma';
        $this->processoAp = 586;
    }
};
