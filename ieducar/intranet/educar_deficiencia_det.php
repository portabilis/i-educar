<?php

use App\Models\LegacyDeficiency;

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_deficiencia;

    public $nm_deficiencia;

    public function Gerar()
    {
        $this->titulo = 'Deficiência ou transtorno - Detalhe';

        $this->cod_deficiencia = $_GET['cod_deficiencia'];

        $registro = LegacyDeficiency::find($this->cod_deficiencia, ['cod_deficiencia', 'nm_deficiencia']);

        if (!$registro) {
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        if ($registro['nm_deficiencia']) {
            $this->addDetalhe(['Deficiência ou transtorno', "{$registro['nm_deficiencia']}"]);
        }
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 631, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_deficiencia_cad.php';
            $this->url_editar = "educar_deficiencia_cad.php?cod_deficiencia={$registro['cod_deficiencia']}";
        }
        $this->url_cancelar = 'educar_deficiencia_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da deficiência ou transtorno', breadcrumbs: [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Deficiência';
        $this->processoAp = '631';
    }
};
