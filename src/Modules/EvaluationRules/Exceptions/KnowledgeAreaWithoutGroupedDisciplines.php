<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class KnowledgeAreaWithoutGroupedDisciplines extends Exception
{
    public function __construct(
        private readonly int $knowledgeAreaId
    ) {
        parent::__construct(
            'Não foi possível encontrar componentes dessa turma agrupados para a área do conhecimento informada.',
            Error::KNOWLEDGE_AREA_WITHOUT_GROUPED_DISCIPLINES
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'knowledge_area_id' => $this->knowledgeAreaId,
        ];
    }
}
