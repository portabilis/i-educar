<?php

use Phinx\Migration\AbstractMigration;

class CriaFuncaoParaPegarQuantidadeDeEtapasDispensadasDoComponenteDeUmaMatricula extends AbstractMigration
{
    public function up()
    {
        $this->execute('CREATE OR REPLACE FUNCTION relatorio.get_qtde_etapa_disciplina_dispensada_matricula(integer, integer)
                        RETURNS integer AS
                        $$
                            SELECT DISTINCT count(dispensa_etapa.etapa)::integer AS qtde_dispensa_etapa
                              FROM pmieducar.dispensa_disciplina
                             INNER JOIN pmieducar.dispensa_etapa ON (dispensa_etapa.ref_cod_dispensa = dispensa_disciplina.cod_dispensa)
                             WHERE dispensa_disciplina.ativo =1
                               AND ref_cod_matricula = $1
                               AND ref_cod_disciplina = $2;
                        $$
                        LANGUAGE sql VOLATILE
                            COST 100;
                        ALTER FUNCTION relatorio.get_qtde_etapa_disciplina_dispensada_matricula(integer, integer)
                            OWNER TO ieducar;
                    ');
    }
}
