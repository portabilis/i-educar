<?php

class ValidadorInscricaoEstadual 
{
    /**
     * Valida o formato numérico da Inscrição Estadual 
     * ou aceita a flag de isenção.
     */
    public function validar(string $ie): bool 
    {
        $ieFormatada = trim($ie);
        return ctype_digit($ieFormatada) || strtoupper($ieFormatada) === 'ISENTO';
    }
}