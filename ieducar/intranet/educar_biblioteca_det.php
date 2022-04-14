<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_biblioteca;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $nm_biblioteca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Biblioteca - Detalhe';

        $this->cod_biblioteca=$_GET['cod_biblioteca'];

        $tmp_obj = new clsPmieducarBiblioteca($this->cod_biblioteca);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_biblioteca_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $idpes = $det_ref_cod_escola['ref_idpes'];

        $obj_escola = new clsPessoaJuridica($idpes);
        $obj_escola_det = $obj_escola->detalhe();
        $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['ref_cod_escola']) {
            $this->addDetalhe([ 'Escola', "{$registro['ref_cod_escola']}"]);
        }
        if ($registro['nm_biblioteca']) {
            $this->addDetalhe([ 'Biblioteca', "{$registro['nm_biblioteca']}"]);
        }
        /* if ($registro["tombo_automatico"])
         {
            $this->addDetalhe(array("Tombo Automático", dbBool($registro["tombo_automatico"]) ? "Sim" : "Não"));
         }*/
        $obj = new clsPmieducarBibliotecaUsuario();
        $lst = $obj->lista($this->cod_biblioteca);
        if ($lst) {
            $tabela = '<TABLE>
                           <TR align=center>
                               <TD bgcolor=#ccdce6><B>Nome</B></TD>
                           </TR>';
            $cont = 0;

            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=#f5f9fd ';
                } else {
                    $color = ' bgcolor=#FFFFFF ';
                }
                $obj_cod_usuario = new clsPessoa_($valor['ref_cod_usuario']);
                $obj_usuario_det = $obj_cod_usuario->detalhe();
                $nome_usuario = $obj_usuario_det['nome'];

                $tabela .= "<TR>
                                <TD {$color} align=left>{$nome_usuario}</TD>
                            </TR>";
                $cont++;
            }
            $tabela .= '</TABLE>';
        }
        if ($tabela) {
            $this->addDetalhe([ 'Usu&aacute;rio', "{$tabela}"]);
        }

        if ($obj_permissoes->permissao_cadastra(591, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_biblioteca_cad.php';
            $this->url_editar = "educar_biblioteca_cad.php?cod_biblioteca={$registro['cod_biblioteca']}";
        }

        $this->url_cancelar = 'educar_biblioteca_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da biblioteca', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Biblioteca';
        $this->processoAp = '591';
    }
};
