<?php

use iEducar\Legacy\Model;

class clsModulesFrequencia extends Model {
    public $id;
    public $ano;
    public $ref_cod_ins;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $ref_cod_turma;
    public $ref_cod_componente_curricular;
    public $ref_cod_turno;
    public $data;
    public $data_inicial;
    public $data_final;
    public $etapa_sequencial;
    public $alunos;

    public function __construct(
        $id = null,
        $ano = null,
        $ref_cod_ins = null,
        $ref_cod_escola = null,
        $ref_cod_curso = null,
        $ref_cod_serie = null,
        $ref_cod_turma = null,
        $ref_cod_componente_curricular = null,
        $ref_cod_turno = null,
        $data = null,
        $data_inicial = null,
        $data_final = null,
        $etapa_sequencial = null,
        $alunos = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}frequencia";

        $this->_from = "
                modules.frequencia f
            JOIN pmieducar.turma t
                ON (t.cod_turma = f.ref_cod_turma)
            JOIN pmieducar.instituicao i
                ON (i.cod_instituicao = t.ref_cod_instituicao)
            JOIN pmieducar.escola e
                ON (e.cod_escola = t.ref_ref_cod_escola)
            JOIN cadastro.juridica j
                ON (j.idpes = e.ref_idpes)
            JOIN pmieducar.curso c
                ON (c.cod_curso = t.ref_cod_curso)
            JOIN pmieducar.serie s
                ON (s.cod_serie = t.ref_ref_cod_serie)
            LEFT JOIN modules.componente_curricular k
                ON (k.id = f.ref_componente_curricular)
            JOIN pmieducar.turma_turno u
                ON (u.id = t.turma_turno_id)
            LEFT JOIN pmieducar.turma_modulo q
                ON (q.ref_cod_turma = t.cod_turma AND q.sequencial = 1)
            LEFT JOIN pmieducar.modulo l
                ON (l.cod_modulo = q.ref_cod_modulo)
        ";

        $this->_campos_lista = $this->_todos_campos = '
            f.id,
            f.data,
            i.nm_instituicao AS instituicao,
            j.fantasia AS escola,
            c.nm_curso AS curso,
            s.nm_serie AS serie,
            t.nm_turma AS turma,
            k.nome AS componente_curricular,
            u.nome AS turno,
            l.nm_tipo AS etapa,
            f.etapa_sequencial AS fase_etapa
        ';


