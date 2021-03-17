<?php



class CoreExt_EnumTipoSanguineoStub extends CoreExt_Enum
{
    const A = 1;
    const B = 2;
    const AB = 3;
    const O = 4;

    protected $_data = [
        self::A => 'A',
        self::B => 'B',
        self::AB => 'AB',
        self::O => 'O'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
