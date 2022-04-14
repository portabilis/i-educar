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
    public $suspenso;
    public $pessoa_logada;

    public $ref_cod_biblioteca;

    public function Gerar()
    {
        $this->titulo = 'Cliente - Detalhe';

        $this->cod_cliente          = $_GET['cod_cliente'];
        $this->ref_cod_biblioteca   = $_GET['ref_cod_biblioteca'];

        $tmp_obj = new clsPmieducarCliente($this->cod_cliente);
        $registro = $tmp_obj->lista($this->cod_cliente, null, null, null, null, null, null, null, null, null, null, null, null, $this->ref_cod_biblioteca);

        if (! $registro) {
            $this->simpleRedirect('educar_cliente_lst.php');
        } else {
            foreach ($registro as $cliente) {
                if ($cliente['nome']) {
                    $this->addDetalhe([ 'Cliente', "{$cliente['nome']}"]);
                }
                if ($cliente['login']) {
                    $this->addDetalhe([ 'Login', "{$cliente['login']}"]);
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
                    $this->suspenso = $motivo;
                    $this->addDetalhe([ 'Motivo da Suspensão', "{$det_motivo_suspensao['nm_motivo']}" ]);
                    $this->addDetalhe([ 'Descrição', "{$det_motivo_suspensao['descricao']}" ]);
                } else {
                    $this->addDetalhe([ 'Status', 'Regular' ]);
                }

                $tipo_cliente = $obj_banco->CampoUnico("SELECT nm_tipo FROM pmieducar.cliente_tipo WHERE ref_cod_biblioteca IN (SELECT ref_cod_biblioteca FROM pmieducar.biblioteca_usuario WHERE ref_cod_usuario = '$this->pessoa_logada') AND cod_cliente_tipo = (SELECT ref_cod_cliente_tipo FROM pmieducar.cliente_tipo_cliente WHERE ref_cod_cliente = '$this->cod_cliente'  AND ref_cod_biblioteca = '$this->ref_cod_biblioteca')");
                if (is_string($tipo_cliente)) {
                    $this->addDetalhe(['Tipo', $tipo_cliente]);
                }
            }
        }
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11)) {
            $this->url_novo        = 'educar_cliente_cad.php';
            $this->url_editar      = "educar_cliente_cad.php?cod_cliente={$cliente['cod_cliente']}&ref_cod_biblioteca={$this->ref_cod_biblioteca}";
            if (is_numeric($this->suspenso)) {
                $this->array_botao     = [ 'Liberar' ];
                $this->array_botao_url = [ "educar_define_status_cliente_cad.php?cod_cliente={$cliente['cod_cliente']}&ref_cod_biblioteca={$this->ref_cod_biblioteca}&status=liberar" ];
            } else {
                $this->array_botao     = [ 'Suspender' ];
                $this->array_botao_url = [ "educar_define_status_cliente_cad.php?cod_cliente={$cliente['cod_cliente']}&ref_cod_biblioteca={$this->ref_cod_biblioteca}&status=suspender" ];
            }
        }

        $this->url_cancelar = 'educar_cliente_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Cliente';
        $this->processoAp = '603';
    }
};
