<?php

namespace App\Services\Educacenso\Version2019\Models;

use App\Models\Educacenso\Registro20;
use App\Services\Educacenso\Version2019\Registro20Import;

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
        $this->nomeTurma = utf8_decode($arrayColumns[5]);
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
        $this->tipoAtividadeComplementar1 = $arrayColumns[21];
        $this->tipoAtividadeComplementar2 = $arrayColumns[22];
        $this->tipoAtividadeComplementar3 = $arrayColumns[23];
        $this->tipoAtividadeComplementar4 = $arrayColumns[24];
        $this->tipoAtividadeComplementar5 = $arrayColumns[25];
        $this->tipoAtividadeComplementar6 = $arrayColumns[26];
        $this->localFuncionamentoDiferenciado = $arrayColumns[27];
        $this->modalidadeCurso = $arrayColumns[28];
        $this->etapaEducacenso = $arrayColumns[29];
        $this->codCurso = $arrayColumns[30];
        $this->componentes = $this->getComponentesByImportFile(array_slice($arrayColumns, 30, 26));
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
