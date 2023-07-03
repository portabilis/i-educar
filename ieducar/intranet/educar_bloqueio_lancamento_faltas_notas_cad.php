<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_bloqueio;

    public $ano;

    public $ref_cod_escola;

    public $etapa;

    public $data_inicio;

    public $data_fim;

    public $modoEdicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_bloqueio = $_GET['cod_bloqueio'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 999848,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_bloqueio_lancamento_faltas_notas_lst.php'
        );

        if (is_numeric(value: $this->cod_bloqueio)) {
            $obj = new clsPmieducarBloqueioLancamentoFaltasNotas(cod_bloqueio: $this->cod_bloqueio);

            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 999848, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
      sprintf('educar_bloqueio_lancamento_faltas_notas_det.php?cod_bloqueio=%d', $this->cod_bloqueio) :
                'educar_bloqueio_lancamento_faltas_notas_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' bloqueio de lançamento de notas e faltas por etapa', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->modoEdicao = ($retorno == 'Editar');

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_bloqueio', valor: $this->cod_bloqueio);

        $this->inputsHelper()->dynamic(helperNames: ['ano', 'instituicao']);

        if ($this->modoEdicao) {
            $objEscola = new clsPmieducarEscola(cod_escola: $this->ref_cod_escola);
            $objEscola = $objEscola->detalhe();

            $options = [
                'required' => false,
                'label' => 'Escola',
                'placeholder' => '',
                'value' => $objEscola['nome'],
                'size' => 35,
                'disabled' => true,
            ];

            $this->inputsHelper()->text(attrNames: 'escola', inputOptions: $options);
        } else {
            $this->inputsHelper()->multipleSearchEscola(attrName: null, inputOptions: ['label' => 'Escola(s)']);
        }

        $selectOptions = [
            1 => '1ª Etapa',
            2 => '2ª Etapa',
            3 => '3ª Etapa',
            4 => '4ª Etapa',
        ];

        $options = ['label' => 'Etapa', 'resources' => $selectOptions, 'value' => $this->etapa];

        $this->inputsHelper()->select(attrName: 'etapa', inputOptions: $options);

        $this->inputsHelper()->date(attrName: 'data_inicio', inputOptions: ['label' => 'Data inicial', 'value' => dataToBrasil(data_original: $this->data_inicio), 'placeholder' => '']);
        $this->inputsHelper()->date(attrName: 'data_fim', inputOptions: ['label' => 'Data final', 'value' => dataToBrasil(data_original: $this->data_fim), 'placeholder' => '']);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 999848, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_bloqueio_lancamento_faltas_notas_lst.php');

        $array_escolas = array_filter(array: $this->escola);

        foreach ($array_escolas as $escolaId) {
            $obj = new clsPmieducarBloqueioLancamentoFaltasNotas(
                cod_bloqueio: null,
                ano: $this->ano,
                ref_cod_escola: $escolaId,
                etapa: $this->etapa,
                data_inicio: Portabilis_Date_Utils::brToPgSQL(date: $this->data_inicio),
                data_fim: Portabilis_Date_Utils::brToPgSQL(date: $this->data_fim)
            );

            $obj->cadastra();
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        $this->simpleRedirect(url: 'educar_bloqueio_lancamento_faltas_notas_lst.php');
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 999848, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_bloqueio_lancamento_faltas_notas_lst.php');

        $obj = new clsPmieducarBloqueioLancamentoFaltasNotas(
            cod_bloqueio: $this->cod_bloqueio,
            ano: $this->ano,
            ref_cod_escola: $this->ref_cod_escola,
            etapa: $this->etapa,
            data_inicio: Portabilis_Date_Utils::brToPgSQL(date: $this->data_inicio),
            data_fim: Portabilis_Date_Utils::brToPgSQL(date: $this->data_fim)
        );

        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect(url: 'educar_bloqueio_lancamento_faltas_notas_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 999848, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_bloqueio_lancamento_faltas_notas_lst.php');

        $obj = new clsPmieducarBloqueioLancamentoFaltasNotas(cod_bloqueio: $this->cod_bloqueio);

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect(url: 'educar_bloqueio_lancamento_faltas_notas_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Bloqueio de lanÃ§amento de notas e faltas por etapa';
        $this->processoAp = 999848;
    }
};
