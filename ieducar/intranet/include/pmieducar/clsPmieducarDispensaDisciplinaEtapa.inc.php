<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarDispensaDisciplinaEtapa extends Model
{
    public $ref_cod_dispensa;
    public $etapa;

    public function __construct(
        $ref_cod_dispensa = null,
        $etapa = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}dispensa_etapa";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_dispensa,
                                                   etapa';

        if (is_numeric($ref_cod_dispensa)) {
            $this->ref_cod_dispensa = $ref_cod_dispensa;
        }

        if (is_numeric($etapa)) {
            $this->etapa = $etapa;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_dispensa) && is_numeric($this->etapa)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_dispensa)) {
                $campos .= "{$gruda}ref_cod_dispensa";
                $valores .= "{$gruda}'{$this->ref_cod_dispensa}'";
                $gruda = ', ';
            }
            if (is_numeric($this->etapa)) {
                $campos .= "{$gruda}etapa";
                $valores .= "{$gruda}'{$this->etapa}'";
                $gruda = ', ';
            }
            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

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
        if (is_numeric($this->ref_cod_dispensa) && is_numeric($this->etapa)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->etapa)) {
                $set .= "{$gruda}etapa = '{$this->etapa}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela}
                           SET $set
                         WHERE ref_cod_dispensa = '{$this->ref_cod_dispensa}'");

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
    public function lista(
        $ref_cod_dispensa = null,
        $etapa = null
    ) {
        $sql = "SELECT {$this->_campos_lista}
              FROM {$this->_tabela}";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ref_cod_dispensa)) {
            $filtros .= "{$whereAnd} ref_cod_dispensa = '{$ref_cod_dispensa}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($etapa)) {
            $filtros .= "{$whereAnd} etapa = '{$etapa}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros;

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
    public function existe()
    {
        if (is_numeric($this->ref_cod_dispensa) && is_numeric($this->etapa)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1
                        FROM {$this->_tabela}
                       WHERE ref_cod_dispensa = '{$this->ref_cod_dispensa}'
                         AND etapa = '{$this->etapa}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a uma dispensa
     */
    public function excluirTodos($ref_cod_dispensa = null)
    {
        if (is_numeric($ref_cod_dispensa)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela}
                            WHERE ref_cod_dispensa = '{$ref_cod_dispensa}'");

            return true;
        }

        return false;
    }
}
