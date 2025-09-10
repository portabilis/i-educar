<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Services\SchoolClass\SchoolClassService;
use DateTime;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
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
        $canExportComponente = $record->curricularEtapaDeEnsino() && !in_array($record->etapaEducacenso, [1, 2, 3]);
        $componentesEducacenso = $record->componentesCodigosEducacenso();

        $horaInicial = (new DateTime($record->horaInicial))->format('H:i');
        $horaFinal = (new DateTime($record->horaFinal))->format('H:i');

        return [
            '20', //  1
            $record->codigoEscolaInep, // 2 - Código de escola - Inep
            $record->codTurma, // 3 - Código da Turma na Entidade/Escola
            '', // 4 - Código da Turma - Inep
            $this->convertStringToCenso($record->nomeTurma), // 5 - Nome da Turma
            $record->tipoMediacaoDidaticoPedagogico, // 6 - Tipo de mediação didático-pedagógica
            $record->presencial() && in_array(1, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 7 - Domingp
            $record->presencial() && in_array(2, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 8 - Segunda-feira
            $record->presencial() && in_array(3, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 9 - Terça-feira
            $record->presencial() && in_array(4, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 10 - Quarta-feira
            $record->presencial() && in_array(5, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 11 - Quinta-feira
            $record->presencial() && in_array(6, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 12 - Sexta-feira
            $record->presencial() && in_array(7, $record->diasSemana) ? $horaInicial . '-' . $horaFinal : '', // 13 - Sábado
            $record->curricularEtapaDeEnsino() ?: 0, //  14 - Curricular (etapa de ensino)
            $record->atividadeComplementar() ?: 0, // 15 - Atividade complementar
            $record->atendimentoEducacionalEspecializado() ?: 0, // 16 - Atendimento educacional especializado - AEE]
            $record->atividadeComplementar() ? ($record->atividadesComplementares[0] ?? '') : '', // 17 - Código 1 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[1] ?? '') : '', // 18 - Código 2 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[2] ?? '') : '', // 19 - Código 3 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[3] ?? '') : '', // 20 - Código 4 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[4] ?? '') : '', // 21 - Código 5 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[5] ?? '') : '', // 22 - Código 6 - Tipos de atividades complementares
            $record->educacaoDistancia() ? '' : $record->localFuncionamentoDiferenciado, // 23 - Local de funcionamento diferenciado
            $record->curricularEtapaDeEnsino() ? $record->classeEspecial ?: 0 : null, // 24 - Turma de Educação Especial (classe especial)
            $record->etapaAgregada, // 25 - Etapa agregada
            $record->etapaEducacenso, // 26 - Etapa
            in_array($record->etapaEducacenso, [39, 40, 64]) ? $record->codCursoProfissional : '', // 27 - Código do curso
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 1 ? 1 : 0) : '', // 28 - Série/ano (séries anuais)
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 2 ? 1 : 0) : '', // 29 - Períodos semestrais
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 3 ? 1 : 0) : '', // 30 - Ciclo(s)
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 4 ? 1 : 0) : '', // 31 - Grupos não seriados com base na idade ou competência
            $record->requereFormasOrganizacaoTurma() ? ($record->formasOrganizacaoTurma === 5 ? 1 : 0) : '', // 32 - Módulos
            $record->formacaoAlternancia ?: 0, // 33 - Turma de Formação por Alternância (proposta pedagógica de formação por alternância: tempo-escola e tempo-comunidade)
            in_array($record->etapaAgregada, [304, 305]) ? ($record->formacaoGeralBasica() ? 1 : 0) : '', // 34 - Formação geral básica
            in_array($record->etapaAgregada, [304, 305]) ? ($record->itinerarioFormativoAprofundamento() ? 1 : 0) : '', // 35 - Itinerário formativo de aprofundamento
            in_array($record->etapaAgregada, [304, 305]) ? ($record->itinerarioFormacaoTecnicaProfissional() ? 1 : 0) : '', // 36 - Itinerário de formação técnica e profissional
            $record->itinerarioFormativoAprofundamento() ? (in_array(TipoItinerarioFormativo::LINGUANGENS, $record->areaItinerario) ? 1 : 0) : '', // 37 - Área do conhecimento de linguagens e suas tecnologias
            $record->itinerarioFormativoAprofundamento() ? (in_array(TipoItinerarioFormativo::MATEMATICA, $record->areaItinerario) ? 1 : 0) : '', // 38 - Área do conhecimento de matemática e suas tecnologias
            $record->itinerarioFormativoAprofundamento() ? (in_array(TipoItinerarioFormativo::CIENCIAS_NATUREZA, $record->areaItinerario) ? 1 : 0) : '', // 39 - Área do conhecimento de ciências da natureza e suas tecnologias
            $record->itinerarioFormativoAprofundamento() ? (in_array(TipoItinerarioFormativo::CIENCIAS_HUMANAS, $record->areaItinerario) ? 1 : 0) : '', // 40 - Área do conhecimento de ciências humanas e sociais aplicadas
            (in_array($record->etapaAgregada, [304, 305]) && $record->itinerarioFormacaoTecnicaProfissional()) ? $record->tipoCursoIntinerario : '', // 41 - Tipo do curso do itinerário de formação técnica e profissional
            (in_array($record->etapaAgregada, [304, 305]) && $record->itinerarioFormacaoTecnicaProfissional()) ? $record->codCursoProfissionalIntinerario : '', // 42 - Código do curso técnico
            $canExportComponente ? $this->getCensoValueForDiscipline(1, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 43 - 1. Química
            $canExportComponente ? $this->getCensoValueForDiscipline(2, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 44 - 2. Física
            $canExportComponente ? $this->getCensoValueForDiscipline(3, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 45 - 3. Matemática
            $canExportComponente ? $this->getCensoValueForDiscipline(4, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 46 - 4. Biologia
            $canExportComponente ? $this->getCensoValueForDiscipline(5, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 47 - 5. Ciências
            $canExportComponente ? $this->getCensoValueForDiscipline(6, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 48 - 6. Língua/Literatura Portuguesa
            $canExportComponente ? $this->getCensoValueForDiscipline(7, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 49 - 7. Língua/Literatura Estrangeira - Inglês
            $canExportComponente ? $this->getCensoValueForDiscipline(8, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 50 - 8. Língua/Literatura Estrangeira - Espanhol
            $canExportComponente ? $this->getCensoValueForDiscipline(9, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 51 - 9. Língua/Literatura Estrangeira - Outra
            $canExportComponente ? $this->getCensoValueForDiscipline(10, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 52 - 10. Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)
            $canExportComponente ? $this->getCensoValueForDiscipline(11, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 53 - 11. Educação Física
            $canExportComponente ? $this->getCensoValueForDiscipline(12, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 54 - 12. História
            $canExportComponente ? $this->getCensoValueForDiscipline(13, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 55 - 13. Geografia
            $canExportComponente ? $this->getCensoValueForDiscipline(14, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 56 - 14. Filosofia
            $canExportComponente ? $this->getCensoValueForDiscipline(16, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 57 - 16. Informática/ Computação
            $canExportComponente ? $this->getCensoValueForDiscipline(17, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 58 - 17. Áreas do conhecimento profissionalizantes
            $canExportComponente ? $this->getCensoValueForDiscipline(23, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 59 - 23. Libras
            $canExportComponente ? $this->getCensoValueForDiscipline(25, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 60 - 25. Áreas do conhecimento pedagógicas
            $canExportComponente ? $this->getCensoValueForDiscipline(26, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 61 - 26. Ensino Religioso
            $canExportComponente ? $this->getCensoValueForDiscipline(27, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 62 - 27. Língua Indígena
            $canExportComponente ? $this->getCensoValueForDiscipline(28, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 63 - 28. Estudos Sociais
            $canExportComponente ? $this->getCensoValueForDiscipline(29, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 64 - 29. Sociologia
            $canExportComponente ? $this->getCensoValueForDiscipline(30, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 65 - 30. Língua/Literatura Estrangeira - Francês
            $canExportComponente ? $this->getCensoValueForDiscipline(31, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 66 - 31. Língua Portuguesa como Segunda Língua
            $canExportComponente ? $this->getCensoValueForDiscipline(32, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 67 - 32. Estágio Curricular Supervisionado
            $canExportComponente ? $this->getCensoValueForDiscipline(33, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 68 - 33. Projeto de vida
            $canExportComponente ? $this->getCensoValueForDiscipline(99, $componentesEducacenso, $record->disciplinasEducacensoComDocentes) : '', // 69 - 99. Outras áreas do conhecimento
            $record->classeComLinguaBrasileiraSinais == 1 ? 1 : 0, // 70 - Turma de Educação Bilíngue de Surdos (classe bilíngue de surdos)
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
        $data->organizacaoCurricular = Portabilis_Utils_Database::pgArrayToArray($data->organizacaoCurricular);
        $data->tipoAtendimento = Portabilis_Utils_Database::pgArrayToArray($data->tipoAtendimento);
        $data->unidadesCurriculares = Portabilis_Utils_Database::pgArrayToArray($data->unidadesCurriculares);
        $data->unidadesCurricularesSemDocenteVinculado = Portabilis_Utils_Database::pgArrayToArray($data->unidadesCurricularesSemDocenteVinculado);
        $data->disciplinasEducacensoComDocentes = Portabilis_Utils_Database::pgArrayToArray($data->disciplinasEducacensoComDocentes);
        $data->areaItinerario = Portabilis_Utils_Database::pgArrayToArray($data->areaItinerario);

        return $data;
    }

    private function copyByPeriod($record)
    {
        if ($record->turmaTurnoId !== Period::FULLTIME) {
            return [$record];
        }

        $service = new SchoolClassService;

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
