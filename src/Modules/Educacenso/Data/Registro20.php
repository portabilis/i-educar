<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro10 as Registro10Model;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Date_Utils;
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
     * @param $year
     * @return array
     */
    public function getData($school, $year)
    {
        $data = $this->repository->getDataForRecord20($school, $year);

        $models = [];
        foreach ($data as $record) {
            $record = $this->processData($record);
            $models[] = $this->hydrateModel($record);
        }

        return $models;
    }

    /**
     * @param $escola
     * @param $year
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
            '' , //  4 Código da Turma - Inep
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
            $record->atividadeComplementar() ? ($record->atividadesComplementares[0] ?? '') : '', //  21 Código 1 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[1] ?? '') : '', //  22 Código 2 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[2] ?? '') : '', //  23 Código 3 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[3] ?? '') : '', //  24 Código 4 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[4] ?? '') : '', //  25 Código 5 - Tipos de atividades complementares
            $record->atividadeComplementar() ? ($record->atividadesComplementares[5] ?? '') : '', //  26 Código 6 - Tipos de atividades complementares
            $record->educacaoDistancia() ? '' : $record->localFuncionamentoDiferenciado, //  27 Local de funcionamento diferenciado
            $record->escolarizacao() ? $record->modalidadeCurso : '', //  28 Modalidade
            $record->escolarizacao() ? $record->etapaEducacenso : '', //  29 Etapa
            in_array($record->etapaEducacenso, [30, 31, 32, 33, 34, 39, 40, 64, 74]) ? $record->codCursoProfissional : '', //  30 Código Curso
            $canExportComponente ? (int) in_array(1, $componentesEducacenso) : '', //  31 1. Química
            $canExportComponente ? (int) in_array(2, $componentesEducacenso) : '', //  32 2. Física
            $canExportComponente ? (int) in_array(3, $componentesEducacenso) : '', //  33 3. Matemática
            $canExportComponente ? (int) in_array(4, $componentesEducacenso) : '', //  34 4. Biologia
            $canExportComponente ? (int) in_array(5, $componentesEducacenso) : '', //  35 5. Ciências
            $canExportComponente ? (int) in_array(6, $componentesEducacenso) : '', //  36 6. Língua/Literatura Portuguesa
            $canExportComponente ? (int) in_array(7, $componentesEducacenso) : '', //  37 7. Língua/Literatura Estrangeira – Inglês
            $canExportComponente ? (int) in_array(8, $componentesEducacenso) : '', //  38 8. Língua/Literatura Estrangeira – Espanhol
            $canExportComponente ? (int) in_array(9, $componentesEducacenso) : '', //  39 9. Língua/Literatura Estrangeira – outra
            $canExportComponente ? (int) in_array(10, $componentesEducacenso) : '', //  40 10. Arte
            $canExportComponente ? (int) in_array(11, $componentesEducacenso) : '', //  41 11. Educação Física
            $canExportComponente ? (int) in_array(12, $componentesEducacenso) : '', //  42 12. História
            $canExportComponente ? (int) in_array(13, $componentesEducacenso) : '', //  43 13. Geografia
            $canExportComponente ? (int) in_array(14, $componentesEducacenso) : '', //  44 14. Filosofia
            $canExportComponente ? (int) in_array(16, $componentesEducacenso) : '', //  45 16. Informática/ Computação
            $canExportComponente ? (int) in_array(17, $componentesEducacenso) : '', //  46 17. Disciplinas dos Cursos Técnicos Profissionais
            $canExportComponente ? (int) in_array(23, $componentesEducacenso) : '', //  47 23. Libras
            $canExportComponente ? (int) in_array(25, $componentesEducacenso) : '', //  48 25. Disciplinas Pedagógicas
            $canExportComponente ? (int) in_array(26, $componentesEducacenso) : '', //  49 26. Ensino Religioso
            $canExportComponente ? (int) in_array(27, $componentesEducacenso) : '', //  50 27. Língua Indígena
            $canExportComponente ? (int) in_array(28, $componentesEducacenso) : '', //  51 28. Estudos Sociais
            $canExportComponente ? (int) in_array(29, $componentesEducacenso) : '', //  52 29. Sociologia
            $canExportComponente ? (int) in_array(30, $componentesEducacenso) : '', //  53 30. Língua/Literatura Estrangeira – Francês
            $canExportComponente ? (int) in_array(31, $componentesEducacenso) : '', //  54 31. Língua Portuguesa como Segunda Língua
            $canExportComponente ? (int) in_array(32, $componentesEducacenso) : '', //  55 32. Estágio Curricular Supervisionado
            $canExportComponente ? (int) in_array(99, $componentesEducacenso) : '', //  56 99. Outras disciplinas
        ];
    }

    /**
     * @param $schoolClassId
     * @param $disciplineIds
     * @return array
     */
    public function getDisciplinesWithoutTeacher($schoolClassId, $disciplineIds)
    {
        return $this->repository->getDisciplinesWithoutTeacher($schoolClassId, $disciplineIds);
    }

    /**
     * @param $data
     */
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

        return $data;
    }
}
