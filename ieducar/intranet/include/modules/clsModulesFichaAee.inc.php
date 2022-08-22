<?php

use iEducar\Legacy\Model;

class clsModulesFichaAee extends Model
{
    public $id;
    public $data;
    public $ref_cod_turma;
    public $ref_cod_matricula;
    public $necessidades_aprendizagem;
    public $caracterizacao_pedagogica;

    public function __construct(
        $id = null,
        $data = null,
        $ref_cod_turma = null,
        $ref_cod_matricula = null,
        $necessidades_aprendizagem = null,
        $caracterizacao_pedagogica = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}ficha_aee";

        $this->_from = "
                modules.ficha_aee as fa            
            JOIN pmieducar.turma t
                ON (t.cod_turma = fa.ref_cod_turma)
            JOIN pmieducar.matricula m
                ON (m.cod_matricula = fa.ref_cod_matricula)  
            JOIN pmieducar.aluno a
                ON (a.cod_aluno = m.ref_cod_aluno)             
            JOIN cadastro.pessoa p
                ON (p.idpes = a.ref_idpes)
            JOIN modules.professor_turma as pt
                ON (pt.turma_id = fa.ref_cod_turma)
            JOIN cadastro.pessoa AS pe
                ON ( pe.idpes = pt.servidor_id )
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
            JOIN pmieducar.turma_turno u
                ON (u.id = t.turma_turno_id)
            LEFT JOIN pmieducar.turma_modulo q
                ON (q.ref_cod_turma = t.cod_turma AND q.sequencial = 1)                              
        ";

        $this->_campos_lista = $this->_todos_campos = '
            fa.id,
            fa.data,
            fa.ref_cod_turma,
            fa.ref_cod_matricula,
            fa.necessidades_aprendizagem,
            fa.caracterizacao_pedagogica, 
            p.nome AS aluno,
            j.fantasia AS escola,
            c.nm_curso AS curso,
            t.nm_turma AS turma,
            s.nm_serie AS serie,
            pe.nome AS professor
        ';


        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_string($data)) {
            $this->data = $data;
        }

        // if (is_numeric($int_ref_cod_curso)) {
        //     $filtros .= "{$whereAnd} c.cod_curso = '{$int_ref_cod_curso}'";
        //     $whereAnd = ' AND ';
        // }

        // if (is_numeric($int_ref_cod_serie)) {
        //     $filtros .= "{$whereAnd} s.cod_serie = '{$int_ref_cod_serie}'";
        //     $whereAnd = ' AND ';
        // }

        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }

        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }

        if (is_string($necessidades_aprendizagem)) {
            $this->necessidades_aprendizagem = $necessidades_aprendizagem;
        }

        if (is_string($caracterizacao_pedagogica)) {
            $this->caracterizacao_pedagogica = $caracterizacao_pedagogica;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_turma)) {
            $db = new clsBanco();

            $campos = "data, created_at";
            $valores = "'{($this->data)}', (NOW() - INTERVAL '3 HOURS')";


            if (is_numeric($this->ref_cod_turma)) {
                $campos     .=  ", ref_cod_turma";
                $valores    .=  ", '$this->ref_cod_turma'";
            }

            if (is_numeric($this->ref_cod_matricula)) {
                $campos     .=  ", ref_cod_matricula";
                $valores    .=  ", '$this->ref_cod_matricula'";
            }

            if (is_string($this->necessidades_aprendizagem)) {
                $campos     .=  ", necessidades_aprendizagem";
                $valores    .=  ", '{$db->escapeString($this->necessidades_aprendizagem)}'";
            }

            if (is_string($this->caracterizacao_pedagogica)) {
                $campos     .=  ", caracterizacao_pedagogica";
                $valores    .=  ", '{$db->escapeString($this->caracterizacao_pedagogica)}'";
            }
            
            $db->Consulta("
                INSERT INTO
                    {$this->_tabela} ( $campos )
                    VALUES ( $valores )
            ");

            return true;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (
            is_numeric($this->id)
            && is_string($this->necessidades_aprendizagem)
            && is_string($this->caracterizacao_pedagogica)
        ) {
            $db = new clsBanco();

            $set = "
                    necessidades_aprendizagem = '{$db->escapeString($this->necessidades_aprendizagem)}',
                    caracterizacao_pedagogica = '{$db->escapeString($this->caracterizacao_pedagogica)}',
                    updated_at = (NOW() - INTERVAL '3 HOURS')";

            $db->Consulta("
                UPDATE
                    {$this->_tabela}
                SET
                    $set
                WHERE
                    id = '{$this->id}'
            ");

            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista(
        $data = null,
        $int_ref_cod_ins = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_turma = null,
        $int_ref_cod_matricula = null,
        $int_servidor_id = null
    ) {

        $sql = "
                SELECT DISTINCT
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

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_servidor_id)) {
            $filtros .= "{$whereAnd} pt.servidor_id = '{$int_servidor_id}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . 'ORDER BY fa.data DESC' . $this->getLimite();

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
    public function detalhe()
    {
        $data = [];

        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    {$this->_todos_campos}                                    
                FROM
                    {$this->_from}
                WHERE
                    fa.id = {$this->id}
            ");

            $db->ProximoRegistro();

            $data['detalhes'] = $db->Tupla();

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
        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_matricula)) {
            $sql = "
                SELECT
                    *
                FROM
                    modules.ficha_aee as fa
                WHERE fa.ref_cod_matricula = '{$this->ref_cod_matricula}'
                    AND fa.ref_cod_turma = '{$this->ref_cod_turma}'
            ";

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
    public function excluir()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                modules.ficha_aee
                WHERE
                    id = '{$this->id}'
            ");

            return true;
        }

        return false;
    }
}
