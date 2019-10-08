<?php

namespace App\Models\Educacenso;

interface RegistroEducacenso
{
    /**
     * Retorna a propriedade da classe correspondente ao dado no arquivo do censo
     *
     * @param int $column
     * @return string
     */
    public function getProperty($column);
}
