<?php

namespace iEducar\Modules\Educacenso\Validator;

class Telefone implements EducacensoValidator
{
    private $message;

    private $nomeCampo;

    private $valor;

    public function __construct($nomeCampo, $valor)
    {
        $this->nomeCampo = $nomeCampo;
        $this->valor = $valor;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $retorno = true;
        if (!$this->validaQuantidadeDeDigitosDoTelefone()) {
            $retorno = false;
        }

        if (!$this->validaPrimeiroDigitoDoTelefone()) {
            $retorno = false;
        }

        if (!$this->validaDigitosSequenciaisDoTelefone()) {
            $retorno = false;
        }

        return $retorno;
    }

    /**
     * @return bool
     */
    protected function validaQuantidadeDeDigitosDoTelefone()
    {
        $quantidadeDeDigitos = strlen($this->valor);

        if ($quantidadeDeDigitos < 8 || $quantidadeDeDigitos > 9) {
            $this->message[] = "O campo: {$this->nomeCampo} deve possuir de 8 a 9 números";

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function validaPrimeiroDigitoDoTelefone()
    {
        $quantidadeDeDigitos = strlen($this->valor);
        $primeiroDigito = substr($this->valor, 0, 1);

        if ($quantidadeDeDigitos == 9 && $primeiroDigito != 9) {
            $this->message[] = "No campo: {$this->nomeCampo} o primeiro dígito deve ser o número 9";

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function validaDigitosSequenciaisDoTelefone()
    {
        $possuiTodosOsDigitosRepetidos = preg_match('/^(.)\1*$/', $this->valor);

        if ($possuiTodosOsDigitosRepetidos) {
            $this->message[] = "Os números do campo: {$this->nomeCampo} não podem ser todos repetidos";

            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
