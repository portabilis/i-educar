<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $instituicao_id;

    public $curso_id;

    public $serie_id;

    public $componente_id;

    public $carga_horaria;

    public $retorno;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->serie_id = $_GET['serie_id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 9998859,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 3,
            str_pagina_redirecionar: 'educar_componentes_serie_lst.php'
        );

        if (is_numeric($this->serie_id)) {
            $retorno = 'Editar';
            $obj = new clsPmieducarSerie($this->serie_id);
            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->curso_id = $registro['ref_cod_curso'];

                $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $obj_curso_det = $obj_curso->detalhe();
                $this->instituicao_id = $obj_curso_det['ref_cod_instituicao'];
                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    int_processo_ap: 9998859,
                    int_idpes_usuario: $this->pessoa_logada,
                    int_soma_nivel_acesso: 3
                );
            }
        }

        $this->url_cancelar = 'educar_componentes_serie_lst.php';

        $this->breadcrumb(currentPage: 'Componentes da série', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        $this->alerta_faixa_etaria = dbBool($this->alerta_faixa_etaria);
        $this->bloquear_matricula_faixa_etaria = dbBool($this->bloquear_matricula_faixa_etaria);
        $this->exigir_inep = dbBool($this->exigir_inep);

        $this->retorno = $retorno;

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $opcoesCurso = ['' => 'Selecione um curso'];
        $opcoesSerie = ['' => 'Selecione uma série'];

        $this->campoOculto(nome: 'curso_id', valor: $this->curso_id);
        $this->campoOculto(nome: 'serie_id', valor: $this->serie_id);
        $this->campoOculto(nome: 'serie_id', valor: $this->serie_id);
        $this->campoOculto(nome: 'retorno', valor: $this->retorno);
        $this->campoOculto(nome: 'sugestao_anos_letivos', valor: json_encode(array_values($this->anosLetivosExistentes())));

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['value' => $this->instituicao_id]);

        $this->campoLista(nome: 'ref_cod_curso', campo: 'Curso', valor: $opcoesCurso, default: $this->curso_id);
        $this->campoLista(nome: 'ref_cod_serie', campo: 'Série', valor: $opcoesSerie, default: $this->serie_id);

        $helperOptions = ['objectName' => 'ref_cod_area_conhecimento'];
        $options = ['label' => 'Áreas de conhecimento',
            'size' => 50,
            'required' => false];

        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $this->campoRotulo(nome: 'componentes_', campo: 'Componentes da série', valor: '<table id=\'componentes\'></table>');

        $scripts = ['/vendor/legacy/Cadastro/Assets/Javascripts/ComponentesSerie.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/ComponentesSerieAcao.js'];
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function Novo()
    {
        // Todas as ações estão sendo realizadas em ComponentesSerieAcao.js
        $this->simpleRedirect('educar_componentes_serie_lst.php');
    }

    public function Editar()
    {
        // Todas as ações estão sendo realizadas em ComponentesSerieAcao.js
        $this->simpleRedirect('educar_componentes_serie_lst.php');
    }

    public function Excluir()
    {
        // Todas as ações estão sendo realizadas em ComponentesSerieAcao.js
        $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_componentes_serie_lst.php');
    }

    public function Formular()
    {
        $this->title = 'Série';
        $this->processoAp = '9998859';
    }
};
