<?php

class SerieController extends ApiCoreController
{
    protected function canGetSeries()
    {
        return $this->validatesId('instituicao') &&
        $this->validatesId('curso');
    }

    protected function getSeries()
    {
        if ($this->canGetSeries()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $cursoId = $this->getRequest()->curso_id;
            $ano = $this->getRequest()->ano;

            $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);
            $canLoadSeriesAlocado = Portabilis_Business_Professor::canLoadSeriesAlocado($instituicaoId);

            if ($isOnlyProfessor && $canLoadSeriesAlocado) {
                $resources = Portabilis_Business_Professor::seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId);
                $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
            } elseif ($escolaId && $cursoId && empty($resources)) {
                $resources = App_Model_IedFinder::getSeries($instituicaoId = null, $escolaId, $cursoId, $ano);
            } else {
                $resources = App_Model_IedFinder::getSeries($instituicaoId = null, null, $cursoId, $ano);
            }

            $options = [];

            foreach ($resources as $serieId => $serie) {
                $options['__' . $serieId] = $this->toUtf8($serie);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'series')) {
            $this->appendResponse($this->getSeries());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
