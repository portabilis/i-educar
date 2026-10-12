<?php

namespace iEducar\Reports;

use iEducar\Reports\Contracts\ReportCardTypes;
use Portabilis_Model_Report_TipoBoletim;

/**
 * Implementação padrão do contrato: lê os tipos de boletim do pacote de
 * relatórios quando ele está instalado e devolve vazio quando não está.
 */
class DefaultReportCardTypes implements ReportCardTypes
{
    public function getEnums(): array
    {
        if (!$this->available()) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        return Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
        // @codeCoverageIgnoreEnd
    }

    public function getReports(): array
    {
        if (!$this->available()) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        return Portabilis_Model_Report_TipoBoletim::getInstance()->getReports();
        // @codeCoverageIgnoreEnd
    }

    private function available(): bool
    {
        return class_exists(Portabilis_Model_Report_TipoBoletim::class);
    }
}
