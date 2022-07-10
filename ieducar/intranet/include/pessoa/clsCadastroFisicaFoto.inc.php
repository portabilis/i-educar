<?php

use iEducar\Legacy\Model;

class clsCadastroFisicaFoto extends Model
{
    public $idpes;
    public $caminho;

    /**
     * Construtor (PHP 5)
     *
     * @return object
     */
    public function __construct($idpes = null, $caminho = null)
    {
        $db = new clsBanco();
        $this->_schema = 'cadastro.';
        $this->_tabela = "{$this->_schema}fisica_foto";

        $this->_campos_lista = $this->_todos_campos = 'idpes, caminho';

        if (is_numeric($idpes)) {
            $tmp_obj = new clsPessoa_($idpes);
            if ($tmp_obj->detalhe()) {
                $this->idpes = $idpes;
            }
        }
        if (is_string($caminho)) {
            $this->caminho = $caminho;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->idpes)) {
                $campos .= "{$gruda}idpes";
                $valores .= "{$gruda}'{$this->idpes}'";
                $gruda = ', ';
            }
            if (is_string($this->caminho)) {
                $campos .= "{$gruda}caminho";
                $valores .= "{$gruda}'{$this->caminho}'";
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
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_string($this->caminho)) {
                $set .= "{$gruda}caminho = '{$this->caminho}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idpes = '{$this->idpes}'");

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
    public function lista($int_idpes = null, $str_caminho = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_idpes)) {
            $filtros .= "{$whereAnd} idpes = '{$int_idpes}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_caminho)) {
            $filtros .= "{$whereAnd} caminho ILIKE '%{$str_caminho}%'";
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
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idpes = '{$this->idpes}'");
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
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE idpes = '{$this->idpes}'");

            return true;
        }

        return false;
    }
}
