<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;

class InepExamValidator implements EducacensoValidator
{
    private $message = '';
    private $resources;
    private $deficiencies;

    public function __construct(array $resources, array $deficiencies)
    {
        $this->resources = array_filter($resources);
        $this->deficiencies = array_filter($deficiencies);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validateNenhum()
            && $this->validateProvas()
            && $this->validateAuxilioLedor()
            && $this->validateAuxilioTranscricao()
            && $this->validateGuiaInterprete()
            && $this->validateTradutorInterprete()
            && $this->validateLeituraLabial()
            && $this->validateProvaAmpliada()
            && $this->validateProvaSuperampliada()
            && $this->validateCdAudioDeficienteVisual()
            && $this->validateProvaLinguaPortuguesaSegundaLingua()
            && $this->validateVideoLibras()
            && $this->validateMaterialDidaticoProvaBraille();
    }

    /**
     * @return bool
     */
    private function validateNenhum()
    {
        if (!in_array(RecursosRealizacaoProvas::NENHUM, $this->resources)) {
            return true;
        }

        if (count($this->resources) > 1) {
            $this->message = 'Não é possível informar mais de uma opção no campo: Recursos necessários para realização de provas, quando a opção: <b>Nenhum</b> estiver selecionada.';

            return false;
        }

        if ($this->validateResource(
            RecursosRealizacaoProvas::NENHUM,
            [],
            [
                Deficiencias::CEGUEIRA,
                Deficiencias::SURDOCEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateProvas()
    {
        $values = [
            RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18,
            RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24,
            RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE,
        ];

        if (count(array_intersect($values, $this->resources)) > 1) {
            $this->setDefaultErrorMessage();

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function validateAuxilioLedor()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::AUXILIO_LEDOR,
            [
                Deficiencias::CEGUEIRA,
                Deficiencias::BAIXA_VISAO,
                Deficiencias::SURDOCEGUEIRA,
                Deficiencias::DEFICIENCIA_FISICA,
                Deficiencias::DEFICIENCIA_INTELECTUAL,
                Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA,
            ],
            [
                Deficiencias::SURDEZ,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateAuxilioTranscricao()
    {
        $resource = RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO;
        $valid = $this->validateResource(
            $resource,
            [
                Deficiencias::CEGUEIRA,
                Deficiencias::BAIXA_VISAO,
                Deficiencias::SURDOCEGUEIRA,
                Deficiencias::DEFICIENCIA_FISICA,
                Deficiencias::DEFICIENCIA_INTELECTUAL,
                Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA,
            ],
            [
            ]
        );

        if ($valid && $this->hasResource($resource) && !empty(array_intersect([Deficiencias::SURDOCEGUEIRA, Deficiencias::CEGUEIRA], $this->deficiencies)) && count($this->resources) < 2) {
            $valid = false;
        }

        if ($valid) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateGuiaInterprete()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::GUIA_INTERPRETE,
            [],
            [
                Deficiencias::SURDOCEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateTradutorInterprete()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS,
            [
                Deficiencias::SURDEZ,
                Deficiencias::DEFICIENCIA_AUDITIVA,
                Deficiencias::SURDOCEGUEIRA,
            ],
            [
                Deficiencias::CEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateLeituraLabial()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::LEITURA_LABIAL,
            [
                Deficiencias::SURDEZ,
                Deficiencias::DEFICIENCIA_AUDITIVA,
                Deficiencias::SURDOCEGUEIRA,
            ],
            [
                Deficiencias::CEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateProvaAmpliada()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18,
            [
                Deficiencias::BAIXA_VISAO,
                Deficiencias::SURDOCEGUEIRA,
            ],
            [
                Deficiencias::CEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateProvaSuperampliada()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24,
            [
                Deficiencias::BAIXA_VISAO,
                Deficiencias::SURDOCEGUEIRA,
            ],
            [
                Deficiencias::CEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateCdAudioDeficienteVisual()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL,
            [
                Deficiencias::CEGUEIRA,
                Deficiencias::BAIXA_VISAO,
                Deficiencias::SURDOCEGUEIRA,
                Deficiencias::DEFICIENCIA_FISICA,
                Deficiencias::DEFICIENCIA_INTELECTUAL,
                Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA,
            ],
            [
                Deficiencias::SURDEZ,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateProvaLinguaPortuguesaSegundaLingua()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS,
            [
                Deficiencias::SURDEZ,
                Deficiencias::DEFICIENCIA_AUDITIVA,
                Deficiencias::SURDOCEGUEIRA,
            ],
            [
                Deficiencias::CEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateVideoLibras()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS,
            [
                Deficiencias::SURDEZ,
                Deficiencias::DEFICIENCIA_AUDITIVA,
                Deficiencias::SURDOCEGUEIRA,
            ],
            [
                Deficiencias::CEGUEIRA,
            ]
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return bool
     */
    private function validateMaterialDidaticoProvaBraille()
    {
        if ($this->validateResource(
            RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE,
            [
                Deficiencias::CEGUEIRA,
                Deficiencias::SURDOCEGUEIRA,
            ],
            []
        )) {
            return true;
        }

        $this->setDefaultErrorMessage();

        return false;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    private function validateResource($resource, $permittedDeficiencies, $forbiddenDeficiencies)
    {
        if (!$this->hasResource($resource)) {
            return true;
        }

        return $this->validatePermittedDeficiencies($permittedDeficiencies) && $this->validateForbiddenDeficiencies($forbiddenDeficiencies);
    }

    /**
     * @return bool
     */
    private function hasResource($resource)
    {
        return in_array($resource, $this->resources);
    }

    /**
     * @return bool
     */
    private function validatePermittedDeficiencies(array $permittedDeficiencies)
    {
        if (empty($permittedDeficiencies)) {
            return true;
        }

        return !empty(array_intersect($permittedDeficiencies, $this->deficiencies));
    }

    /**
     * @return bool
     */
    private function validateForbiddenDeficiencies(array $forbiddenDeficiencies)
    {
        if (empty($forbiddenDeficiencies)) {
            return true;
        }

        return empty(array_intersect($forbiddenDeficiencies, $this->deficiencies));
    }

    /**
     * @return void
     */
    private function setDefaultErrorMessage()
    {
        $this->message = 'O campo: Recursos necessários para realização de provas foi preenchido incorretamente. Clique <a href="#" class="open-dialog-recursos-prova-inep">aqui</a> para conferir as regras de preenchimento desse campo.';
    }
}
