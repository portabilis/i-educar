<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_cliente_tipo;
    public $ref_cod_biblioteca;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Tipo Cliente - Detalhe';

        $this->cod_cliente_tipo=$_GET['cod_cliente_tipo'];

        $tmp_obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
        $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
        $registro['ref_cod_biblioteca'] = $det_ref_cod_biblioteca['nm_biblioteca'];
        $registro['ref_cod_instituicao'] = $det_ref_cod_biblioteca['ref_cod_instituicao'];
        $registro['ref_cod_escola'] = $det_ref_cod_biblioteca['ref_cod_escola'];
        if ($registro['ref_cod_instituicao']) {
            $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
            $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
        }
        if ($registro['ref_cod_escola']) {
            $obj_ref_cod_escola = new clsPmieducarEscola();
            $det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro['ref_cod_escola']));
            $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($registro['ref_cod_instituicao'] && $nivel_usuario == 1) {
            $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
        }
        if ($registro['ref_cod_escola'] && ($nivel_usuario == 1 || $nivel_usuario == 2)) {
            $this->addDetalhe([ 'Escola', "{$registro['ref_cod_escola']}"]);
        }
        if ($registro['ref_cod_biblioteca'] && ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)) {
            $this->addDetalhe([ 'Biblioteca', "{$registro['ref_cod_biblioteca']}"]);
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Tipo Cliente', "{$registro['nm_tipo']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }

        $obj_cliente_tp_exemplar_tp = new clsPmieducarClienteTipoExemplarTipo();
        $lst_cliente_tp_exemplar_tp = $obj_cliente_tp_exemplar_tp->lista($this->cod_cliente_tipo);
        if ($lst_cliente_tp_exemplar_tp) {
            $tabela = '<TABLE>
                           <TR align=center>
                               <TD bgcolor=#ccdce6><B>Tipo Exemplar</B></TD>
                               <TD bgcolor=#ccdce6><B>Dias Empr&eacute;stimo</B></TD>
                           </TR>';
            $cont = 0;

            foreach ($lst_cliente_tp_exemplar_tp as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=#f5f9fd ';
                } else {
                    $color = ' bgcolor=#FFFFFF ';
                }
                $obj_exemplar_tipo = new clsPmieducarExemplarTipo($valor['ref_cod_exemplar_tipo']);
                $det_exemplar_tipo = $obj_exemplar_tipo->detalhe();
                $nm_tipo = $det_exemplar_tipo['nm_tipo'];

                $tabela .= "<TR>
                                <TD {$color} align=left>{$nm_tipo}</TD>
                                <TD {$color} align=left>{$valor['dias_emprestimo']}</TD>
                            </TR>";
                $cont++;
            }
            $tabela .= '</TABLE>';
        }
        if ($tabela) {
            $this->addDetalhe([ 'Tipo Exemplar', "{$tabela}"]);
        }

        if ($obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_cliente_tipo_cad.php';
            $this->url_editar = "educar_cliente_tipo_cad.php?cod_cliente_tipo={$registro['cod_cliente_tipo']}";
        }

        $this->url_cancelar = 'educar_cliente_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de clientes', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo Cliente ';
        $this->processoAp = '596';
    }
};
