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

        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';
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
            echo "<script>
                        parent.document.getElementById('ref_cod_operador').options[parent.document.getElementById('ref_cod_operador').options.length] = new Option('$this->nome', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_operador').value = '$cadastrou';
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function Formular()
    {
        $this->title = 'Operador';
        $this->processoAp = '589';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
