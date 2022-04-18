<?php

use iEducar\Legacy\Model;

class clsModulesComponenteMinistradoBNCCEspecificacao extends Model {
    public $id;
    public $conteudo_ministrado_id;
    public $planejamento_aula_bncc_especificacao_id;

    public function __construct(
        $id = null,
        $conteudo_ministrado_id = null,
        $planejamento_aula_bncc_especificacao_id = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}conteudo_ministrado_bncc_especificacao";

        $this->_from = "
            modules.conteudo_ministrado_bncc_especificacao as cmbe
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($conteudo_ministrado_id)) {
            $this->conteudo_ministrado_id = $conteudo_ministrado_id;
        }

        if (is_numeric($planejamento_aula_bncc_especificacao_id)) {
            $this->planejamento_aula_bncc_especificacao_id = $planejamento_aula_bncc_especificacao_id;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->conteudo_ministrado_id) && is_numeric($this->planejamento_aula_bncc_especificacao_id)) {
            $db = new clsBanco();

            $db->Consulta("
                INSERT INTO {$this->_tabela}
                    (conteudo_ministrado_id, planejamento_aula_bncc_especificacao_id)
                VALUES ('{$this->conteudo_ministrado_id}', '{$this->planejamento_aula_bncc_especificacao_id}')
            ");

            return true;
        }

        return false;
    }

    /**
     * Lista relacionamentos entre os conteudos e o plano de aula
     *
     * @return array
     */
    public function lista($conteudo_ministrado_id) {
        if (is_numeric($conteudo_ministrado_id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    *
                FROM
                    modules.conteudo_ministrado_bncc_especificacao as cmbe
                WHERE
                    cmbe.conteudo_ministrado_id = '{$conteudo_ministrado_id}'
            ");

            while($db->ProximoRegistro()) {
                $cmbe = $db->Tupla();

                $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao($cmbe['planejamento_aula_bncc_especificacao_id']);
                $bncc_especificacao_id = $obj->detalhe()[0]['bncc_especificacao_id'];

                $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_id);
                $cmbe['bncc_especificacao'] = $obj->detalhe();

                $especificacoes[] = $cmbe;
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
                    cmbe.id = {$this->id}
            ");

            $db->ProximoRegistro();
            $data = $db->Tupla();

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
        if (is_numeric($this->conteudo_ministrado_id) && is_numeric($this->planejamento_aula_bncc_especificacao_id)) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    {$this->_tabela}
                WHERE
                    conteudo_ministrado_id = '{$this->conteudo_ministrado_id}' AND planejamento_aula_bncc_especificacao_id = '{$this->planejamento_aula_bncc_especificacao_id}'
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
            $resultado['remover'][] = $atuaisConteudos[$i]['planejamento_aula_bncc_especificacao_id']; 
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

    /**
     * Retorna array com registro(s) de aula com ligação com o conteúdo informado, no caso de ausência, vazio
     *
     * @return array
     */
    public function existeLigacaoRegistroAula ($planejamento_aula_conteudos_ids) {
        if (is_array($planejamento_aula_conteudos_ids) && count($planejamento_aula_conteudos_ids) > 0) {
            $data = [];

            $db = new clsBanco();

            $sql = "
                SELECT DISTINCT
                    conteudo_ministrado_id as id
                FROM
                    modules.conteudo_ministrado_bncc_especificacao as cmbe
            ";

            $whereAnd = ' WHERE';

            foreach ($planejamento_aula_conteudos_ids as $key => $planejamento_aula_bncc_especificacao_id) {
                $sql .= "{$whereAnd} cmbe.planejamento_aula_bncc_especificacao_id = {$planejamento_aula_bncc_especificacao_id}";
                $whereAnd = ' OR';
            }

            $db->Consulta($sql);

            while($db->ProximoRegistro()) {
                $data[] = $db->Campo('id');
            }

            return $data;
        }

        return [];
    }
}
