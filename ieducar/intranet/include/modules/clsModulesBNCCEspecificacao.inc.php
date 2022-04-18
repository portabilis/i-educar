<?php

use iEducar\Legacy\Model;

class clsModulesBNCCEspecificacao extends Model {
    public $id;
    public $bncc_id;
    public $especificacao;

    public function __construct(
        $id = null,
        $bncc_id = null,
        $especificacao = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}bncc_especificacao";

        $this->_from = "
            modules.bncc_especificacao as be
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($bncc_id)) {
            $this->bncc_id = $bncc_id;
        }

        if (is_string($especificacao)) {
            $this->especificacao = $especificacao;
        }
    }

    /**
     * Lista as especificações de um BNCC
     *
     * @return array
     */
    public function lista($bncc_id = null) {
        $bncc_especificacao = [];

        $db = new clsBanco();

        $sql = "
            SELECT
                *
            FROM
                modules.bncc_especificacao as be
        ";

        if (is_numeric($bncc_id)) {
            $sql .= " WHERE be.bncc_id = '{$bncc_id}'";
        }

        $db->Consulta($sql);

        while($db->ProximoRegistro()){
            $bncc_especificacao[] = $db->Tupla();
        }

        return $bncc_especificacao;
    }

    /**
     * Lista as especificações de vários BNCCs
     *
     * @return array
     */
    public function lista2($bnccArray = null) {
        $bncc_especificacao = [];

        $db = new clsBanco();

        $sql = "
            SELECT
                *
            FROM
                modules.bncc_especificacao as be
        ";

        if (is_array($bnccArray)) {
            $sql .= " WHERE be.bncc_id IN (";

            for ($i=0; $i < count($bnccArray); $i++) {
                $separador = $i < count($bnccArray) -1 ? ',' : '';
                $bncc = $bnccArray[$i];

                $sql .= $bncc . $separador;
            }

            $sql .= ")";
        }

        $db->Consulta($sql);

        while($db->ProximoRegistro()){
            $bncc_especificacao[] = $db->Tupla();
        }

        return $bncc_especificacao;
    }

    
    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe () {
        $data = [];

        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_from}
                WHERE
                    be.id = {$this->id}
            ");

            $db->ProximoRegistro();
            $data = $db->Tupla();

            return $data;
        }

        return false;
    }
}
