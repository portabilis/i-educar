<?php

namespace App\Services\Educacenso\Version2022\Models;

use App\Models\Educacenso\Registro20;
use App\Services\Educacenso\Version2019\Registro20Import;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;

class Registro20Model extends Registro20
{
    public function hydrateModel($arrayColumns)
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->registro = $arrayColumns[1];
        $this->codigoEscolaInep = $arrayColumns[2];
        $this->codTurma = $arrayColumns[3];
        $this->inepTurma = $arrayColumns[4];
        $this->nomeTurma = mb_convert_encoding($arrayColumns[5], 'ISO-8859-1', 'UTF-8');
        $this->tipoMediacaoDidaticoPedagogico = $arrayColumns[6];
        $this->horaInicial = $arrayColumns[7];
        $this->horaInicialMinuto = $arrayColumns[8];
        $this->horaFinal = $arrayColumns[9];
        $this->horaFinalMinuto = $arrayColumns[10];
        $this->diaSemanaDomingo = $arrayColumns[11];
        $this->diaSemanaSegunda = $arrayColumns[12];
        $this->diaSemanaTerca = $arrayColumns[13];
        $this->diaSemanaQuarta = $arrayColumns[14];
        $this->diaSemanaQuinta = $arrayColumns[15];
        $this->diaSemanaSexta = $arrayColumns[16];
        $this->diaSemanaSabado = $arrayColumns[17];
        $this->tipoAtendimentoEscolarizacao = $arrayColumns[18];
        $this->tipoAtendimentoAtividadeComplementar = $arrayColumns[19];
        $this->tipoAtendimentoAee = $arrayColumns[20];

        $this->estruturaCurricular = array_filter([
            $arrayColumns[21] ? EstruturaCurricular::FORMACAO_GERAL_BASICA : null,
            $arrayColumns[22] ? EstruturaCurricular::ITINERARIO_FORMATIVO : null,
            $arrayColumns[23] ? EstruturaCurricular::NAO_SE_APLICA : null,
        ]);

        $this->tipoAtividadeComplementar1 = $arrayColumns[24];
        $this->tipoAtividadeComplementar2 = $arrayColumns[25];
        $this->tipoAtividadeComplementar3 = $arrayColumns[26];
        $this->tipoAtividadeComplementar4 = $arrayColumns[27];
        $this->tipoAtividadeComplementar5 = $arrayColumns[28];
        $this->tipoAtividadeComplementar6 = $arrayColumns[29];
        $this->localFuncionamentoDiferenciado = $arrayColumns[30];
        $this->modalidadeCurso = $arrayColumns[31];
        $this->etapaEducacenso = $arrayColumns[32];
        $this->codCurso = $arrayColumns[33];

        $this->formasOrganizacaoTurma = array_filter([
            $arrayColumns[34] ? FormaOrganizacaoTurma::SERIE_ANO : null,
            $arrayColumns[35] ? FormaOrganizacaoTurma::SEMESTRAL : null,
            $arrayColumns[36] ? FormaOrganizacaoTurma::CICLOS : null,
            $arrayColumns[37] ? FormaOrganizacaoTurma::NAO_SERIADO : null,
            $arrayColumns[38] ? FormaOrganizacaoTurma::MODULES : null,
            $arrayColumns[39] ? FormaOrganizacaoTurma::ALTERNANCIA_REGULAR : null,
        ]);

        $this->formasOrganizacaoTurma = (count($this->formasOrganizacaoTurma) === 1) ? $this->formasOrganizacaoTurma[0] : null;

        $this->unidadesCurriculares = array_filter([
            $arrayColumns[40] ? UnidadesCurriculares::ELETIVAS : null,
            $arrayColumns[41] ? UnidadesCurriculares::LIBRAS : null,
            $arrayColumns[42] ? UnidadesCurriculares::LINGUA_INDIGENA : null,
            $arrayColumns[43] ? UnidadesCurriculares::LINGUA_LITERATURA_ESTRANGEIRA_ESPANHOL : null,
            $arrayColumns[44] ? UnidadesCurriculares::LINGUA_LITERATURA_ESTRANGEIRA_FRANCES : null,
            $arrayColumns[45] ? UnidadesCurriculares::LINGUA_LITERATURA_ESTRANGEIRA_OUTRA : null,
            $arrayColumns[46] ? UnidadesCurriculares::PROJETO_DE_VIDA : null,
            $arrayColumns[47] ? UnidadesCurriculares::TRILHAS_DE_APROFUNDAMENTO_APRENDIZAGENS : null,
        ]);

        $this->componentes = $this->getComponentesByImportFile(array_slice($arrayColumns, 47, 73));
    }

    private function getComponentesByImportFile($componentesImportacao)
    {
        $arrayComponentes = array_keys(Registro20Import::getComponentes());

        $componentesExistentes = [];
        foreach ($componentesImportacao as $key => $value) {
            if ($value != '1') {
                continue;
            }

            $componentesExistentes[] = $arrayComponentes[$key];
        }

        return $componentesExistentes;
    }
}
