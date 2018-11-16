<?php


use Phinx\Migration\AbstractMigration;

class AdicionaTurnoAoVinculoTurmaProfessor extends AbstractMigration
{
    /**
     * Up migration.
     *
     * @return void
     */
    public function up()
    {
        $this->execute('ALTER TABLE modules.professor_turma ADD turno_id int NULL;');

        $this->execute(
            '
                ALTER TABLE modules.professor_turma 
                ADD CONSTRAINT professor_turma_turma_turno_id_fk 
                FOREIGN KEY (turno_id) 
                REFERENCES pmieducar.turma_turno(id) 
                ON UPDATE RESTRICT 
                ON DELETE RESTRICT;
	        '
        );
    }

    /**
     * Down migration.
     *
     * @return void
     */
    public function down()
    {
        $this->execute('ALTER TABLE modules.professor_turma DROP CONSTRAINT professor_turma_turma_turno_id_fk;');

        $this->execute('ALTER TABLE modules.professor_turma DROP COLUMN turno_id;');
    }
}
