<?php

use iEducar\Legacy\Model;

class clsModulesComponenteMinistrado extends Model {
    public $id;
    public $frequencia_id;
    public $atividades;
    public $observacao;
    public $conteudos;
    public $especificacoes;

    public function __construct(
        $id = null,
        $frequencia_id = null,
        $atividades = null,
        $observacao = null,
        $conteudos = null
        //$especificacoes = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}conteudo_ministrado";

        $this->_from = "
                modules.conteudo_ministrado as cm
            JOIN modules.frequencia f
                ON (cm.frequencia_id = f.id)
            JOIN pmieducar.turma t
                ON (t.cod_turma = f.ref_cod_turma)
            JOIN pmieducar.instituicao i
                ON (i.cod_instituicao = t.ref_cod_instituicao)
            JOIN pmieducar.escola e
                ON (e.cod_escola = t.ref_ref_cod_escola)
            JOIN cadastro.juridica j
                ON (j.idpes = e.ref_idpes)
            JOIN pmieducar.curso c
                ON (c.cod_curso = t.ref_cod_curso)
            JOIN pmieducar.serie s
                ON (s.cod_serie = t.ref_ref_cod_serie)
            LEFT JOIN modules.componente_curricular k
                ON (k.id = f.ref_componente_curricular)
            JOIN pmieducar.turma_turno u
                ON (u.id = t.turma_turno_id)
            LEFT JOIN pmieducar.turma_modulo q
                ON (q.ref_cod_turma = t.cod_turma AND q.sequencial = 1)
            LEFT JOIN pmieducar.modulo l
                ON (l.cod_modulo = q.ref_cod_modulo)
            JOIN modules.professor_turma as pt
                ON (pt.turma_id = f.ref_cod_turma)
            JOIN modules.professor_turma_disciplina as ptd
                ON (pt.id = ptd.professor_turma_id AND
                    (ptd.componente_curricular_id = f.ref_componente_curricular OR f.ref_componente_curricular IS NULL))
        ";

        $this->_campos_lista = $this->_todos_campos = '
            cm.id,
            f.id as frequencia,
            f.data,
            i.nm_instituicao AS instituicao,
            j.fantasia AS escola,
            c.nm_curso AS curso,
            s.nm_serie AS serie,
            t.nm_turma AS turma,
            k.nome AS componente_curricular,
            u.nome AS turno,
            l.nm_tipo AS etapa,
            f.etapa_sequencial AS fase_etapa
        ';


        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($frequencia_id)) {
            $this->frequencia_id = $frequencia_id;
        }

