<?php

class Avaliacao_AllTests extends UnitBaseTest
{
    protected $_name = 'Suíte de testes do módulo Avaliacao';

    protected $_file = __FILE__;

    public static function suite()
    {
        $instance = new self();

        return $instance->addDirectoryTests();
    }
}
