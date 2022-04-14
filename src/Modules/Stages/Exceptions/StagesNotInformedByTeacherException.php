<?php

namespace iEducar\Modules\Stages\Exceptions;

class StagesNotInformedByTeacherException extends MissingStagesException
{
    /**
     * @inheritDoc
     */
    protected function getExceptionCode()
    {
        return self::TEACHER_ERROR;
    }
}
