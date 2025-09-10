<?php

use App\Models\DeficiencyType;
use App\Models\LegacyDeficiency;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\Transtornos;

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_deficiencia;

    public $nm_deficiencia;

    public function Gerar()
    {
        $this->titulo = 'Deficiência ou transtorno - Detalhe';

        $this->cod_deficiencia = $_GET['cod_deficiencia'];

        $registro = LegacyDeficiency::find($this->cod_deficiencia);

        if (!$registro) {
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $deficiencies = Deficiencias::getDescriptiveValues();
        $disorders = Transtornos::getDescriptiveValues();
        $types = DeficiencyType::getDescriptiveValues();

        if ($registro['nm_deficiencia']) {
            $this->addDetalhe(['Descrição', "{$registro['nm_deficiencia']}"]);
        }

        if ($registro['deficiency_type_id']) {
            $this->addDetalhe(['Tipo', $types[$registro['deficiency_type_id']]]);
        }

        if ($registro['deficiencia_educacenso']) {
            $this->addDetalhe(['Deficiência no Educacenso', "{$deficiencies[$registro['deficiencia_educacenso']]}"]);
        }

        if ($registro['transtorno_educacenso']) {
            $this->addDetalhe(['Transtorno no Educacenso', "{$disorders[$registro['transtorno_educacenso']]}"]);
        }

        if ($registro['exigir_laudo_medico']) {
            $this->addDetalhe(['Exigir laudo médico', 'Sim']);
        } else {
            $this->addDetalhe(['Exigir laudo médico', 'Não']);
        }

        if ($registro['desconsidera_regra_diferenciada']) {
            $this->addDetalhe(['Desconsidera regra diferenciada', 'Sim']);
        } else {
            $this->addDetalhe(['Desconsidera regra diferenciada', 'Não']);
        }

        $obj_permissoes = new clsPermissoes;
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
