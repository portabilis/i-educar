<?php

namespace App\Services;

use App\Models\RegraAvaliacao;
use App\Models\Serie;

class EscolaSerieService
{
    /**
     * Retorna as regras de avaliação da série
     *
     * @param $serieId
     * @return RegraAvaliacao[]
     */
    public function getRegrasAvaliacaoSerie($serieId)
    {
        $serie = Serie::with('regrasAvaliacao')->find($serieId);
        return $serie->regrasAvaliacao;
    }

    /**
     * Verifica se a regra de avaliação da série permite definir componentes por etapa
     *
     * @param $serieId
     * @param $anoLetivo
     * @return bool
     */
    public function seriePermiteDefinirComponentesPorEtapa($serieId, $anoLetivo)
    {
        /** @var Serie $serie */
        $serie = Serie::with('regrasAvaliacao')
            ->whereCodSerie($serieId)
            ->get()
            ->first();

        if (empty($serie)) {
            return false;
        }

        $regraAvaliacao = $serie->regrasAvaliacao()
            ->wherePivot('ano_letivo', $anoLetivo)
            ->get()
            ->first();

        if (empty($regraAvaliacao)) {
            return false;
        }

        return $regraAvaliacao->definir_componente_etapa == 1;
    }


}
