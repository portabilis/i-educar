<?php

class App_Model_Educacenso
{
    public static function etapas_multisseriadas()
    {
        return array(12,13,22,23,24,72,56,64);
    }

    public static function etapasEnsinoUnificadas()
    {
        return array(3);
    }

    public static function etapasDaTurma($etapaEnsino)
    {
        $etapas = array();
        switch ($etapaEnsino) {
            case '12':
            case '13':
                $etapas = array(4, 5, 6, 7, 8, 9, 10, 11);
                break;
            case '22':
            case '23':
                $etapas = array(14, 15, 16, 17, 18, 19, 20, 21, 41);
                break;
            case '24':
                $etapas = array(4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 41);
                break;
            case '72':
                $etapas = array(69,70);
                break;
            case '56':
                $etapas = array(1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 41);
                break;
            case '64':
                $etapas = array(39, 40);
                break;
        }
        return $etapas;
    }
}
