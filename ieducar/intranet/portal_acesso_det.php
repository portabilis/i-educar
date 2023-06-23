<?php

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_acesso;

    public $data_hora;

    public $ip_externo;

    public $ip_interno;

    public $cod_pessoa;

    public $obs;

    public $sucesso;

    public function Gerar()
    {
        $this->titulo = 'Acesso - Detalhe';

        $this->cod_acesso = $_GET['cod_acesso'];

        $tmp_obj = new clsPortalAcesso(cod_acesso: $this->cod_acesso);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect(url: 'portal_acesso_lst.php');
        }

        if ($registro['cod_acesso']) {
            $this->addDetalhe(detalhe: ['Acesso', "{$registro['cod_acesso']}"]);
        }
        if ($registro['data_hora']) {
            $this->addDetalhe(detalhe: ['Data Hora', dataFromPgToBr(data_original: $registro['data_hora'], formatacao: 'd/m/Y H:i')]);
        }
        if ($registro['ip_externo']) {
            $this->addDetalhe(detalhe: ['Ip Externo', "{$registro['ip_externo']}"]);
        }
        if ($registro['ip_interno']) {
            $this->addDetalhe(detalhe: ['Ip Interno', "{$registro['ip_interno']}"]);
        }
        if ($registro['cod_pessoa']) {
            $this->addDetalhe(detalhe: ['Pessoa', "{$registro['cod_pessoa']}"]);
        }
        if ($registro['obs']) {
            $this->addDetalhe(detalhe: ['Obs', "{$registro['obs']}"]);
        }
        if (!is_null(value: $registro['sucesso'])) {
            $this->addDetalhe(detalhe: ['Sucesso', dbBool(val: $registro['sucesso']) ? 'Sim' : 'Não']);
        }

        $this->url_novo = 'portal_acesso_cad.php';
        $this->url_editar = "portal_acesso_cad.php?cod_acesso={$registro['cod_acesso']}";

        $this->url_cancelar = 'portal_acesso_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Acesso';
        $this->processoAp = '666';
    }
};
