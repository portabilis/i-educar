<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarBackup extends Model
{
    public $idBackup;
    public $caminho;
    public $dataBackup;

    public function __construct(
        $idBackup = null,
        $caminho = null,
        $dataBackup = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}backup";

        $this->_campos_lista = $this->_todos_campos = 'id,
                                                       caminho,
                                                       data_backup';

        if (is_numeric($idBackup)) {
            $this->idBackup = $idBackup;
        }
        if (is_string($caminho)) {
            $this->caminho = $caminho;
        }
        if (is_string($dataBackup)) {
            $this->dataBackup = $dataBackup;
        }
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista(
        $idBackup = null,
        $caminho = null,
        $dataBackup = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($idBackup)) {
            $filtros .= "{$whereAnd}id = '{$idBackup}'";
            $whereAnd = ' AND ';
        }
        if (is_string($caminho)) {
            $filtros .= "{$whereAnd} caminho LIKE '%{$caminho}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($dataBackup)) {
            $filtros .= "{$whereAnd} date(data_backup) = '{$dataBackup}'";
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
}
