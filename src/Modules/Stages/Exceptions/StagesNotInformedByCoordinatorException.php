<?php

namespace iEducar\Modules\Stages\Exceptions;

class StagesNotInformedByCoordinatorException extends MissingStagesException
{
    /**
     * @inheritDoc
     */
    protected function getExceptionMessage($missingStages)
    {
        $message = 'O secretário/coordenador deve lançar as notas das etapas: %s deste componente curricular.';

        return sprintf($message, join(', ', $missingStages));
    }

    /**
     * @inheritDoc
     */
    protected function getExceptionCode()
    {
        return self::COORDINATOR_ERROR;
    }
}
