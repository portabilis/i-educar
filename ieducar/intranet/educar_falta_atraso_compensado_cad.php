<?php

use App\Models\LegacyAbsenceDelayCompensate;

return new class extends clsCadastro
{
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

        $this->cod_compensado = $_GET['cod_compensado'];
        $this->ref_cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            )
        );

        if (is_numeric($this->cod_compensado)) {
            $obj = LegacyAbsenceDelayCompensate::find($this->cod_compensado);
            $registro = $obj->getAttributes();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->data_inicio = dataFromPgToBr($this->data_inicio);
                $this->data_fim = dataFromPgToBr($this->data_fim);
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
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
        $this->campoOculto(nome: 'cod_compensado', valor: $this->cod_compensado);
        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);

        // Foreign keys
        $obrigatorio = true;
        $get_instituicao = true;
        $get_escola = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // Data
        $this->campoData(nome: 'data_inicio', campo: 'Data Inicio', valor: $this->data_inicio, obrigatorio: true);
        $this->campoData(nome: 'data_fim', campo: 'Data Fim', valor: $this->data_fim, obrigatorio: true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: "educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}"
        );

        if (is_null($this->ref_cod_escola)) {
            $this->mensagem = 'O campo escola deve ser preenchido.';

            return false;
        }

        // Transforma a data para o formato aceito pelo banco
        $this->data_inicio = dataToBanco($this->data_inicio);
        $this->data_fim = dataToBanco($this->data_fim);

        $obj = new LegacyAbsenceDelayCompensate();
        $obj->ref_cod_servidor = $this->ref_cod_servidor;
        $obj->ref_cod_escola = $this->ref_cod_escola;
        $obj->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
        $obj->data_inicio = $this->data_inicio;
        $obj->data_fim = $this->data_fim;
        $obj->ref_usuario_cad = $this->pessoa_logada;

        if ($obj->save()) {
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
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            )
        );

        // Transforma a data para o formato aceito pelo banco
        $this->data_inicio = dataToBanco($this->data_inicio);
        $this->data_fim = dataToBanco($this->data_fim);

        $obj = LegacyAbsenceDelayCompensate::find($this->cod_compensado);
        $obj->ref_cod_servidor = $this->ref_cod_servidor;
        $obj->ref_cod_escola = $this->ref_cod_escola;
        $obj->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
        $obj->data_inicio = $this->data_inicio;
        $obj->data_fim = $this->data_fim;
        $obj->ref_usuario_exc = $this->pessoa_logada;

        if ($obj->save()) {
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
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: sprintf(
                'educar_falta_atraso_det.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao
            )
        );

        $obj = LegacyAbsenceDelayCompensate::find($this->cod_compensado);

        if ($obj->delete()) {
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
