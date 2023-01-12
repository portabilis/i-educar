<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAula extends Model {
    public $id;
    public $ref_cod_turma;
    public $ref_componente_curricular_array;
    public $etapa_sequencial;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $bnccs;
    public $conteudos;
    public $referencias;
    public $bncc_especificacoes;
    public $servidor_id;

    public function __construct(
        $id = null,
        $ref_cod_turma = null,
        $ref_componente_curricular_array = null,
        $etapa_sequencial = null,
        $data_inicial = null,
        $data_final = null,
        $ddp = null,
        $atividades = null,
        $bnccs = null,
        $conteudos = null,
        $referencias = null,
        $bncc_especificacoes = null,
        $recursos_didaticos = null,
        $registro_adaptacao = null,
        $servidor_id = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula";

        $this->_from = "
                modules.planejamento_aula as pa
            JOIN modules.planejamento_aula_componente_curricular as pacc
                ON (pacc.planejamento_aula_id = pa.id)
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
                ON (k.id = pacc.componente_curricular_id)
            JOIN pmieducar.turma_turno u
                ON (u.id = t.turma_turno_id)
            LEFT JOIN pmieducar.turma_modulo q
                ON (q.ref_cod_turma = t.cod_turma AND q.sequencial = 1)
            LEFT JOIN pmieducar.modulo l
                ON (l.cod_modulo = q.ref_cod_modulo)
            JOIN modules.professor_turma as pt
                ON (pt.turma_id = pa.ref_cod_turma)
            LEFT JOIN cadastro.pessoa AS pe_registro
                ON ( pe_registro.idpes = pa.servidor_id )
            JOIN cadastro.pessoa AS pe
                ON ( pe.idpes = pt.servidor_id )
            JOIN modules.professor_turma_disciplina as ptd
                ON (pt.id = ptd.professor_turma_id AND ptd.componente_curricular_id = pacc.componente_curricular_id)
        ";

        $this->_campos_lista = $this->_todos_campos = '
            pa.id,
            pa.data_inicial,
            pa.data_final,
            pa.ref_cod_turma,
            pa.ddp,
            pa.atividades,
            pa.recursos_didaticos,
            pa.registro_adaptacao,
            pa.referencias,
            pa.fl_validado,
            pa.servidor_id AS cod_professor_registro,
            i.nm_instituicao AS instituicao,
            j.fantasia AS escola,
            c.nm_curso AS curso,
            s.nm_serie AS serie,
            t.nm_turma AS turma,
            u.nome AS turno,
            l.nm_tipo AS etapa,
            pa.etapa_sequencial AS fase_etapa,
            pt.servidor_id,
            pe.nome AS professor_turma,
	        pe_registro.nome AS professor_registro,
            s.cod_serie,
            e.cod_escola
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }

        if (is_array($ref_componente_curricular_array)) {
            $this->ref_componente_curricular_array = $ref_componente_curricular_array;
        }

        if (is_numeric($etapa_sequencial)) {
            $this->etapa_sequencial = $etapa_sequencial;
        }

        $this->data_inicial = $data_inicial;

        $this->data_final = $data_final;

        if (is_string($ddp)) {
            $this->ddp = $ddp;
        }

        if (is_string($atividades)) {
            $this->atividades = $atividades;
        }

        if (is_string($referencias)) {
            $this->referencias = $referencias;
        }

        if(is_array($bnccs)){
            $this->bnccs = $bnccs;
        }

        if(is_array($bncc_especificacoes)){
            $this->bncc_especificacoes = $bncc_especificacoes;
        }

        if(is_array($conteudos)){
            $this->conteudos = $conteudos;
        }

        if (is_string($recursos_didaticos)) {
            $this->recursos_didaticos = $recursos_didaticos;
        }

        if (is_string($registro_adaptacao)) {
            $this->registro_adaptacao = $registro_adaptacao;
        }

        if (is_numeric($servidor_id)) {
            $this->servidor_id = $servidor_id;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra() {
        if (is_numeric($this->ref_cod_turma)
            && is_array($this->ref_componente_curricular_array)
            && is_numeric($this->etapa_sequencial)
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_string($this->ddp)
            && is_string($this->atividades)
            && is_array($this->bnccs)
            && is_string($this->referencias)
            && is_array($this->bncc_especificacoes)
            && is_string($this->recursos_didaticos)
            && is_string($this->registro_adaptacao)
            && is_numeric($this->servidor_id)
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

            $campos .= "{$gruda}etapa_sequencial";
            $valores .= "{$gruda}'{$this->etapa_sequencial}'";
            $gruda = ', ';

            $campos .= "{$gruda}ddp";
            $valores .= "{$gruda}'{$db->escapeString($this->ddp)}'";
            $gruda = ', ';

            $campos .= "{$gruda}atividades";
            $valores .= "{$gruda}'{$db->escapeString($this->atividades)}'";
            $gruda = ', ';

            $campos .= "{$gruda}referencias";
            $valores .= "{$gruda}'{$db->escapeString($this->referencias)}'";
            $gruda = ', ';

            $campos .= "{$gruda}recursos_didaticos";
            $valores .= "{$gruda}'{$db->escapeString($this->recursos_didaticos)}'";
            $gruda = ', ';

            $campos .= "{$gruda}registro_adaptacao";
            $valores .= "{$gruda}'{$db->escapeString($this->registro_adaptacao)}'";
            $gruda = ', ';

            $campos .= "{$gruda}servidor_id";
            $valores .= "{$gruda}'{$this->servidor_id}'";
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

            foreach ($this->ref_componente_curricular_array as $key => $ref_componente_curricular) {
                $obj = new clsModulesPlanejamentoAulaComponenteCurricular(null, $id, $ref_componente_curricular[1]);
                $obj->cadastra();
            }

            foreach ($this->bnccs as $key => $bncc_array) {
                foreach ($bncc_array[1] as $key => $bncc_id) {
                    $obj = new clsModulesPlanejamentoAulaBNCC(null, $id, $bncc_id);
                    $obj->cadastra();
                }
            }

            if (is_array($this->conteudos)) {
                foreach ($this->conteudos as $key => $conteudo) {
                    $obj = new clsModulesPlanejamentoAulaConteudo(null, $id, $conteudo[1]);
                    $obj->cadastra();
                }
            }

            foreach ($this->bncc_especificacoes as $key => $bncc_especificacoes_array) {
                foreach ($bncc_especificacoes_array[1] as $key => $bncc_especificacao_id) {
                    $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_id);
                    $bncc_id = $obj->detalhe()['bncc_id'];

                    $obj = new clsModulesPlanejamentoAulaBNCC(null, $id, $bncc_id);
                    $planejamento_aula_bncc_id = $obj->detalhe2()['id'];

                    $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao(null, $planejamento_aula_bncc_id, $bncc_especificacao_id);
                    $obj->cadastra();
                }
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
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_string($this->ddp)
            && is_string($this->atividades)
            && is_array($this->bnccs)
            && is_array($this->bncc_especificacoes)
            && is_array($this->ref_componente_curricular_array)
            && is_string($this->referencias)
            && is_string($this->recursos_didaticos)
            && is_string($this->registro_adaptacao)
        ) {

            $db = new clsBanco();

            $set = "
                data_inicial = '{$this->data_inicial}',
                data_final = '{$this->data_final}',
                ddp = '{$db->escapeString($this->ddp)}',
                atividades = '{$db->escapeString($this->atividades)}',
                referencias = '{$db->escapeString($this->referencias)}',
                recursos_didaticos = '{$db->escapeString($this->recursos_didaticos)}',
                registro_adaptacao = '{$db->escapeString($this->registro_adaptacao)}',
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


            $obj = new clsModulesPlanejamentoAulaComponenteCurricular();
            foreach ($obj->lista($this->id) as $key => $componenteCurriculares) {
                $cc_atuais[] = $componenteCurriculares;
            }

            $obj = new clsModulesComponenteCurricular();
            $cc_diferenca = $obj->retornaDiferencaEntreConjuntosCC($cc_atuais, $this->ref_componente_curricular_array);

            foreach ($cc_diferenca['adicionar'] as $ccAdicionarArray){
                if (isset($ccAdicionarArray[1]) && !empty($ccAdicionarArray[1])) {
                    $obj = new clsModulesPlanejamentoAulaComponenteCurricular(null, $this->id, $ccAdicionarArray[1]);
                    $obj->cadastra();
                }
            }

            foreach ($cc_diferenca['remover'] as $cc_remover){
                $obj = new clsModulesPlanejamentoAulaComponenteCurricular(null, $this->id, $cc_remover);
                $obj->excluir();
            }

            $obj = new clsModulesPlanejamentoAulaBNCC();
            foreach ($obj->lista($this->id) as $key => $bncc) {
                $bnccs_atuais[] = $bncc;
            }

            $obj = new clsModulesBNCC(null, $this->id);
            $bncc_diferenca = $obj->retornaDiferencaEntreConjuntosBNCC($bnccs_atuais, $this->bnccs);

            foreach ($bncc_diferenca['adicionar'] as $bnccAdicionarArray){
                foreach ($bnccAdicionarArray[1] as $bncc_id) {
                    if (empty($bncc_id)) continue;
                    $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_id);
                    $obj->cadastra();
                }
            }

            foreach ($bncc_diferenca['remover'] as $bncc_remover){
                $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_remover);
                $obj->excluir();
            }

            $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao();
            foreach ($obj->lista($this->id) as $key => $bncc_especificacao) {
                $bnccs_especificacoes_atuais[] = $bncc_especificacao;
            }

            $obj = new clsModulesBNCCEspecificacao();
            $bncc_especificacao_diferenca = $obj->retornaDiferencaEntreConjuntosBNCC($bnccs_especificacoes_atuais, $this->bncc_especificacoes);

            foreach ($bncc_especificacao_diferenca['adicionar'] as $bnccEspecificacaoAdicionarArray){
                foreach ($bnccEspecificacaoAdicionarArray[1] as $bncc_especificacao_id) {
                    if (empty($bncc_especificacao_id)) continue;

                    $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_id);
                    $bncc_id = $obj->detalhe()['bncc_id'];

                    $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_id);
                    $planejamento_aula_bncc_id = $obj->detalhe2()['id'];

                    $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao(null, $planejamento_aula_bncc_id, $bncc_especificacao_id);
                    $obj->cadastra();
                }
            }

            foreach ($bncc_especificacao_diferenca['remover'] as $bncc_especificacao_remover){
                $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_remover);
                $bncc_id = $obj->detalhe()['bncc_id'];

                $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_id);
                $planejamento_aula_bncc_id = $obj->detalhe2()['id'];

                $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao(null, $planejamento_aula_bncc_id, $bncc_especificacao_remover);
                $obj->excluir();
            }

            if (is_array($this->conteudos)) {
                $obj = new clsModulesPlanejamentoAulaConteudo();
                $conteudos_atuais = $obj->lista($this->id);
                $conteudo_diferenca = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $this->conteudos);

                foreach ($conteudo_diferenca['adicionar'] as $key => $conteudo_adicionar){
                    $obj = new clsModulesPlanejamentoAulaConteudo(null, $this->id, $conteudo_adicionar[1]);
                    $obj->cadastra();
                }

                foreach ($conteudo_diferenca['remover'] as $key => $conteudo_remover){
                    $obj = new clsModulesPlanejamentoAulaConteudo(null, $this->id, $conteudo_remover[2]);
                    $obj->excluir();
                }

                foreach ($conteudo_diferenca['editar'] as $key => $conteudo_editar){
                    $obj = new clsModulesPlanejamentoAulaConteudo($conteudo_editar[0], null, $conteudo_editar[1]);
                    $obj->edita();
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
        $time_data = null,
        $arrayEscolasUsuario = null,
        $bool_validado = null
    ) {
        $sql = "
            SELECT DISTINCT
                {$this->_campos_lista}
            FROM
                {$this->_from}
        ";

        $whereAnd = ' AND ';
        $filtros = " WHERE TRUE ";

        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} (EXTRACT(YEAR FROM pa.data_inicial) = '{$int_ano}' OR EXTRACT(YEAR FROM pa.data_final) = '{$int_ano}')";
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

        if ($time_data) {
            $filtros .= "{$whereAnd} pa.data_inicial <= '{$time_data}' AND pa.data_final >= '{$time_data}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_etapa)) {
            $filtros .= "{$whereAnd} pa.etapa_sequencial = '{$int_etapa}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_servidor_id)) {
            $filtros .= "{$whereAnd} pt.servidor_id = '{$int_servidor_id}'";
            $whereAnd = ' AND ';
        }

        if (is_array($arrayEscolasUsuario) && count($arrayEscolasUsuario) >= 1) {
            $filtros .= "{$whereAnd} e.cod_escola IN (" . implode(',', $arrayEscolasUsuario) . ")";
            $whereAnd = ' AND ';
        }

        if (is_bool($bool_validado) && $bool_validado) {
            $filtros .= "{$whereAnd} pa.fl_validado = 'true' ";
        }

        if (is_bool($bool_validado) && !$bool_validado) {
            $filtros .= "{$whereAnd} pa.fl_validado = 'false' ";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico(
            "SELECT
                COUNT(DISTINCT pa.ID)
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

            $obj = new clsModulesPlanejamentoAulaComponenteCurricular();
            $data['componentesCurriculares'] = $obj->lista($this->id);

            $obj = new clsModulesPlanejamentoAulaBNCC();
            $data['bnccs'] = $obj->lista($this->id);

            $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao();
            $data['especificacoes'] = $obj->lista($this->id);

            $obj = new clsModulesPlanejamentoAulaConteudo();
            $data['conteudos'] = $obj->lista($this->id);

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
        // if ($this->data_inicial && $this->data_final && is_numeric($this->ref_cod_turma) && is_numeric($this->ref_componente_curricular_array) && is_numeric($this->etapa_sequencial)) {
        //     $sql = "
        //         SELECT
        //             *
        //         FROM
        //             modules.planejamento_aula as pa
        //         WHERE
        //             pa.data_inicial >= '{$this->data_inicial}'
        //             AND pa.data_final <= '{$this->data_final}'
        //             AND pa.ref_cod_turma = '{$this->ref_cod_turma}'
        //             AND pa.ref_componente_curricular = '{$this->ref_componente_curricular_array}'
        //             AND pa.etapa_sequencial = '{$this->etapa_sequencial}'
        //     ";

        //     $db = new clsBanco();
        //     $db->Consulta($sql);
        //     $db->ProximoRegistro();

        //     return $db->Tupla();
        // }

        return false;
    }

    public function existeComponentePeriodo () {
         if ($this->data_inicial && $this->data_final && $this->ref_cod_turma && is_array($this->ref_componente_curricular_array)) {
             $refsComponentes = [];

             foreach ($this->ref_componente_curricular_array as $refComponente) {
                 unset($refComponente[0]);
                 foreach ($refComponente as $ref) {
                     array_push($refsComponentes, $ref);
                 }
             }

             $sql = "
                 SELECT
                     pa.*,
	                 pacc.componente_curricular_id
                 FROM
                     modules.planejamento_aula as pa
                 JOIN modules.planejamento_aula_componente_curricular as pacc
                    ON (pacc.planejamento_aula_id = pa.id)
                 WHERE
                     ('{$this->data_inicial}' BETWEEN DATE(pa.data_inicial) AND DATE(pa.data_final)
                     OR
                     '{$this->data_final}' BETWEEN DATE(pa.data_inicial) AND DATE(pa.data_final))
                     AND pacc.componente_curricular_id IN (".implode(',', $refsComponentes).")
                     AND pa.ref_cod_turma = '{$this->ref_cod_turma}'
             ";

             $db = new clsBanco();
             $db->Consulta($sql);
             $db->ProximoRegistro();

             return $db->Tupla();
         }

        return false;
    }

    public function existeComponenteByData ($data) {
        if (!empty($this->ref_cod_turma) && !empty($this->etapa_sequencial)) {
            $sql = "
             SELECT
                 pa.*,
                 pacc.componente_curricular_id
             FROM
                 modules.planejamento_aula as pa
             JOIN modules.planejamento_aula_componente_curricular as pacc
                ON (pacc.planejamento_aula_id = pa.id)
             WHERE
                 '{$data}' BETWEEN DATE(pa.data_inicial) AND DATE(pa.data_final)
                 AND pa.ref_cod_turma = '{$this->ref_cod_turma}' AND pa.etapa_sequencial = '{$this->etapa_sequencial}'";

            if (is_array($this->ref_componente_curricular_array)) {
                $sql .= " AND pacc.componente_curricular_id IN (" . implode(',', $this->ref_componente_curricular_array) . ")";
            }

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna array com registro(s) de aula com ligação com o plano de aula informado ou no caso de erro, false
     *
     * @return false|array
     */
    public function existeLigacaoRegistroAula () {
        if (is_numeric($this->id)) {
            $data = [];

            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    conteudo_ministrado_id as id, COUNT(conteudo_ministrado_id)
                FROM
                    modules.planejamento_aula_conteudo as pac
                CROSS JOIN LATERAL (
                    SELECT
                        cmc.conteudo_ministrado_id
                    FROM
                        modules.conteudo_ministrado_conteudo as cmc
                    WHERE
                        cmc.planejamento_aula_conteudo_id = pac.id
                ) sub
                WHERE
                    pac.planejamento_aula_id = '{$this->id}'
                GROUP BY
                    conteudo_ministrado_id
            ");

            while($db->ProximoRegistro()) {
                $data[] = [$db->Campo('id'), $db->Campo('count')];
            }

            return $data;
        }

        return false;
    }

    /**
     * Retorna array com registro(s) de aula com ligação com o plano de aula informado ou no caso de erro, false
     *
     * @return false|array
     */
    public function existeLigacaoRegistroAulaByFrequencia () {
        if (is_numeric($this->id)) {
            $data = [];

            $db = new clsBanco();
            $db->Consulta("
                SELECT DISTINCT
                    cmc.conteudo_ministrado_id as id,
                    cm.frequencia_id
                FROM
                    modules.planejamento_aula_conteudo AS pac
                    INNER JOIN modules.conteudo_ministrado_conteudo cmc ON pac.ID = cmc.planejamento_aula_conteudo_id
                    INNER JOIN modules.conteudo_ministrado cm ON cmc.conteudo_ministrado_id = cm.ID
                WHERE
                    pac.planejamento_aula_id = '{$this->id}'
            ");

            while($db->ProximoRegistro()) {
                $data[] = [
                    'id' => $db->Campo('id'),
                    'frequencia_id' => $db->Campo('frequencia_id')
                ];
            }

            return $data;
        }

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

    public function getHabilidadesEspecificacoesUtilizados ($turmaId, $ano, $userId)
    {
        $planejamentos = $this->lista($ano,
            null,
            null,
            null,
            null,
            $turmaId,
            null,
            null,
            null,
            null,
            null,
            $userId);


        $bnccsUtilizados = [];
        $escpecificacoesUtilizados = [];

        foreach ($planejamentos as $planejamento) {
            $obj = new clsModulesPlanejamentoAulaBNCC();
            $bnccs = $obj->lista($planejamento['id']);

            foreach ($bnccs as $bncc) {
                if (in_array($bncc['id'], $bnccsUtilizados)) {
                    continue;
                }

                $bnccsUtilizados[] = (int) $bncc['id'];
            }

            $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao();
            $especificacoes = $obj->lista($planejamento['id']);

            foreach ($especificacoes as $especificacao) {

                if (in_array($especificacao['bncc_especificacao_id'], $escpecificacoesUtilizados)) {
                    continue;
                }

                $escpecificacoesUtilizados[] = $especificacao['bncc_especificacao_id'];
            }
        }

        return [
            'bncss_utilizados' => $bnccsUtilizados,
            'especificacoes_utilizados' => $escpecificacoesUtilizados
        ];

    }

    public function updateValidacao($bool_validacao, $ref_validacao_user_id = null, $data_validacao  = null) {
        if (is_numeric($this->id)) {
            $db = new clsBanco();


           $set = "fl_validado = " . ($bool_validacao ? "true" : "false ");

            if ($bool_validacao) {
                $set .= ", ref_validacao_user_id = '{$ref_validacao_user_id}'";
                $set .= ", data_validacao = '{$data_validacao}'";

            }

            $db->Consulta("
                UPDATE
                    {$this->_tabela}
                SET
                    $set
                WHERE
                    id = '{$this->id}'
            ");


            return true;
        }

        return false;
    }

    public function getMensagem ($receptor_user_id) {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("
                SELECT
                    emissor_user_id,
                    receptor_user_id
                FROM
                    public.mensagens
                WHERE
                    registro_id = '{$this->id}'
                LIMIT 1
            ");

            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

}
