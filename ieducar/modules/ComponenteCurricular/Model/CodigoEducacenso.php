<?php

use iEducar\Support\DescriptionValue;

class ComponenteCurricular_Model_CodigoEducacenso extends CoreExt_Enum
{
    use DescriptionValue;

    protected $_data = [
        null => 'Selecione',
        1 => 'Química',
        2 => 'Física',
        3 => 'Matemática',
        4 => 'Biologia',
        5 => 'Ciências',
        6 => 'Língua / Literatura Portuguesa',
        7 => 'Língua / Literatura Estrangeira - Inglês',
        8 => 'Língua / Literatura Estrangeira - Espanhol',
        30 => 'Língua / Literatura Estrangeira - Francês',
        9 => 'Língua / Literatura Estrangeira - Outra',
        10 => 'Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)',
        11 => 'Educação Física',
        12 => 'História',
        13 => 'Geografia',
        14 => 'Filosofia',
        28 => 'Estudos Sociais',
        29 => 'Sociologia',
        16 => 'Informática / Computação',
        17 => 'Áreas do conhecimento profissionalizantes ',
        23 => 'Libras',
        25 => 'Áreas do conhecimento pedagógicas ',
        26 => 'Ensino religioso',
        27 => 'Língua indígena',
        31 => 'Língua Portuguesa como Segunda Língua',
        32 => 'Estágio Curricular Supervisionado',
        33 => 'Projeto de vida',
        99 => 'Outras áreas do conhecimento',
    ];

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return $this
     */
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return self::getInstance()->getData();
    }
}
