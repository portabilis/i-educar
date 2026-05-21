<?php

use App\Models\RegionalType;

return new class extends clsDetalhe
{
    public $titulo;

    public $id;

    public $name;

    public $description;

    public function Gerar()
    {
        $this->titulo = 'Regional - Detalhe';

        $registro = RegionalType::find(request()->integer('id'))?->getAttributes();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_regional_lst.php');
        }

        if ($registro['id']) {
            $this->addDetalhe(detalhe: ['Código Regional', "{$registro['id']}"]);
        }
        if ($registro['name']) {
            $this->addDetalhe(detalhe: ['Nome', "{$registro['name']}"]);
        }
        if ($registro['description']) {
            $this->addDetalhe(detalhe: ['Descrição', nl2br(string: "{$registro['description']}")]);
        }

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 5841, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_regional_cad.php';
            $this->url_editar = "educar_regional_cad.php?id={$registro['id']}";
        }

        $this->url_cancelar = 'educar_regional_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da Regional', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Regionais';
        $this->processoAp = '5841';
    }
};
