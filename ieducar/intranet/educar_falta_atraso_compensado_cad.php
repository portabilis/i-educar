<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $cod_compensado;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $ref_cod_servidor;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_inicio;
    public $data_fim;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_compensado      = $_GET['cod_compensado'];
        $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
        $this->ref_cod_escola      = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            )
        );

        if (is_numeric($this->cod_compensado)) {
            $obj = new clsPmieducarFaltaAtrasoCompensado($this->cod_compensado);
            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->data_inicio   = dataFromPgToBr($this->data_inicio);
                $this->data_fim      = dataFromPgToBr($this->data_fim);
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = sprintf(
            'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_escola,
            $this->ref_cod_instituicao
        );

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // Primary keys
        $this->campoOculto('cod_compensado', $this->cod_compensado);
        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

        // Foreign keys
        $obrigatorio     = true;
        $get_instituicao = true;
        $get_escola      = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // Data
        $this->campoData('data_inicio', 'Data Inicio', $this->data_inicio, true);
        $this->campoData('data_fim', 'Data Fim', $this->data_fim, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}"
        );

        // Transforma a data para o formato aceito pelo banco
        $this->data_inicio = dataToBanco($this->data_inicio);
        $this->data_fim    = dataToBanco($this->data_fim);

        $obj = new clsPmieducarFaltaAtrasoCompensado(
            null,
            $this->ref_cod_escola,
            $this->ref_cod_instituicao,
            $this->ref_cod_servidor,
            $this->pessoa_logada,
            $this->pessoa_logada,
            $this->data_inicio,
            $this->data_fim,
            null,
            null,
            1
        );

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect(sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            ));
        }

        $this->mensagem = 'Cadastro não realizado.<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            )
        );

        // Transforma a data para o formato aceito pelo banco
        $this->data_inicio = dataToBanco($this->data_inicio);
        $this->data_fim    = dataToBanco($this->data_fim);

        $obj = new clsPmieducarFaltaAtrasoCompensado(
            $this->cod_compensado,
            $this->ref_cod_escola,
            $this->ref_cod_instituicao,
            $this->ref_cod_servidor,
            $this->pessoa_logada,
            $this->pessoa_logada,
            $this->data_inicio,
            $this->data_fim,
            $this->data_cadastro,
            $this->data_exclusao,
            $this->ativo
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect(sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            ));
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            635,
            $this->pessoa_logada,
            7,
            sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            )
        );

        // Transforma a data para o formato aceito pelo banco
        $this->data_inicio = dataToBanco($this->data_inicio);
        $this->data_fim    = dataToBanco($this->data_fim);

        $obj = new clsPmieducarFaltaAtrasoCompensado(
            $this->cod_compensado,
            $this->ref_cod_escola,
            $this->ref_cod_instituicao,
            $this->ref_cod_servidor,
            $this->pessoa_logada,
            $this->pessoa_logada,
            $this->data_inicio,
            $this->data_fim,
            $this->data_cadastro,
            $this->data_exclusao,
            0
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect("educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}");
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Falta Atraso Compensado';
        $this->processoAp = 635;
    }
};
