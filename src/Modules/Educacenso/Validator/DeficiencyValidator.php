<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\Deficiencias;

class DeficiencyValidator implements EducacensoValidator
{
    private $message;
    private $values;
    private const CEGUEIRA_FORBIDDEN_DEFICIENCIES = [
        Deficiencias::BAIXA_VISAO,
        Deficiencias::SURDEZ,
        Deficiencias::SURDOCEGUEIRA,
    ];
    private const BAIXA_VISAO_FORBIDDEN_DEFICIENCIES = [
        Deficiencias::SURDOCEGUEIRA,
    ];
    private const SURDEZ_FORBIDDEN_DEFICIENCIES = [
        Deficiencias::DEFICIENCIA_AUDITIVA,
        Deficiencias::SURDOCEGUEIRA,
    ];
    private const DEFICIENCIA_AUDITIVA_FORBIDDEN_DEFICIENCIES = [
        Deficiencias::SURDOCEGUEIRA,
    ];

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (count($this->values) <= 1) {
            return true;
        }

        $validations = [
            [
                Deficiencias::CEGUEIRA,
                self::CEGUEIRA_FORBIDDEN_DEFICIENCIES,
            ],
            [
                Deficiencias::BAIXA_VISAO,
                self::BAIXA_VISAO_FORBIDDEN_DEFICIENCIES,
            ],
            [
                Deficiencias::SURDEZ,
                self::SURDEZ_FORBIDDEN_DEFICIENCIES,
            ],
            [
                Deficiencias::DEFICIENCIA_AUDITIVA,
                self::DEFICIENCIA_AUDITIVA_FORBIDDEN_DEFICIENCIES,
            ],
        ];

        foreach ($validations as $validation) {
            if ($this->hasForbiddenValues($validation[0], $validation[1])) {
                $this->message = $this->getForbiddenDeficiencyMessage($validation[0], $validation[1]);
                return false;
            }
        }

        return true;
    }

    private function hasForbiddenValues($deficiency, $forbiddenValues): bool
    {
        return in_array($deficiency, $this->values) && !empty(array_intersect($this->values, $forbiddenValues));

    }

    /**
     * @return string
     */
    private function getForbiddenDeficiencyMessage($choosedDeficiency, $forbiddenDeficiencies)
    {
        $descriptions = Deficiencias::getDescriptiveValues();

        $forbiddenDescriptions = array_filter($descriptions, function($key) use ($forbiddenDeficiencies){
            return in_array($key, $forbiddenDeficiencies);
        }, ARRAY_FILTER_USE_KEY);

        $forbiddenDescriptions = implode(', ', $forbiddenDescriptions);

        $choosedDescription = $descriptions[$choosedDeficiency];

        return "Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.";
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
