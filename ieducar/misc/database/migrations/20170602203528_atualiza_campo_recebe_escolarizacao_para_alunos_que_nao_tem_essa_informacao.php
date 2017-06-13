<?php

use Phinx\Migration\AbstractMigration;

class AtualizaCampoRecebeEscolarizacaoParaAlunosQueNaoTemEssaInformacao extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.aluno
                           SET recebe_escolarizacao_em_outro_espaco = 3
                         WHERE (recebe_escolarizacao_em_outro_espaco IS NULL 
                            OR recebe_escolarizacao_em_outro_espaco = 0)
                           AND cod_aluno IN (SELECT ref_cod_aluno
                                               FROM pmieducar.matricula
                                              WHERE ativo = 1
                                                AND ano = 2017);");
    }
}
