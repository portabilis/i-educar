<?php

class App_Model_LocalFuncionamentoDiferenciado extends CoreExt_Enum
{
    const NAO_ESTA = 0;
    const SALA_ANEXA = 1;
    const UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO = 2;
    const UNIDADE_PRISIONAL = 3;

    protected $_data = [
        self::NAO_ESTA => 'A turma não está em local de funcionamento diferenciado',
        self::SALA_ANEXA => 'Sala anexa',
        self::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO => 'Unidade de atendimento socioeducativo',
        self::UNIDADE_PRISIONAL => 'Unidade prisional',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
