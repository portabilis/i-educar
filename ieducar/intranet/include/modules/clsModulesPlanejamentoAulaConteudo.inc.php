<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAulaConteudo extends Model {
    public $id;
    public $planejamento_aula_id;
    public $conteudo;

    public function __construct(
        $id = null,
        $planejamento_aula_id = null,
        $conteudo = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula_conteudo";

        $this->_from = "
            modules.planejamento_aula_conteudo as pac
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($planejamento_aula_id)) {
            $this->planejamento_aula_id = $planejamento_aula_id;
        }

        $this->conteudo = $conteudo;
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->planejamento_aula_id) && $this->conteudo != '') {
            $db = new clsBanco();

            $db->Consulta("
                INSERT INTO {$this->_tabela}
                    (planejamento_aula_id, conteudo)
                VALUES ({$this->planejamento_aula_id}, {$this->conteudo})
            ");

            return true;
        }

        return false;
    }

    /**
     * Lista relacionamentos entre os conteudos e o planejamento de aula
     *
     * @return array
     */
    public function lista($planejamento_aula_id) {
        $db = new clsBanco();

        $db->Consulta("
            SELECT
                pac.conteudo
            FROM
                modules.planejamento_aula_conteudo as pac
            WHERE
                pac.planejamento_aula_id = '{$planejamento_aula_id}'
        ");

        $db->ProximoRegistro();

        $conteudos = $db->Tupla();

        return $conteudos;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe () {
        $data = [];

        if (is_numeric($this->planejamento_aula_id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_from}
                WHERE
                    pac.planejamento_aula_id = {$this->planejamento_aula_id}
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
        return false;
    }
}
