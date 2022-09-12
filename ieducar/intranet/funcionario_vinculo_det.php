<?php

return new class extends clsDetalhe {
    public $cod_usuario;

    public function Gerar()
    {
        $this->cod_usuario = $this->pessoa_logada;

        $this->titulo = 'Detalhe do Vínculo';

        $cod_func = $_GET['cod_func'] ?? null;

        $db = new clsBanco();

        $db->Consulta("SELECT nm_vinculo, abreviatura FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo = '$cod_func'");

        if ($db->ProximoRegistro()) {
            list($nm_vinculo, $abreviatura) = $db->Tupla();
            $this->addDetalhe(['Nome', $nm_vinculo]);
            $this->addDetalhe(['Abreviatura', $abreviatura]);
        }

        $this->url_novo = 'funcionario_vinculo_cad.php';
        $this->url_editar = "funcionario_vinculo_cad.php?cod_funcionario_vinculo={$cod_func}";
        $this->url_cancelar = 'funcionario_vinculo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do vínculo');
    }

    public function Formular()
    {
        $this->title = 'Vínculo Funcionários';
        $this->processoAp = '190';
    }
};
