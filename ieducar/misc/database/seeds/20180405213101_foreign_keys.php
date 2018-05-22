<?php


use Phinx\Migration\AbstractMigration;

class ForeignKeys extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->execute('
        --
        -- Name: menu_tipo_usuario_ref_cod_menu_submenu_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: postgres
        --
        
        ALTER TABLE ONLY menu_tipo_usuario
            ADD CONSTRAINT menu_tipo_usuario_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;
        
        
        --
        -- Name: menu_tipo_usuario_ref_cod_tipo_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: postgres
        --
        
        ALTER TABLE ONLY menu_tipo_usuario
            ADD CONSTRAINT menu_tipo_usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;
        
        
        --
        -- Name: menu_funcionario_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: postgres
        --
        
        ALTER TABLE ONLY portal.menu_funcionario
            ADD CONSTRAINT menu_funcionario_ibfk_1 FOREIGN KEY (ref_cod_menu_submenu) REFERENCES menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;

        ');
    }
}
