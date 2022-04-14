<?php

/**
 * App_Model_NivelAcesso class.
 *
 * Define os valores inteiros usados nas comparações das verificações de
 * acesso da classe clsPermissoes.
 *
 * Esses valores são verificados com o uso do operador binário &, resultando
 * na seguinte tabela verdade:
 *
 * <code>
 * +------------------------+---+---+---+----+
 * | Nível acessos          | 1 | 3 | 7 | 11 |
 * +------------------------+---+---+---+----+
 * | Poli-institucional (1) | T | T | T |  T |
 * +------------------------+---+---+---+----+
 * | Institucional      (2) | F | T | T |  T |
 * +------------------------+---+---+---+----+
 * | Escola             (4) | F | F | T |  F |
 * +------------------------+---+---+---+----+
 * | Biblioteca         (8) | F | F | F |  T |
 * +------------------------+---+---+---+----+
 *
 * Onde, T = TRUE; F = FALSE
 * </code>
 */
class App_Model_NivelAcesso extends CoreExt_Enum
{
    const POLI_INSTITUCIONAL = 1;
    const INSTITUCIONAL = 3;
    const SOMENTE_ESCOLA = 7;
    const SOMENTE_BIBLIOTECA = 11;

    protected $_data = [
        self::POLI_INSTITUCIONAL => 'Poli-institucional',
        self::INSTITUCIONAL => 'Institucional',
        self::SOMENTE_ESCOLA => 'Somente escola',
        self::SOMENTE_BIBLIOTECA => 'Somente biblioteca'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
