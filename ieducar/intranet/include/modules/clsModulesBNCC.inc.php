<?php

use iEducar\Legacy\Model;

class clsModulesBNCC extends Model {
    public $ref_cod_serie;
    public $ref_cod_componente_curricular;

    public function __construct(
        $ref_cod_serie = null,
        $ref_cod_componente_curricular = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = "{$this->_schema}learning_objectives_and_skills";

        $this->_from = "
            learning_objectives_and_skills as bncc
        ";

        $this->_campos_lista = $this->_todos_campos = '
            bncc.id,
            bncc.code,
            bncc.description,
            bncc.field_of_experience,
            bncc.thematic_unit,
            bncc.discipline,
            unnest(bncc.grades) as grade
        ';

        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }

        if (is_numeric($ref_cod_componente_curricular)) {
            $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        

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
        $int_ref_cod_serie = null,
        $int_ref_cod_componente_curricular = null
    ) {
        $sql = "
            WITH select_ as (
                SELECT
                    {$this->_campos_lista}
                FROM
                    {$this->_from}
            )
                SELECT * FROM select_
        ";

        $whereAnd = ' AND ';
        $filtros = " WHERE TRUE ";

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} grade = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_componente_curricular)) {
            $filtros .= "{$whereAnd} discipline = '{$int_ref_cod_componente_curricular}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("
            WITH select_ as (
                SELECT
                    {$this->_campos_lista}
                FROM
                    {$this->_from}
            )
                SELECT COUNT(0) FROM select_ {$filtros}" 
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
