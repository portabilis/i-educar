<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAulaComponenteCurricular extends Model {
    public $id;
    public $planejamento_aula_id;
    public $componente_curricular_id;
    public $plano_aee;

    public function __construct(
        $id = null,
        $planejamento_aula_id = null,
        $componente_curricular_id = null,
        $plano_aee = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula_componente_curricular";

        $this->_from = "
            modules.planejamento_aula_componente_curricular as pacc
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

        if (is_numeric($componente_curricular_id)) {
            $this->componente_curricular_id = $componente_curricular_id;
        }

        if (is_string($plano_aee)) {
            $this->plano_aee = $plano_aee;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->planejamento_aula_id) && is_numeric($this->componente_curricular_id)) {
            $db = new clsBanco();

            $db->Consulta("
                INSERT INTO {$this->_tabela}
                    (planejamento_aula_id, componente_curricular_id)
                VALUES ({$this->planejamento_aula_id}, {$this->componente_curricular_id})
            ");

            return true;
        }

        return false;
    }

    /**
     * Cria um novo registro do Planejamento AEE
     *
     * @return bool
     */
    public function cadastra_componente_aee() {
        if (is_numeric($this->planejamento_aula_id) && is_numeric($this->componente_curricular_id)) {
            $db = new clsBanco();

            $db->Consulta("
                INSERT INTO {$this->_tabela}
                    (planejamento_aula_id, componente_curricular_id, plano_aee)
                VALUES ({$this->planejamento_aula_id}, {$this->componente_curricular_id}, '{$this->plano_aee}')
            ");

            return true;
        }

        return false;
    }

    /**
     * Lista relacionamentos entre CC e o plano de aula
     *
     * @return array
     */
    public function lista($planejamento_aula_id) {
        if (is_numeric($planejamento_aula_id)) {
            $db = new clsBanco();

            $db->Consulta("
                SELECT
                    pac.planejamento_aula_id,
                    pac.componente_curricular_id,
                    cc.nome,
                    cc.abreviatura
                FROM
                    modules.planejamento_aula_componente_curricular AS pac
                JOIN modules.componente_curricular cc on (pac.componente_curricular_id = cc.id)
                WHERE
                    pac.planejamento_aula_id = '{$planejamento_aula_id}'
                    AND pac.plano_aee IS NULL
            ");

            $componentes = [];

            while($db->ProximoRegistro()) {
                $componentes[] = [
                    'id' => $db->Tupla()['componente_curricular_id'],
                    'abreviatura' => $db->Tupla()['abreviatura'],
                    'nome' => $db->Tupla()['nome'],
                ];
            }

            return $componentes;
        }

        return false;
    }

      /**
     * Lista relacionamentos entre CC e o plano de aula AEE
     *
     * @return array
     */
    public function lista_aee($planejamento_aula_id) {
        if (is_numeric($planejamento_aula_id)) {
            $db = new clsBanco();

            $db->Consulta("
                SELECT
                    pac.planejamento_aula_id,
                    pac.componente_curricular_id,
                    cc.nome,
                    cc.abreviatura
                FROM
                    modules.planejamento_aula_componente_curricular AS pac
                JOIN modules.componente_curricular cc on (pac.componente_curricular_id = cc.id)
                WHERE
                    pac.planejamento_aula_id = '{$planejamento_aula_id}'
                    AND pac.plano_aee = 'S'
            ");

            $componentes = [];

            while($db->ProximoRegistro()) {
                $componentes[] = [
                    'id' => $db->Tupla()['componente_curricular_id'],
                    'abreviatura' => $db->Tupla()['abreviatura'],
                    'nome' => $db->Tupla()['nome'],
                ];
            }

            return $componentes;
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

        if (is_numeric($this->planejamento_aula_id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_from}
                WHERE
                    pacc.planejamento_aula_id = {$this->planejamento_aula_id}
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
        if (is_numeric($this->planejamento_aula_id) && is_numeric($this->componente_curricular_id)) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    {$this->_tabela}
                WHERE
                    planejamento_aula_id = '{$this->planejamento_aula_id}' AND componente_curricular_id = '{$this->componente_curricular_id}'
            ");

            return true;
        }

        return false;
    }
}
