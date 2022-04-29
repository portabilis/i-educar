<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_infra_predio_comodo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_infra_comodo_funcao;
    public $ref_cod_infra_predio;
    public $nm_comodo;
    public $desc_comodo;
    public $area;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_infra_predio_comodo=$_GET['cod_infra_predio_comodo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(574, $this->pessoa_logada, 7, 'educar_infra_predio_comodo_lst.php');

        if (is_numeric($this->cod_infra_predio_comodo)) {
            $obj = new clsPmieducarInfraPredioComodo($this->cod_infra_predio_comodo);
            $registro  = $obj->detalhe();
            if ($registro) {
                $obj_infra_comodo = new clsPmieducarInfraPredio($registro['ref_cod_infra_predio']);
                $det_comodo = $obj_infra_comodo->detalhe();
                $registro['ref_cod_escola'] = $det_comodo['ref_cod_escola'];

                $obj_escola = new clsPmieducarEscola($det_comodo['ref_cod_escola']);
                $det_escola = $obj_escola->detalhe();
                $registro['ref_cod_instituicao'] = $det_escola['ref_cod_instituicao'];
                //echo "<pre>";print_r($registro);die;
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = true;
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_infra_predio_comodo_det.php?cod_infra_predio_comodo={$registro['cod_infra_predio_comodo']}" : 'educar_infra_predio_comodo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' ambiente', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_infra_predio_comodo', $this->cod_infra_predio_comodo);

        $obrigatorio = true;
        $get_escola  = true;
        $this->inputsHelper()->dynamic(['instituicao','escola']);

        $opcoes_predio = [ '' => 'Selecione' ];

        // EDITAR
        if ($this->ref_cod_escola) {
            $objTemp = new clsPmieducarInfraPredio();
            $lista = $objTemp->lista(null, null, null, $this->ref_cod_escola, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes_predio["{$registro['cod_infra_predio']}"] = "{$registro['nm_predio']}";
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 400, \'educar_infra_predio_cad_pop.php\');';
        if ($this->ref_cod_escola && $this->ref_cod_instituicao) {
            $script = "<img id='img_colecao' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_colecao' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }
        $this->campoLista('ref_cod_infra_predio', 'Prédio', $opcoes_predio, $this->ref_cod_infra_predio, '', false, '', $script);

        $opcoes_funcao = [ '' => 'Selecione' ];

        // EDITAR
        if ($this->ref_cod_escola) {
            $objTemp = new clsPmieducarInfraComodoFuncao();
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_escola);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes_funcao["{$registro['cod_infra_comodo_funcao']}"] = "{$registro['nm_funcao']}";
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 250, \'educar_infra_comodo_funcao_cad_pop.php\');';
        if ($this->ref_cod_escola && $this->ref_cod_instituicao) {
            $script = "<img id='img_colecao2' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_colecao2' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }
        $this->campoLista('ref_cod_infra_comodo_funcao', 'Tipo de ambiente', $opcoes_funcao, $this->ref_cod_infra_comodo_funcao, '', false, '', $script);

        // text
        $this->campoTexto('nm_comodo', 'Ambiente', $this->nm_comodo, 43, 255, true);
        $this->campoMonetario('area', 'área m²', $this->area, 10, 255, true);
        $this->campoMemo('desc_comodo', 'Descrição do ambiente', $this->desc_comodo, 60, 5, false);
    }

    public function Novo()
    {
        $this->area = str_replace('.', '', $this->area);
        $this->area = str_replace(',', '.', $this->area);
        $obj = new clsPmieducarInfraPredioComodo(null, null, $this->pessoa_logada, $this->ref_cod_infra_comodo_funcao, $this->ref_cod_infra_predio, $this->nm_comodo, $this->desc_comodo, $this->area, null, null, 1);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_infra_predio_comodo_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->area = str_replace('.', '', $this->area);
        $this->area = str_replace(',', '.', $this->area);

        $obj = new clsPmieducarInfraPredioComodo($this->cod_infra_predio_comodo, $this->pessoa_logada, null, $this->ref_cod_infra_comodo_funcao, $this->ref_cod_infra_predio, $this->nm_comodo, $this->desc_comodo, $this->area, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_infra_predio_comodo_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarInfraPredioComodo($this->cod_infra_predio_comodo, $this->pessoa_logada, null, null, null, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_infra_predio_comodo_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-infra-predio-comodo-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Ambiente';
        $this->processoAp = '574';
    }
};
