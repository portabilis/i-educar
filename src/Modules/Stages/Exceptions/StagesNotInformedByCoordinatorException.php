<?php

namespace iEducar\Modules\Stages\Exceptions;

class StagesNotInformedByCoordinatorException extends MissingStagesException
{
    /**
     * @inheritDoc
     */
    protected function getExceptionMessage($missingStages)
    {
        $message = 'O secretário/coordenador deve lançar as notas das etapas: %s %s.';

        return sprintf($message, join($this->stageName . ', ', $missingStages), $this->stageName);
    }

    /**
     * @inheritDoc
     */
    protected function getExceptionCode()
    {
        return self::COORDINATOR_ERROR;
    }
}
