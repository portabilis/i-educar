<?php

require_once 'CoreExt/Enum.php';

class ComponenteCurricular_Model_CodigoEducacenso extends CoreExt_Enum
{
    protected $_data = [
        null => 'Selecione',
        1 => 'Química',
        2 => 'Física',
        3 => 'Matemática',
        4 => 'Biologia',
        5 => 'Ciências',
        6 => 'Língua/Literatura portuguesa',
        7 => 'Língua/Literatura extrangeira - Inglês',
        8 => 'Língua/Literatura extrangeira - Espanhol',
        30 => 'Língua/Literatura extrangeira - Francês',
        9 => 'Língua/Literatura extrangeira - Outra',
        10 => 'Artes (educação artística, teatro, dança, música, artes plásticas e outras)',
        11 => 'Educação física',
        12 => 'História',
        13 => 'Geografia',
        14 => 'Filosofia',
        28 => 'Estudos sociais',
        29 => 'Sociologia',
        16 => 'Informática/Computação',
        17 => 'Disciplinas dos Cursos Técnicos Profissionais;',
        23 => 'LIBRAS',
        25 => 'Disciplinas pedagógicas',
        26 => 'Ensino religioso',
        27 => 'Língua indígena',
        31 => 'Língua Portuguesa como Segunda Língua',
        32 => 'Estágio Curricular Supervisionado',
        99 => 'Outras disciplinas'
    ];

    public function getData()
    {
        return $this->_data;
    }

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
