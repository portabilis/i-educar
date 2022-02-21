<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAula extends Model {
    public $id;
    public $ref_cod_turma;
    public $ref_componente_curricular_id;
    public $etapa_sequencial;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $bnccs;
    public $conteudos;

    public function __construct(
        $id = null,
        $ref_cod_turma = null,
        $ref_componente_curricular_id = null,
        $etapa_sequencial = null,
        $data_inicial = null,
        $data_final = null,
        $ddp = null,
        $atividades = null,
        $bnccs = null,
        $conteudos = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula";

        $this->_from = "
                modules.planejamento_aula as pa
            JOIN pmieducar.turma t
                ON (t.cod_turma = pa.ref_cod_turma)
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
                ON (k.id = pa.ref_componente_curricular)
            JOIN pmieducar.turma_turno u
                ON (u.id = t.turma_turno_id)
            LEFT JOIN pmieducar.turma_modulo q
                ON (q.ref_cod_turma = t.cod_turma AND q.sequencial = 1)
            LEFT JOIN pmieducar.modulo l
                ON (l.cod_modulo = q.ref_cod_modulo)
        ";

        $this->_campos_lista = $this->_todos_campos = '
            pa.id,
            pa.data_inicial,
            pa.data_final,
            pa.ref_cod_turma,
            pa.ref_componente_curricular as ref_cod_componente_curricular,
            pa.ddp,
            pa.atividades,
            i.nm_instituicao AS instituicao,
            j.fantasia AS escola,
            c.nm_curso AS curso,
            s.nm_serie AS serie,
            t.nm_turma AS turma,
            k.nome AS componente_curricular,
            u.nome AS turno,
            l.nm_tipo AS etapa,
            pa.etapa_sequencial AS fase_etapa
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }

        if (is_numeric($ref_componente_curricular_id)) {
            $this->ref_componente_curricular_id = $ref_componente_curricular_id;
        }

        if (is_numeric($etapa_sequencial)) {
            $this->etapa_sequencial = $etapa_sequencial;
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
        if (is_numeric($this->ref_cod_turma)
            && is_numeric($this->etapa_sequencial)
            && $this->data_inicial != ''
            && $this->data_final != ''
            && $this->ddp != ''
            && $this->atividades != ''
            && is_array($this->bnccs)
            && is_array($this->conteudos)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}data_inicial";
            $valores .= "{$gruda}'{$this->data_inicial}'";
            $gruda = ', ';

            $campos .= "{$gruda}data_final";
            $valores .= "{$gruda}'{$this->data_final}'";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_turma";
            $valores .= "{$gruda}'{$this->ref_cod_turma}'";
            $gruda = ', ';

            if(is_numeric($this->ref_componente_curricular_id)) {
                $campos .= "{$gruda}ref_componente_curricular";
                $valores .= "{$gruda}'{$this->ref_componente_curricular_id}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}etapa_sequencial";
            $valores .= "{$gruda}'{$this->etapa_sequencial}'";
            $gruda = ', ';

            $campos .= "{$gruda}ddp";
            $valores .= "{$gruda}'{$this->ddp}'";
            $gruda = ', ';

            $campos .= "{$gruda}atividades";
            $valores .= "{$gruda}'{$this->atividades}'";
            $gruda = ', ';

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}(NOW() - INTERVAL '3 HOURS')";
            $gruda = ', ';

            $db->Consulta("
                INSERT INTO
                    {$this->_tabela} ( $campos )
                    VALUES ( $valores )
            ");

            $id = $db->InsertId("{$this->_tabela}_id_seq");

            foreach ($this->bnccs as $key => $bncc_id) {
                $obj = new clsModulesPlanejamentoAulaBNCC(null, $id, $bncc_id);
                $obj->cadastra();
            }

            foreach ($this->conteudos as $key => $conteudo) {
                $obj = new clsModulesPlanejamentoAulaConteudo(null, $id, $conteudo);
                $obj->cadastra();
            }

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
        if (is_numeric($this->id)
            && $this->ddp != ''
            && $this->atividades != ''
            && is_array($this->bnccs)
            && is_array($this->conteudos)
        ) {
            $db = new clsBanco();

            $set = "
                ddp = '{$this->ddp}',
                atividades = '{$this->atividades}',
                data_atualizacao = (NOW() - INTERVAL '3 HOURS')
            ";

            $db->Consulta("
                UPDATE
                    {$this->_tabela}
                SET
                    $set
                WHERE
                    id = '{$this->id}'
            ");

            $obj = new clsModulesPlanejamentoAulaBNCC();
            $bnccs_atuais = $obj->lista($this->id)['ids'];

            $obj = new clsModulesBNCC(null, $this->id);
            $bncc_diferenca = $obj->retornaDiferencaEntreConjuntosBNCC($bnccs_atuais, $this->bnccs);

            foreach ($bncc_diferenca['adicionar'] as $key => $bncc_adicionar){
                $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_adicionar);
                $obj->cadastra();
            }

            foreach ($bncc_diferenca['remover'] as $key => $bncc_remover){
                $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_remover);
                $obj->excluir();
            }


            $obj = new clsModulesPlanejamentoAulaConteudo();
            $conteudos_atuais = $obj->lista($this->id);
            $conteudo_diferenca = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $this->conteudos);
            //dd($conteudo_diferenca);
            foreach ($conteudo_diferenca['adicionar'] as $key => $conteudo_adicionar){
                $obj = new clsModulesPlanejamentoAulaConteudo(null, $this->id, $conteudo_adicionar);
                $obj->cadastra();
            }

            foreach ($conteudo_diferenca['remover'] as $key => $conteudo_remover){
                $obj = new clsModulesPlanejamentoAulaConteudo(null, $this->id, $conteudo_remover);
                $obj->excluir();
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
        $int_etapa = null
    ) {
        $sql = "
                SELECT
                    {$this->_campos_lista}
                FROM
                    {$this->_from}
                ";

        $whereAnd = ' AND ';
        $filtros = " WHERE TRUE ";

        if(is_numeric($int_ano)){
            $filtros .= "{$whereAnd} t.ano = '{$int_ano}'";
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
            $filtros .= "{$whereAnd} pa.data_inicial >= '{$time_data_inicial}'";
            $whereAnd = ' AND ';
        }

        if ($time_data_final) {
            $filtros .= "{$whereAnd} pa.data_final <= '{$time_data_final}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_etapa)) {
            $filtros .= "{$whereAnd} pa.etapa_sequencial = '{$int_etapa}'";
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
                    {$this->_todos_campos}
                FROM
                    {$this->_from}
                WHERE
                    pa.id = {$this->id}
            ");

            $db->ProximoRegistro();

            $data['detalhes'] = $db->Tupla();

            $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id);
            $data['bnccs'] = $obj->detalhe();

            $obj = new clsModulesPlanejamentoAulaConteudo(null, $this->id);
            $data['conteudos'] = $obj->detalhe();

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
                    {$this->_tabela}
                WHERE
                    id = '{$this->id}'
            ");

            return true;
        }

        return false;
    }
}