        if (is_string($atividades)) {
            $this->atividades = $atividades;
        }

        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }

        if(is_array($conteudos)){
            $this->conteudos = $conteudos;
        }

        /*if(is_array($especificacoes)){
            $this->especificacoes = $especificacoes;
        }*/
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->frequencia_id)) {
            $db = new clsBanco();

            $campos = "frequencia_id, data_cadastro";
            $valores = "'{$this->frequencia_id}', (NOW() - INTERVAL '3 HOURS')";

            if(is_string($this->atividades)){
                $campos     .=  ", atividades";
                $valores    .=  ", '{$db->escapeString($this->atividades)}'";
            }

            if(is_string($this->observacao)){
                $campos     .=  ", observacao";
                $valores    .=  ", '{$db->escapeString($this->observacao)}'";
            }

            $db->Consulta("
                INSERT INTO
                    {$this->_tabela} ( $campos )
                    VALUES ( $valores )
            ");

            $id = $db->InsertId("{$this->_tabela}_id_seq");

            if (is_array($this->conteudos)) {
                foreach ($this->conteudos as $key => $conteudo) {
                    $obj = new clsModulesComponenteMinistradoConteudo(null, $id, $conteudo);
                    $obj->cadastra();
                }
            }

            /*foreach ($this->especificacoes as $key => $especificacao) {
                $obj = new clsModulesComponenteMinistradoBNCCEspecificacao(null, $id, $especificacao);
                $obj->cadastra();
            }*/

            return $id;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita() {
        if (is_numeric($this->id) && is_string($this->atividades)) {
            $db = new clsBanco();

            $set = "atividades = '{$db->escapeString($this->atividades)}',
                    observacao = NULLIF('{$db->escapeString($this->observacao)}',''),
                    data_atualizacao = (NOW() - INTERVAL '3 HOURS')";

            $db->Consulta("
                UPDATE
                    {$this->_tabela}
                SET
                    $set
                WHERE
                    id = '{$this->id}'
            ");

            if (is_array($this->conteudos)) {
                $obj = new clsModulesComponenteMinistradoConteudo();
                foreach ($obj->lista($this->id) as $key => $conteudo) {
                    $conteudos_atuais[] = $conteudo;
                }

                $obj = new clsModulesComponenteMinistradoConteudo(null, $this->id);
                $conteudos_diferenca = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $this->conteudos);

                foreach ($conteudos_diferenca['adicionar'] as $key => $conteudo_adicionar){
                    $obj = new clsModulesComponenteMinistradoConteudo(null, $this->id, $conteudo_adicionar);
                    $obj->cadastra();
                }

                foreach ($conteudos_diferenca['remover'] as $key => $conteudo_remover){
                    $obj = new clsModulesComponenteMinistradoConteudo(null, $this->id, $conteudo_remover);
                    $obj->excluir();
                }
            }


            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista (
        $int_ano = null,
        $int_ref_cod_ins = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_turma = null,
        $int_ref_cod_componente_curricular = null,
        $int_ref_cod_turno = null,
        $time_data_inicial = null,
        $time_data_final = null,
        $int_etapa = null,
        $int_servidor_id = null,
        $int_frequencia_id = null
    ) {

        $sql = "
                SELECT DISTINCT
                    {$this->_campos_lista},
                    cm.atividades,
                    cm.observacao,
                    f.ref_cod_turma as cod_turma
                FROM
                    {$this->_from}
                ";

        $whereAnd = ' AND ';
        $filtros = " WHERE TRUE ";

        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} EXTRACT(YEAR FROM f.data) = '{$int_ano}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_ins)) {
            $filtros .= "{$whereAnd} i.cod_instituicao = '{$int_ref_cod_ins}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} e.cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} c.cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} s.cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_componente_curricular)) {
            $filtros .= "{$whereAnd} k.id = '{$int_ref_cod_componente_curricular}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turno)) {
            $filtros .= "{$whereAnd} t.turma_turno_id = '{$int_ref_cod_turno}'";
            $whereAnd = ' AND ';
        }

        if ($time_data_inicial) {
            $filtros .= "{$whereAnd} f.data >= '{$time_data_inicial}'";
            $whereAnd = ' AND ';
        }

        if ($time_data_final) {
            $filtros .= "{$whereAnd} f.data <= '{$time_data_final}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_etapa)) {
            $filtros .= "{$whereAnd} f.etapa_sequencial = '{$int_etapa}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_servidor_id)) {
            $filtros .= "{$whereAnd} pt.servidor_id = '{$int_servidor_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_frequencia_id)) {
            $filtros .= "{$whereAnd} cm.frequencia_id = '{$int_frequencia_id}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico(
            "SELECT
                COUNT(0)
            FROM
                {$this->_from}
            {$filtros}"
        );

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
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
                    {$this->_todos_campos},
                    cm.atividades,
                    cm.observacao,
                    f.ref_cod_turma as cod_turma
                FROM
                    {$this->_from}
                WHERE
                    cm.id = {$this->id}
            ");

            $db->ProximoRegistro();

            $data['detalhes'] = $db->Tupla();

            $obj = new clsModulesComponenteMinistradoConteudo();
            $data['conteudos'] = $obj->lista($this->id);

            $obj = new clsModulesComponenteMinistradoBNCCEspecificacao();
            $data['especificacoes'] = $obj->lista($this->id);

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
        if (is_numeric($this->id)) {
            $db = new clsBanco();

            $db->Consulta("
                DELETE FROM
                    modules.conteudo_ministrado
                WHERE
                    id = '{$this->id}'
            ");

            return true;
        }

        return false;
    }
}
