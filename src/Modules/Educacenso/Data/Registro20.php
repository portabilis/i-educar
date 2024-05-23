<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Services\SchoolClass\SchoolClassService;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\SchoolClass\Period;
use Portabilis_Utils_Database;

class Registro20 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro20Model
     */
    protected $model;

    /**
     * @param $escola
     * @return array
     */
    public function getData($school, $year)
    {
        $data = $this->repository->getDataForRecord20($school, $year);

        $models = [];
        foreach ($data as $record) {
            $record = $this->processData($record);
            $recordCopies = $this->copyByPeriod($record);
            foreach ($recordCopies as $recordCopy) {
                $models[] = $this->hydrateModel($recordCopy);
            }
        }

        return $models;
    }

    /**
     * @return array
     */
    public function getExportFormatData($escola, $year)
    {
        $records = $this->getData($escola, $year);

        $data = [];

        foreach ($records as $record) {
            $data[] = $this->getRecordExportData($record);
        }

        return $data;
    }

    /**
     * @param $Registro20Model
     * @return array
     */
    public function getRecordExportData($record)
    {
        $canExportComponente = $record->escolarizacao() && !in_array($record->etapaEducacenso, [1, 2, 3]);
        $componentesEducacenso = $record->componentesCodigosEducacenso();

        return [
            '20', //  1
            $record->codigoEscolaInep, //  2 Código de escola - Inep
            $record->codTurma, //  3 Código da Turma na Entidade/Escola
            '', //  4 Código da Turma - Inep
            $this->convertStringToCenso($record->nomeTurma), //  5 Nome da Turma
            $record->tipoMediacaoDidaticoPedagogico, //  6 Tipo de mediação didático-pedagógica
            $record->presencial() ? ($record->horaInicial ? substr($record->horaInicial, 0, 2) : '') : '', //  7 Hora Inicial - Hora
            $record->presencial() ? ($record->horaInicial ? substr($record->horaInicial, 3, 2) : '') : '', //  8 Hora Inicial - Minuto
            $record->presencial() ? ($record->horaFinal ? substr($record->horaFinal, 0, 2) : '') : '', //  9 Hora Final - Hora
            $record->presencial() ? ($record->horaFinal ? substr($record->horaFinal, 3, 2) : '') : '', //  10 Hora Final - Minuto
            $record->presencial() ? (int) in_array(1, $record->diasSemana) : '', //  11 Domingo
            $record->presencial() ? (int) in_array(2, $record->diasSemana) : '', //  12 Segunda-feira
            $record->presencial() ? (int) in_array(3, $record->diasSemana) : '', //  13 Terça-feira
            $record->presencial() ? (int) in_array(4, $record->diasSemana) : '', //  14 Quarta-feira
            $record->presencial() ? (int) in_array(5, $record->diasSemana) : '', //  15 Quinta-feira
            $record->presencial() ? (int) in_array(6, $record->diasSemana) : '', //  16 Sexta-feira
            $record->presencial() ? (int) in_array(7, $record->diasSemana) : '', //  17 Sábado
            $record->escolarizacao() ?: 0, //  18 Escolarização
            $record->atividadeComplementar() ?: 0, //  19 Atividade complementar
            $record->atendimentoEducacionalEspecializado() ?: 0, //  20 Atendimento educacional especializado - AEE]
            $record->escolarizacao() ? (int) in_array(1, $record->estruturaCurricular) : '', // 21 Formação geral básica
            $record->escolarizacao() ? (int) in_array(2, $record->estruturaCurricular) : '', // 22 Itinerário formativo
            $record->escolarizacao() ? (int) in_array(3, $record->estruturaCurricular) : '', // 23 Não se aplica
            $record->atividadeComplementar() ? ($record->atividadesComplementares[0] ?? '') : '', //  24 Código 1 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[1] ?? '') : '', //  25 Código 2 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[2] ?? '') : '', //  26 Código 3 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[3] ?? '') : '', //  27 Código 4 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[4] ?? '') : '', //  28 Código 5 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[5] ?? '') : '', //  29 Código 6 - Tipos de atividades complementares
            $record->educacaoDistancia() ? '' : $record->localFuncionamentoDiferenciado, //  30 Local de funcionamento diferenciado
            ($record->formacaoGeralBasica() || $record->estruturaCurricularNaoSeAplica()) ? $record->modalidadeCurso : '', //  31 Modalidade
            ($record->formacaoGeralBasica() || $record->estruturaCurricularNaoSeAplica()) ? $record->etapaEducacenso : '', //  32 Etapa
            in_array($record->etapaEducacenso, [30, 31, 32, 33, 34, 39, 40, 64, 74]) ? $record->codCursoProfissional : '', //  33 Código Curso
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 1 ? 1 : 0) : '', // 34 Série/ano (séries anuais)
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 2 ? 1 : 0) : '', // 35 Períodos semestrais
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 3 ? 1 : 0) : '', // 36 Ciclo(s)
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 4 ? 1 : 0) : '', // 37 Grupos não seriados com base na idade ou competência
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 5 ? 1 : 0) : '', // 38 Módulos
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 6 ? 1 : 0) : '', // 39 Alternância regular de períodos de estudos
            $record->itinerarioFormativo() ? (int) in_array(1, $record->unidadesCurriculares) : '', // 40 Eletivas
            $record->itinerarioFormativo() ? (int) in_array(2, $record->unidadesCurriculares) : '', // 41 Libras
            $record->itinerarioFormativo() ? (int) in_array(3, $record->unidadesCurriculares) : '', // 42 Língua indígena
            $record->itinerarioFormativo() ? (int) in_array(4, $record->unidadesCurriculares) : '', // 43 Língua/Literatura estrangeira - Espanhol
            $record->itinerarioFormativo() ? (int) in_array(5, $record->unidadesCurriculares) : '', // 44 Língua/Literatura estrangeira - Francês
            $record->itinerarioFormativo() ? (int) in_array(6, $record->unidadesCurriculares) : '', // 45 Língua/Literatura estrangeira - outra
            $record->itinerarioFormativo() ? (int) in_array(7, $record->unidadesCurriculares) : '', // 46 Projeto de vida
            $record->itinerarioFormativo() ? (int) in_array(8, $record->unidadesCurriculares) : '', // 47 Trilhas de aprofundamento/aprendizagens
            $record->itinerarioFormativo() ? $record->outrasUnidadesCurricularesObrigatorias : '', // 48 Outras Unidades Curriculares Obrogatórias
            $canExportComponente ? $this->getCensoValueForDiscipline(1, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 49 1. Química
            $canExportComponente ? $this->getCensoValueForDiscipline(2, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 50 2. Física
            $canExportComponente ? $this->getCensoValueForDiscipline(3, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 51 3. Matemática
            $canExportComponente ? $this->getCensoValueForDiscipline(4, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 52 4. Biologia
            $canExportComponente ? $this->getCensoValueForDiscipline(5, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 53 5. Ciências
            $canExportComponente ? $this->getCensoValueForDiscipline(6, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 54 6. Língua/Literatura Portuguesa
            $canExportComponente ? $this->getCensoValueForDiscipline(7, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 55 7. Língua/Literatura Estrangeira - Inglês
            $canExportComponente ? $this->getCensoValueForDiscipline(8, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 56 8. Língua/Literatura Estrangeira - Espanhol
            $canExportComponente ? $this->getCensoValueForDiscipline(9, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 57 9. Língua/Literatura Estrangeira - Outra
            $canExportComponente ? $this->getCensoValueForDiscipline(10, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 58 10. Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)
            $canExportComponente ? $this->getCensoValueForDiscipline(11, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 59 11. Educação Física
            $canExportComponente ? $this->getCensoValueForDiscipline(12, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 60 12. História
            $canExportComponente ? $this->getCensoValueForDiscipline(13, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 61 13. Geografia
            $canExportComponente ? $this->getCensoValueForDiscipline(14, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 62 14. Filosofia
            $canExportComponente ? $this->getCensoValueForDiscipline(16, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 63 16. Informática/ Computação
            $canExportComponente ? $this->getCensoValueForDiscipline(17, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 64 17. Áreas do conhecimento profissionalizantes
            $canExportComponente ? $this->getCensoValueForDiscipline(23, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 65 23. Libras
            $canExportComponente ? $this->getCensoValueForDiscipline(25, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 66 25. Áreas do conhecimento pedagógicas
            $canExportComponente ? $this->getCensoValueForDiscipline(26, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 67 26. Ensino Religioso
            $canExportComponente ? $this->getCensoValueForDiscipline(27, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 68 27. Língua Indígena
            $canExportComponente ? $this->getCensoValueForDiscipline(28, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 69 28. Estudos Sociais
            $canExportComponente ? $this->getCensoValueForDiscipline(29, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 70 29. Sociologia
            $canExportComponente ? $this->getCensoValueForDiscipline(30, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 71 30. Língua/Literatura Estrangeira - Francês
            $canExportComponente ? $this->getCensoValueForDiscipline(31, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 72 31. Língua Portuguesa como Segunda Língua
            $canExportComponente ? $this->getCensoValueForDiscipline(32, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 73 32. Estágio Curricular Supervisionado
            $canExportComponente ? $this->getCensoValueForDiscipline(33, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 74 33. Projeto de vida
            $canExportComponente ? $this->getCensoValueForDiscipline(99, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 75 99. Outras áreas do conhecimento
            $record->classeComLinguaBrasileiraSinais == 1 ? 1 : 0, // 76 Classe bilíngue de surdos tendo a Libras (Língua Brasileira de Sinais) como língua de instrução, ensino, comunicação e interação e a língua portuguesa escrita como segunda língua
        ];
    }

    /**
     * @return int
     */
    private function getCensoValueForDiscipline($discipline, $disciplines, $disciplinesWithTeacher)
    {
        if (in_array($discipline, $disciplines) && in_array($discipline, $disciplinesWithTeacher)) {
            return 1; // oferece a área do conhecimento/componente curricular com docente vinculado
        }

        if (in_array($discipline, $disciplines) && !in_array($discipline, $disciplinesWithTeacher)) {
            return 2; // oferece a área do conhecimento/componente curricular sem docente vinculado
        }

        return 0;
    }

    /**
     * @return array
     */
    public function getDisciplinesWithoutTeacher($schoolClassId, $disciplineIds)
    {
        return $this->repository->getDisciplinesWithoutTeacher($schoolClassId, $disciplineIds);
    }

    protected function hydrateModel($data)
    {
        $model = clone $this->model;
        foreach ($data as $field => $value) {
            if (property_exists($model, $field)) {
                $model->$field = $value;
            }
        }

        return $model;
    }

    private function processData($data)
    {
        $data->localFuncionamento = Portabilis_Utils_Database::pgArrayToArray($data->localFuncionamento);
        $data->diasSemana = Portabilis_Utils_Database::pgArrayToArray($data->diasSemana);
        $data->atividadesComplementares = Portabilis_Utils_Database::pgArrayToArray($data->atividadesComplementares);
        $data->estruturaCurricular = Portabilis_Utils_Database::pgArrayToArray($data->estruturaCurricular);
        $data->unidadesCurriculares = Portabilis_Utils_Database::pgArrayToArray($data->unidadesCurriculares);
        $data->unidadesCurricularesSemDocenteVinculado = Portabilis_Utils_Database::pgArrayToArray($data->unidadesCurricularesSemDocenteVinculado);
        $data->disciplinasEducacensoComDocentes = Portabilis_Utils_Database::pgArrayToArray($data->disciplinasEducacensoComDocentes);

        return $data;
    }

    private function copyByPeriod($record)
    {
        if ($record->turmaTurnoId !== Period::FULLTIME) {
            return [$record];
        }

        $service = new SchoolClassService();

        $periodsNames = (new Period)->getDescriptiveValues();
        $studentPeriods = $service->getStudentsPeriods($record->codTurma);

        $hasPeriods = $studentPeriods->isNotEmpty() && ($studentPeriods->count() > 1 || !$studentPeriods->contains(Period::FULLTIME));

        if ($hasPeriods) {
            return $studentPeriods->map(function ($periodId) use ($record, $periodsNames) {
                $newRecord = clone $record;
                $periodName = $periodsNames[$periodId];

                $newRecord->codTurma .= '-' . $periodId;
                $newRecord->nomeTurma .= ' - ' . $periodName;

                if ($periodId === Period::MORNING) {
                    $newRecord->horaInicial = $record->horaInicialMatutino;
                    $newRecord->horaFinal = $record->horaFinalMatutino;
                    $newRecord->turmaTurnoId = Period::MORNING;
                } elseif ($periodId === Period::AFTERNOON) {
                    $newRecord->horaInicial = $record->horaInicialVespertino;
                    $newRecord->horaFinal = $record->horaFinalVespertino;
                    $newRecord->turmaTurnoId = Period::AFTERNOON;
                }

                return $newRecord;
            })->toArray();
        }

        return [$record];
    }
}
