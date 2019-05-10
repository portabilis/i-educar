<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;

class Register30CommonDataAnalysis implements AnalysisInterface
{
    /**
     * @var Registro30
     */
    private $data;

    /**
     * @var array
     */
    private $messages = [];

    public function __construct(RegistroEducacenso $data)
    {
        $this->data = $data;
    }

    public function run()
    {
        $data = $this->data;


    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}