<?php

use App\Models\LegacyAverageFormula;

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'FormulaMedia_Model_FormulaDataMapper';

    protected $_titulo = 'Listagem de fórmulas de cálculo de média';

    protected $_processoAp = 948;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Fórmula de cálculo' => 'formula_media',
        'Tipo fórmula' => 'tipo_formula',
    ];

    public function Gerar()
    {
        parent::Gerar();

        $this->campoTexto(
            nome: 'nome',
            campo: 'Nome',
            valor: request('nome'),
        );

        $this->campoTexto(
            nome: 'formula_media',
            campo: 'Fórmula de cálculo',
            valor: request('formula_media'),
        );

        $tipoFormula = collect(FormulaMedia_Model_TipoFormula::getInstance()->getEnums())->prepend('Todos os tipos', '');
        $this->campoLista(
            nome: 'tipo_formula',
            campo: 'Tipo de fórmula',
            valor: $tipoFormula,
            default: request('tipo_formula')
        );
    }

    public function getEntries()
    {
        return LegacyAverageFormula::query()
            ->when(request('nome'), fn ($q, $nome) => $q->whereRaw('unaccent(nome) ~* unaccent(?)', $nome))
            ->when(request('formula_media'), function ($q, $formulaMedia) {
                return $q->where('formula_media', 'ilike', "%{$formulaMedia}%");
            })
            ->when(request('tipo_formula'), fn ($q, $tipoFormula) => $q->where('tipo_formula', $tipoFormula))
            ->orderBy('nome')
            ->get()
            ->map(function ($averageFormula) {
                $tipoFormula = FormulaMedia_Model_TipoFormula::getInstance()->getEnums();
                $averageFormula->tipo_formula = $tipoFormula[$averageFormula->tipo_formula];

                return $averageFormula;
            });
    }

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de fórmulas de média', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}
