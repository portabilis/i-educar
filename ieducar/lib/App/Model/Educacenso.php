<?php

class App_Model_Educacenso
{
    public static function etapas_multisseriadas()
    {
        return [12,13,22,23,24,72,56,64];
    }

    public static function etapasDaTurma($etapaEnsino)
    {
        $etapas = [];
        switch ($etapaEnsino) {
            case '12':
            case '13':
                $etapas = [4, 5, 6, 7, 8, 9, 10, 11];
                break;
            case '22':
            case '23':
                $etapas = [14, 15, 16, 17, 18, 19, 20, 21, 41];
                break;
            case '24':
                $etapas = [4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 41];
                break;
            case '72':
                $etapas = [69,70];
                break;
            case '56':
                $etapas = [1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 41];
                break;
            case '64':
                $etapas = [39, 40];
                break;
        }

        return $etapas;
    }
}
