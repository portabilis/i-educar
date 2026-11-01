<?php

class Portabilis_Business_Professor
{
    public static function isProfessor($instituicaoId, $userId)
    {
        if (is_numeric($instituicaoId)) {
            $sql = '
                select funcao.professor
                from pmieducar.servidor_funcao, pmieducar.funcao
                where funcao.cod_funcao = servidor_funcao.ref_cod_funcao
                and funcao.professor = 1
                and servidor_funcao.ref_ref_cod_instituicao = $1
                and servidor_funcao.ref_cod_servidor = $2
            ';

            $options = ['params' => [$instituicaoId, $userId], 'return_only' => 'first-field'];
        } else {
            $sql = '
                select funcao.professor
                from pmieducar.servidor_funcao, pmieducar.funcao
                where funcao.cod_funcao = servidor_funcao.ref_cod_funcao
                and funcao.professor = 1
                and servidor_funcao.ref_cod_servidor = $1
            ';

            $options = ['params' => [$userId], 'return_only' => 'first-field'];
        }

        return self::fetchPreparedQuery($sql, $options) == '1';
    }

    public static function isOnlyProfessor($instituicaoId, $userId)
    {
        $sql = "
            SELECT DISTINCT funcao.professor
            FROM pmieducar.servidor_funcao, pmieducar.funcao
            WHERE funcao.cod_funcao = servidor_funcao.ref_cod_funcao
            AND servidor_funcao.ref_cod_servidor = {$userId}
        ";

        if (is_numeric($instituicaoId)) {
            $sql .= " AND servidor_funcao.ref_ref_cod_instituicao =  {$instituicaoId}";
        }

        $funcoes = self::fetchPreparedQuery($sql);

        return count($funcoes) == 1 && $funcoes[0]['professor'];
    }

