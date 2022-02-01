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
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}bncc";

        $this->_from = "
            modules.bncc as bncc
        ";

        $this->_campos_lista = $this->_todos_campos = '
            bncc.id,
            bncc.codigo,
            bncc.habilidade,
            bncc.campo_experiencia,
            bncc.unidade_tematica,
            bncc.componente_curricular_id,
            unnest(bncc.serie_ids) as serie_id
        ';

        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }

        if (is_numeric($ref_cod_componente_curricular)) {
            $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
        }
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista (
        $int_frequencia = null
    ) {
        $sql = "
            WITH select_ as (
                SELECT
                    {$this->_campos_lista}
                FROM
                    {$this->_from}
            )
                SELECT
                    select_.id,
                    codigo,
                    habilidade,
                    campo_experiencia,
                    unidade_tematica,
                    componente_curricular_id,
                    select_.serie_id
                FROM select_
                CROSS JOIN modules.frequencia as f
                JOIN pmieducar.turma as t
                    ON (t.cod_turma = f.ref_cod_turma)
				JOIN modules.componente_curricular as cc
					ON (cc.codigo_educacenso = select_.componente_curricular_id)
                WHERE select_.serie_id::integer = t.etapa_educacenso AND (f.ref_componente_curricular IS NULL OR cc.id = f.ref_componente_curricular)
        ";

        $whereAnd = ' AND ';
        $filtros = "";

        if (is_numeric($int_frequencia)) {
            $filtros .= "{$whereAnd} f.id = '{$int_frequencia}'";
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
                SELECT
                    COUNT(0)
                FROM select_
                CROSS JOIN modules.frequencia as f
                JOIN pmieducar.turma as t
                    ON (t.cod_turma = f.ref_cod_turma)
				JOIN modules.componente_curricular as cc
					ON (cc.codigo_educacenso = select_.componente_curricular_id)
                WHERE select_.serie_id::integer = t.etapa_educacenso AND (f.ref_componente_curricular IS NULL OR cc.id = f.ref_componente_curricular)
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
}
