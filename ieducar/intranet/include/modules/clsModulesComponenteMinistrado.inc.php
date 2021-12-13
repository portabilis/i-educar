<?php

use iEducar\Legacy\Model;

class clsModulesComponenteMinistrado extends Model {
    public $id;
    public $frequencia_id;
    public $observacao;
    public $frequencia_bncc;

    public function __construct(
            $id = null,
            $frequencia_id = null,
            $observacao = null,
            $frequencia_bncc = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}conteudo_ministrado";

        $this->_from = "
                modules.conteudo_ministrado as cm
            JOIN modules.frequencia f
                ON (cm.frequencia_id = f.id)
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
            cm.id,
            f.id as frequencia,
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


        if (($ano)) {
            $this->ano = $ano;
        }
        if (is_numeric($frequencia_id)) {
            $this->frequencia_id = $frequencia_id;
        }
        if (($observacao)) {
            $this->observacao = $observacao;
        }
        if($frequencia_bncc){
            $this->frequencia_bncc = $frequencia_bncc;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->frequencia_id) && $this->observacao != '' && $this->frequencia_bncc) {
            $db = new clsBanco();

            $campos = "frequencia_id, observacao, data_cadastro";
            $valores = "'{$this->frequencia_id}', '{$this->observacao}', (NOW() - INTERVAL '3 HOURS')";

            $db->Consulta("
                INSERT INTO
                    {$this->_tabela} ( $campos )
                    VALUES ( $valores )
            ");

            $id = $db->InsertId("{$this->_tabela}_id_seq");

            $campos = "conteudo_ministrado_id, bncc_id";

            $gruda = '';
            $valores = '';
            foreach ($this->frequencia_bncc as $key => $bncc) {
                $valores .= $gruda . "(" . "'" . $id . "'" . ", " . "'" . $bncc . "'" .")";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela}_bncc ( $campos ) VALUES $valores");

            return $id;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita($id = null) {
        

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
    public function detalhe ($id = null) {
        if (is_numeric($id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    {$this->_todos_campos},
                    cm.observacao,
                    f.ref_cod_turma as cod_turma
                FROM
                    {$this->_from}
                WHERE
                    f.id = {$id}
            ");

            $db->ProximoRegistro();

            $data['detalhes'] = $db->Tupla();

            // --------------------------------------------------------------------- //

            $db->Consulta("
                SELECT
                    STRING_AGG (lok.code::character varying, ',') as codes,
                    STRING_AGG (lok.description::character varying, ',') as descriptions
                FROM
                    modules.conteudo_ministrado_bncc as cm
                JOIN public.learning_objectives_and_skills as lok
                    ON (lok.id = cm.bncc_id)
                GROUP BY
                    cm.conteudo_ministrado_id
                HAVING
                    cm.conteudo_ministrado_id = {$id}
            ");
            $db->ProximoRegistro();

            $data['bncc'] = $db->Tupla();

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

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir ($id = null) {

        return false;
    }
}
