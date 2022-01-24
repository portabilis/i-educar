<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_nivel;
    public $ref_cod_categoria_nivel;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_nivel_anterior;
    public $nm_nivel;
    public $salario_base;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_categoria;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_categoria_nivel = $_GET['cod_categoria'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, "educar_categoria_nivel_det.php?cod_categoria_nivel={$this->cod_nivel}", true);

        if (is_numeric($this->ref_cod_categoria_nivel)) {
            $obj = new clsPmieducarCategoriaNivel($this->ref_cod_categoria_nivel);
            $registro  = $obj->detalhe();
            if ($registro) {
                $this->nm_categoria = $registro['nm_categoria_nivel'];
                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(829, $this->pessoa_logada, 3, null, true)) {
                    $this->fexcluir = true;
                }

                $obj_niveis = new clsPmieducarNivel();
                $obj_niveis->setOrderby('cod_nivel');
                $lst_niveis = $obj_niveis->lista(null, $this->ref_cod_categoria_nivel, null, null, null, null, null, null, null, null, null, 1);

                if ($lst_niveis) {
                    foreach ($lst_niveis as $id => $nivel) {
                        $id++;
                        $nivel['salario_base'] = number_format($nivel['salario_base'], 2, ',', '.');
                        $this->cod_nivel[] = [$nivel['nm_nivel'],$nivel['salario_base'],$id,$nivel['cod_nivel']];
                    }
                } else {
                    $this->cod_nivel[] = ['','','1',''];
                }

                $retorno = 'Editar';
            }
        } else {
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->url_cancelar = "educar_categoria_nivel_det.php?cod_categoria_nivel={$this->cod_nivel}";
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Adicionar níveis à categoria', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_categoria_nivel', $this->ref_cod_categoria_nivel);

        $this->campoRotulo('nm_categoria', 'Categoria', $this->nm_categoria);

        $this->campoTabelaInicio('tab01', 'Níveis', ['Nome Nível','Salário','Ordem'], $this->cod_nivel);

        $this->campoTexto('nm_nivel', 'Nome Nível', '', 30, 100, true);
        $this->campoMonetario('salario_base', 'Salario Base', $this->salario_base, 10, 8, true);
        $this->campoNumero('nr_nivel', 'Ordem', '1', 5, 5, false, false, false, false, false, false, true);
        $this->campoOculto('cod_nivel', '');

        $this->campoTabelaFim();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, "educar_categoria_nivel_det.php?cod_categoria_nivel={$this->cod_nivel}", true);

        $obj = new clsPmieducarNivel($this->cod_nivel, $this->ref_cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_nivel_anterior, $this->nm_nivel, $this->salario_base, $this->data_cadastro, $this->data_exclusao, $this->ativo);

        $obj->desativaTodos();

        if ($this->nm_nivel) {
            $nivel_anterior = null;
            $niveis = [];
            foreach ($this->nm_nivel as $id => $nm_nivel) {
                $obj_nivel = new clsPmieducarNivel($this->cod_nivel[$id], $this->ref_cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $nivel_anterior, $nm_nivel, str_replace(',', '.', str_replace('.', '', $this->salario_base[$id])), null, null, 1);
                if ($obj_nivel->existe()) {
                    $obj_nivel->edita();
                    $nivel_anterior = $this->cod_nivel[$id];
                } else {
                    $nivel_anterior = $obj_nivel->cadastra();
                }

                $niveis[] = $nivel_anterior;
            }

            /**
             * desativa todos os subniveis dos niveis que nao se
             * encontram ativos
             */

            if ($niveis) {
                $obj = new clsPmieducarSubnivel(null, $this->pessoa_logada, $this->pessoa_logada, null, $this->ref_cod_nivel);

                $obj->desativaTodos($niveis);
            }

            $this->simpleRedirect("educar_categoria_nivel_det.php?cod_categoria_nivel={$this->ref_cod_categoria_nivel}");
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        if (!$this->Novo()) {
            return false;
        }
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(829, $this->pessoa_logada, 3, 'educar_nivel_lst.php', true);

        $obj = new clsPmieducarNivel($this->cod_nivel, $this->ref_cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_nivel_anterior, $this->nm_nivel, $this->salario_base, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->desativaTodos();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_categoria_nivel_det.php?cod_categoria_nivel={$this->ref_cod_categoria_nivel}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-nivel-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Nível';
        $this->processoAp = '829';
    }
};
