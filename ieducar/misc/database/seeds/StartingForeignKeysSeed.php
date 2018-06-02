<?php

use Phinx\Seed\AbstractSeed;

class StartingForeignKeysSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $this->execute('
        --
        -- Name: menu_tipo_usuario_ref_cod_menu_submenu_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: postgres
        --
        
        ALTER TABLE ONLY pmieducar.menu_tipo_usuario
            ADD CONSTRAINT menu_tipo_usuario_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;
        
        
        --
        -- Name: menu_tipo_usuario_ref_cod_tipo_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: postgres
        --
        
        ALTER TABLE ONLY pmieducar.menu_tipo_usuario
            ADD CONSTRAINT menu_tipo_usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES pmieducar.tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;
        
        
        --
        -- Name: menu_funcionario_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: postgres
        --
        
        ALTER TABLE ONLY portal.menu_funcionario
            ADD CONSTRAINT menu_funcionario_ibfk_1 FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;

        ');
    }
}
