<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAulaBNCCEspecificacaoAee extends Model {
    public $id;
    public $planejamento_aula_bncc_aee_id;
    public $bncc_especificacao_id;

    public function __construct(
        $id = null,
        $planejamento_aula_bncc_aee_id = null,
        $bncc_especificacao_id = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula_bncc_especificacao_aee";

        $this->_from = "
            modules.planejamento_aula_bncc_especificacao_aee as pabe
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($planejamento_aula_bncc_aee_id)) {
            $this->planejamento_aula_bncc_aee_id = $planejamento_aula_bncc_aee_id;
        }

        if (is_numeric($bncc_especificacao_id)) {
            $this->bncc_especificacao_id = $bncc_especificacao_id;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->planejamento_aula_bncc_aee_id) && is_numeric($this->bncc_especificacao_id)) {
            $db = new clsBanco();

            $db->Consulta("
                INSERT INTO {$this->_tabela}
                    (planejamento_aula_bncc_aee_id, bncc_especificacao_id)
                VALUES ({$this->planejamento_aula_bncc_aee_id}, {$this->bncc_especificacao_id})
            ");

            return true;
        }

        return false;
    }

    /**
     * Lista relacionamentos entre BNCC e o plano de aula
     *
     * @return array
     */
    public function lista($planejamento_aula_bncc_aee_id) {
        if (is_numeric($planejamento_aula_bncc_aee_id)) {
            $db = new clsBanco();

            $db->Consulta("
                SELECT
                    pabe.*,
                    be.especificacao
                FROM
                    modules.planejamento_aula_bncc_especificacao_aee as pabe
                JOIN modules.planejamento_aula_bncc_aee as pab
                    ON (pab.id = pabe.planejamento_aula_bncc_id)
                JOIN modules.planejamento_aula_aee as pa
                    ON (pa.id = pab.planejamento_aula_bncc_aee_id)
                JOIN modules.bncc_especificacao_aee as be
	                ON (be.id = pabe.bncc_especificacao_id)
                WHERE
                    pa.id = '{$planejamento_aula_bncc_aee_id}'
            ");

            while($db->ProximoRegistro()) {
                $especificacoes[] = $db->Tupla();
            }

            return $especificacoes;
        }

        return false;
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
                    pabe.id = {$this->id}
            ");

            while ($db->ProximoRegistro()) {
                $data[] = $db->Tupla();
            }

            return $data;
        }

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
    public function excluir () {
        if (is_numeric($this->planejamento_aula_bncc_aee_id) && is_numeric($this->bncc_especificacao_id)) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    {$this->_tabela}
                WHERE
                planejamento_aula_bncc_aee_id = '{$this->planejamento_aula_bncc_aee_id}' AND bncc_especificacao_id = '{$this->bncc_especificacao_id}'
            ");

            return true;
        }

        return false;
    }
}
