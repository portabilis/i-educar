<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_nivel;
    public $ref_cod_categoria;
    public $ref_cod_nivel;
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

        $this->ref_cod_categoria = $_GET['ref_cod_categoria'];
        $this->ref_cod_nivel = $_GET['ref_cod_nivel'];

        $obj_permissoes = new clsPermissoes();
        $permite_cadastrar = $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, '', true);

        if (!$permite_cadastrar) {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        if (is_numeric($this->ref_cod_categoria) && is_numeric($this->ref_cod_nivel)) {
            $obj_nivel_categoria = new clsPmieducarNivel();
            $lst_nivel_categoria = $obj_nivel_categoria->lista($this->ref_cod_nivel, $this->ref_cod_categoria, null, null, null, null, null, null, null, null);

            if ($lst_nivel_categoria) {
                $lst_niveis = array_shift($lst_nivel_categoria);

                $obj = new clsPmieducarCategoriaNivel($this->ref_cod_categoria);
                $registro  = $obj->detalhe();

                $this->nm_categoria = $registro['nm_categoria_nivel'];

                $this->nm_nivel = $lst_niveis['nm_nivel'];

                $obj_niveis = new clsPmieducarSubnivel();
                $obj_niveis->setOrderby('cod_subnivel');
                $lst_niveis = $obj_niveis->lista(null, null, null, null, $this->ref_cod_nivel, null, null, null, null, null, 1);

                if ($lst_niveis) {
                    foreach ($lst_niveis as $id => $nivel) {
                        $id++;
                        $nivel['salario'] = number_format($nivel['salario'], 2, ',', '.');
                        $this->cod_nivel[] = [$nivel['nm_subnivel'],$nivel['salario'],$id,$nivel['cod_subnivel']];
                    }
                } else {
                    $this->cod_nivel[] = ['','','1',''];
                }

                $retorno = 'Editar';
            }
        } else {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $this->url_cancelar = false;

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_categoria', $this->ref_cod_categoria);
        $this->campoOculto('ref_cod_nivel', $this->ref_cod_nivel);

        $this->campoRotulo('nm_categoria', 'Categoria', $this->nm_categoria);
        $this->campoRotulo('nm_nivel', 'Nível', $this->nm_nivel);

        $this->campoTabelaInicio('tab01', 'Subn&iacute;veis', ['Nome Subn&iacute;vel','Sal&aacute;rio','Ordem'], $this->cod_nivel);

        $this->campoTexto('nm_nivel', 'Nome Subn&iacute;vel', '', 30, 100, true);
        $this->campoMonetario('salario_base', 'Salario Base', $this->salario_base, 10, 8, true);
        $this->campoNumero('nr_nivel', 'Ordem', '1', 5, 5, false, false, false, false, false, false, true);
        $this->campoOculto('cod_nivel', '');

        $this->campoTabelaFim();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $permite_cadastrar = $obj_permissoes->permissao_cadastra(829, $this->pessoa_logada, 3, '', true);

        if (!$permite_cadastrar) {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $obj = new clsPmieducarSubnivel(null, $this->pessoa_logada, $this->pessoa_logada, null, $this->ref_cod_nivel);

        // FIXME #parameters
        $obj->desativaTodos(null);

        if ($this->nm_nivel) {
            $nivel_anterior = null;
            foreach ($this->nm_nivel as $id => $nm_nivel) {
                $obj_nivel = new clsPmieducarSubnivel($this->cod_nivel[$id], $this->pessoa_logada, $this->pessoa_logada, $nivel_anterior, $this->ref_cod_nivel, $nm_nivel, null, null, 1, str_replace(',', '.', str_replace('.', '', $this->salario_base[$id])));
                if ($obj_nivel->existe()) {
                    $obj_nivel->edita();
                    $nivel_anterior = $this->cod_nivel[$id];
                } else {
                    $nivel_anterior = $obj_nivel->cadastra();
                }
            }

            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

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
        $permite_excluir= $obj_permissoes->permissao_excluir(829, $this->pessoa_logada, 3, '', true);

        if (!$permite_excluir) {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $obj = new clsPmieducarSubnivel($this->cod_nivel, $this->pessoa_logada, $this->pessoa_logada);
        $excluiu = $obj->excluirTodos();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse("educar_categoria_nivel_det.php?cod_categoria_nivel={$this->ref_cod_categoria_nivel}")
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-subniveis-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Nivel';
        $this->processoAp   = '829';
        $this->renderMenu   = false;
        $this->renderMenuSuspenso = false;
    }
};
