<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_cliente;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_biblioteca;
    public $ref_cod_biblioteca_atual;
    public $ref_cod_cliente_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $login_;
    public $senha_;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $del_cod_cliente;
    public $del_cod_cliente_tipo;
    public $observacoes;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_cliente   = $_GET['cod_cliente'];
        $this->ref_cod_biblioteca = $_GET['ref_cod_biblioteca'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');
        if (is_numeric($this->cod_cliente) && is_numeric($this->ref_cod_biblioteca)) {
            $obj = new clsPmieducarCliente($this->cod_cliente);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $this->login_ = $this->login;
                $this->senha_ = $this->senha;

                $observacoes =  $this->observacoes;

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(603, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_cliente_det.php?cod_cliente={$registro['cod_cliente']}&ref_cod_biblioteca={$this->ref_cod_biblioteca}" : 'educar_cliente_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_cliente', $this->cod_cliente);
        $this->campoOculto('requisita_senha', '0');
        $opcoes = [ '' => 'Pesquise a pessoa clicando na lupa ao lado' ];
        if ($this->ref_idpes) {
            $objTemp = new clsPessoaFisica($this->ref_idpes);
            $detalhe = $objTemp->detalhe();
            $opcoes["{$detalhe['idpes']}"] = $detalhe['nome'];
        }

        // Caso o cliente não exista, exibe um campo de pesquisa, senão, mostra um rótulo
        if (!$this->cod_cliente) {
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(0);
            $parametros->adicionaCampoSelect('ref_idpes', 'idpes', 'nome');
            $parametros->setPessoa('F');
            $parametros->setPessoaCPF('N');
            $parametros->setCodSistema(null);
            $parametros->setPessoaNovo('S');
            $parametros->setPessoaTela('frame');

            $dados = [
        'nome' => 'Cliente',
        'campo' => '',
        'valor' => [null => 'Para procurar, clique na lupa ao lado.'],
        'default' => null,
        'acao' => '',
        'descricao' => '',
        'caminho' => 'pesquisa_pessoa_lst.php',
        'descricao2' => '',
        'flag' => null,
        'pag_cadastro' => null,
        'disabled' => '',
        'div' => false,
        'serializedcampos' => $parametros->serializaCampos(),
        'duplo' => false,
        'obrigatorio' => true
      ];
            $this->setOptionsListaPesquisa('ref_idpes', $dados);
        } else {
            $this->campoTexto('codigo', 'Código', $this->cod_cliente, 9, 9, null, null, null, null, null, null, null, true);
            $this->campoOculto('ref_idpes', $this->ref_idpes);
            $this->campoRotulo('nm_cliente', 'Cliente', $detalhe['nome']);
        }

        // text
        $this->campoNumero('login', 'Login', $this->login_, 9, 9, false);
        $this->campoSenha('senha', 'Senha', $this->senha_, false);

        if ($this->cod_cliente && $this->ref_cod_biblioteca) {
            $db = new clsBanco();

            // Cria campo oculto com o ID da biblioteca atual ao qual usuário está cadastrado
            $this->ref_cod_biblioteca_atual = $this->ref_cod_biblioteca;
            $this->campoOculto('ref_cod_biblioteca_atual', $this->ref_cod_biblioteca_atual);

            // obtem o codigo do tipo de cliente, apartir da tabela cliente_tipo_cliente
            $this->ref_cod_cliente_tipo = $db->CampoUnico("SELECT ref_cod_cliente_tipo FROM pmieducar.cliente_tipo_cliente WHERE ref_cod_cliente = '$this->cod_cliente'");
        }

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca', 'bibliotecaTipoCliente']);

        $obs_options = [
      'required'    => false,
      'label'       => 'Observações:',
      'cols'        => 35,
      'placeholder' => '',
      'max_length'  => 255,
      'value'       => $this->observacoes
    ];
        $this->inputsHelper()->textArea('observacoes', $obs_options);
    }

    /**
     * Sobrescrita do método clsCadastro::Novo.
     *
     * Insere novo registro nas tabelas pmieducar.cliente e pmieducar.cliente_tipo_cliente.
     */
    public function Novo()
    {
        $senha = md5($this->senha . 'asnk@#*&(23');

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

        $obj = new clsPmieducarCliente();
        $lista = $obj->lista(null, null, null, $this->ref_idpes, null, null, null, null, null, null, 1);
        if (!$lista) {
            $obj_cliente = new clsPmieducarCliente();
            $lst_cliente = $obj_cliente->lista(null, null, null, null, $this->login);

            if ($lst_cliente && $this->login != '') {
                $this->mensagem = 'Este login já está sendo utilizado por outra pessoa!<br>';
            } else {
                $obj = new clsPmieducarCliente(
                    $this->cod_cliente,
                    null,
                    $this->pessoa_logada,
                    $this->ref_idpes,
                    $this->login,
                    $senha,
                    $this->data_cadastro,
                    $this->data_exclusao,
                    1,
                    $this->observacoes
                );

                $this->cod_cliente = $cadastrou = $obj->cadastra();
                if ($cadastrou) {
                    $obj->cod_cliente = $this->cod_cliente;

                    $this->cod_cliente = $cadastrou;
                    $obj_cliente_tipo = new clsPmieducarClienteTipoCliente(
                        $this->ref_cod_cliente_tipo,
                        $this->cod_cliente,
                        null,
                        null,
                        $this->pessoa_logada,
                        $this->pessoa_logada,
                        1
                    );

                    if ($obj_cliente_tipo->existeCliente()) {
                        if ($obj_cliente_tipo->trocaTipo()) {
                            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                            $this->simpleRedirect('educar_definir_cliente_tipo_lst.php');
                        }
                    } else {
                        $obj_cliente_tipo = new clsPmieducarClienteTipoCliente(
                            $this->ref_cod_cliente_tipo,
                            $this->cod_cliente,
                            null,
                            null,
                            $this->pessoa_logada,
                            null,
                            1,
                            $this->ref_cod_biblioteca
                        );

                        if ($obj_cliente_tipo->cadastra()) {
                            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                            $this->simpleRedirect('educar_cliente_lst.php');
                        }
                    }
                }

                $this->mensagem = 'Cadastro não realizado.<br>';

                return false;
            }
        } else {
            $obj = new clsPmieducarCliente();
            $registro = $obj->lista(null, null, null, $this->ref_idpes, null, null, null, null, null, null, 1);
            if ($registro) {
                $this->cod_cliente = $registro[0]['cod_cliente'];
            }

            $this->ativo = 1;

            $sql = "SELECT COUNT(0) FROM pmieducar.cliente_tipo_cliente WHERE ref_cod_cliente = {$this->cod_cliente}
        AND ref_cod_biblioteca = {$this->ref_cod_biblioteca} AND ativo = 1";

            $db = new clsBanco();
            $possui_biblio = $db->CampoUnico($sql);
            if ($possui_biblio == 0) {
                $obj_cliente_tipo_cliente = new clsPmieducarClienteTipoCliente(
                    $this->ref_cod_cliente_tipo,
                    $this->cod_cliente,
                    null,
                    null,
                    $this->pessoa_logada,
                    null,
                    null,
                    $this->ref_cod_biblioteca
                );

                if (!$obj_cliente_tipo_cliente->cadastra()) {
                    $this->mensagem = 'Não cadastrou';

                    return false;
                } else {
                    $this->simpleRedirect('educar_cliente_lst.php');
                }
            } else {
                //$this->Editar();
                $this->mensagem = 'O cliente já está cadastrado!<br>';
            }
        }
    }

    /**
     * Sobrescrita do método clsCadastro::Editar.
     *
     * Verifica:
     * - Se usuário tem permissão de edição
     * - Se usuário existe na biblioteca atual
     *   - Se existir, troca pela biblioteca escolhida na interface
     *   - Senão, cadastra como cliente da biblioteca
     */
    public function Editar()
    {
        $senha = md5($this->senha . 'asnk@#*&(23');
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

        $obj = new clsPmieducarCliente(
            $this->cod_cliente,
            $this->pessoa_logada,
            $this->pessoa_logada,
            $this->ref_idpes,
            $this->login,
            $senha,
            $this->data_cadastro,
            $this->data_exclusao,
            $this->ativo,
            $this->observacoes
        );

        $editou = $obj->edita();

        if ($editou) {
            // Cria objeto clsPemieducarClienteTipoCliente configurando atributos usados nas queries
            $obj_cliente_tipo = new clsPmieducarClienteTipoCliente(
                $this->ref_cod_cliente_tipo,
                $this->cod_cliente,
                null,
                null,
                $this->pessoa_logada,
                $this->pessoa_logada,
                1,
                $this->ref_cod_biblioteca
            );

            // clsPmieducarClienteTipoCliente::trocaTipoBiblioteca recebe o valor antigo para usar
            // na cláusula WHERE
            if ($obj_cliente_tipo->existeClienteBiblioteca($_POST['ref_cod_biblioteca_atual'])) {
                if ($obj_cliente_tipo->trocaTipoBiblioteca($_POST['ref_cod_biblioteca_atual'])) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    $this->simpleRedirect('educar_cliente_lst.php');
                }
            } else {
                $obj_cliente_tipo = new clsPmieducarClienteTipoCliente(
                    $this->ref_cod_cliente_tipo,
                    $this->cod_cliente,
                    null,
                    null,
                    $this->pessoa_logada,
                    null,
                    1,
                    $this->ref_cod_biblioteca
                );

                if ($obj_cliente_tipo->cadastra()) {
                    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                    $this->simpleRedirect('educar_cliente_lst.php');
                }
            }
        }

        $this->mensagem = 'Edição não realizada.<br>';
        die();
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

        $obj = new clsPmieducarCliente($this->cod_cliente, $this->pessoa_logada, null, $this->ref_idpes, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-cliente-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Cliente';
        $this->processoAp = '603';
    }
};
