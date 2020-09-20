<?php

namespace App\Services;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyRegistration;
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
    private $disciplinasNaoExistentesNaSerieDaEscola;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function createExemptionByDisciplineArray(LegacyRegistration $registration, $disciplineArray, $exemptionTypeId, $description)
    {
        foreach($disciplineArray as $discipline) {
            $this->createExemption($registration, $discipline, $exemptionTypeId, $description);
        }
    }

    public function createExemption(LegacyRegistration $registration, $disciplineId, $exemptionTypeId, $description)
    {
        $objetoDispensa = $this->handleExemptionObject($registration, $disciplineId, $exemptionTypeId, $description);

        if (!$this->existeComponenteSerie($registration->ref_ref_cod_serie, $registration->ref_ref_cod_escola, $disciplineId)) {
            $this->disciplinasNaoExistentesNaSerieDaEscola[] = $this->nomeDisciplina($disciplineId);
            return;
        }

        if ($objetoDispensa->existe()) {
            $discipline = LegacyDisciplineExemption::findOrFail($objetoDispensa->detalhe()['cod_dispensa']);
            $objDispensaEtapa = new clsPmieducarDispensaDisciplinaEtapa();
            $objDispensaEtapa->excluirTodos($discipline->getKey());
            $objetoDispensa->edita();
            $this->cadastraEtapasDaDispensa($discipline);
            return;
        }

        $codigoDispensa = $objetoDispensa->cadastra();
        if (!$codigoDispensa) {
            $this->mensagem = 'Cadastro não realizado.<br />';

            return false;
        }

        $exemption = LegacyDisciplineExemption::findOrFail($codigoDispensa);

        $this->cadastraEtapasDaDispensa($exemption);
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

    public function existeComponenteSerie($serieId, $escolaId, $disciplinaId)
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

    public function cadastraEtapasDaDispensa(LegacyDisciplineExemption $exemption)
    {
        foreach ($exemption->stages as $stage) {
            $this->removeNotasDaDisciplinaNaEtapa(
                $exemption->ref_cod_matricula,
                $exemption->ref_cod_disciplina,
                $stage->etapa
            );
            $this->removeFaltasDaDisciplinaNaEtapa(
                $exemption->ref_cod_matricula,
                $exemption->ref_cod_disciplina,
                $stage->etapa
            );
            $objetoEtapaDaDispensa = new clsPmieducarDispensaDisciplinaEtapa($exemption->getKey(), $stage->etapa);
            $objetoEtapaDaDispensa->cadastra();
        }
    }

    public function removeNotasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
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

    public function removeFaltasDaDisciplinaNaEtapa($matriculaId, $disciplinaId, $etapa)
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
}
