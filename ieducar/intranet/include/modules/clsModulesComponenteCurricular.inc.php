<?php

use iEducar\Legacy\Model;

error_reporting(E_ERROR);
ini_set('display_errors', 1);

class clsModulesComponenteCurricular extends Model
{
    public $id;
    public $instituicao_id;
    public $area_conhecimento_id;
    public $nome;
    public $abreviatura;
    public $tipo_base;
    public $codigo_educacenso;
    public $ordenamento;

    public function __construct()
    {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}componente_curricular";

        $this->_campos_lista = $this->_todos_campos = 'cc.id, cc.instituicao_id, cc.area_conhecimento_id, cc.nome,
      cc.abreviatura, cc.tipo_base, cc.codigo_educacenso, cc.ordenamento';
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($instituicao_id = null, $nome = null, $abreviatura = null, $tipo_base = null, $area_conhecimento_id = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista}, ac.nome as area_conhecimento
              FROM {$this->_tabela} cc
              INNER JOIN modules.area_conhecimento ac ON cc.area_conhecimento_id = ac.id ";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($instituicao_id)) {
            $filtros .= "{$whereAnd} cc.instituicao_id = '{$instituicao_id}'";
            $whereAnd = ' AND ';
        }
        if (is_string($nome)) {
            $name = $db->escapeString($nome);
            $filtros .= "{$whereAnd} unaccent(cc.nome) ILIKE unaccent('%{$name}%')";
            $whereAnd = ' AND ';
        }
        if (is_string($abreviatura)) {
            $abrevia = $db->escapeString($abreviatura);
            $filtros .= "{$whereAnd} cc.abreviatura LIKE '%{$abrevia}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($tipo_base)) {
            $filtros .= "{$whereAnd} cc.tipo_base >= '{$tipo_base}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($area_conhecimento_id)) {
            $filtros .= "{$whereAnd} cc.area_conhecimento_id = '{$area_conhecimento_id}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} cc {$filtros}");

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

    public function listaComponentesPorCurso($instituicao_id = null, $curso = null)
    {
        $sql = 'SELECT DISTINCT(mca.componente_curricular_id) AS id, cc.nome AS nome
                  FROM modules.componente_curricular_ano_escolar mca
                 INNER JOIN pmieducar.serie s ON (s.cod_serie = mca.ano_escolar_id)
                 INNER JOIN modules.componente_curricular cc ON (cc.id = mca.componente_curricular_id)';

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($instituicao_id)) {
            $filtros .= "{$whereAnd} cc.instituicao_id = '{$instituicao_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($curso)) {
            $filtros .= "{$whereAnd} s.ref_cod_curso = '{$curso}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $resultado[] = $db->Tupla();
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }
}
