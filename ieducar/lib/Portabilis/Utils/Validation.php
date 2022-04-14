<?php

class Portabilis_Utils_Validation
{
    public static function validatesCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) != 11) {
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
            '99999999999'
        ];

        if (in_array($cpf, $cpfsInvalidos)) {
            return false;
        }

        // calcula primeiro dígito verificador
        $soma = 0;

        for ($i = 0; $i < 9; $i++) {
            $soma += ((10 - $i) * $cpf[$i]);
        }

        $primeiroDigito = 11 - ($soma % 11);

        if ($primeiroDigito >= 10) {
            $primeiroDigito = 0;
        }

        // calcula segundo dígito verificador
        $soma = 0;

        for ($i = 0; $i < 10; $i++) {
            $soma += ((11 - $i) * $cpf[$i]);
        }

        $segundoDigito = 11 - ($soma % 11);

        if ($segundoDigito >= 10) {
            $segundoDigito = 0;
        }

        return ($primeiroDigito == $cpf[9] && $segundoDigito == $cpf[10]);
    }
}
