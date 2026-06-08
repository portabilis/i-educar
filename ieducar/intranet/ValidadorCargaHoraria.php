<?php

class ValidadorCargaHoraria 
{
    public function validar(string $carga): bool 
    {
        $cargaFormatada = trim($carga);
        if (!preg_match('/^(\d+):([0-5]\d)$/', $cargaFormatada, $matches)) {
            return false;
        }
        return (int)$matches[1] > 0 || (int)$matches[2] > 0;
    }
}