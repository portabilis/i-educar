<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoPedagogico extends Model {
    public $id;
    public $turma_id;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $bnccs;
    public $conteudos;

    public function __construct(
        $id = null,
        $turma_id = null,
        $data_inicial = null,
        $data_final = null,
        $ddp = null,
        $atividades = null,
        $bnccs = null,
        $conteudos = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_pedagogico";

        $this->_from = "
            modules.planejamento_pedagogico as pp
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($turma_id)) {
            $this->turma_id = $turma_id;
        }

        $this->data_inicial = $data_inicial;

        $this->data_final = $data_final;

        $this->ddp = $ddp;

        $this->atividades = $atividades;

        if(is_array($bnccs)){
            $this->bnccs = $bnccs;
        }

        if(is_array($conteudos)){
            $this->conteudos = $conteudos;
        }
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
