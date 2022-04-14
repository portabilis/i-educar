<?php

namespace iEducar\Modules\Educacenso\Validator;

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\Regulamentacao;

class CnpjMantenedoraPrivada implements EducacensoValidator
{
    private $message;

    private $escola;

    public function __construct(Registro00 $escola)
    {
        $this->escola = $escola;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->escola->cnpjMantenedoraPrincipal) {
            return true;
        }

        if ($this->escola->mantenedoraEscolaPrivada != MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS) {
            return true;
        }

        if ($this->escola->regulamentacao != Regulamentacao::SIM) {
            return true;
        }

        $this->message = "Dados para formular o registro 00 da escola {$this->escola->nome} não encontrados. Verificamos que a mantenedora da escola é uma Instituição sem fins lucrativos e a escola é regulamentada pelo conselho/órgão, portanto é necessário informar o CNPJ da mantenedora principal desta unidade escolar;";

        return false;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
