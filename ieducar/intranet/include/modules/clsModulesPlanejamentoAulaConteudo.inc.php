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
                VALUES ('{$this->planejamento_aula_id}', '{$this->conteudo}')
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
                *
            FROM
                modules.planejamento_aula_conteudo as pac
            WHERE
                pac.planejamento_aula_id = '{$planejamento_aula_id}'
        ");

        while($db->ProximoRegistro()) {
            $conteudos[] = $db->Tupla();
        }

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
        if (is_numeric($this->planejamento_aula_id) && $this->conteudo) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    {$this->_tabela}
                WHERE
                    planejamento_aula_id = '{$this->planejamento_aula_id}' AND conteudo = '{$this->conteudo}'
            ");

            return true;
        }

        return false;
    }

    /**
     * Retorna array com duas arrays, uma com os conteúdos a serem cadastrados e a outra com os que devem ser removidos
     *
     * @return array
     */
    public function retornaDiferencaEntreConjuntosConteudos($atuaisConteudos, $novosConteudos) {
        $resultado = [];
        $resultado['adicionar'] = $novosConteudos;

        for ($i=0; $i < count($atuaisConteudos); $i++) {
            $resultado['remover'][] = $atuaisConteudos[$i]['conteudo']; 
        }
        $atuaisConteudos = $resultado['remover'];

        for ($i=0; $i < count($novosConteudos); $i++) { 
            $novo = $novosConteudos[$i];

            for ($j=0; $j < count($atuaisConteudos); $j++) {
                $atual = $atuaisConteudos[$j];

                if ($novo == $atual) {
                    unset($resultado['adicionar'][$i]);
                    unset($resultado['remover'][$j]);
                }
            }
        }

        return $resultado;
    }
}
