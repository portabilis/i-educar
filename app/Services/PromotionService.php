<?php

namespace App\Services;

use App\Models\LegacyEnrollment;
use CoreExt_Controller_Request;
use CoreExt_Exception;
use PromocaoApiController;

class PromotionService
{
    /**
     * @var LegacyEnrollment
     */
    private $enrollment;

    public function __construct(LegacyEnrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function fakeRequest()
    {
        $fakeRequest = new CoreExt_Controller_Request(
            [
                'data' => [
                    'oper' => 'post',
                    'resource' => 'promocao',
                    'matricula_id' => $this->enrollment->ref_cod_matricula,
                    'instituicao_id' => $this->enrollment->schoolClass->school->ref_cod_instituicao,
                    'ano' => $this->enrollment->registration->ano,
                    'escola' => $this->enrollment->schoolClass->school_id,
                    'curso' => $this->enrollment->schoolClass->ref_cod_curso,
                    'serie' => $this->enrollment->schoolClass->ref_ref_cod_serie,
                    'turma' => $this->enrollment->ref_cod_turma
                ]
            ]
        );

        $promocaoApi = new PromocaoApiController();
        $promocaoApi->setRequest($fakeRequest);

        try {
            $promocaoApi->Gerar();
        } catch (CoreExt_Exception $exception) {
            // Quando o aluno não possuir enturmação na escola que está
            // cancelando a matrícula, uma Exception era lançada ao
            // instanciar o ServiceBoletim, este catch garante que não irá
            // quebrar o processo.
        }
    }
}
