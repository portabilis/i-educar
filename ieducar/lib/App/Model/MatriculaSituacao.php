<?php

require_once 'CoreExt/Enum.php';

class App_Model_MatriculaSituacao extends CoreExt_Enum
{
    const APROVADO = 1;
    const REPROVADO = 2;
    const EM_ANDAMENTO = 3;
    const TRANSFERIDO = 4;
    const RECLASSIFICADO = 5;
    const ABANDONO = 6;
    const EM_EXAME = 7;
    const APROVADO_APOS_EXAME = 8;
    const APROVADO_SEM_EXAME = 10;
    const PRE_MATRICULA = 11;
    const APROVADO_COM_DEPENDENCIA = 12;
    const APROVADO_PELO_CONSELHO = 13;
    const REPROVADO_POR_FALTAS = 14;
    const FALECIDO = 15;

    protected $_data = [
        self::APROVADO => 'Aprovado',
        self::REPROVADO => 'Retido',
        self::EM_ANDAMENTO => 'Cursando',
        self::TRANSFERIDO => 'Transferido',
        self::RECLASSIFICADO => 'Reclassificado',
        self::ABANDONO => 'Abandono',
        self::EM_EXAME => 'Em exame',
        self::APROVADO_APOS_EXAME => 'Aprovado após exame',
        self::PRE_MATRICULA => 'Pré-matrícula',
        self::APROVADO_COM_DEPENDENCIA => 'Aprovado com dependência',
        self::APROVADO_PELO_CONSELHO => 'Aprovado pelo conselho',
        self::REPROVADO_POR_FALTAS => 'Reprovado por faltas',
        self::FALECIDO => 'Falecido'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

    public static function getSituacao($id)
    {
        $instance = self::getInstance()->_data;

        return $instance[$id];
    }

    /**
     * Retorna todas as situação da matrícula consideradas "finais".
     *
     * @return array
     */
    public static function getSituacoesFinais()
    {
        return [
            self::APROVADO,
            self::REPROVADO,
            self::APROVADO_APOS_EXAME,
            self::APROVADO_COM_DEPENDENCIA,
            self::APROVADO_PELO_CONSELHO,
            self::REPROVADO_POR_FALTAS,
        ];
    }
}
