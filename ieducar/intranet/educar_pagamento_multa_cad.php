<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_pagamento_multa;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $nm_pessoa;
    public $ref_idpes;
    public $valor_pago_bib;
    public $valor_divida;
    public $valor_pagamento;
    public $valor_pendente;
    public $data_cadastro;
    public $ref_cod_biblioteca;
    public $total_divida;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_cliente    = $_GET['cod_cliente'];
        $this->ref_cod_biblioteca = $_GET['cod_biblioteca'];

        if (!$this->ref_cod_cliente || !$this->ref_cod_biblioteca) {
            $this->simpleRedirect('educar_pagamento_multa_lst.php');
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(622, $this->pessoa_logada, 11, 'educar_pagamento_multa_lst.php');

        if (is_numeric($this->ref_cod_cliente)) {
            $obj_total_divida = new clsPmieducarExemplarEmprestimo();
            $total_obj_divida = $obj_total_divida->totalMultaPorBiblioteca($this->ref_cod_cliente, $this->ref_cod_biblioteca, true);
            $this->total_divida = $total_obj_divida[0]['sum'];

            $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
            $lst_exemplar_emprestimo = $obj_exemplar_emprestimo->listaDividaPagamentoCliente($this->ref_cod_cliente, null, $this->ref_cod_cliente_tipo, $this->pessoa_logada, $this->ref_cod_biblioteca, $this->ref_cod_escola, $this->ref_cod_instituicao);
            if ($lst_exemplar_emprestimo) {
                foreach ($lst_exemplar_emprestimo as $registro) {
                    if (is_numeric($registro['valor_multa'])) {
                        $this->valor_divida_bib = $registro['valor_multa'];
                    } else {
                        $this->valor_divida_bib = 0;
                    }
                    if (is_numeric($registro['valor_pago'])) {
                        $this->valor_pago_bib = $registro['valor_pago'];
                    } else {
                        $this->valor_pago_bib = 0;
                    }
                }
            }
            $obj_cliente     = new clsPmieducarCliente($this->ref_cod_cliente);
            $det_cliente     = $obj_cliente->detalhe();
            if ($det_cliente) {
                $this->ref_idpes = $det_cliente['ref_idpes'];
                $obj_pessoa      = new clsPessoa_($this->ref_idpes);
                $det_pessoa      = $obj_pessoa->detalhe();
                if ($det_pessoa) {
                    $this->nm_pessoa = $det_pessoa['nome'];
                }
                $obj_divida = new clsPmieducarExemplarEmprestimo(null, null, null, $this->ref_cod_cliente);
                $det_divida = $obj_divida->clienteDividaTotal($this->ref_idpes, $this->ref_cod_cliente, null, $this->ref_cod_biblioteca);
                if ($det_divida) {
                    foreach ($det_divida as $divida) {
                        $this->valor_divida = $divida['valor_multa'];
                    }
                }
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_pagamento_multa_det.php?cod_cliente={$this->ref_cod_cliente}" : 'educar_pagamento_multa_lst.php';
        $this->nome_url_cancelar = 'Cancelar';
        $this->nome_url_sucesso  = 'Pagar';
        $this->acao_enviar       = 'validaValor()';
        $this->valor_pendente    = $this->total_divida - $this->valor_pago_bib;

        $this->breadcrumb('Pagamento da dívida', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_cliente', $this->ref_cod_cliente);

        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        $this->campoRotulo('nm_cliente', 'Cliente', $this->nm_pessoa);

        $this->campoMonetario('total_divida', 'Total de dívidas', $this->total_divida, 11, 11, false, '', '', 'onChange', true);

        $this->campoMonetario('valor_pago_bib', 'Total pago', $this->valor_pago_bib, 11, 11, false, '', '', 'onChange', true);

        $this->campoMonetario('valor_pendente', 'Total pendente', $this->valor_pendente, 11, 11, false, '', '', 'onChange', true);

        $this->campoMonetario('valor_pagamento', 'Valor do Pagamento', $this->valor_pagamento, 11, 11, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(622, $this->pessoa_logada, 11, 'educar_pagamento_multa_lst.php');

        $this->valor_pagamento = str_replace(',', '.', $this->valor_pagamento);
        $obj = new clsPmieducarPagamentoMulta(null, $this->pessoa_logada, $this->ref_cod_cliente, $this->valor_pagamento, null, $this->ref_cod_biblioteca);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_pagamento_multa_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(622, $this->pessoa_logada, 11, 'educar_pagamento_multa_lst.php');

        $obj = new clsPmieducarPagamentoMulta($this->cod_pagamento_multa, null, $this->ref_cod_cliente, $this->valor_pago, null, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_pagamento_multa_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(622, $this->pessoa_logada, 11, 'educar_pagamento_multa_lst.php');

        $obj = new clsPmieducarPagamentoMulta($this->cod_pagamento_multa, null, $this->ref_cod_cliente, $this->valor_pago, null, $this->ref_cod_biblioteca);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_pagamento_multa_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pagamento-multa-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Pagamento Multa';
        $this->processoAp = '622';
    }
};
