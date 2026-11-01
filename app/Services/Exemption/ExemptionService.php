<?php

namespace App\Services\Exemption;

use App\Models\Builders\LegacyDisciplineExemptionBuilder;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use App\Models\LegacyExemptionType;
use App\Models\LegacyRegistration;
use App\Models\LegacyUser;
use App\Services\PromotionService;
use App\User;
use App_Model_IedFinder;
use Avaliacao_Model_FaltaAlunoDataMapper;
use Avaliacao_Model_FaltaComponenteDataMapper;
use Avaliacao_Model_NotaAlunoDataMapper;
use Avaliacao_Model_NotaComponenteDataMapper;
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

    /**
     * @var false
     */
    public $isBatch = false;

    /**
     * @var false
     */
    public $keepAbsences = false;

    /**
     * @var false
     */
    public $keepScores = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function createExemptionByDisciplineArray(
        LegacyRegistration $registration,
        $disciplineArray,
        $exemptionTypeId,
        $description,
        $stages
    ) {
        foreach ($disciplineArray as $discipline) {
            $this->createExemption($registration, $discipline, $exemptionTypeId, $description, $stages);
        }
    }

    public function createExemption(LegacyRegistration $registration, $disciplineId, $exemptionTypeId, $description, $stages)
    {
        $exemptionData = $this->validatedExemptionData($registration, $disciplineId, $exemptionTypeId, $description);

        if (!$this->existeComponenteSerie($registration->ref_ref_cod_serie, $registration->ref_ref_cod_escola, $disciplineId)) {
            $this->disciplinasNaoExistentesNaSerieDaEscola[] = $this->nomeDisciplina($disciplineId);

            return;
        }

        $exemption = $this->findExemption($exemptionData);

        if ($exemption) {
            LegacyExemptionStage::query()->where('ref_cod_dispensa', $exemption->getKey())->delete();
            $this->applyExemptionUpdate($exemptionData, $exemption);
            $this->cadastraEtapasDaDispensa($exemption, $stages);
            $exemption->batch = $this->isBatch;
            $exemption->save();

            return;
        }

        $exemption = $this->storeExemption($exemptionData);

        if (!$exemption) {
            throw new Exception;
        }

        $this->cadastraEtapasDaDispensa($exemption, $stages);
    }

    public function updateExemption(
        LegacyDisciplineExemption $exemption,
        LegacyRegistration $registration,
        $exemptionTypeId,
        $description
    ): bool {
        return $this->applyExemptionUpdate(
            $this->validatedExemptionData($registration, $exemption->ref_cod_disciplina, $exemptionTypeId, $description),
            $exemption
        );
    }

    public function deleteExemption(LegacyDisciplineExemption $exemption, LegacyRegistration $registration): bool
    {
        $exemptionData = $this->validatedExemptionData($registration, $exemption->ref_cod_disciplina, null, null);

        if (!$this->hasCompleteLogicalKey($exemptionData) || !is_numeric($exemptionData['ref_usuario_exc'])) {
            return false;
        }

        $exemption->forceDelete();

        return true;
    }

    public function findExemptionByRegistration(
        $registrationId,
        $gradeId,
        $schoolId,
        $disciplineId
    ): ?LegacyDisciplineExemption {
        $exemptionData = [
            'ref_cod_matricula' => $registrationId,
            'ref_cod_serie' => $gradeId,
            'ref_cod_escola' => $schoolId,
            'ref_cod_disciplina' => $disciplineId,
        ];

        if (!$this->hasCompleteLogicalKey($exemptionData)) {
            return null;
        }

        return $this->queryByLogicalKey($exemptionData)->first();
    }

    private function validatedExemptionData(
        LegacyRegistration $registration,
        $disciplineId,
        $exemptionTypeId,
        $description
    ): array {
        $userId = $this->user->getKey();
        $registrationId = $registration->getKey();
        $gradeId = $registration->ref_ref_cod_serie;
        $schoolId = $registration->ref_ref_cod_escola;

        $userExists = is_numeric($userId) && LegacyUser::query()->whereKey($userId)->exists();

        $data = [
            'ref_usuario_exc' => $userExists ? $userId : null,
            'ref_usuario_cad' => $userExists ? $userId : null,
            'ref_cod_matricula' => null,
            'ref_cod_tipo_dispensa' => null,
            'ref_cod_disciplina' => null,
            'ref_cod_serie' => null,
            'ref_cod_escola' => null,
            'observacao' => is_string($description) ? $description : null,
        ];

        if (is_numeric($registrationId) && LegacyRegistration::query()->whereKey($registrationId)->exists()) {
            $data['ref_cod_matricula'] = $registrationId;
        }

        if (is_numeric($exemptionTypeId) && LegacyExemptionType::query()->whereKey($exemptionTypeId)->exists()) {
            $data['ref_cod_tipo_dispensa'] = $exemptionTypeId;
        }

        if ($this->disciplineLinkedToGrade($disciplineId, $gradeId, $schoolId)) {
            $data['ref_cod_disciplina'] = $disciplineId;
            $data['ref_cod_serie'] = $gradeId;
            $data['ref_cod_escola'] = $schoolId;
        }

        return $data;
    }

    private function disciplineLinkedToGrade($disciplineId, $gradeId, $schoolId): bool
    {
        if (!is_numeric($disciplineId) || !is_numeric($schoolId) || !is_numeric($gradeId)) {
            return false;
        }

        return LegacyDisciplineAcademicYear::query()
            ->where('componente_curricular_id', $disciplineId)
            ->where('ano_escolar_id', $gradeId)
            ->exists();
    }

    private function findExemption(array $exemptionData): ?LegacyDisciplineExemption
    {
        if (!$this->hasCompleteLogicalKey($exemptionData)) {
            return null;
        }

        return $this->queryByLogicalKey($exemptionData)->first([
            'cod_dispensa',
            'ref_cod_matricula',
            'ref_cod_disciplina',
            'batch',
        ]);
    }

    private function applyExemptionUpdate(array $exemptionData, LegacyDisciplineExemption $exemption): bool
    {
        if (!$this->hasCompleteLogicalKey($exemptionData) || !is_numeric($exemptionData['ref_usuario_exc'])) {
            return false;
        }

        $values = ['data_exclusao' => now(), 'ativo' => 1];

        foreach (['ref_usuario_exc', 'ref_usuario_cad', 'ref_cod_tipo_dispensa'] as $column) {
            if (is_numeric($exemptionData[$column])) {
                $values[$column] = $exemptionData[$column];
            }
        }

        if (is_string($exemptionData['observacao'])) {
            $values['observacao'] = $exemptionData['observacao'];
        }

        $exemption->update($values);

        return true;
    }

    private function storeExemption(array $exemptionData): ?LegacyDisciplineExemption
    {
        if (!$this->hasCompleteLogicalKey($exemptionData)
            || !is_numeric($exemptionData['ref_usuario_cad'])
            || !is_numeric($exemptionData['ref_cod_tipo_dispensa'])
        ) {
            return null;
        }

        return LegacyDisciplineExemption::query()->create([
            'ref_cod_matricula' => $exemptionData['ref_cod_matricula'],
            'ref_cod_serie' => $exemptionData['ref_cod_serie'],
            'ref_cod_escola' => $exemptionData['ref_cod_escola'],
            'ref_cod_disciplina' => $exemptionData['ref_cod_disciplina'],
            'ref_usuario_cad' => $exemptionData['ref_usuario_cad'],
            'ref_cod_tipo_dispensa' => $exemptionData['ref_cod_tipo_dispensa'],
            'ativo' => 1,
            'observacao' => $exemptionData['observacao'],
            'batch' => $this->isBatch,
        ]);
    }

    private function hasCompleteLogicalKey(array $exemptionData): bool
    {
        return is_numeric($exemptionData['ref_cod_matricula'])
            && is_numeric($exemptionData['ref_cod_serie'])
            && is_numeric($exemptionData['ref_cod_escola'])
            && is_numeric($exemptionData['ref_cod_disciplina']);
    }

    private function queryByLogicalKey(array $exemptionData): LegacyDisciplineExemptionBuilder
    {
        return LegacyDisciplineExemption::query()
            ->withTrashed()
            ->whereExemptionKey(
                $exemptionData['ref_cod_matricula'],
                $exemptionData['ref_cod_serie'],
                $exemptionData['ref_cod_escola'],
                $exemptionData['ref_cod_disciplina']
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
            if (is_numeric($stage)) {
                LegacyExemptionStage::create([
                    'ref_cod_dispensa' => $exemption->getKey(),
                    'etapa' => $stage,
                ]);
            }
        }
    }

    private function removeNotasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
    {
        if ($this->keepScores) {
            return false;
        }

        $notaAlunoMapper = new Avaliacao_Model_NotaAlunoDataMapper;
        $notaAluno = $notaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);

        if (empty($notaAluno)) {
            return false;
        }

        $notaAluno = $notaAluno[0]->id;
        $notaComponenteCurricularMapper = new Avaliacao_Model_NotaComponenteDataMapper;
        $notaComponenteCurricular = $notaComponenteCurricularMapper->findAll([], [
            'nota_aluno_id' => $notaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa,
        ]);
        if (empty($notaComponenteCurricular)) {
            return false;
        }
        $notaComponenteCurricularMapper->delete($notaComponenteCurricular[0]);

        return true;
    }

    private function removeFaltasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
    {
        if ($this->keepAbsences) {
            return false;
        }

        $faltaAlunoMapper = new Avaliacao_Model_FaltaAlunoDataMapper;
        $faltaAluno = $faltaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);
        if (empty($faltaAluno)) {
            return false;
        }

        $faltaAluno = $faltaAluno[0]->id;
        $faltaComponenteCurricularMapper = new Avaliacao_Model_FaltaComponenteDataMapper;
        $faltaComponenteCurricular = $faltaComponenteCurricularMapper->findAll([], [
            'falta_aluno_id' => $faltaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa,
        ]);
        if (empty($faltaComponenteCurricular)) {
            return false;
        }
        $faltaComponenteCurricularMapper->delete($faltaComponenteCurricular[0]);

        return true;
    }

    public function runsPromotion(LegacyRegistration $registration, $stages)
    {
        $_GET['etapa'] = $this->maiorEtapaUtilizada($registration, $stages);
        $promocao = new PromotionService($registration->lastEnrollment()->first());
        $promocao->fakeRequest();
    }

    public function maiorEtapaUtilizada($registration, $stages)
    {
        $where = [
            'ref_ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_ano' => $registration->ano,
        ];

        $totalEtapas = LegacyAcademicYearStage::query()->where($where)->count();
        $arrayEtapas = [];

        for ($i = 1; $i <= $totalEtapas; $i++) {
            $arrayEtapas[$i] = strval($i);
        }

        $arrayEtapas = array_diff($arrayEtapas, $stages);

        if (count($arrayEtapas) === 0) {
            return null;
        }

        return max($arrayEtapas);
    }
}
