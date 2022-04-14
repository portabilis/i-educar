<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_cliente;
    public $ref_cod_cliente_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $login;
    public $senha;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Cliente - Detalhe';

        $this->cod_cliente          = $_GET['cod_cliente'];
        $this->ref_cod_cliente_tipo = $_GET['cod_cliente_tipo'];

        if (!(isset($this->cod_cliente) && isset($this->ref_cod_cliente_tipo))) {
            $this->simpleRedirect('educar_definir_cliente_tipo_lst.php');
        }

        $tmp_obj = new clsPmieducarCliente();
        $registro = $tmp_obj->listaCompleta(
            $this->cod_cliente,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            $this->ref_cod_cliente_tipo
        );

        if (! $registro) {
            $this->simpleRedirect('educar_definir_cliente_tipo_lst.php');
        } else {
            foreach ($registro as $cliente) {
                if ($cliente['nome']) {
                    $this->addDetalhe([ 'Cliente', "{$cliente['nome']}"]);
                }
                if ($cliente['nm_biblioteca']) {
                    $this->addDetalhe([ 'Biblioteca', "{$cliente['nm_biblioteca']}"]);
                }
                if ($cliente['nm_tipo']) {
                    $this->addDetalhe([ 'Tipo do Cliente', "{$cliente['nm_tipo']}"]);
                }
                $obj_banco = new clsBanco();
                $sql_unico = "SELECT ref_cod_motivo_suspensao
                                FROM pmieducar.cliente_suspensao
                               WHERE ref_cod_cliente = {$cliente['cod_cliente']}
                                 AND data_liberacao IS NULL
                                 AND EXTRACT ( DAY FROM ( NOW() - data_suspensao ) ) < dias";
                $motivo    = $obj_banco->CampoUnico($sql_unico);
                if (is_numeric($motivo)) {
                    $this->addDetalhe([ 'Status', 'Suspenso' ]);
                    $obj_motivo_suspensao = new clsPmieducarMotivoSuspensao($motivo);
                    $det_motivo_suspensao = $obj_motivo_suspensao->detalhe();
                    $this->addDetalhe([ 'Motivo da Suspensão', "{$det_motivo_suspensao['nm_motivo']}" ]);
                    $this->addDetalhe([ 'Descrição', "{$det_motivo_suspensao['descricao']}" ]);
                } else {
                    $this->addDetalhe([ 'Status', 'Regular' ]);
                }
            }
        }
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(623, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_definir_cliente_tipo_cad.php';
            $this->url_editar = "educar_definir_cliente_tipo_cad.php?cod_cliente={$cliente['cod_cliente']}&cod_cliente_tipo={$cliente['cod_cliente_tipo']}";
        }

        $this->url_cancelar = 'educar_definir_cliente_tipo_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Cliente';
        $this->processoAp = '623';
    }
};
