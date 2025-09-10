<?php

use iEducar\Legacy\Model;

class clsModulesProfessorTurma extends Model
{
    public $id;

    public $ano;

    public $instituicao_id;

    public $servidor_id;

    public $turma_id;

    public $funcao_exercida;

    public $tipo_vinculo;

    public $permite_lancar_faltas_componente;

    public $turno_id;

    public $codUsuario;

    public $data_inicial;

    public $data_fim;

    public $leciona_itinerario_tecnico_profissional;

    public $area_itinerario;

    /**
     * Construtor.
     *
     * @param null $id
     * @param null $ano
     * @param null $instituicao_id
     * @param null $servidor_id
     * @param null $turma_id
     * @param null $funcao_exercida
     * @param null $tipo_vinculo
     * @param null $permite_lancar_faltas_componente
     * @param null $turno_id
     * @param null $data_inicial
     * @param null $data_fim
     * @param null $leciona_itinerario_tecnico_profissional
     * @param null $area_itinerario
     */
    public function __construct(
        $id = null,
        $ano = null,
        $instituicao_id = null,
        $servidor_id = null,
        $turma_id = null,
        $funcao_exercida = null,
        $tipo_vinculo = null,
        $permite_lancar_faltas_componente = null,
        $turno_id = null,
        $data_inicial = null,
        $data_fim = null,
        $leciona_itinerario_tecnico_profissional = null,
        $area_itinerario = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}professor_turma";

