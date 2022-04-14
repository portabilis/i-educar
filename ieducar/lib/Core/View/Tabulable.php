<?php

interface Core_View_Tabulable
{
    /**
     * Retorna um array associativo no formato nome do campo => label.
     *
     * @return array
     */
    public function getTableMap();
}
