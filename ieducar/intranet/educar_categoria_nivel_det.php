<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_categoria_nivel;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_categoria_nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Categoria Nivel - Detalhe';

        $this->cod_categoria_nivel=$_GET['cod_categoria_nivel'];

        $tmp_obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        if ($registro['cod_categoria_nivel']) {
            $this->addDetalhe([ 'Categoria', "{$registro['cod_categoria_nivel']}"]);
        }
        if ($registro['nm_categoria_nivel']) {
            $this->addDetalhe([ 'Nome Categoria', "{$registro['nm_categoria_nivel']}"]);
        }

        $tab_niveis = null;

        $obj_nivel = new clsPmieducarNivel();
        $lst_nivel = $obj_nivel->buscaSequenciaNivel($this->cod_categoria_nivel);

        if ($lst_nivel) {
            $tab_niveis .= '<table cellspacing=\'0\' cellpadding=\'0\' width=\'200\' border=\'0\'>';

            $class2 = $class2 == 'formlttd' ? 'formmdtd' : 'formlttd' ;
            $tab_niveis .= ' <tr>
                                <td bgcolor=\'#ccdce6\' align=\'center\'>N&iacute;veis</td>
                                <td bgcolor=\'#ccdce6\' align=\'center\'>Subn&iacute;veis</td>
                            </tr>';
            foreach ($lst_nivel as $nivel) {
                $tab_niveis .= " <tr class='$class2' align='center'>
                                    <td align='left'>{$nivel['nm_nivel']}</td>
                                    <td align='center'><a style='color:#0ac336;' href='javascript:popless(\"{$nivel['cod_nivel']}\")'><i class='fa fa-plus-square' aria-hidden='true'></i></a></td>
                                </tr>";

                $class2 = $class2 == 'formlttd' ? 'formmdtd' : 'formlttd' ;
            }
            $tab_niveis .=  '</table>';

            $this->addDetalhe(['N&iacute;veis', "$tab_niveis"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, null, true)) {
            $this->url_novo = 'educar_categoria_nivel_cad.php';
            $this->url_editar = "educar_categoria_nivel_cad.php?cod_categoria_nivel={$registro['cod_categoria_nivel']}";
            $this->array_botao[] = 'Adicionar Níveis';
            $this->array_botao_url[] = "educar_nivel_cad.php?cod_categoria={$registro['cod_categoria_nivel']}";
        }

        $this->url_cancelar = 'educar_categoria_nivel_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhes da categoria/nível', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function makeExtra()
    {
        return str_replace(
            '#cod_categoria_nivel',
            $_GET['cod_categoria_nivel'],
            file_get_contents(__DIR__ . '/scripts/extra/educar-categoria-nivel-det.js')
        );
    }

    public function Formular()
    {
        $this->title = 'Servidores - Detalhe Categoria Nível';
        $this->processoAp = '829';
    }
};
