<?php

namespace iEducar\Modules\Stages\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class MissingStagesException extends Exception
{
    public const DEFAULT_ERROR = Error::MISSING_STAGE_DEFAULT_ERROR;
    public const TEACHER_ERROR = Error::MISSING_STAGE_TEACHER_ERROR;
    public const COORDINATOR_ERROR = Error::MISSING_STAGE_COORDINATOR_ERROR;

    /**
     * @var array
     */
    protected $missingStages;

    /**
     * @var string
     */
    protected $stageName;

    /**
     * StagesNotInformedByTeacherException constructor.
     *
     * @param array  $missingStages
     * @param string $stageName
     */
    public function __construct($missingStages, $stageName)
    {
        $this->missingStages = $missingStages;
        $this->stageName = $stageName;

        parent::__construct(
            $this->getExceptionMessage($missingStages),
            $this->getExceptionCode()
        );
    }

    /**
     * Return missing stages.
     *
     * @return array
     */
    private function getFormatedMissingStages()
    {
        return array_map(function ($stage) {
            return $stage . 'º ' . $this->stageName;
        }, $this->missingStages);
    }

    /**
     * Return message to be used in exception.
     *
     * @param array $missingStages
     *
     * @return string
     */
    protected function getExceptionMessage($missingStages)
    {
        $message = 'Nota somente pode ser lançada após lançar notas nas etapas: %s %s.';

        return sprintf($message, join($this->stageName . ', ', $missingStages), $this->stageName);
    }

    /**
     * Return code to be used in exception.
     *
     * @return int
     */
    protected function getExceptionCode()
    {
        return self::DEFAULT_ERROR;
    }

    /**
     * Return more information about error.
     *
     * @return array
     */
    public function getExtraInfo()
    {
        return [
            'missing_stages' => $this->getFormatedMissingStages()
        ];
    }
}
