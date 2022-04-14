<?php

use Illuminate\Support\Facades\Session;

return new class extends clsCadastro {
    public $cod_biblioteca;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $nm_biblioteca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $biblioteca_usuario;
    public $ref_cod_usuario;
    public $incluir_usuario;
    public $excluir_usuario;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->tipo_biblioteca = Session::get('biblioteca.tipo_biblioteca');

        $this->cod_biblioteca=$_GET['cod_biblioteca'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(591, $this->pessoa_logada, 3, 'educar_biblioteca_lst.php');

        if (is_numeric($this->cod_biblioteca)) {
            $obj = new clsPmieducarBiblioteca($this->cod_biblioteca);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(591, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_biblioteca_det.php?cod_biblioteca={$registro['cod_biblioteca']}" : 'educar_biblioteca_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' biblioteca', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_biblioteca', $this->cod_biblioteca);

        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        // foreign keys
        $instituicao_obrigatorio = true;
        $get_escola = true;

        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        // text
        $this->campoTexto('nm_biblioteca', 'Biblioteca', $this->nm_biblioteca, 30, 255, true);
        /*if ($this->tombo_automatico)
            $this->campoBoolLista("tombo_automatico", "Biblioteca possui tombo automático", $this->tombo_automatico);
        else
            $this->campoBoolLista("tombo_automatico", "Biblioteca possui tombo automático", "t");*/
//      $this->campoCheck("tombo_automatico", "Biblioteca possui tombo automático", dbBool($this->tombo_automatico));

        //-----------------------INCLUI USUARIOS------------------------//
        $this->campoQuebra();

        if ($_POST['biblioteca_usuario']) {
            $this->biblioteca_usuario = unserialize(urldecode($_POST['biblioteca_usuario']));
        }
        if (is_numeric($this->cod_biblioteca) && !$_POST) {
            $obj = new clsPmieducarBibliotecaUsuario($this->cod_biblioteca);
            $registros = $obj->lista($this->cod_biblioteca);
            if ($registros) {
                foreach ($registros as $campo) {
                    $this->biblioteca_usuario['ref_cod_usuario_'][] = $campo['ref_cod_usuario'];
                }
            }
        }
        if ($_POST['ref_cod_usuario']) {
            $this->biblioteca_usuario['ref_cod_usuario_'][] = $_POST['ref_cod_usuario'];
            unset($this->ref_cod_usuario);
        }

        $this->campoOculto('excluir_usuario', '');
        unset($aux);

        if ($this->biblioteca_usuario) {
            foreach ($this->biblioteca_usuario as $key => $campo) {
                if ($campo) {
                    foreach ($campo as $chave => $usuarios) {
                        if ($this->excluir_usuario == $usuarios) {
                            $this->biblioteca_usuario[$chave] = null;
                            $this->excluir_usuario = null;
                        } else {
                            $obj_cod_usuario = new clsPessoa_($usuarios);
                            $obj_usuario_det = $obj_cod_usuario->detalhe();
                            $nome_usuario = $obj_usuario_det['nome'];
                            $this->campoTextoInv("ref_cod_usuario_{$usuarios}", '', $nome_usuario, 30, 255, false, false, false, '', "<a href='#' onclick=\"getElementById('excluir_usuario').value = '{$usuarios}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>");
                            $aux['ref_cod_usuario_'][] = $usuarios;
                        }
                    }
                }
            }
            unset($this->biblioteca_usuario);
            $this->biblioteca_usuario = $aux;
        }

        $this->campoOculto('biblioteca_usuario', serialize($this->biblioteca_usuario));

        $opcoes = [ '' => 'Selecione' ];
        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarUsuario();
            $objTemp->setOrderby('nivel ASC');
            $lista = $objTemp->lista(null, null, $this->ref_cod_instituicao, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $obj_cod_usuario = new clsPessoa_($registro['cod_usuario']);
                    $obj_usuario_det = $obj_cod_usuario->detalhe();
                    $nome_usuario = $obj_usuario_det['nome'];
                    $opcoes["{$registro['cod_usuario']}"] = "{$nome_usuario}";
                }
            }
        }

        $this->campoLista('ref_cod_usuario', 'Usu&aacute;rio', $opcoes, $this->ref_cod_usuario, '', false, '', "<a href='#' onclick=\"getElementById('incluir_usuario').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>", false, false);

        $this->campoOculto('incluir_usuario', '');

        $this->campoQuebra();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(591, $this->pessoa_logada, 3, 'educar_biblioteca_lst.php');
        $obj = new clsPmieducarBiblioteca(null, $this->ref_cod_instituicao, $this->ref_cod_escola, $this->nm_biblioteca, null, null, null, null, null, null, 1, null);
        $this->cod_biblioteca = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_biblioteca = $this->cod_biblioteca;
            //-----------------------CADASTRA USUARIOS------------------------//
            $this->biblioteca_usuario = unserialize(urldecode($this->biblioteca_usuario));
            if ($this->biblioteca_usuario) {
                foreach ($this->biblioteca_usuario as $campo) {
                    for ($i = 0; $i < sizeof($campo) ; $i++) {
                        $obj = new clsPmieducarBibliotecaUsuario($cadastrou, $campo[$i]);
                        $cadastrou2  = $obj->cadastra();
                        if (!$cadastrou2) {
                            $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                            return false;
                        }
                    }
                }
            }
            //-----------------------FIM CADASTRA USUARIOS------------------------//

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_biblioteca_lst.php');
            $this->simpleRedirect('educar_biblioteca_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(591, $this->pessoa_logada, 3, 'educar_biblioteca_lst.php');
        $obj = new clsPmieducarBiblioteca($this->cod_biblioteca, $this->ref_cod_instituicao, $this->ref_cod_escola, $this->nm_biblioteca, null, null, null, null, null, null, 1, null);
        $editou = $obj->edita();
        if ($editou) {
            //-----------------------EDITA USUARIOS------------------------//
            $this->biblioteca_usuario = unserialize(urldecode($this->biblioteca_usuario));
            $obj  = new clsPmieducarBibliotecaUsuario($this->cod_biblioteca);
            $excluiu = $obj->excluirTodos();
            if ($excluiu) {
                if ($this->biblioteca_usuario) {
                    foreach ($this->biblioteca_usuario as $campo) {
                        for ($i = 0; $i < sizeof($campo) ; $i++) {
                            $obj = new clsPmieducarBibliotecaUsuario($this->cod_biblioteca, $campo[$i]);
                            $cadastrou3  = $obj->cadastra();
                            if (!$cadastrou3) {
                                $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

                                return false;
                            }
                        }
                    }
                }
            }
            //-----------------------FIM EDITA USUARIOS------------------------//

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_biblioteca_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(591, $this->pessoa_logada, 3, 'educar_biblioteca_lst.php');

        $obj = new clsPmieducarBiblioteca($this->cod_biblioteca, null, null, null, null, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_biblioteca_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/biblioteca-cad.js');
    }
};
