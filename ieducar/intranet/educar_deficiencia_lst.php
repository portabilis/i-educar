<?php

use App\Models\DeficiencyType;
use App\Models\LegacyDeficiency;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\Transtornos;
use iEducar\Support\View\SelectOptions;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_deficiencia;

    public $nm_deficiencia;

    public function Gerar()
    {
        $this->titulo = 'Deficiência e transtorno - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Descrição',
            'Tipo',
            'Educacenso',
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto(nome: 'nm_deficiencia', campo: 'Deficiência e transtorno', valor: $this->nm_deficiencia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: false);

        $options = [
            'label' => 'Tipo',
            'resources' => SelectOptions::deficiencyTypes(),
            'value' => request()->integer('deficiency_type_id'),
        ];

        $this->inputsHelper()->select(attrName: 'deficiency_type_id', inputOptions: $options);

        // Paginador
        $this->limite = 20;
        $lista = LegacyDeficiency::query()
            ->filter([
                'name' => $this->nm_deficiencia,
            ])
            ->when(
                request()->integer('deficiency_type_id') !== 0,
                function ($query) {
                    $query->where('deficiency_type_id', request()->integer('deficiency_type_id'));
                }
            )
            ->orderBy('nm_deficiencia')
            ->paginate(
                perPage: $this->limite,
                pageName: 'pagina_' . $this->nome
            );
        $total = $lista->total();

        // monta a lista
        if ($lista->isNotEmpty()) {
            $deficiencies = Deficiencias::getDescriptiveValues();
            $disorders = Transtornos::getDescriptiveValues();
            $types = DeficiencyType::getDescriptiveValues();

            foreach ($lista as $registro) {
                // muda os campos data

                // pega detalhes de foreign_keys

                $educacenso = '';

                if ($registro['deficiency_type_id'] === 1) {
                    $educacenso = $deficiencies[$registro['deficiencia_educacenso']];
                }

                if ($registro['deficiency_type_id'] === 2) {
                    $educacenso = $disorders[$registro['transtorno_educacenso']];
                }

                $this->addLinhas([
                    "<a href=\"educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}\">{$registro['nm_deficiencia']}</a>",
                    "<a href=\"educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}\">{$types[$registro['deficiency_type_id']]}</a>",
                    "<a href=\"educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}\">{$educacenso}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_deficiencia_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 631, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_deficiencia_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de deficiência e transtorno', breadcrumbs: [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Deficiência';
        $this->processoAp = '631';
    }
};
