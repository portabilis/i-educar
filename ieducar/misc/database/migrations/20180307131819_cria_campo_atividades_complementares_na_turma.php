<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoAtividadesComplementaresNaTurma extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.turma ADD COLUMN atividades_complementares INTEGER[];");
        $this->execute("UPDATE pmieducar.turma
                           SET atividades_complementares = array_append(atividades_complementares, atividade_complementar_1)
                         WHERE atividade_complementar_1 IS NOT NULL;

                         UPDATE pmieducar.turma
                           SET atividades_complementares = array_append(atividades_complementares, atividade_complementar_2)
                         WHERE atividade_complementar_2 IS NOT NULL;

                         UPDATE pmieducar.turma
                           SET atividades_complementares = array_append(atividades_complementares, atividade_complementar_3)
                         WHERE atividade_complementar_3 IS NOT NULL;

                         UPDATE pmieducar.turma
                           SET atividades_complementares = array_append(atividades_complementares, atividade_complementar_4)
                         WHERE atividade_complementar_4 IS NOT NULL;

                         UPDATE pmieducar.turma
                           SET atividades_complementares = array_append(atividades_complementares, atividade_complementar_5)
                         WHERE atividade_complementar_5 IS NOT NULL;

                         UPDATE pmieducar.turma
                           SET atividades_complementares = array_append(atividades_complementares, atividade_complementar_6)
                         WHERE atividade_complementar_6 IS NOT NULL;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN atividades_complementares;");
    }
}