        if ($id) {
            $this->id = $id;
        }
        if ($ano) {
            $this->ano = $ano;
        }
        if (is_numeric($ref_cod_ins)) {
            $this->ref_cod_ins = $ref_cod_ins;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }
        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }
        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }
        if (is_numeric($ref_cod_componente_curricular)) {
            $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
        }
        if (is_numeric($ref_cod_turno)) {
            $this->ref_cod_turno = $ref_cod_turno;
        }
        if (($data)) {
            $this->data = $data;
        }
        if (($data_inicial)) {
            $this->data_inicial = $data_inicial;
        }
        if (($data_final)) {
            $this->data_final = $data_final;
        }
        if ($etapa_sequencial) {
            $this->etapa_sequencial = $etapa_sequencial;
        }
        if (($alunos)) {
            $this->alunos = $alunos;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if ($this->data && is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_serie) && $this->etapa_sequencial) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}data";
            $valores .= "{$gruda}'{$this->data}'";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_turma";
            $valores .= "{$gruda}'{$this->ref_cod_turma}'";
            $gruda = ', ';

            if(is_numeric($this->ref_cod_componente_curricular)) {
                $campos .= "{$gruda}ref_componente_curricular";
                $valores .= "{$gruda}'{$this->ref_cod_componente_curricular}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}etapa_sequencial";
            $valores .= "{$gruda}'{$this->etapa_sequencial}'";
            $gruda = ', ';

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}(NOW() - INTERVAL '3 HOURS')";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES ( $valores )");

            $id = $db->InsertId("{$this->_tabela}_id_seq");

            if ($this->alunos) {
                $campos = '';
                $valores = '';
                $gruda = '';

                $campos .= "{$gruda}ref_frequencia";
                $gruda = ', ';

                $campos .= "{$gruda}ref_cod_matricula";

                $campos .= "{$gruda}justificativa";

                $gruda = '';
                foreach ($this->alunos as $key => $aluno) {
                    $valores .= $gruda . "(" . "'" . $id . "'" . ", " . "'" . $key . "'" . ", " . "'" . $aluno[0] . "'" . ")";
                    $gruda = ', ';
                }

                $db->Consulta("INSERT INTO modules.frequencia_aluno ( $campos ) VALUES $valores");

                // ###########################################################################################################

                $obj = new clsPmieducarSerie();
                $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($this->ref_cod_serie);

                foreach ($this->alunos as $key => $aluno) {
                    $matricula_id = $key;

                    $db->Consulta("
                            INSERT INTO
                                modules.falta_aluno (matricula_id, tipo_falta)
                                SELECT
                                    '{$matricula_id}', '{$tipo_presenca}'
                                WHERE NOT EXISTS
                                    (SELECT
                                        1
                                    FROM
                                        modules.falta_aluno
                                    WHERE matricula_id = '{$matricula_id}' AND tipo_falta = '{$tipo_presenca}')
                                    RETURNING id
                    ");

                    $falta_aluno_id = $this->faltaAlunoExiste($matricula_id, $tipo_presenca);

                    if ($tipo_presenca == 1) {
                        $db->Consulta("
                            WITH update_falta_geral AS (
                                UPDATE
                                    modules.falta_geral
                                SET
                                    quantidade = quantidade + 1
                                WHERE
                                    falta_aluno_id = '{$falta_aluno_id}' AND etapa = '{$this->etapa_sequencial}'
                            )
                                INSERT INTO
                                    modules.falta_geral (falta_aluno_id, quantidade, etapa)
                                    SELECT
                                        '{$falta_aluno_id}', '1', '{$this->etapa_sequencial}'
                                    WHERE NOT EXISTS
                                        (SELECT
                                            1
                                        FROM
                                            modules.falta_geral
                                        WHERE falta_aluno_id = '{$falta_aluno_id}' AND etapa = '{$this->etapa_sequencial}')
                        ");
                    } else if ($tipo_presenca == 2) {
                        $db->Consulta("
                            WITH update_falta_componente_curricular AS (
                                UPDATE
                                    modules.falta_componente_curricular
                                SET
                                    quantidade = quantidade + 1
                                WHERE
                                    falta_aluno_id = '{$falta_aluno_id}'
                                    AND componente_curricular_id = '{$this->ref_cod_componente_curricular}'
                                    AND etapa = '{$this->etapa_sequencial}'
                            )
                                INSERT INTO
                                    modules.falta_componente_curricular (falta_aluno_id, componente_curricular_id, quantidade, etapa)
                                    SELECT
                                        '{$falta_aluno_id}', {$this->ref_cod_componente_curricular}, '1', '{$this->etapa_sequencial}'
                                    WHERE NOT EXISTS
                                        (SELECT
                                            1
                                        FROM
                                            modules.falta_componente_curricular
                                        WHERE falta_aluno_id = '{$falta_aluno_id}'
                                        AND componente_curricular_id = '{$this->ref_cod_componente_curricular}'
                                        AND etapa = '{$this->etapa_sequencial}')
                        ");
                    } else {
                        return false;
                    }
                }
            }

            return $id;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita() {
        if (is_numeric($this->id) && is_numeric($this->etapa_sequencial)) {
            $obj = new clsPmieducarSerie();
            $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($this->ref_cod_serie);

            $db = new clsBanco();
            
            $set = '';
            $gruda = '';

            if ($this->data) {
                $set .= "{$gruda}data = '{$this->data}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa_sequencial)) {
                $set .= "{$gruda}etapa_sequencial = '{$this->etapa_sequencial}'";
                $gruda = ', ';
            }

            $set .= "{$gruda}data_atualizacao = (NOW() - INTERVAL '3 HOURS')";
            $gruda = ', ';

            if ($set) {
                $db->Consulta("
                    UPDATE
                        modules.frequencia
                    SET
                        $set
                    WHERE
                        id = '{$this->id}'
                ");

                $matriculas_antigas = $this->detalhe($this->id)['matriculas']['refs_cod_matricula'];
                if ($matriculas_antigas != '') $matriculas_antigas = explode(',', $this->detalhe($this->id)['matriculas']['refs_cod_matricula']);

                $matriculas_novas = $this->alunos;

                for ($i=0; $i < count($matriculas_antigas); $i++) { 
                    $matricula_antiga = $matriculas_antigas[$i];

                    if ($matriculas_novas[$matricula_antiga]) {
                        $matricula = array_search($matriculas_novas[$matricula_antiga], $matriculas_novas);
                        $justificativa = $matriculas_novas[$matricula_antiga][0];

                        // Atualizar

                        $db->Consulta("
                            UPDATE
                                modules.frequencia_aluno
                            SET
                                justificativa = '{$justificativa}'
                            WHERE
                                ref_frequencia = '{$this->id}' AND ref_cod_matricula = '{$matricula}'
                        ");

                        unset($matriculas_novas[$matricula_antiga]);
                    } else {
                        // Excluir

                        $db->Consulta("
                            DELETE FROM
                                modules.frequencia_aluno
                            WHERE
                                ref_frequencia = '{$this->id}' AND ref_cod_matricula = '{$matricula_antiga}'
                        ");

                        // #########################################################################################
 
                        $falta_aluno_id = $this->faltaAlunoExiste($matricula_antiga, $tipo_presenca);

                        if ($tipo_presenca == 1){
                            $from = 'modules.falta_geral';
                            $where = "falta_aluno_id = '{$falta_aluno_id}' AND etapa = '{$this->etapa_sequencial}'";
                        } else if ($tipo_presenca == 2) {
                            $from = 'modules.falta_componente_curricular';
                            $where = "falta_aluno_id = '{$falta_aluno_id}'
                                        AND componente_curricular_id = '{$this->ref_cod_componente_curricular}'
                                        AND etapa = '{$this->etapa_sequencial}'";
                        } else {
                            return false;
                        }

                        $db->Consulta("
                            UPDATE
                                {$from}
                            SET
                                quantidade = quantidade - 1
                            WHERE
                                {$where}
                        ");
                    }
                }

                foreach ($matriculas_novas as $matricula_id => $info) {
                    $justificativa = $info[0];

                    // Inserir

                    $db->Consulta("
                        INSERT INTO
                            modules.frequencia_aluno

                            (ref_frequencia, ref_cod_matricula, justificativa)
                        VALUES
                            ('{$this->id}', '{$matricula_id}', '{$justificativa}')
                    ");

                    // #########################################################################################
 
                    $db->Consulta("
                            INSERT INTO
                                modules.falta_aluno (matricula_id, tipo_falta)
                                SELECT
                                    '{$matricula_id}', '{$tipo_presenca}'
                                WHERE NOT EXISTS
                                    (SELECT
                                        1
                                    FROM
                                        modules.falta_aluno
                                    WHERE matricula_id = '{$matricula_id}' AND tipo_falta = '{$tipo_presenca}')
                                    RETURNING id
                    ");

                    $falta_aluno_id = $this->faltaAlunoExiste($matricula_id, $tipo_presenca);

                    if ($tipo_presenca == 1) {
                        $db->Consulta("
                            WITH update_falta_geral AS (
                                UPDATE
                                    modules.falta_geral
                                SET
                                    quantidade = quantidade + 1
                                WHERE
                                    falta_aluno_id = '{$falta_aluno_id}' AND etapa = '{$this->etapa_sequencial}'
                            )
                                INSERT INTO
                                    modules.falta_geral (falta_aluno_id, quantidade, etapa)
                                    SELECT
                                        '{$falta_aluno_id}', '1', '{$this->etapa_sequencial}'
                                    WHERE NOT EXISTS
                                        (SELECT
                                            1
                                        FROM
                                            modules.falta_geral
                                        WHERE falta_aluno_id = '{$falta_aluno_id}' AND etapa = '{$this->etapa_sequencial}')
                        ");
                    } else if ($tipo_presenca == 2) {
                        $db->Consulta("
                            WITH update_falta_componente_curricular AS (
                                UPDATE
                                    modules.falta_componente_curricular
                                SET
                                    quantidade = quantidade + 1
                                WHERE
                                    falta_aluno_id = '{$falta_aluno_id}'
                                    AND componente_curricular_id = '{$this->ref_cod_componente_curricular}'
                                    AND etapa = '{$this->etapa_sequencial}'
                            )
                                INSERT INTO
                                    modules.falta_componente_curricular (falta_aluno_id, componente_curricular_id, quantidade, etapa)
                                    SELECT
                                        '{$falta_aluno_id}', {$this->ref_cod_componente_curricular}, '1', '{$this->etapa_sequencial}'
                                    WHERE NOT EXISTS
                                        (SELECT
                                            1
                                        FROM
                                            modules.falta_componente_curricular
                                        WHERE falta_aluno_id = '{$falta_aluno_id}'
                                        AND componente_curricular_id = '{$this->ref_cod_componente_curricular}'
                                        AND etapa = '{$this->etapa_sequencial}')
                        ");
                    } else {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista (
            $int_ano = null,
            $int_ref_cod_ins = null,
            $int_ref_cod_escola = null,
            $int_ref_cod_curso = null,
            $int_ref_cod_serie = null,
            $int_ref_cod_turma = null,
            $int_ref_cod_componente_curricular = null,
            $int_ref_cod_turno = null,
            $time_data_inicial = null,
            $time_data_final = null,
            $int_etapa = null
        ) {
        $sql = "
                SELECT
                    {$this->_campos_lista}
                FROM
                    {$this->_from}
                ";

        $whereAnd = ' AND ';
        $filtros = " WHERE TRUE ";

    
        if (is_numeric($int_ref_cod_ins)) {
            $filtros .= "{$whereAnd} i.cod_instituicao = '{$int_ref_cod_ins}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} e.cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} c.cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} s.cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_componente_curricular)) {
            $filtros .= "{$whereAnd} k.id = '{$int_ref_cod_componente_curricular}'";
            $whereAnd = ' AND ';
        }
        
        if (is_numeric($int_ref_cod_turno)) {
            $filtros .= "{$whereAnd} t.turma_turno_id = '{$int_ref_cod_turno}'";
            $whereAnd = ' AND ';
        }

        if ($time_data_inicial) {
            $filtros .= "{$whereAnd} f.data >= '{$time_data_inicial}'";
            $whereAnd = ' AND ';
        }

        if ($time_data_final) {
            $filtros .= "{$whereAnd} f.data <= '{$time_data_final}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_etapa)) {
            $filtros .= "{$whereAnd} f.etapa_sequencial = '{$int_etapa}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico(
            "SELECT
                COUNT(0)
            FROM
                {$this->_from}
            {$filtros}"
        );

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe () {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    {$this->_todos_campos},
                    t.cod_turma as cod_turma,
                    k.id as ref_cod_componente_curricular,
                    t.ref_cod_instituicao,
                    t.ref_ref_cod_escola as ref_cod_escola,
                    t.ref_cod_curso,
			        t.ref_ref_cod_serie as ref_cod_serie,
                    t.cod_turma as ref_cod_turma,
                    f.ref_componente_curricular as ref_cod_componente_curricular
                FROM
                    {$this->_from}
                WHERE
                    f.id = {$this->id}
            ");
            $db->ProximoRegistro();

            $data['detalhes'] = $db->Tupla();

            // --------------------------------------------------------------------- //

            $db->Consulta("
                SELECT
                    STRING_AGG (f.ref_cod_matricula::character varying, ',') as refs_cod_matricula,
                    STRING_AGG (f.justificativa::character varying, ',') as justificativas
                FROM
                    modules.frequencia_aluno f
                GROUP BY
                    f.ref_frequencia
                HAVING
                    f.ref_frequencia = {$this->id}
            ");
            $db->ProximoRegistro();

            $data['matriculas'] = $db->tupla();

            // --------------------------------------------------------------------- //

            $sql = "
                SELECT
                    m.cod_matricula as matricula,
                    p.nome
                FROM
                    pmieducar.aluno a
                JOIN pmieducar.matricula m
                    ON (a.cod_aluno = m.ref_cod_aluno)
                JOIN pmieducar.matricula_turma t
                    ON (m.cod_matricula = t.ref_cod_matricula)
                JOIN cadastro.pessoa p
                    ON (p.idpes = a.ref_idpes)
            ";

            if ($data['detalhes']['ref_cod_componente_curricular']) {
                $sql .= "
                    FULL JOIN pmieducar.dispensa_disciplina c
                        ON (m.cod_matricula = c.ref_cod_matricula AND {$data['detalhes']['ref_cod_componente_curricular']} = c.ref_cod_disciplina)
                    WHERE t.ref_cod_turma = {$data['detalhes']['cod_turma']} AND c.ref_cod_disciplina IS NULL AND c.ref_cod_matricula IS NULL
                ";
            } else {
                $sql .= "
                    WHERE t.ref_cod_turma = {$data['detalhes']['cod_turma']}
                ";
            }

            $db->Consulta("
                {$sql}
            ");

            while ($db->ProximoRegistro()) {
                $data['alunos'][$db->campo('matricula')] = $db->tupla();
            }

            return $data;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe () {
        if ($this->data && is_numeric($this->ref_cod_turma) && is_numeric($this->etapa_sequencial)) {
            $sql = "
                SELECT
                    {$this->_todos_campos},
                    t.cod_turma as cod_turma,
                    k.id as ref_cod_componente_curricular
                FROM
                    {$this->_from}
                WHERE
                    f.data = '{$this->data}'
                    AND f.ref_cod_turma = '{$this->ref_cod_turma}'
                    AND f.etapa_sequencial = '{$this->etapa_sequencial}'
            ";

            if (is_numeric($this->ref_cod_componente_curricular))
                $sql .= "AND f.ref_componente_curricular = '{$this->ref_cod_componente_curricular}'";

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir () {
        if (is_numeric($this->id)) {
            $obj = new clsPmieducarSerie();
            $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($this->ref_cod_serie);

            $matriculas = $this->pegaMatriculasApartirFrequenciaId($this->id);

            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    modules.frequencia
                WHERE
                    id = '{$this->id}'
            ");

            ###########################################################################################################

            foreach ($matriculas as $matricula_id) {
                $falta_aluno_id = $this->faltaAlunoExiste($matricula_id, $tipo_presenca);

                if (is_numeric($falta_aluno_id)) {
                    $from = '';
                    $where = '';

                    if ($tipo_presenca == 1){
                        $from = 'modules.falta_geral';
                        $where = "falta_aluno_id = '{$falta_aluno_id}' AND etapa = '{$this->etapa_sequencial}'";
                    } else if ($tipo_presenca == 2) {
                        $from = 'modules.falta_componente_curricular';
                        $where = "falta_aluno_id = '{$falta_aluno_id}'
                                    AND componente_curricular_id = '{$this->ref_cod_componente_curricular}'
                                    AND etapa = '{$this->etapa_sequencial}'";
                    } else {
                        return false;
                    }

                    $db->Consulta("
                        UPDATE
                            {$from}
                        SET
                            quantidade = quantidade - 1
                        WHERE
                            {$where}
                    ");
                }
            }
            
            return true;
        }

        return false;
    }

    /**
     * Retorna o ID de falta_aluno informando matricula e tipo de falta se existir, caso contrário retorna false
     *
     * @return any
     */
    public function faltaAlunoExiste ($matricula_id, $tipo_falta) {
        if (is_numeric($matricula_id) && is_numeric($tipo_falta)) {
            $db = new clsBanco();

            $db->Consulta("
                SELECT
                    f.id
                FROM
                    modules.falta_aluno f
                WHERE
                    matricula_id = {$matricula_id} AND tipo_falta = {$tipo_falta}
            ");

            $db->ProximoRegistro();
            return $db->Campo('id');
        }

        return false;
    }

    /**
     * Retorna array contendo matrículas que batem com ID da frequencia informada
     *
     * @return array
     */
    public function pegaMatriculasApartirFrequenciaId ($id = null) {
        $db = new clsBanco();

        $db->Consulta("
            SELECT
                STRING_AGG (a.ref_cod_matricula::character varying, ',') as refs_cod_matricula        
            FROM
                modules.frequencia f 
            JOIN modules.frequencia_aluno a
                ON (f.id = a.ref_frequencia)
            GROUP BY
                f.id
            HAVING
                f.id = {$id}
        ");

        $db->ProximoRegistro();
        $temp_matriculas = $db->Campo('refs_cod_matricula');

        return explode(',', $temp_matriculas);
    }
}
