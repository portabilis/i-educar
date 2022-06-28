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
                if(empty($bnccArray[$i])) continue;
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

    /**
     * Retorna array com duas arrays, uma com os BNCC ESPECIFICAÇÕES a serem cadastrados e a outra com os que devem ser removidos
     *
     * @return array
     */
    public function retornaDiferencaEntreConjuntosBNCC($atuaisBNCCEspecificacao, $novosBNCCEspecificacao)
    {
        $resultado = [];
        $resultado['adicionar'] = $novosBNCCEspecificacao;

        for ($i = 0; $i < count($atuaisBNCCEspecificacao); $i++) {
            $resultado['remover'][] = $atuaisBNCCEspecificacao[$i]['bncc_especificacao_id'];
        }

        $atuaisBNCC = $resultado['remover'];

        for ($i = 0; $i < count($novosBNCCEspecificacao); $i++) {
            $novoArray = $novosBNCCEspecificacao[$i][1];

            for ($j = 0; $j < count($atuaisBNCC); $j++) {
                $atual = $atuaisBNCC[$j];

                if ($indiceAtual = array_search($atual, $novoArray)) {
                    unset($resultado['adicionar'][$i][1][$indiceAtual]);
                    unset($resultado['remover'][$j]);
                }
            }
        }

        return $resultado;
    }
}
