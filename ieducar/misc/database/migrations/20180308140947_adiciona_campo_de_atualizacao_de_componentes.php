<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoDeAtualizacaoDeComponentes extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE escola_serie_disciplina ADD COLUMN updated_at TIMESTAMP DEFAULT now() NOT NULL;
            ALTER TABLE componente_curricular_turma ADD COLUMN updated_at TIMESTAMP DEFAULT now() NOT NULL;

            CREATE OR REPLACE FUNCTION update_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = now();
                RETURN NEW;
            END;
            $$ language 'plpgsql';

            CREATE TRIGGER update_escola_serie_disciplina_updated_at
            BEFORE UPDATE ON escola_serie_disciplina
            FOR EACH ROW EXECUTE PROCEDURE update_updated_at();

            CREATE TRIGGER update_componente_curricular_turma_updated_at
            BEFORE UPDATE ON componente_curricular_turma
            FOR EACH ROW EXECUTE PROCEDURE update_updated_at();
        ");
    }

    public function down()
    {
        $this->execute("
            ALTER TABLE escola_serie_disciplina DROP COLUMN updated_at;
            ALTER TABLE componente_curricular_turma DROP COLUMN updated_at;

            DROP FUNCTION update_updated_at() CASCADE;
        ");
    }
}