        $this->_campos_lista = $this->_todos_campos = ' pt.id, pt.ano, pt.instituicao_id, pt.servidor_id, pt.turma_id, pt.funcao_exercida, pt.tipo_vinculo, pt.permite_lancar_faltas_componente, pt.turno_id, pt.data_inicial, pt.data_fim, pt.leciona_itinerario_tecnico_profissional, pt.area_itinerario';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($turma_id)) {
            $this->turma_id = $turma_id;
        }

        if (is_numeric($ano)) {
            $this->ano = $ano;
        }

        if (is_numeric($instituicao_id)) {
            $this->instituicao_id = $instituicao_id;
        }

        if (is_numeric($servidor_id)) {
            $this->servidor_id = $servidor_id;
        }

        if (is_numeric($funcao_exercida)) {
            $this->funcao_exercida = $funcao_exercida;
        }

        if (is_numeric($tipo_vinculo)) {
            $this->tipo_vinculo = $tipo_vinculo;
        }

        if (is_numeric($turno_id)) {
            $this->turno_id = $turno_id;
        }

        if (isset($permite_lancar_faltas_componente)) {
            $this->permite_lancar_faltas_componente = '1';
        } else {
            $this->permite_lancar_faltas_componente = '0';
        }

        if (is_string($data_inicial)) {
            $this->data_inicial = $data_inicial;
        }

        if (is_string($data_fim)) {
            $this->data_fim = $data_fim;
        }

        if (is_numeric($leciona_itinerario_tecnico_profissional)) {
            $this->leciona_itinerario_tecnico_profissional = $leciona_itinerario_tecnico_profissional;
        }

        if (is_array($area_itinerario)) {
            $this->area_itinerario = $area_itinerario;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return int|bool
     *
     * @throws Exception
     */
    public function cadastra()
    {
        if (
            is_numeric($this->turma_id)
            && is_numeric($this->funcao_exercida)
            && is_numeric($this->ano)
            && is_numeric($this->servidor_id)
            && is_numeric($this->instituicao_id)
        ) {
            $db = new clsBanco;
            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->instituicao_id)) {
                $campos .= "{$gruda}instituicao_id";
                $valores .= "{$gruda}'{$this->instituicao_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->servidor_id)) {
                $campos .= "{$gruda}servidor_id";
                $valores .= "{$gruda}'{$this->servidor_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_id)) {
                $campos .= "{$gruda}turma_id";
                $valores .= "{$gruda}'{$this->turma_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->funcao_exercida)) {
                $campos .= "{$gruda}funcao_exercida";
                $valores .= "{$gruda}'{$this->funcao_exercida}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_vinculo)) {
                $campos .= "{$gruda}tipo_vinculo";
                $valores .= "{$gruda}'{$this->tipo_vinculo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->permite_lancar_faltas_componente)) {
                $campos .= "{$gruda}permite_lancar_faltas_componente";
                $valores .= "{$gruda}'{$this->permite_lancar_faltas_componente}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_id)) {
                $campos .= "{$gruda}turno_id";
                $valores .= "{$gruda}'{$this->turno_id}'";
                $gruda = ', ';
            }

            if (is_string($this->data_inicial) && !empty($this->data_inicial)) {
                $campos .= "{$gruda}data_inicial";
                $valores .= "{$gruda}'{$this->data_inicial}'";
                $gruda = ', ';
            }

            if (is_string($this->data_fim) && !empty($this->data_fim)) {
                $campos .= "{$gruda}data_fim";
                $valores .= "{$gruda}'{$this->data_fim}'";
                $gruda = ', ';
            }

            if (is_numeric($this->leciona_itinerario_tecnico_profissional)) {
                $campos .= "{$gruda}leciona_itinerario_tecnico_profissional";
                $valores .= "{$gruda}'{$this->leciona_itinerario_tecnico_profissional}'";
                $gruda = ', ';
            }

            if (is_array($this->area_itinerario)) {
                $campos .= "{$gruda}area_itinerario";
                $valores .= "{$gruda} " . Portabilis_Utils_Database::arrayToPgArray($this->area_itinerario) . ' ';
                $gruda = ', ';
            }

            $campos .= "{$gruda}updated_at";
            $valores .= "{$gruda} CURRENT_TIMESTAMP";

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $id = $db->InsertId("{$this->_tabela}_id_seq");
            $this->id = $id;

            return $id;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function edita()
    {
        if (
            is_numeric($this->id)
            && is_numeric($this->turma_id)
            && is_numeric($this->funcao_exercida)
            && is_numeric($this->ano)
            && is_numeric($this->servidor_id)
            && is_numeric($this->instituicao_id)
        ) {
            $db = new clsBanco;
            $set = '';
            $gruda = '';

            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->instituicao_id)) {
                $set .= "{$gruda}instituicao_id = '{$this->instituicao_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->servidor_id)) {
                $set .= "{$gruda}servidor_id = '{$this->servidor_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_id)) {
                $set .= "{$gruda}turma_id = '{$this->turma_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->funcao_exercida)) {
                $set .= "{$gruda}funcao_exercida = '{$this->funcao_exercida}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_vinculo)) {
                $set .= "{$gruda}tipo_vinculo = '{$this->tipo_vinculo}'";
                $gruda = ', ';
            } elseif (is_null($this->tipo_vinculo)) {
                $set .= "{$gruda}tipo_vinculo = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->permite_lancar_faltas_componente)) {
                $set .= "{$gruda}permite_lancar_faltas_componente = '{$this->permite_lancar_faltas_componente}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_id)) {
                $set .= "{$gruda}turno_id = '{$this->turno_id}'";
                $gruda = ', ';
            } elseif (is_null($this->turno_id)) {
                $set .= "{$gruda}turno_id = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->leciona_itinerario_tecnico_profissional)) {
                $set .= "{$gruda}leciona_itinerario_tecnico_profissional = '{$this->leciona_itinerario_tecnico_profissional}'";
                $gruda = ', ';
            } elseif (is_null($this->leciona_itinerario_tecnico_profissional)) {
                $set .= "{$gruda}leciona_itinerario_tecnico_profissional = NULL";
                $gruda = ', ';
            }

            if (is_array($this->area_itinerario)) {
                $set .= "{$gruda} area_itinerario = " . Portabilis_Utils_Database::arrayToPgArray($this->area_itinerario) . ' ';
                $gruda = ', ';
            } else {
                $set .= "{$gruda} area_itinerario = NULL";
                $gruda = ', ';
            }

            if (is_string($this->data_inicial) && !empty($this->data_inicial)) {
                $set .= "{$gruda}data_inicial = '{$this->data_inicial}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_inicial = NULL ";
                $gruda = ', ';
            }

            if (is_string($this->data_fim) && !empty($this->data_fim)) {
                $set .= "{$gruda}data_fim = '{$this->data_fim}'";
            } else {
                $set .= "{$gruda}data_fim = NULL ";
            }

            $set .= "{$gruda}updated_at = CURRENT_TIMESTAMP";
            $gruda = ', ';

            if ($set) {
                $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");
                $this->detalhe();

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @param null $servidor_id
     * @param null $instituicao_id
     * @param null $ano
     * @param null $ref_cod_escola
     * @param null $ref_cod_curso
     * @param null $ref_cod_serie
     * @param null $ref_cod_turma
     * @param null $funcao_exercida
     * @param null $tipo_vinculo
     * @return array|bool
     *
     * @throws Exception
     */
    public function lista(
        $servidor_id = null,
        $instituicao_id = null,
        $ano = null,
        $ref_cod_escola = null,
        $ref_cod_curso = null,
        $ref_cod_serie = null,
        $ref_cod_turma = null,
        $funcao_exercida = null,
        $tipo_vinculo = null
    ) {
        $sql = "

            SELECT
                {$this->_campos_lista},
                t.nm_turma,
                t.cod_turma as ref_cod_turma,
                t.ref_ref_cod_serie as ref_cod_serie,
                textcat_all(s.nm_serie) AS nm_serie,
                t.ref_cod_curso,
                textcat_all(DISTINCT c.nm_curso) AS nm_curso,
                t.ref_ref_cod_escola as ref_cod_escola,
                p.nome as nm_escola
            FROM {$this->_tabela} pt
        ";
        $filtros = '
            JOIN pmieducar.turma t ON pt.turma_id = t.cod_turma
            LEFT JOIN pmieducar.turma_serie ts ON ts.turma_id = t.cod_turma
            JOIN pmieducar.serie s ON s.cod_serie = coalesce(ts.serie_id, t.ref_ref_cod_serie)
            JOIN pmieducar.curso c ON s.ref_cod_curso = c.cod_curso
            JOIN pmieducar.escola e ON t.ref_ref_cod_escola = e.cod_escola
            JOIN cadastro.pessoa p ON e.ref_idpes = p.idpes
        WHERE true ';

        $whereAnd = ' AND ';

        if (is_numeric($servidor_id)) {
            $filtros .= "{$whereAnd} pt.servidor_id = '{$servidor_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($instituicao_id)) {
            $filtros .= "{$whereAnd} pt.instituicao_id = '{$instituicao_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} pt.ano = '{$ano}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= "{$whereAnd} t.ref_ref_cod_escola = '{$ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = t.ref_ref_cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_curso)) {
            $filtros .= "{$whereAnd} t.ref_cod_curso = '{$ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_serie)) {
            $filtros .= "{$whereAnd} t.ref_ref_cod_serie = '{$ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($funcao_exercida)) {
            $filtros .= "{$whereAnd} pt.funcao_exercida = '{$funcao_exercida}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($tipo_vinculo)) {
            $filtros .= "{$whereAnd} pt.tipo_vinculo = '{$tipo_vinculo}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco;
        $countCampos = count(explode(',', $this->_campos_lista)) + 8;
        $resultado = [];

        $groupBy = '
            GROUP BY
                pt.id,
                t.cod_turma,
                p.nome
        ';

        $sql .= $filtros . $groupBy . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} pt {$filtros}");

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
     * Retorna um array com os dados de um registro.
     *
     * @return array|bool
     *
     * @throws Exception
     */
    public function detalhe()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco;
            $db->Consulta("SELECT {$this->_campos_lista}, t.nm_turma, s.nm_serie, c.nm_curso, p.nome as nm_escola
                     FROM {$this->_tabela} pt, pmieducar.turma t, pmieducar.serie s, pmieducar.curso c,
                     pmieducar.escola e, cadastro.pessoa p
                     WHERE pt.turma_id = t.cod_turma AND t.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso
                     AND t.ref_ref_cod_escola = e.cod_escola AND e.ref_idpes = p.idpes AND id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array|false
     *
     * @throws Exception
     */
    public function existe()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco;
            $db->Consulta("SELECT 1 FROM {$this->_tabela} pt WHERE id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * @return int|bool
     */
    public function existe2()
    {
        if (
            is_numeric($this->ano)
            && is_numeric($this->instituicao_id)
            && is_numeric($this->servidor_id)
            && is_numeric($this->turma_id)
        ) {
            $db = new clsBanco;
            $sql = "SELECT id FROM {$this->_tabela} pt WHERE ano = '{$this->ano}' AND turma_id = '{$this->turma_id}'
               AND instituicao_id = '{$this->instituicao_id}' AND servidor_id = '{$this->servidor_id}' ";

            if (is_numeric($this->id)) {
                $sql .= " AND id <> {$this->id}";
            }

            return $db->UnicoCampo($sql);
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function excluir()
    {
        if (is_numeric($this->id)) {
            $this->detalhe();
            $sql = "DELETE FROM {$this->_tabela} pt WHERE id = '{$this->id}'";
            $db = new clsBanco;
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function gravaComponentes($professor_turma_id, $componentes)
    {
        $this->excluiComponentes($professor_turma_id);
        $db = new clsBanco;
        foreach ($componentes as $componente) {
            $db->Consulta("INSERT INTO modules.professor_turma_disciplina VALUES ({$professor_turma_id},{$componente})");
        }
    }

    public function excluiComponentes($professor_turma_id)
    {
        $db = new clsBanco;
        $db->Consulta("DELETE FROM modules.professor_turma_disciplina WHERE professor_turma_id = {$professor_turma_id}");
    }

    public function retornaComponentesVinculados($professor_turma_id)
    {
        $componentesVinculados = [];
        $sql = "SELECT componente_curricular_id
                  FROM modules.professor_turma_disciplina
                 WHERE professor_turma_id = {$professor_turma_id}";
        $db = new clsBanco;
        $db->Consulta($sql);
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $componentesVinculados[] = $tupla['componente_curricular_id'];
        }

        return $componentesVinculados;
    }

    public function retornaNomeDoComponente($idComponente)
    {
        $mapperComponente = new ComponenteCurricular_Model_ComponenteDataMapper;
        $componente = $mapperComponente->find(['id' => $idComponente]);

        return $componente->nome;
    }
}
