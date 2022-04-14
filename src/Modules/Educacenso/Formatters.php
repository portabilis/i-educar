<?php

namespace iEducar\Modules\Educacenso;

use Portabilis_String_Utils;

trait Formatters
{
    public function cpfToCenso($cpf)
    {
        $cpf = str_replace(['.', '-'], '', int2CPF($cpf));

        return $cpf == '00000000000' ? null : $cpf;
    }

    public function cnpjToCenso($cnpj)
    {
        $cnpj = str_replace(['.', '-', '/'], '', int2CNPJ($cnpj));

        return $cnpj == '00000000000000' ? null : $cnpj;
    }

    public function upperAndUnaccent($string)
    {
        $string = Portabilis_String_Utils::toUtf8($string);
        $string = preg_replace(
            [
                '/(á|à|ã|â|ä)/',
                '/(Á|À|Ã|Â|Ä)/',
                '/(é|è|ê|ë)/',
                '/(É|È|Ê|Ë)/',
                '/(í|ì|î|ï)/',
                '/(Í|Ì|Î|Ï)/',
                '/(ó|ò|õ|ô|ö)/',
                '/(Ó|Ò|Õ|Ô|Ö)/',
                '/(ú|ù|û|ü)/',
                '/(Ú|Ù|Û|Ü)/',
                '/(ñ)/',
                '/(Ñ)/',
                '/(ç)/',
                '/(Ç)/'
            ],
            explode(' ', 'a A e E i I o O u U n N c C'),
            $string
        );

        return mb_strtoupper($string);
    }

    public function convertStringToAlpha($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas letras
        $alphas = range('A', 'Z');
        $caracteresAceitos = [' '];
        $caracteresAceitos = array_merge($alphas, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        //Elimina espaços indesejados
        $string = trim($string);
        $string = preg_replace('/( )+/', ' ', $string);

        return $string;
    }

    public function convertStringToCenso($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas letras e numeros e alguns caracteres especiais
        $alphas = range('A', 'Z');
        $numbers = range(0, 9);
        $caracteresAceitos = [' ', 'ª', 'º', '-'];
        $caracteresAceitos = array_merge($numbers, $caracteresAceitos);
        $caracteresAceitos = array_merge($alphas, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        //Elimina espaços indesejados
        $string = trim($string);
        $string = preg_replace('/( )+/', ' ', $string);

        return $string;
    }

    public function convertStringToCertNovoFormato($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas números e letra X
        $numbers = range(0, 9);
        $caracteresAceitos = [' ', 'x', 'X'];
        $caracteresAceitos = array_merge($numbers, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        return $string;
    }

    public function convertEmailToCenso($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas letras e numeros e alguns caracteres especiais
        $alphas = range('A', 'Z');
        $numbers = range(0, 9);
        $caracteresAceitos = ['_', '-', '@', '.'];
        $caracteresAceitos = array_merge($numbers, $caracteresAceitos);
        $caracteresAceitos = array_merge($alphas, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        return $string;
    }
}
