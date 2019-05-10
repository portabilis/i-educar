<?php

namespace iEducar\Modules\Educacenso\Analysis;

use App\Models\Educacenso\RegistroEducacenso;

class Register30ManagerDataAnalysis implements AnalysisInterface
{
    private $data;

    /**
     * @var array
     */
    private $messages;

    public function __construct(RegistroEducacenso $data)
    {
        $this->data = $data;
    }

    public function run()
    {
        if (true) {
            $this->messages[] = [
                'text' => '',
                'path' => '',
                'linkPath' => '',
                'fail' => true,
            ];
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }
}