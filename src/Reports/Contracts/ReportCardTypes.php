<?php

namespace iEducar\Reports\Contracts;

interface ReportCardTypes
{
    /**
     * Tipos de boletim disponíveis, indexados pelo código gravado na turma.
     *
     * @return array<int, string>
     */
    public function getEnums(): array;

    /**
     * Chave do relatório de cada tipo de boletim, indexada pelo código.
     *
     * @return array<int, string>
     */
    public function getReports(): array;
}
