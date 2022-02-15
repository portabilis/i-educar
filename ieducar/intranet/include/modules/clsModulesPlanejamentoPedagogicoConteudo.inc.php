<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoPedagogicoConteudo extends Model {
    public $id;
    public $planejamento_pedagogico_id;
    public $conteudo;

    public function __construct(
        $id = null,
        $planejamento_pedagogico_id = null,
        $conteudo = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_pedagogico_conteudo";

        $this->_from = "
            modules.planejamento_pedagogico_conteudo as ppc
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($planejamento_pedagogico_id)) {
            $this->planejamento_pedagogico_id = $planejamento_pedagogico_id;
        }

        $this->conteudo = $conteudo;
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita() {
        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista (
        
    ) {
        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe () {
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
