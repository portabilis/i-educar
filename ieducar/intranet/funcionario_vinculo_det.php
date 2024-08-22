<?php

use App\Models\LegacyBondType;

return new class extends clsDetalhe
{
    public $cod_usuario;

    public function Gerar()
    {
        $this->cod_usuario = $this->pessoa_logada;

        $this->titulo = 'Detalhe do Vínculo';

        $legacyBondType = LegacyBondType::findOrFail(request('cod_func'));

        $this->addDetalhe(detalhe: ['Nome', $legacyBondType->nm_vinculo]);
        $this->addDetalhe(detalhe: ['Abreviatura', $legacyBondType->abreviatura]);

        $this->url_novo = 'funcionario_vinculo_cad.php';
        $this->url_editar = 'funcionario_vinculo_cad.php?cod_funcionario_vinculo=' . request('cod_func');
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
