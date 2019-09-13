<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\RegistroEducacenso;

interface AnalysisInterface
{
    public function __construct(RegistroEducacenso $data);

    public function run();

    public function getMessages(): array;
}