<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\RegistroEducacenso;

class Register30StudentDataAnalysis implements AnalysisInterface
{
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

    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}