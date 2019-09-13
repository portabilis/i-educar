<?php

use iEducar\Legacy\Model;

require_once 'include/public/geral.inc.php';

class clsPublicSetorBai extends Model
{
    public $idsetorbai;
    public $nome;

    public function __construct($idsetorbai = null, $nome = null)
    {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = $this->_schema . 'setor_bai ';

        $this->_campos_lista = $this->_todos_campos = ' idsetorbai, nome ';

        if (is_numeric($idsetorbai)) {
            $this->idsetorbai = $idsetorbai;
        }

        if (is_string($nome)) {
            $this->nome = $nome;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->nome)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}nome";
            $valores .= "{$gruda}'{$this->nome}'";
            $gruda = ', ';

            $db->Consulta(sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->_tabela,
                $campos,
                $valores
            ));

            return $db->InsertId('seq_setor_bai');
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
        if (is_numeric($this->idsetorbai)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '{$this->nome}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta(sprintf(
                    'UPDATE %s SET %s WHERE idsetorbai = \'%d\'',
                    $this->_tabela,
                    $set,
                    $this->idsetorbai
                ));

                return true;
            }
        }

        return false;
    }

    public function lista($int_idsetorbai = null, $nome = null)
    {
        $select = "SELECT {$this->_todos_campos} FROM {$this->_tabela} ";

        $sql = $select;

        $whereAnd = ' WHERE ';
        $filtros = '';
        if (is_numeric($int_idsetorbai)) {
            $filtros .= "{$whereAnd} idsetorbai = '{$int_idsetorbai}'";
            $whereAnd = ' AND ';
        }

        if (is_string($nome)) {
            $filtros .= "{$whereAnd} nome LIKE '%{$nome}%'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();

        $countCampos = count(explode(', ', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico(sprintf(
            'SELECT COUNT(0) FROM %s %s',
            $this->_tabela,
            $filtros
        ));

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
        if (is_numeric($this->idsetorbai)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT %s FROM %s WHERE idsetorbai = \'%d\'',
                $this->_todos_campos,
                $this->_tabela,
                $this->idsetorbai
            );

            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->idsetorbai)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT 1 FROM %s WHERE idsetorbai = \'%d\'',
                $this->_tabela,
                $this->idsetorbai
            );

            $db->Consulta($sql);

            if ($db->ProximoRegistro()) {
                return true;
            }
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
        if (is_numeric($this->idsetorbai)) {
            $db = new clsBanco();

            $sql = sprintf(
                'DELETE FROM %s WHERE idsetorbai = \'%d\'',
                $this->_tabela,
                $this->idsetorbai
            );

            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
