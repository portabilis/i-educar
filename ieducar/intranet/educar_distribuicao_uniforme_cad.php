<?php

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_distribuicao_uniforme;
    public $ref_cod_aluno;
    public $ano;
    public $agasalho_qtd;
    public $camiseta_curta_qtd;
    public $camiseta_longa_qtd;
    public $meias_qtd;
    public $bermudas_tectels_qtd;
    public $bermudas_coton_qtd;
    public $tenis_qtd;
    public $data;
    public $agasalho_tm;
    public $camiseta_curta_tm;
    public $camiseta_longa_tm;
    public $meias_tm;
    public $bermudas_tectels_tm;
    public $bermudas_coton_tm;
    public $tenis_tm;
    public $ref_cod_escola;
    public $kit_completo;
    public $camiseta_infantil_qtd;
    public $camiseta_infantil_tm;
    public $calca_jeans_qtd;
    public $calca_jeans_tm;
    public $saia_qtd;
    public $saia_tm;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_distribuicao_uniforme=$_GET['cod_distribuicao_uniforme'];
        $this->ref_cod_aluno=$_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->cod_distribuicao_uniforme)) {
            $obj = new clsPmieducarDistribuicaoUniforme($this->cod_distribuicao_uniforme);

            $registro  = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->data = Portabilis_Date_Utils::pgSqlToBr($this->data);

                $this->kit_completo = dbBool($this->kit_completo);

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? "educar_distribuicao_uniforme_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&cod_distribuicao_uniforme={$registro['cod_distribuicao_uniforme']}"
            : "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Distribuições de uniforme escolar', breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = !$this->$campo ?  $val : $this->$campo;
            }
        }

        $objEscola = new clsPmieducarEscola();
        $lista = $objEscola->lista();

        $escolaOpcoes = ['' => 'Selecione'];

        foreach ($lista as $escola) {
            $escolaOpcoes["{$escola['cod_escola']}"] = "{$escola['nome']}";
        }

        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        $this->campoOculto(nome: 'cod_distribuicao_uniforme', valor: $this->cod_distribuicao_uniforme);

        $this->campoNumero(nome: 'ano', campo: 'Ano', valor: $this->ano, tamanhovisivel: 4, tamanhomaximo: 4, obrigatorio: true);

        $this->inputsHelper()->date(attrName: 'data', inputOptions: [
            'label' => 'Data da distribuição',
            'value' => $this->data,
            'placeholder' => '',
            'size' => 10
        ]);

        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        $this->inputsHelper()->checkbox(attrName: 'kit_completo', inputOptions: [
            'label' => 'Kit completo', 'value' => $this->kit_completo
        ]);

        $this->inputsHelper()->integer(attrName: 'agasalho_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de agasalhos (jaqueta e calça)',
            'value' => $this->agasalho_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline'  => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'agasalho_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->agasalho_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'camiseta_curta_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de camisetas (manga curta)',
            'value' => $this->camiseta_curta_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'camiseta_curta_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->camiseta_curta_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'camiseta_longa_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de camisetas (manga longa)',
            'value' => $this->camiseta_longa_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'camiseta_longa_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->camiseta_longa_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'camiseta_infantil_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de camisetas infantis (sem manga)',
            'value' => $this->camiseta_infantil_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'camiseta_infantil_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->camiseta_infantil_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'calca_jeans_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de calças jeans',
            'value' => $this->calca_jeans_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'calca_jeans_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->calca_jeans_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'meias_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de meias',
            'value' => $this->meias_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'meias_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->meias_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'saia_qtd', inputOptions: [
            'required' => false,
            'label' => 'Quantidade de saias',
            'value' => $this->saia_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'saia_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->saia_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'bermudas_tectels_qtd', inputOptions: [
            'required' => false,
            'label' => 'Bermudas tectels (masculino)',
            'value' => $this->bermudas_tectels_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'bermudas_tectels_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->bermudas_tectels_tm,
            'max_length'  => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'bermudas_coton_qtd', inputOptions: [
            'required' => false,
            'label' => 'Bermudas coton (feminino)',
            'value' => $this->bermudas_coton_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'bermudas_coton_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->bermudas_coton_tm,
            'max_length' => 10,
            'size' => 10
        ]);

        $this->inputsHelper()->integer(attrName: 'tenis_qtd', inputOptions: [
            'required' => false,
            'label' => 'Tênis',
            'value' => $this->tenis_qtd,
            'max_length' => 2,
            'size' => 2,
            'inline' => true,
            'placeholder' => ''
        ]);

        $this->inputsHelper()->text(attrNames: 'tenis_tm', inputOptions: [
            'required' => false,
            'label' => ' Tamanho',
            'value' => $this->tenis_tm,
            'max_length'  => 10,
            'size' => 10
        ]);
    }

    public function Novo()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_tmp = $obj = new clsPmieducarDistribuicaoUniforme();
        $lista_tmp = $obj_tmp->lista(ref_cod_aluno: $this->ref_cod_aluno, ano: $this->ano);

        if ($lista_tmp) {
            $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';

            return false;
        }

        $obj = new clsPmieducarDistribuicaoUniforme(
            ref_cod_aluno: $this->ref_cod_aluno,
            ano: $this->ano,
            kit_completo: !is_null($this->kit_completo),
            agasalho_qtd: $this->agasalho_qtd,
            camiseta_curta_qtd: $this->camiseta_curta_qtd,
            camiseta_longa_qtd: $this->camiseta_longa_qtd,
            meias_qtd: $this->meias_qtd,
            bermudas_tectels_qtd: $this->bermudas_tectels_qtd,
            bermudas_coton_qtd: $this->bermudas_coton_qtd,
            tenis_qtd: $this->tenis_qtd,
            data: $this->data,
            agasalho_tm: $this->agasalho_tm,
            camiseta_curta_tm: $this->camiseta_curta_tm,
            camiseta_longa_tm: $this->camiseta_longa_tm,
            meias_tm: $this->meias_tm,
            bermudas_tectels_tm: $this->bermudas_tectels_tm,
            bermudas_coton_tm: $this->bermudas_coton_tm,
            tenis_tm: $this->tenis_tm,
            ref_cod_escola: $this->ref_cod_escola,
            camiseta_infantil_qtd: $this->camiseta_infantil_qtd,
            camiseta_infantil_tm: $this->camiseta_infantil_tm,
            calca_jeans_qtd: $this->calca_jeans_qtd,
            calca_jeans_tm: $this->calca_jeans_tm,
            saia_qtd: $this->saia_qtd,
            saia_tm: $this->saia_tm
        );

        $this->cod_distribuicao_uniforme = $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->redirectIf(condition: true, url: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_tmp = $obj = new clsPmieducarDistribuicaoUniforme();
        $lista_tmp = $obj_tmp->lista(ref_cod_aluno: $this->ref_cod_aluno, ano: $this->ano);

        if ($lista_tmp) {
            foreach ($lista_tmp as $reg) {
                if ($reg['cod_distribuicao_uniforme'] != $this->cod_distribuicao_uniforme) {
                    $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';

                    return false;
                }
            }
        }

        $obj = new clsPmieducarDistribuicaoUniforme(
            cod_distribuicao_uniforme: $this->cod_distribuicao_uniforme,
            ref_cod_aluno: $this->ref_cod_aluno,
            ano: $this->ano,
            kit_completo: !is_null($this->kit_completo),
            agasalho_qtd: $this->agasalho_qtd,
            camiseta_curta_qtd: $this->camiseta_curta_qtd,
            camiseta_longa_qtd: $this->camiseta_longa_qtd,
            meias_qtd: $this->meias_qtd,
            bermudas_tectels_qtd: $this->bermudas_tectels_qtd,
            bermudas_coton_qtd: $this->bermudas_coton_qtd,
            tenis_qtd: $this->tenis_qtd,
            data: $this->data,
            agasalho_tm: $this->agasalho_tm,
            camiseta_curta_tm: $this->camiseta_curta_tm,
            camiseta_longa_tm: $this->camiseta_longa_tm,
            meias_tm: $this->meias_tm,
            bermudas_tectels_tm: $this->bermudas_tectels_tm,
            bermudas_coton_tm: $this->bermudas_coton_tm,
            tenis_tm: $this->tenis_tm,
            ref_cod_escola: $this->ref_cod_escola,
            camiseta_infantil_qtd: $this->camiseta_infantil_qtd,
            camiseta_infantil_tm: $this->camiseta_infantil_tm,
            calca_jeans_qtd: $this->calca_jeans_qtd,
            calca_jeans_tm: $this->calca_jeans_tm,
            saia_qtd: $this->saia_qtd,
            saia_tm: $this->saia_tm
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->redirectIf(condition: true, url: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj = new clsPmieducarDistribuicaoUniforme($this->cod_distribuicao_uniforme);
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->redirectIf(condition: true, url: "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-distribuicao-uniforme-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Distribuição de uniforme';
        $this->processoAp = 578;
    }
};
