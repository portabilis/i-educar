<?php

namespace App\Services;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolStage;
use App\User;
use App_Model_IedFinder;
use Avaliacao_Model_FaltaAlunoDataMapper;
use Avaliacao_Model_FaltaComponenteDataMapper;
use Avaliacao_Model_NotaAlunoDataMapper;
use Avaliacao_Model_NotaComponenteDataMapper;
use clsPmieducarDispensaDisciplina;
use clsPmieducarDispensaDisciplinaEtapa;
use Exception;

class ExemptionService
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    public $disciplinasNaoExistentesNaSerieDaEscola;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function createExemptionByDisciplineArray(
        LegacyRegistration $registration,
        $disciplineArray,
        $exemptionTypeId,
        $description,
        $stages)
    {
        foreach($disciplineArray as $discipline) {
            $this->createExemption($registration, $discipline, $exemptionTypeId, $description, $stages);
        }
    }

    public function createExemption(LegacyRegistration $registration, $disciplineId, $exemptionTypeId, $description, $stages)
    {
        $objetoDispensa = $this->handleExemptionObject($registration, $disciplineId, $exemptionTypeId, $description);

        if (!$this->existeComponenteSerie($registration->ref_ref_cod_serie, $registration->ref_ref_cod_escola, $disciplineId)) {
            $this->disciplinasNaoExistentesNaSerieDaEscola[] = $this->nomeDisciplina($disciplineId);
            return;
        }

        if ($objetoDispensa->existe()) {
            $exemption = LegacyDisciplineExemption::findOrFail($objetoDispensa->detalhe()['cod_dispensa']);
            $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
            $objDispensaEtapa->excluirTodos($exemption->getKey());
            $objetoDispensa->edita();
            $this->cadastraEtapasDaDispensa($exemption, $stages);
            return;
        }

        $codigoDispensa = $objetoDispensa->cadastra();
        if (!$codigoDispensa) {
            throw new Exception();
        }

        $exemption = LegacyDisciplineExemption::findOrFail($codigoDispensa);

        $this->cadastraEtapasDaDispensa($exemption, $stages);
    }

    public function updateExemptionByDisciplineArray(LegacyDisciplineExemption $disciplineExemption, $disciplineArray, $exemptionTypeId, $description)
    {
        $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
        $objDispensaEtapa->excluirTodos($disciplineExemption->getKey());

        $objetoDispensa = $this->handleExemptionObject($disciplineExemption->ref_cod_matricula, $disciplineArray, $exemptionTypeId, $description);

        $this->cadastraEtapasDaDispensa($disciplineExemption);

        if (!$objetoDispensa->edita()) {
            throw new Exception();
        }
    }

    private function handleExemptionObject(LegacyRegistration $registration, $disciplineId, $exemptionTypeId, $description)
    {
        return new clsPmieducarDispensaDisciplina(
            $registration->getKey(),
            $registration->ref_ref_cod_serie,
            $registration->ref_ref_cod_escola,
            $disciplineId,
            $this->user->getKey(),
            $this->user->getKey(),
            $exemptionTypeId,
            null,
            null,
            1,
            $description
        );
    }

    private function existeComponenteSerie($serieId, $escolaId, $disciplinaId)
    {
        try {
            App_Model_IedFinder::getEscolaSerieDisciplina(
                $serieId,
                $escolaId,
                null,
                $disciplinaId
            );
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    private function nomeDisciplina($disciplinaId)
    {
        return LegacyDiscipline::find($disciplinaId)->name;
    }

    public function cadastraEtapasDaDispensa(LegacyDisciplineExemption $exemption, $stages)
    {
        foreach ($stages as $stage) {
            $this->removeNotasDaDisciplinaNaEtapa(
                $exemption->ref_cod_matricula,
                $exemption->ref_cod_disciplina,
                $stage
            );
            $this->removeFaltasDaDisciplinaNaEtapa(
                $exemption->ref_cod_matricula,
                $exemption->ref_cod_disciplina,
                $stage
            );
            $objetoEtapaDaDispensa = new clsPmieducarDispensaDisciplinaEtapa($exemption->getKey(), $stage);
            $objetoEtapaDaDispensa->cadastra();
        }
    }

    private function removeNotasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
    {
        $notaAlunoMapper = new Avaliacao_Model_NotaAlunoDataMapper();
        $notaAluno = $notaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);
        $notaAluno = $notaAluno[0]->id;
        if (empty($notaAluno)) {
            return false;
        }
        $notaComponenteCurricularMapper = new Avaliacao_Model_NotaComponenteDataMapper();
        $notaComponenteCurricular = $notaComponenteCurricularMapper->findAll([], [
            'nota_aluno_id' => $notaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa
        ]);
        if (empty($notaComponenteCurricular)) {
            return false;
        }
        $notaComponenteCurricularMapper->delete($notaComponenteCurricular[0]);

        return true;
    }

    private function removeFaltasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
    {
        $faltaAlunoMapper = new Avaliacao_Model_FaltaAlunoDataMapper();
        $faltaAluno = $faltaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);
        $faltaAluno = $faltaAluno[0]->id;
        if (empty($faltaAluno)) {
            return false;
        }
        $faltaComponenteCurricularMapper = new Avaliacao_Model_FaltaComponenteDataMapper();
        $faltaComponenteCurricular = $faltaComponenteCurricularMapper->findAll([], [
            'falta_aluno_id' => $faltaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa
        ]);
        if (empty($faltaComponenteCurricular)) {
            return false;
        }
        $faltaComponenteCurricularMapper->delete($faltaComponenteCurricular[0]);

        return true;
    }

    public function runsPromotion(LegacyRegistration $registration)
    {
        $_GET['etapa'] = $this->maiorEtapaUtilizada($registration);
        $promocao = new PromotionService($registration->lastEnrollment()->first());
        $promocao->fakeRequest();
    }

    public function maiorEtapaUtilizada($registration)
    {
        $where = [
            'ref_ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_ano' => $registration->ano,
        ];

        $totalEtapas['total'] = LegacySchoolStage::query()->where($where)->count();
        $arrayEtapas = [];

        for ($i = 1; $i <= $totalEtapas['total']; $i++)
        {
            $arrayEtapas[$i] = strval($i);
        }

        $arrayEtapas = array_diff($arrayEtapas, $this->etapa);
        return max($arrayEtapas);
    }
}
