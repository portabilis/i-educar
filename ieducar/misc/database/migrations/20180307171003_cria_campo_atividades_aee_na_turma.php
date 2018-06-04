<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoAtividadesAeeNaTurma extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.turma ADD COLUMN atividades_aee INTEGER[];");
        $this->execute("UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 1)
                         WHERE aee_braille = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 2)
                         WHERE aee_recurso_optico = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 3)
                         WHERE aee_estrategia_desenvolvimento = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 4)
                         WHERE aee_tecnica_mobilidade = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 5)
                         WHERE aee_libras = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 6)
                         WHERE aee_caa = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 7)
                         WHERE aee_curricular = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 8)
                         WHERE aee_soroban = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 9)
                         WHERE aee_informatica = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 10)
                         WHERE aee_lingua_escrita = 1;
                        
                        UPDATE pmieducar.turma
                           SET atividades_aee = array_append(atividades_aee, 11)
                         WHERE aee_autonomia = 1;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN atividades_aee;");
    }
}
