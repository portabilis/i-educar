<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';

    protected $_titulo = 'Listagem de regras de avaliação';

    protected $_processoAp = 947;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Sistema de nota' => 'tipoNota',
        'Progressão' => 'tipoProgressao',
        'Média aprovação' => 'media',
        'Média exame' => 'mediaRecuperacao',
        'Fórmula média' => 'formulaMedia',
        'Fórmula recuperação' => 'formulaRecuperacao',
        'Recuperação paralela' => 'tipoRecuperacaoParalela',
    ];

    public function Gerar()
    {
        parent::Gerar();

        $this->inputsHelper()->dynamic(
            helperNames: 'ano',
            inputOptions: [
                'required' => false,
                'value' => request('ano'),
            ]
        );

        $series = LegacyGrade::query()
            ->active()
            ->orderBy('nm_serie')
            ->get([
                'nm_serie',
                'cod_serie',
            ])
            ->prepend([
                'nm_serie' => 'Selecione uma série',
                'cod_serie' => '',
            ])
            ->pluck('nm_serie', 'cod_serie')
            ->toArray();

        $this->campoLista(
            nome: 'cod_serie',
            campo: 'Série',
            valor: $series,
            default: request('cod_serie'),
            obrigatorio: false
        );
    }

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de regras de avaliações', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function getEntries()
    {
        return LegacyEvaluationRule::query()
            ->with([
                'averageFormula',
                'recoveryFormula',
            ])
            ->when(request('ano'), function ($query, $ano) {
                $query->whereHas('gradeYears', function ($query) use ($ano) {
                    $query->where('ano_letivo', $ano);
                });
            })
            ->when(request('cod_serie'), function ($query, $cod_serie) {
                $query->whereHas('gradeYears', function ($query) use ($cod_serie) {
                    $query->where('serie_id', $cod_serie);
                });
            })
            ->get()
            ->map(function ($rule) {
                $rule->tipoNota = RegraAvaliacao_Model_Nota_TipoValor::getInstance()->getValue($rule->tipo_nota);
                $rule->tipoProgressao = RegraAvaliacao_Model_TipoProgressao::getInstance()->getValue($rule->tipo_progressao);
                $rule->media = $rule->media ? number_format($rule->media, 2, ',', '.') : '';
                $rule->mediaRecuperacao = $rule->media_recuperacao ? number_format($rule->media_recuperacao, 2, ',', '.') : '';
                $rule->formulaMedia = $rule->averageFormula->formula_media;
                $rule->formulaRecuperacao = $rule->recoveryFormula->formula_media;
                $rule->tipoRecuperacaoParalela = RegraAvaliacao_Model_TipoRecuperacaoParalela::getInstance()->getValue($rule->tipo_recuperacao_paralela);

                return $rule;
            });
    }
}
