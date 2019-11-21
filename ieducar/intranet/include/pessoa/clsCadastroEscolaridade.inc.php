<?php

use App\Models\LegacySchoolingDegree;
use iEducar\Legacy\Model;

class clsCadastroEscolaridade extends Model
{
    public $idesco;
    public $descricao;
    public $escolaridade;

    /**
     * Construtor (PHP 4).
     */
    public function __construct($idesco = null, $descricao = null, $escolaridade = null)
    {
        $db = new clsBanco();
        $this->_schema = 'cadastro.';
        $this->_tabela = "{$this->_schema}escolaridade";

        $this->_campos_lista = $this->_todos_campos = 'idesco, descricao, escolaridade';

        if (is_numeric($idesco)) {
            $this->idesco = $idesco;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_numeric($escolaridade)) {
            $this->escolaridade = $escolaridade;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->descricao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $this->idesco = $db->CampoUnico('SELECT MAX(idesco) + 1
                      FROM cadastro.escolaridade');

            // Se for nulo, é o primeiro registro da tabela
            if (is_null($this->idesco)) {
                $this->idesco = 1;
            }

            if (is_numeric($this->idesco)) {
                $campos .= "{$gruda}idesco";
                $valores .= "{$gruda}'{$this->idesco}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->escolaridade)) {
                $campos .= "{$gruda}escolaridade";
                $valores .= "{$gruda}'{$this->escolaridade}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            return $this->idesco;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->idesco)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->escolaridade)) {
                $set .= "{$gruda}escolaridade = '{$this->escolaridade}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idesco = '{$this->idesco}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista($int_idesco = null, $str_descricao = null, $escolaridade = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_idesco)) {
            $filtros .= "{$whereAnd} idesco = '{$int_idesco}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($escolaridade)) {
            $filtros .= "{$whereAnd} escolaridade = {$escolaridade} ";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} descricao ILIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->idesco)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idesco = '{$this->idesco}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool | array
     */
    public function excluir()
    {
        if (is_numeric($this->idesco)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE idesco = '{$this->idesco}'");

            return true;
        }

        return false;
    }

    /**
     *  Verifica se o tipo de escolaridade está sendo referenciado nas outras tabelas que possuem FK,
     *  vai retorna uma lista de idpes que possuem vínculo
     *
     * @return array|bool
     * @throws Exception
     */
    public function findUsages() {
        if (! is_numeric($this->idesco)) {
            return false;
        }

        $employees = LegacySchoolingDegree::select('cod_servidor')
            ->join('pmieducar.servidor', 'ref_idesco', '=', 'idesco', 'left')
            ->where('idesco', $this->idesco)
            ->get();

        foreach ($employees as $key => $employe) {
            $results[$key]['cod_servidor'] = $employe->cod_servidor;
        }

        if (count($results) == 1 && !$results[0]['cod_servidor']) {
            return false;
        }

        return json_encode($results);
    }
}
