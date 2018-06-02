<?php

use Phinx\Migration\AbstractMigration;

class AdicionaIndicesEducacensoIes extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            CREATE INDEX idx_educacenso_ies_ies_id
            ON modules.educacenso_ies(ies_id);

            CREATE EXTENSION IF NOT EXISTS pg_trgm;

            CREATE OR REPLACE FUNCTION f_unaccent(text)
                RETURNS text AS
            $func$
            SELECT public.unaccent(\'public.unaccent\', $1)
            $func$  LANGUAGE sql IMMUTABLE;

            CREATE INDEX idx_educacenso_ies_nome_gin_trgm
            ON modules.educacenso_ies
              USING gin (f_unaccent(nome) gin_trgm_ops);

            ANALYZE modules.educacenso_ies;
        ');
    }

    public function down()
    {
        $this->execute('
            DROP INDEX modules.idx_educacenso_ies_ies_id;
            DROP INDEX modules.idx_educacenso_ies_nome_gin_trgm;
            DROP FUNCTION f_unaccent(text);
            DROP EXTENSION pg_trgm;
        ');

    }
}
