<?php

namespace iEducar\Modules\Educacenso\Validator;

use App\Models\Educacenso\Registro20;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

class FormaOrganizacaoTurma implements EducacensoValidator
{
    private $turma;

    public function __construct(Registro20 $turma)
    {
        $this->turma = $turma;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (empty($this->turma->formasOrganizacaoTurma)) {
            return true;
        }

        if ($this->turma->tipoAtendimento !== TipoAtendimentoTurma::ESCOLARIZACAO) {
            return true;
        }

        if (in_array($this->turma->etapaEducacenso, [1, 2, 3, 24])) {
            return true;
        }

        $validOptionForEducacensoStage = [
            1 => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 69, 70, 71, 72, 73, 74, 67
            ],
            2 => [
                25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 69, 70, 71, 72, 73, 74, 67, 68
            ],
            3 => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56
            ],
            4 => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 69, 70, 71, 72, 73, 74, 67, 68
            ],
            5 => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 69, 70, 71, 72, 73, 74, 67,68
            ],
            6 => [
                19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 69, 70, 71, 72, 73, 74, 67, 68
            ]
        ];

        return in_array($this->turma->etapaEducacenso, $validOptionForEducacensoStage[$this->turma->formasOrganizacaoTurma]);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return 'Teste';
    }
}
