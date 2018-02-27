<?php

use Phinx\Migration\AbstractMigration;

class RemoveCamposNaoUtilizados extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN agua_rede_publica;
                        ALTER TABLE pmieducar.escola DROP COLUMN agua_poco_artesiano;
                        ALTER TABLE pmieducar.escola DROP COLUMN agua_cacimba_cisterna_poco;
                        ALTER TABLE pmieducar.escola DROP COLUMN agua_fonte_rio;
                        ALTER TABLE pmieducar.escola DROP COLUMN agua_inexistente;
                        ALTER TABLE pmieducar.escola DROP COLUMN energia_rede_publica;
                        ALTER TABLE pmieducar.escola DROP COLUMN energia_gerador;
                        ALTER TABLE pmieducar.escola DROP COLUMN energia_outros;
                        ALTER TABLE pmieducar.escola DROP COLUMN energia_inexistente;
                        ALTER TABLE pmieducar.escola DROP COLUMN esgoto_rede_publica;
                        ALTER TABLE pmieducar.escola DROP COLUMN esgoto_fossa;
                        ALTER TABLE pmieducar.escola DROP COLUMN esgoto_inexistente;
                        ALTER TABLE pmieducar.escola DROP COLUMN lixo_coleta_periodica;
                        ALTER TABLE pmieducar.escola DROP COLUMN lixo_queima;
                        ALTER TABLE pmieducar.escola DROP COLUMN lixo_joga_outra_area;
                        ALTER TABLE pmieducar.escola DROP COLUMN lixo_recicla;
                        ALTER TABLE pmieducar.escola DROP COLUMN lixo_enterra;
                        ALTER TABLE pmieducar.escola DROP COLUMN lixo_outros;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN agua_rede_publica INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN agua_poco_artesiano INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN agua_cacimba_cisterna_poco INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN agua_fonte_rio INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN agua_inexistente INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN energia_rede_publica INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN energia_gerador INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN energia_outros INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN energia_inexistente INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN esgoto_rede_publica INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN esgoto_fossa INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN esgoto_inexistente INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN lixo_coleta_periodica INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN lixo_queima INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN lixo_joga_outra_area INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN lixo_recicla INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN lixo_enterra INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN lixo_outros INTEGER;");
    }
}
