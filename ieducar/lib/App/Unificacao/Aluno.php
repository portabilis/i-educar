<?php

require_once 'CoreExt/Exception.php';

class App_Unificacao_Aluno
{
    public static function unifica($codAlunoPrincipal, $codAlunos, $codPessoa, clsBanco $db)
    {
        self::validaParametros($codAlunoPrincipal, $codAlunos, $codPessoa);

        $codAlunos = implode(',', $codAlunos);

        $db->consulta(
            "
                UPDATE pmieducar.historico_escolar
                SET 
                    ref_cod_aluno = {$codAlunoPrincipal},
                    sequencial = he.seq+he.max_seq
                FROM
                (
                    SELECT 
                        ref_cod_aluno AS aluno,
                        sequencial AS seq,
                        COALESCE
                        (
                            (
                                SELECT max(sequencial)
                                FROM pmieducar.historico_escolar
                                WHERE ref_cod_aluno = {$codAlunoPrincipal}
                            ),
                            0
                        ) AS max_seq
                    FROM pmieducar.historico_escolar
                    WHERE ref_cod_aluno IN ({$codAlunos})
                ) AS he
                WHERE sequencial = he.seq
                AND ref_cod_aluno = he.aluno
            "
        );

        $db->consulta("UPDATE pmieducar.matricula SET ref_cod_aluno = {$codAlunoPrincipal} where ref_cod_aluno in ({$codAlunos})");
        $db->consulta("UPDATE pmieducar.aluno SET ativo = 0, data_exclusao = now(), ref_usuario_exc = {$codPessoa} where cod_aluno in ({$codAlunos})");

        return true;
    }

    private static function validaParametros($codAlunoPrincipal, $codAlunos, $codPessoa)
    {
        if (!is_numeric($codAlunoPrincipal)) {
            throw new CoreExt_Exception('Parâmetro $codAlunoPrincipal deve ser um inteiro');
        }

        if (!is_array($codAlunos) || !count($codAlunos)) {
            throw new CoreExt_Exception('Parâmetro $codAlunos deve ser um array de códigos de alunos');
        }

        if (!is_numeric($codPessoa)) {
            throw new CoreExt_Exception('Parâmetro $codPessoa deve ser um inteiro');
        }
    }
}
