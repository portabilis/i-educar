<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_reserva;
    public $ref_usuario_libera;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $data_reserva;
    public $data_prevista_disponivel;
    public $data_retirada;
    public $ref_cod_exemplar;

    public function Gerar()
    {
        $this->titulo = 'Reservas - Detalhe';

        $this->cod_reserva=$_GET['cod_reserva'];

        $tmp_obj = new clsPmieducarReservas($this->cod_reserva);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_reservas_lst.php');
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
        if ($registro['data_reserva']) {
            $this->addDetalhe([ 'Data Reserva', dataFromPgToBr($registro['data_reserva'], 'd/m/Y') ]);
        }
        if ($registro['data_prevista_disponivel']) {
            $this->addDetalhe([ 'Data Prevista Disponível', dataFromPgToBr($registro['data_prevista_disponivel'], 'd/m/Y') ]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(609, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_reservas_login_cad.php';
        }

        $this->url_cancelar = 'educar_reservas_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da reserva', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Reservas';
        $this->processoAp = '609';
    }
};
