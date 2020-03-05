<?php

namespace App\Services\SebExport;

use App\Queries\SebExport\ExportQuery;
use Illuminate\Database\Eloquent\Collection;

class ExportService
{
    private $converter;

    public function __construct(EducacensoStepConverter $converter)
    {
        $this->converter = $converter;
    }

    public function export(array $filters) : string
    {
        $export = '';
        foreach ($this->records($filters) as $record) {
            $export .= $this->makeLine($record) . "\n";
        }

        return $export;
    }

    private function makeLine($record) : string
    {
        $line = "{$record->ano};";
        $line .= "{$record->inep_escola};";
        $line .= "{$record->cpf};";
        $line .= "{$record->nome_social};";
        $line .= "{$record->cod_matricula};";
        $line .= $this->convertEducacensoStep($record->etapa_educacenso) . ';';
        $line .= ($record->emancipado ? 1 : 0) . ';';

        $countResponsibles = 0;
        if ($record->cpf_mae) {
            $countResponsibles++;
            $line .= "{$record->cpf_mae};1;";
        }

        if ($record->cpf_pai) {
            $countResponsibles++;
            $line .= "{$record->cpf_pai};2;";
        }

        if ($record->cpf_responsavel) {
            $countResponsibles++;
            $line .= "{$record->cpf_responsavel};3;";
        }

        for ($i = $countResponsibles; $i < 3; $i++) {
            $line .= ';;';
        }

        return substr($line, 0, -1);
    }

    private function convertEducacensoStep(int $educacensoStep) : string
    {
        return $this->converter->convert($educacensoStep);
    }

    private function records(array $filters) : Collection
    {
        return (new ExportQuery())->query($filters)->get();
    }
}