    public static function cursosAlocado($instituicaoId, $escolaId, $userId, $ano = null)
    {
        if (self::necessarioVinculoTurma($instituicaoId)) {
            $ano = self::anoDoVinculo($ano);

            if ($ano === false) {
                return [];
            }

            $sql = '
                SELECT DISTINCT
                    c.cod_curso as id,
                    CASE WHEN (c.descricao is not null and c.descricao <> \'\')
                        THEN c.nm_curso||\' (\'||c.descricao||\')\'
                    ELSE c.nm_curso END as nome
                FROM modules.professor_turma pt
                INNER JOIN pmieducar.turma t
                ON (t.cod_turma = pt.turma_id)
                LEFT JOIN pmieducar.turma_serie ts
                ON (ts.turma_id = t.cod_turma)
                INNER JOIN pmieducar.serie s
                ON (s.cod_serie = COALESCE(ts.serie_id, t.ref_ref_cod_serie))
                INNER JOIN pmieducar.curso c
                ON (c.cod_curso = s.ref_cod_curso)
                WHERE t.ref_ref_cod_escola = $1
                AND pt.servidor_id = $2
                AND t.ativo = 1
                AND t.visivel != \'f\'
            ';

            $params = [$escolaId, $userId];

            if ($ano !== null) {
                $params[] = $ano;
                $sql .= ' AND pt.ano = $3';
            }

            $sql .= ' ORDER BY nome';
            $options = ['params' => $params];
        } else {
            $sql = '
                select
                    cod_curso as id,
                    CASE WHEN (curso.descricao is not null and curso.descricao <> \'\')
                        THEN curso.nm_curso||\' (\'||curso.descricao||\')\'
                    ELSE curso.nm_curso END as nome
                from
                    pmieducar.servidor_curso_ministra,
                    pmieducar.curso,
                    pmieducar.escola_curso,
                    pmieducar.escola
                where escola.ref_cod_instituicao = $1
                and escola.cod_escola = $2
                and escola_curso.ref_cod_curso = cod_curso
                and escola_curso.ref_cod_escola = cod_escola
                and servidor_curso_ministra.ref_cod_curso = curso.cod_curso
                and ref_cod_servidor = $3
            ';

            $options = ['params' => [$instituicaoId, $escolaId, $userId]];
        }

        return self::fetchPreparedQuery($sql, $options);
    }

    public static function seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId, $ano = null)
    {
        $instituicaoId = $instituicaoId ?: 0;
        $escolaId = $escolaId ?: 0;
        $cursoId = $cursoId ?: 0;

        if (self::canLoadSeriesAlocado($instituicaoId)) {
            $ano = self::anoDoVinculo($ano);

            if ($ano === false) {
                return [];
            }

            $sql = '
                SELECT DISTINCT
                    s.cod_serie as id,
                    CASE WHEN (s.descricao is not null and s.descricao <> \'\')
                    THEN s.nm_serie||\' (\'||s.descricao||\')\'
                    ELSE s.nm_serie END as nome
                FROM modules.professor_turma pt
                INNER JOIN pmieducar.turma t
                ON (t.cod_turma = pt.turma_id)
                LEFT JOIN pmieducar.turma_serie ts
                ON (ts.turma_id = t.cod_turma)
                INNER JOIN pmieducar.serie s
                ON (s.cod_serie = COALESCE(ts.serie_id, t.ref_ref_cod_serie))
                WHERE pt.instituicao_id = $1
                AND t.ref_ref_cod_escola = $2
                AND s.ref_cod_curso = $3
                AND pt.servidor_id = $4
                AND t.ativo = 1
                AND t.visivel != \'f\'
            ';

            $params = [$instituicaoId, $escolaId, $cursoId, $userId];

            if ($ano !== null) {
                $params[] = $ano;
                $sql .= ' AND pt.ano = $5';
            }

            $sql .= ' ORDER BY nome';

            return self::fetchPreparedQuery($sql, ['params' => $params]);
        }
    }

    public static function canLoadSeriesAlocado($instituicaoId)
    {
        return self::necessarioVinculoTurma($instituicaoId);
    }

    public static function turmasAlocado($instituicaoId, $escolaId, $serieId, $userId, $ano = null)
    {
        if (self::necessarioVinculoTurma($instituicaoId)) {
            $ano = self::anoDoVinculo($ano);

            if ($ano === false) {
                return [];
            }

            $sql = '
                SELECT DISTINCT
                    t.cod_turma as id,
                    t.nm_turma as nome,
                    t.ano
                FROM modules.professor_turma pt
                INNER JOIN pmieducar.turma t
                ON (t.cod_turma = pt.turma_id)
                LEFT JOIN pmieducar.turma_serie ts
                ON (ts.turma_id = t.cod_turma)
                WHERE t.ref_ref_cod_escola = $1
                AND (
                    t.ref_ref_cod_serie = $2
                    OR ts.serie_id = $2
                )
                AND pt.servidor_id = $3
                AND t.ativo = 1
                AND t.visivel != \'f\'
            ';

            $params = [$escolaId, $serieId, $userId];

            if ($ano !== null) {
                $params[] = $ano;
                $sql .= ' AND pt.ano = $4';
            }

            $sql .= ' ORDER BY t.nm_turma ASC';

            return self::fetchPreparedQuery($sql, ['params' => $params]);
        } else {
            // Feito gambiarra para que quando professor tenha alocação liste todas turmas do turno integral
            $sql = '
                SELECT
                    t.cod_turma AS id,
		                t.nm_turma AS nome,
 	                  t.ano
                FROM pmieducar.turma AS t
                LEFT JOIN pmieducar.turma_serie AS ts ON ts.turma_id = t.cod_turma
                WHERE t.ref_ref_cod_escola = $1
                AND (
                    t.ref_ref_cod_serie = $2
                    OR ts.serie_id = $2
                )
                AND t.ativo = 1
                AND t.visivel != \'f\'
                AND (
                        t.turma_turno_id IN (
                        SELECT s.periodo
                        FROM servidor_alocacao AS s
                        WHERE s.ref_cod_escola = t.ref_ref_cod_escola
                        AND s.ref_cod_servidor = $3
                        AND ativo = 1
                    ) OR (
                        t.turma_turno_id = 4
                        AND (
                            SELECT 1
                            FROM servidor_alocacao AS sea
                            WHERE sea.ref_cod_escola = t.ref_ref_cod_escola
                            AND sea.ref_cod_servidor = $3
                            AND t.ativo = 1
                            LIMIT 1
                        ) IS NOT NULL
                    )
                )
                ORDER BY t.nm_turma ASC
            ';
        }

        return self::fetchPreparedQuery($sql, ['params' => [$escolaId, $serieId, $userId]]);
    }

    public static function componentesCurricularesAlocado($instituicaoId, $turmaId, $anoLetivo, $userId)
    {
        if (self::necessarioVinculoTurma($instituicaoId)) {
            $sql = '
                SELECT DISTINCT
                    cc.id, cc.nome,
                    ac.nome as area_conhecimento,
                    ac.secao as secao_area_conhecimento
                FROM modules.professor_turma pt
                INNER JOIN modules.professor_turma_disciplina ptd
                ON (ptd.professor_turma_id = pt.id)
                INNER JOIN modules.componente_curricular cc
                ON (cc.id = ptd.componente_curricular_id)
                INNER JOIN modules.area_conhecimento ac
                ON (cc.area_conhecimento_id = ac.id)
                WHERE pt.turma_id = $1
                AND pt.servidor_id = $2
                ORDER BY ac.secao, ac.nome, cc.nome
            ';

            $componentes = self::fetchPreparedQuery($sql, ['params' => [$turmaId, $userId]]);
        } else {
            $componentes = self::componentesCurricularesTurmaAlocado($turmaId, $anoLetivo, $userId);

            if (empty($componentes)) {
                $componentes = self::componentesCurricularesCursoAlocado($turmaId, $anoLetivo, $userId);
            }
        }

        return $componentes;
    }

    protected static function componentesCurricularesTurmaAlocado($turmaId, $anoLetivo, $userId)
    {
        $sql = '
            select
                cc.id,
                cc.nome,
                ac.nome as area_conhecimento,
                ac.secao as secao_area_conhecimento
            from
                modules.componente_curricular_turma as cct,
                pmieducar.turma,
                modules.componente_curricular as cc,
                modules.area_conhecimento as ac,
                pmieducar.escola_ano_letivo as al,
                pmieducar.servidor_disciplina as scc
            where turma.cod_turma = $1
            and cct.turma_id = turma.cod_turma
            and cct.escola_id = turma.ref_ref_cod_escola
            and cct.componente_curricular_id = cc.id
            and al.ano = $2
            and cct.escola_id = al.ref_cod_escola
            and scc.ref_ref_cod_instituicao = turma.ref_cod_instituicao
            and scc.ref_cod_servidor = $3
            and scc.ref_cod_curso = turma.ref_cod_curso
            and scc.ref_cod_disciplina = cc.id
            and cc.area_conhecimento_id = ac.id
            order by ac.secao, ac.nome, cc.nome
        ';

        $options = ['params' => [$turmaId, $anoLetivo, $userId]];

        return self::fetchPreparedQuery($sql, $options);
    }

    protected static function componentesCurricularesCursoAlocado($turmaId, $anoLetivo, $userId)
    {
        $sql = '
            select
                cc.id as id,
                cc.nome as nome,
                ac.nome as area_conhecimento,
                ac.secao as secao_area_conhecimento
            from
                pmieducar.serie,
                pmieducar.escola_serie_disciplina as esd,
                pmieducar.turma,
                modules.componente_curricular as cc,
                modules.area_conhecimento as ac,
                pmieducar.escola_ano_letivo as al,
                pmieducar.servidor_disciplina as scc
            where turma.cod_turma = $1
            and serie.cod_serie = turma.ref_ref_cod_serie
            and esd.ref_ref_cod_escola = turma.ref_ref_cod_escola
            and esd.ref_ref_cod_serie = serie.cod_serie
            and esd.ref_cod_disciplina = cc.id
            and al.ano = $2
            and esd.ref_ref_cod_escola = al.ref_cod_escola
            and serie.ativo = 1
            and esd.ativo = 1
            and al.ativo = 1
            and scc.ref_ref_cod_instituicao = turma.ref_cod_instituicao
            and scc.ref_cod_servidor = $3
            and scc.ref_cod_curso = serie.ref_cod_curso
            and scc.ref_cod_disciplina = cc.id
            and cc.area_conhecimento_id = ac.id
            order by ac.secao, ac.nome, cc.nome
        ';

        $options = ['params' => [$turmaId, $anoLetivo, $userId]];

        return self::fetchPreparedQuery($sql, $options);
    }

    protected static function fetchPreparedQuery($sql, $options = [])
    {
        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }

    /**
     * null quando a tela não informa o ano; false quando ele não é um ano válido para a coluna (inteiro de 1 a 32767) e a lista volta vazia.
     */
    private static function anoDoVinculo($ano)
    {
        if ($ano === null || $ano === '') {
            return null;
        }

        return filter_var($ano, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 32767]]);
    }

    private static function necessarioVinculoTurma($instituicaoId)
    {
        $sql = 'SELECT exigir_vinculo_turma_professor FROM pmieducar.instituicao WHERE cod_instituicao = $1';

        return self::fetchPreparedQuery($sql, ['params' => [$instituicaoId], 'return_only' => 'first-field']) == 1;
    }
}
