<?php

use Phinx\Migration\AbstractMigration;

class RemoveTabelaTurmaDiaSemana extends AbstractMigration
{
    public function up()
    {
        $this->execute('DROP TABLE pmieducar.turma_dia_semana;');
    }

    public function down()
    {
        $this->execute(
            'CREATE TABLE pmieducar.turma_dia_semana
            (
                dia_semana NUMERIC(1,0) NOT NULL,
                ref_cod_turma INTEGER NOT NULL,
                hora_inicial TIME WITHOUT TIME ZONE,
                hora_final TIME WITHOUT TIME ZONE,
                CONSTRAINT turma_dia_semana_pkey PRIMARY KEY (dia_semana, ref_cod_turma),
                CONSTRAINT turma_dia_semana_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma)
                    REFERENCES pmieducar.turma (cod_turma) MATCH SIMPLE
                    ON UPDATE RESTRICT ON DELETE RESTRICT
            )
            WITH (
              OIDS=TRUE
            );'
        );

        $this->execute(
            'CREATE TRIGGER fcn_aft_update
             AFTER INSERT OR UPDATE
             ON pmieducar.turma_dia_semana
             FOR EACH ROW
             EXECUTE PROCEDURE pmieducar.fcn_aft_update();'
        );
    }
    
}
