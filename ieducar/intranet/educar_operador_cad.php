<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_operador;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nome;
    public $valor;
    public $fim_sentenca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_operador=$_GET['cod_operador'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 589, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0, str_pagina_redirecionar: 'educar_operador_lst.php', super_usuario: true);

        if (is_numeric(value: $this->cod_operador)) {
            $obj = new clsPmieducarOperador(cod_operador: $this->cod_operador);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr(data_original: $this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr(data_original: $this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 589, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0, super_usuario: true)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_operador_det.php?cod_operador={$registro['cod_operador']}" : 'educar_operador_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'cod_operador', valor: $this->cod_operador);
        $this->campoTexto(nome: 'nome', campo: 'Nome', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'valor', campo: 'Valor', valor: $this->valor, colunas: 60, linhas: 10, obrigatorio: true);
        $opcoes = [ 'Não', 'Sim' ];
        $this->campoLista(nome: 'fim_sentenca', campo: 'Fim Sentenca', valor: $opcoes, default: $this->fim_sentenca);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 589, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0, str_pagina_redirecionar: 'educar_operador_lst.php', super_usuario: true);

        $obj = new clsPmieducarOperador(cod_operador: $this->cod_operador, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, nome: $this->nome, valor: $this->valor, fim_sentenca: $this->fim_sentenca, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_nivel_ensino_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 589, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0, str_pagina_redirecionar: 'educar_operador_lst.php', super_usuario: true);

        $obj = new clsPmieducarOperador(cod_operador: $this->cod_operador, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, nome: $this->nome, valor: $this->valor, fim_sentenca: $this->fim_sentenca, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: $this->ativo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_operador_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 589, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0, str_pagina_redirecionar: 'educar_operador_lst.php', super_usuario: true);

        $obj = new clsPmieducarOperador(cod_operador: $this->cod_operador, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, nome: $this->nome, valor: $this->valor, fim_sentenca: $this->fim_sentenca, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_operador_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Operador';
        $this->processoAp = '589';
    }
};
