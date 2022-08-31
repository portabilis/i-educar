<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAulaConteudoAee extends Model {
    public $id;
    public $planejamento_aula_aee_id;
    public $conteudo;

    public function __construct(
        $id = null,
        $planejamento_aula_aee_id = null,
        $conteudo = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula_conteudo_aee";

        $this->_from = "
            modules.planejamento_aula_conteudo_aee as pac
        ";

        $this->_campos_lista = $this->_todos_campos = '
            *
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($planejamento_aula_aee_id)) {
            $this->planejamento_aula_aee_id = $planejamento_aula_aee_id;
        }

        if (is_string($conteudo)) {
            $this->conteudo = $conteudo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->planejamento_aula_aee_id) && is_string($this->conteudo)) {
            $db = new clsBanco();

            $db->Consulta("
                INSERT INTO {$this->_tabela}
                    (planejamento_aula_aee_id, conteudo)
                VALUES ('{$this->planejamento_aula_aee_id}', '{$db->escapeString($this->conteudo)}')
            ");

            return true;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita() {
        if (is_numeric($this->id) && is_string($this->conteudo) && $this->conteudo !== "") {
            $db = new clsBanco();
            $sql = "
                UPDATE
                    {$this->_from}
                SET
                    conteudo = '{$db->escapeString($this->conteudo)}'
                WHERE
                    id = {$this->id}
            ";

            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    /**
     * Lista relacionamentos entre os conteudos e o plano de aula
     *
     * @return array
     */
    public function lista($planejamento_aula_aee_id) {
        if (is_numeric($planejamento_aula_aee_id)) {
            $db = new clsBanco();

            $db->Consulta("
                SELECT
                    *
                FROM
                    modules.planejamento_aula_conteudo_aee as pac
                WHERE
                    pac.planejamento_aula_aee_id = '{$planejamento_aula_aee_id}'
                ORDER BY pac.id ASC
            ");

            while($db->ProximoRegistro()) {
                $conteudos[] = $db->Tupla();
            }

            return $conteudos;
        }

        return false;
    }

    /**
     * Lista relacionamentos entre os conteúdos e o plano de aula retornando se os conteúdos estão sendo usados
     *
     * @return array
     */
    public function lista2($planejamento_aula_aee_id) {
        if (is_numeric($planejamento_aula_aee_id)) {
            $db = new clsBanco();

            $db->Consulta("
                SELECT
                    pac.*,
                    CASE
                        WHEN cmc.planejamento_aula_conteudo_aee_id IS NULL THEN false
                        WHEN cmc.planejamento_aula_conteudo_aee_id IS NOT NULL THEN true
                    END usando
                FROM
                    modules.planejamento_aula_conteudo_aee as pac
                LEFT JOIN modules.conteudo_ministrado_conteudo_aee as cmc
                    ON (cmc.planejamento_aula_conteudo_aee_id = pac.id)
                WHERE
                    pac.planejamento_aula_aee_id =  '{$planejamento_aula_aee_id}'
            ");

            while($db->ProximoRegistro()) {
                $conteudos[] = $db->Tupla();
            }

            return $conteudos;
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
                    pac.id = {$this->id}
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
        if (is_numeric($this->planejamento_aula_aee_id) && is_string($this->conteudo)) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    {$this->_tabela}
                WHERE
                    planejamento_aula_aee_id = '{$this->planejamento_aula_aee_id}' AND conteudo = '{$db->escapeString($this->conteudo)}'
            ");

            return true;
        }

        return false;
    }

    /**
     * Retorna array com três arrays,
     * uma com os conteúdos a serem cadastrados,
     * outra com os que devem ser removidos,
     * e outra com os que devem ser editados
     *
     * @return array
     */
    public function retornaDiferencaEntreConjuntosConteudos ($conteudosAtuais, $conteudosNovos) {
        $resultado = ['adicionar' => [], 'remover' => [], 'editar' => []];

        $conteudosAtuaisClone = array_merge($conteudosAtuais);
        $conteudosNovosClone = array_merge($conteudosNovos);

        for ($i = count($conteudosNovos) - 1; $i >= 0; $i--) {
            $novo = $conteudosNovos[$i];

            $chaveId = array_search($novo[0], array_column($conteudosAtuais, 'id'));
            $valueId = array_search($novo[1], array_column($conteudosAtuais, 'conteudo'));

            if (is_numeric($chaveId)) {
                if (!is_numeric($valueId)) {
                    $resultado['editar'][] = $conteudosNovosClone[$chaveId];

                    unset($conteudosAtuaisClone[$chaveId]);
                    array_pop($conteudosNovos);
                } else {
                    if ($chaveId === $valueId)
                        unset($conteudosAtuaisClone[$chaveId]);
                    else
                        dd("Algum conteúdo foi trocado de lugar; isso não é suportado pelo sistema. É possível apenas removê-lo ou editá-lo.");
                }
            } else {
                $resultado['adicionar'][] = $conteudosNovosClone[$i];

                array_pop($conteudosNovos);
            }
        }

        $resultado['remover'] = $conteudosAtuaisClone;

        return $resultado;
    }

    /**
     * Retorna array com conteudos os que devem ser removidos
     *
     * @return array
     */
    public function retornaConteudosRemovidos($atuaisConteudos, $novosConteudos) {
        $resultado = [];
        $resultado['adicionar'] = $novosConteudos;

        for ($i=0; $i < count($atuaisConteudos); $i++) {
            $resultado['remover'][$i]['id'] = $atuaisConteudos[$i]['id'];
            $resultado['remover'][$i]['conteudo'] = $atuaisConteudos[$i]['conteudo'];
        }
        $atuaisConteudos = $resultado['remover'];

        for ($i=0; $i < count($novosConteudos); $i++) {
            $novo = $novosConteudos[$i];

            for ($j=0; $j < count($atuaisConteudos); $j++) {
                $atual = $atuaisConteudos[$j];

                if ($novo == $atual['conteudo']) {
                    unset($resultado['adicionar'][$i]);
                    unset($resultado['remover'][$j]);
                }
            }
        }
        $resultado = array_column($resultado['remover'], 'id');

        return $resultado;
    }
}