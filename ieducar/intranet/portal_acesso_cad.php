<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_acesso;
    public $data_hora;
    public $ip_externo;
    public $ip_interno;
    public $cod_pessoa;
    public $obs;
    public $sucesso;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_acesso=$_GET['cod_acesso'];

        if (is_numeric($this->cod_acesso)) {
            $obj = new clsPortalAcesso($this->cod_acesso);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_hora = dataFromPgToBr($this->data_hora);

                $this->fexcluir = true;

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "portal_acesso_det.php?cod_acesso={$registro['cod_acesso']}" : 'portal_acesso_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_acesso', $this->cod_acesso);

        // foreign keys

        // text
        $this->campoTexto('ip_externo', 'Ip Externo', $this->ip_externo, 30, 255, true);
        $this->campoTexto('ip_interno', 'Ip Interno', $this->ip_interno, 30, 255, true);
        $this->campoNumero('cod_pessoa', 'Pessoa', $this->cod_pessoa, 15, 255, true);
        $this->campoMemo('obs', 'Obs', $this->obs, 60, 10, false);

        // data
        $this->campoData('data_hora', 'Data Hora', $this->data_hora, true);

        // time

        // bool
        $this->campoBoolLista('sucesso', 'Sucesso', $this->sucesso);
        //$this->campoCheck( "sucesso", "Sucesso", ( $this->sucesso == 't' ) );
    }

    public function Novo()
    {
        $obj = new clsPortalAcesso($this->cod_acesso, $this->data_hora, $this->ip_externo, $this->ip_interno, $this->cod_pessoa, $this->obs, $this->sucesso);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('portal_acesso_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPortalAcesso($this->cod_acesso, $this->data_hora, $this->ip_externo, $this->ip_interno, $this->cod_pessoa, $this->obs, $this->sucesso);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('portal_acesso_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPortalAcesso($this->cod_acesso, $this->data_hora, $this->ip_externo, $this->ip_interno, $this->cod_pessoa, $this->obs, $this->sucesso);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('portal_acesso_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Acesso';
        $this->processoAp = '666';
    }
};
