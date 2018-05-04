<?php

class alteraAtestadoParaDeclaracao
{
    var $altera_atestado_para_declaracao;

    /**
     * alteraAtestadoParaDeclaracao constructor.
     */
    function __construct($altera_atestado_para_declaracao)
    {
        $this->altera_atestado_para_declaracao = $altera_atestado_para_declaracao;
    }

    /**
     * @return bool
     * @throws Exception
     * Altera de  atestado para declaração e
     * declaração para atestado submenus
     */
    function editaMenus()
    {
        $this->editaPmicontrolesisMenu();
        $this->editaPortalSubmenu();
        $this->editaMenu();
    }

    protected function editaPmicontrolesisMenu()
    {
        $db = new clsBanco();
        $set = '';
        // Cod_menu que não devem ser alterados, mesmo quando possuem o nome de Declaração ou Atestado
        $excecao = implode(', ', array(999229, 999812));

        if (dbBool($this->altera_atestado_para_declaracao)) {
            $set .= "tt_menu = REPLACE(tt_menu, 'Atestado' , 'Declaração') ";
            $busca = 'Atestado';
        } else {
            $set .= "tt_menu = REPLACE(tt_menu, 'Declaração' , 'Atestado') ";
            $busca = 'Declaração';
        }

        if ($set) {
            $db->Consulta("UPDATE pmicontrolesis.menu SET $set WHERE tt_menu like '{$busca}%' AND ref_cod_menu_submenu is not null AND cod_menu not in ({$excecao})");
            return true;
        }
        return false;
    }

    protected function editaPortalSubmenu()
    {
        $db = new clsBanco();
        $set = '';

        // Cod_menu que não devem ser alterados, mesmo quando possuem o nome de Declaração ou Atestado
        $excecao = implode(', ', array(999229, 999812));

        if (dbBool($this->altera_atestado_para_declaracao)) {
            $set .= "nm_submenu = REPLACE(nm_submenu, 'Atestado' , 'Declaração') ";
            $busca = 'Atestado';
        } else {
            $set .= "nm_submenu = REPLACE(nm_submenu, 'Declaração' , 'Atestado') ";
            $busca = 'Declaração';
        }

        if ($set) {
            $db->Consulta("UPDATE portal.menu_submenu SET $set WHERE nm_submenu like '{$busca}%' AND cod_menu_submenu not in ({$excecao})");
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws Exception
     * Altera de  atestados para declarações e
     * declarações para atestados menus
     */
    protected function editaMenu()
    {
        $db = new clsBanco();
        $set = '';

        if (dbBool($this->altera_atestado_para_declaracao)) {
            $set .= "tt_menu = 'Declarações' ";
            $busca = 'Atestados';
        } else {
            $set .= "tt_menu = 'Atestados'";
            $busca = 'Declarações';
        }

        if ($set) {
            $db->Consulta("UPDATE pmicontrolesis.menu SET $set WHERE tt_menu = '{$busca}' AND ref_cod_menu_submenu is null");
            return true;
        }
        return false;
    }
}