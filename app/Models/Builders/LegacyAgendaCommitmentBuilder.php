<?php

namespace App\Models\Builders;

class LegacyAgendaCommitmentBuilder extends LegacyBuilder
{
    /**
     * Filtra os compromissos ativos
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }

    /**
     * Filtra por compromisso, incluindo todas as suas versões
     */
    public function whereCommitment(int $commitment): self
    {
        return $this->where('cod_agenda_compromisso', $commitment);
    }

    /**
     * Filtra por agenda
     */
    public function whereAgenda(int $agenda): self
    {
        return $this->where('ref_cod_agenda', $agenda);
    }

    /**
     * Filtra por versão
     */
    public function whereVersion(int $version): self
    {
        return $this->where('versao', $version);
    }

    /**
     * Filtra os compromissos iniciados dentro do intervalo
     */
    public function betweenStartDates(string $start, string $end): self
    {
        return $this->whereBetween('data_inicio', [$start, $end]);
    }

    /**
     * Filtra os compromissos que possuem data de fim, excluindo as anotações
     */
    public function withEndDate(): self
    {
        return $this->whereNotNull('data_fim');
    }
}
