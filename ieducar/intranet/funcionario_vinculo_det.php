<?php

return new class extends clsDetalhe
{
    public $cod_usuario;

    public function Gerar()
    {
        $this->cod_usuario = $this->pessoa_logada;

        $this->titulo = 'Detalhe do Vínculo';

        $cod_func = $_GET['cod_func'] ?? null;

        $db = new clsBanco();

        $db->Consulta(consulta: "SELECT nm_vinculo, abreviatura FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo = '$cod_func'");

        if ($db->ProximoRegistro()) {
            [$nm_vinculo, $abreviatura] = $db->Tupla();
            $this->addDetalhe(detalhe: ['Nome', $nm_vinculo]);
            $this->addDetalhe(detalhe: ['Abreviatura', $abreviatura]);
        }

        $this->url_novo = 'funcionario_vinculo_cad.php';
        $this->url_editar = "funcionario_vinculo_cad.php?cod_funcionario_vinculo={$cod_func}";
        $this->url_cancelar = 'funcionario_vinculo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do vínculo');
    }

    public function Formular()
    {
        $this->title = 'Vínculo Funcionários';
        $this->processoAp = '190';
    }
};
