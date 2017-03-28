<?php

use Phinx\Migration\AbstractMigration;

class AjustadoFunctionGetMediaTurma extends AbstractMigration
{
    public function change()
    {
      $this->execute("CREATE OR REPLACE FUNCTION relatorio.get_media_turma( turma_i integer, componente_i integer, etapa_i integer) RETURNS numeric AS $$
                      BEGIN
                      RETURN (SELECT avg(nota_componente_curricular.nota)
                                  FROM modules.nota_componente_curricular,
                                      modules.nota_aluno,
                                      pmieducar.matricula m,
                                      pmieducar.matricula_turma mt
                                  WHERE nota_componente_curricular.nota_aluno_id = nota_aluno.id
                                  AND nota_componente_curricular.componente_curricular_id = componente_i
                                  AND nota_aluno.matricula_id = m.cod_matricula
                                  AND m.cod_matricula = mt.ref_cod_matricula
                                  AND mt.ativo = 1
                                  AND m.ativo = 1
                                  AND mt.ref_cod_turma = turma_i
                                  AND nota_componente_curricular.etapa = etapa_i::varchar);
                      END; $$ LANGUAGE plpgsql VOLATILE COST 100;

                      ALTER FUNCTION relatorio.get_media_turma(integer, integer, integer) OWNER TO postgres;");
    }
}
