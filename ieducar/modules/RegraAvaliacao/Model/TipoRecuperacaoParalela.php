<?php

use App\Models\LegacyEvaluationRule;

require_once 'CoreExt/Enum.php';

class RegraAvaliacao_Model_TipoRecuperacaoParalela extends CoreExt_Enum
{
    const NAO_USAR = LegacyEvaluationRule::PARALLEL_REMEDIAL_NONE;
    const USAR_POR_ETAPA = LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE;
    const USAR_POR_ETAPAS_ESPECIFICAS = LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE;

    protected $_data = [
        LegacyEvaluationRule::PARALLEL_REMEDIAL_NONE => 'Não usar recuperação paralela',
        LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE => 'Usar uma recuperação paralela por etapa',
        LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE => 'Usar uma recuperação paralela por etapas específicas',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
