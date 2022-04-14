<?php

class Avaliacao_Service_AllTests extends TestCollector
{
    protected $_name = 'Suíte de testes do service Avaliacao_Service_Boletim do módulo Avaliacao';
    protected $_file = __FILE__;

    public static function suite()
    {
        $instance = new self();

        return $instance->addDirectoryTests();
    }
}
