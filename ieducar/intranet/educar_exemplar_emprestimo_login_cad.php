<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_cod_biblioteca;
    public $login_;
    public $senha_;
    public $ref_cod_cliente;

    public function Inicializar()
    {
        $retorno = 'Novo';

        Session::forget([
            'emprestimo.cod_cliente',
            'emprestimo.ref_cod_biblioteca',
        ]);
        Session::save();
        Session::start();

//      $this->url_cancelar = "educar_exemplar_emprestimo_lst.php";
//      $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    public function Gerar()
    {
        unset($this->login_);
        unset($this->senha_);
        unset($this->ref_cod_biblioteca);

        /*$obj_biblioteca_usuario = new clsPmieducarBibliotecaUsuario();
        $lst_biblioteca_usuario = $obj_biblioteca_usuario->lista(null, $this->pessoa_logada);

        $opcoes = array( "" => "Selecione" );
//      $bibliotecas_usuario = "biblioteca_usuario = new Array();\n";
        if( is_array( $lst_biblioteca_usuario ) && count( $lst_biblioteca_usuario ) )
        {
            foreach ( $lst_biblioteca_usuario AS $biblioteca )
            {
                $obj_biblioteca = new clsPmieducarBiblioteca( $biblioteca['ref_cod_biblioteca'] );
                $det_biblioteca = $obj_biblioteca->detalhe();
//              $nm_biblioteca = $det_biblioteca['nm_biblioteca'];
//              $requisita_senha = $det_biblioteca['requisita_senha'];
                $opcoes["{$biblioteca['ref_cod_biblioteca']}"] = "{$det_biblioteca['nm_biblioteca']}";

//              $bibliotecas_usuario .= "biblioteca_usuario[biblioteca_usuario.length] = new Array({$biblioteca["ref_cod_biblioteca"]},{$requisita_senha});\n";
            }
        }
//      echo "<script>{$bibliotecas_usuario}</script>";
        $this->campoLista( "ref_cod_biblioteca", "Biblioteca", $opcoes, $this->ref_cod_biblioteca);
*/

        $get_escola     = 1;
        $escola_obrigatorio = false;
        $get_biblioteca = 1;
        $instituicao_obrigatorio = true;
        $biblioteca_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');
        // text
        $this->campoNumero('login_', 'Login', $this->login_, 9, 9, false);
        $this->campoSenha('senha_', 'Senha', $this->senha_, false);

        $this->campoOculto('requisita_senha', '0');

        $this->campoTexto('nm_cliente1', 'Cliente', $this->nm_cliente, 30, 255, false, false, false, '', "<img border=\"0\" class=\"btn\" onclick=\"pesquisa_cliente();\" id=\"ref_cod_cliente_lupa\" name=\"ref_cod_cliente_lupa\" src=\"imagens/lupaT.png\"\/>", '', '', true);
        $this->campoOculto('ref_cod_cliente', $this->ref_cod_cliente);
        $this->botao_enviar = false;
        $this->array_botao = ['Continuar', 'Cancelar'];
        $this->array_botao_url_script = ['acao2();','go(\'educar_exemplar_emprestimo_lst.php\');'];
        $this->acao_executa_submit = false;
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(610, $this->pessoa_logada, 11, 'educar_exemplar_emprestimo_lst.php');

        if ($this->ref_cod_cliente) {
            Session::put([
                'emprestimo.cod_cliente' => $this->ref_cod_cliente,
                'emprestimo.ref_cod_biblioteca' => $this->ref_cod_biblioteca,
            ]);

            throw new HttpResponseException(
                new RedirectResponse(
                    URL::to('intranet/educar_exemplar_emprestimo_cad.php')
                )
            );
        } elseif ($this->login_ && $this->senha_) {
            $this->senha_ = md5($this->senha_.'asnk@#*&(23');

            $obj_cliente = new clsPmieducarCliente();
            $lst_cliente = $obj_cliente->lista(null, null, null, null, $this->login_, $this->senha_, null, null, null, null, 1);

            if (is_array($lst_cliente) && count($lst_cliente)) {
                $cliente = array_shift($lst_cliente);
                $cod_cliente = $cliente['cod_cliente'];

                $obj_cliente_tipo_cliente = new clsPmieducarClienteTipoCliente();
                $lst_cliente_tipo_cliente = $obj_cliente_tipo_cliente->lista(null, $cod_cliente);

                if (is_array($lst_cliente_tipo_cliente) && count($lst_cliente_tipo_cliente)) {
                    foreach ($lst_cliente_tipo_cliente as $cliente_tipo) {
                        // tipo do cliente
                        $cod_cliente_tipo = $cliente_tipo['ref_cod_cliente_tipo'];

                        $obj_cliente_tipo = new clsPmieducarClienteTipo($cod_cliente_tipo);
                        $det_cliente_tipo = $obj_cliente_tipo->detalhe();
                        $biblioteca_cliente = $det_cliente_tipo['ref_cod_biblioteca'];

                        if ($this->ref_cod_biblioteca == $biblioteca_cliente) {
                            Session::put([
                                'emprestimo.cod_cliente' => $cod_cliente,
                                'emprestimo.ref_cod_biblioteca' => $this->ref_cod_biblioteca,
                            ]);

                            throw new HttpResponseException(
                                new RedirectResponse(
                                    URL::to('intranet/educar_exemplar_emprestimo_cad.php')
                                )
                            );
                        }
                    }
                    echo '<script> alert(\'Cliente não pertence a biblioteca escolhida!\'); </script>';

                    return true;
                }
            } else {
                $this->mensagem = 'Login e/ou Senha incorreto(s).<br>';

                return false;
            }
        } else {
            $this->mensagem = 'Preencha o(s) campo(s) corretamente.<br>';

            return false;
        }
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-exemplar-emprestimo-login-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Empréstimo';
        $this->processoAp = '610';
    }
};
