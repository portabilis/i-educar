<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_emprestimo;
    public $ref_usuario_devolucao;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $ref_cod_exemplar;
    public $data_retirada;
    public $data_devolucao;
    public $valor_multa;

    public function Gerar()
    {
        $this->titulo = 'Exemplar Empr&eacute;stimo - Detalhe';

        $this->cod_emprestimo=$_GET['cod_emprestimo'];

        $tmp_obj = new clsPmieducarExemplarEmprestimo($this->cod_emprestimo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_exemplar_emprestimo_lst.php');
        }

        $obj_ref_cod_exemplar = new clsPmieducarExemplar($registro['ref_cod_exemplar']);
        $det_ref_cod_exemplar = $obj_ref_cod_exemplar->detalhe();

        $acervo = $det_ref_cod_exemplar['ref_cod_acervo'];
        $obj_acervo = new clsPmieducarAcervo($acervo);
        $det_acervo = $obj_acervo->detalhe();
        $titulo_exemplar = $det_acervo['titulo'];

        $obj_cliente = new clsPmieducarCliente($registro['ref_cod_cliente']);
        $det_cliente = $obj_cliente->detalhe();
        $ref_idpes = $det_cliente['ref_idpes'];
        $obj_pessoa = new clsPessoa_($ref_idpes);
        $det_pessoa = $obj_pessoa->detalhe();
        $registro['ref_cod_cliente'] = $det_pessoa['nome'];

        if ($registro['ref_cod_cliente']) {
            $this->addDetalhe([ 'Cliente', "{$registro['ref_cod_cliente']}"]);
        }
        if ($titulo_exemplar) {
            $this->addDetalhe([ 'Obra', "{$titulo_exemplar}"]);
        }
        if ($registro['ref_cod_exemplar']) {
            $this->addDetalhe([ 'Tombo', "{$registro['ref_cod_exemplar']}"]);
        }
        if ($registro['data_retirada']) {
            $this->addDetalhe([ 'Data Retirada', dataFromPgToBr($registro['data_retirada'], 'd/m/Y') ]);
        }
        if ($registro['valor_multa']) {
            $this->addDetalhe([ 'Valor Multa', "{$registro['valor_multa']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(610, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_exemplar_emprestimo_login_cad.php';
        }

        $this->url_cancelar = 'educar_exemplar_emprestimo_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Exemplar Empr&eacute;stimo';
        $this->processoAp = '610';
    }
};
