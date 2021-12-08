<?php

use iEducar\Legacy\Model;

class clsModulesComponenteMinistrado extends Model {
    public $id;
    public $ano;
    public $ref_cod_ins;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $ref_cod_turma;
    public $ref_cod_componente_curricular;
    public $ref_cod_turno;
    public $data;
    public $data_inicial;
    public $data_final;
    public $etapa_sequencial;
    public $alunos;

    public function __construct(
            $id = null,
            $ano = null,
            $ref_cod_ins = null,
            $ref_cod_escola = null,
            $ref_cod_curso = null,
            $ref_cod_serie = null,
            $ref_cod_turma = null,
            $ref_cod_componente_curricular = null,
            $ref_cod_turno = null,
            $data = null,
            $data_inicial = null,
            $data_final = null,
            $etapa_sequencial = null,
            $alunos = null
        ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}frequencia";

        $this->_from = "
                modules.frequencia f
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
        ";

        $this->_campos_lista = $this->_todos_campos = '
            f.id,
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


        if (($ano)) {
            $this->ano = $ano;
        }
        if (is_numeric($ref_cod_ins)) {
            $this->ref_cod_ins = $ref_cod_ins;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }
        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }
        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }
        if (is_numeric($ref_cod_componente_curricular)) {
            $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
        }
        if (is_numeric($ref_cod_turno)) {
            $this->ref_cod_turno = $ref_cod_turno;
        }
        if (($data)) {
            $this->data = $data;
        }
        if (($data_inicial)) {
            $this->data_inicial = $data_inicial;
        }
        if (($data_final)) {
            $this->data_final = $data_final;
        }
        if (($etapa_sequencial)) {
            $this->etapa_sequencial = $etapa_sequencial;
        }
        if (($alunos)) {
            $this->alunos = $alunos;
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
    public function edita($id = null) {
        

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
            $int_etapa = null
        ) {
       
        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe ($id = null) {

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
    public function excluir ($id = null) {

        return false;
    }
}
