<?php

class Portabilis_Utils_Validation
{
    /**
    * Valida um CPF.
    *
    * @author Lucas D'Avila <lucasdavila@portabilis.com.br>
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $cpf CPF a ser validado.
    *
    * @return bool
    */
    public static function validatesCpf($cpf)
    {
        // converte o CPF para string e remove todos os caracteres que não números
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        // invalida se não tiver 11 caracteres
        if (mb_strlen($cpf) !== 11) {
            return false;
        }

        $cpfsInvalidos = [
            '00000000000',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
            '66666666666',
            '77777777777',
            '88888888888',
            '99999999999',
        ];

        // verifica se o CPF informado está entre os inválidos
        if (in_array($cpf, $cpfsInvalidos)) {
            return false;
        }

        // verifica se o módulo 11 bate com o primeiro dígito
        if (!self::isValidMod11Digit(substr($cpf, 0, 9), $cpf[9], '0')) {
            return false;
        }

        // verifica se o módulo 11 bate com o segundo dígito
        if (!self::isValidMod11Digit(substr($cpf, 0, 10), $cpf[10], '0')) {
            return false;
        }

        // finalmente valida o CPF
        return true;
    }

    /**
    * Valida os novos formatos de Certidão Civil.
    *
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $certidao Certidão a ser validada.
    * @param  bool  $throwException optional Se deseja lançar uma exceção caso a certidão seja inválida.
    *
    * @throws \Exception Se $certidao for inválida e $throwException for true.
    *
    * @return bool
    */
    public static function validatesCertidaoNovoFormato($certidao, $throwException = false)
    {
        // invalida se não tiver 32 caracteres
        if (mb_strlen($certidao) !== 32) {
            if ($throwException) {
                throw new \Exception('Tamanho inválido');
            }

            return false;
        }

        // converte para maiúsculas
        $certidao = mb_strtoupper($certidao);

        // obtém primeiros 30 dígitos
        $numeros = mb_substr($certidao, 0, 30);

        // obtém 1º e 2º digito verificador
        $primeiroDigito = mb_substr($certidao, 30, 1);
        $segundoDigito = mb_substr($certidao, 31, 1);

        // verifica se contém apenas caracteres válidos
        if (!self::hasOnlyValidChars($numeros, '/\d/')
            || !self::hasOnlyValidChars($primeiroDigito . $segundoDigito, '/\d|X/')) {
            if ($throwException) {
                throw new \Exception('Caractere(s) inválido(s)');
            }

            return false;
        }

        /*
         * verifica se o módulo 11 não foi calculado e portanto informado como XX conforme publicação do CNJ no
         * Diário da Justiça - Edição nº 198/2009 (Página 18) - Provimento nº 3, Artigo 7º, Inciso IX, Parágrafo 2º
         *
         * neste caso a certidão deve ser validada
         */
        if ($primeiroDigito . $segundoDigito === 'XX') {
            return true;
        }

        // verifica se o módulo 11 bate com o primeiro dígito
        if (!self::isValidMod11Digit($numeros, $primeiroDigito, '1')) {
            if ($throwException) {
                throw new \Exception('Valor inválido');
            }

            return false;
        }

        // verifica se o módulo 11 bate com o segundo dígito
        if (!self::isValidMod11Digit($numeros . $primeiroDigito, $segundoDigito, '1')) {
            if ($throwException) {
                throw new \Exception('Valor inválido');
            }

            return false;
        }

        // finalmente valida a certidão civil
        return true;
    }

    /**
    * Valida o tipo da Certidão Civil (novo formato).
    *
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $certidao Certidão a ser validada.
    * @param  string  $tipo Tipo da certidão a ser validada.
    *
    * @throws \Exception Se o $tipo fornecido for inválido.
    *
    * @return bool
    */
    public static function validatesTipoCertidaoNovoFormato($certidao, $tipo)
    {
        $tiposValidos = [
            '1', // Livro A (Nascimento)
            '2', // Livro B (Casamento)
            '3', // Livro B Auxiliar (Casamento Religioso com efeito civil)
            '4', // Livro C (Óbito)
            '5', // Livro C Auxiliar (Natimorto)
            '6', // Livro D (Registro de Proclamas)
            '7', // Livro E (Demais atos relativos ao registro civil ou livro E único)
            '8', // Livro E (Desdobrado para registro especifico das Emancipações)
            '9', // Livro E (Desdobrado para registro especifico das Interdições)
        ];

        if (!in_array($tipo, $tiposValidos)) {
            throw new \Exception('Tipo inválido');
        }

        if (substr($certidao, 14, 1) !== $tipo) {
            return false;
        }

        return true;
    }

    /**
    * Valida o ano da Certidão Civil (novo formato).
    *
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $certidao Certidão a ser validada.
    * @param  string  $anoBase optional Ano base de geração da certidão.
    *
    * @return bool
    */
    public static function validatesAnoCertidaoNovoFormato($certidao, $anoBase = null)
    {
        $ano = substr($certidao, 10, 4);
        $anoAtual = date('Y');

        if (($anoBase !== null && $ano < $anoBase) || $ano > $anoAtual) {
            return false;
        }

        return true;
    }

    /**
    * Verifica se uma string possui apenas caracteres válidos de acordo com a expressão regular fornecida.
    *
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $value String que será verificada.
    * @param  string  $validCharsRegex Expressão regular com os caracteres válidos.
    *
    * @return bool
    */
    public static function hasOnlyValidChars($value, $validCharsRegex)
    {
        if (mb_strlen(preg_replace($validCharsRegex, '', $value)) !== 0) {
            return false;
        }

        return true;
    }

    /**
    * Verifica se o dígito informado é o resultado do módulo 11 da string informada.
    *
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $value String que será calculado o módulo 11.
    * @param  string  $digit Dígito para verificar se bate com o módulo 11.
    * @param  string  $result10 String caso o módulo 11 seja 10.
    *
    * @return bool
    */
    public static function isValidMod11Digit($value, $digit, $result10)
    {
        if ($digit !== self::calcMod11($value, $result10)) {
            return false;
        }

        return true;
    }

    /**
    * Calcula o módulo 11 de uma string.
    *
    * @author Thieres Tembra <tdt@mytdt.com.br>
    *
    * @param  string  $value String que será calculado o módulo 11.
    * @param  string  $convertResult10 String que será retornada caso o módulo 11 seja 10.
    *
    * @return string
    */
    public static function calcMod11($value, $convertResult10)
    {
        $sum = 0;
        $mult = 9;

        for ($i = mb_strlen($value) - 1; $i >= 0; $i--) {
            $sum += $value[$i] * $mult--;
            if ($mult === -1) {
                $mult = 10;
            }
        }

        $mod = $sum % 11;

        if ($mod === 10) {
            return $convertResult10;
        }

        return (string) $mod;
    }
}
