<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\RegistroEducacenso;

interface EducacensoExportRule
{
    public static function handle(RegistroEducacenso $registro): RegistroEducacenso;
}
