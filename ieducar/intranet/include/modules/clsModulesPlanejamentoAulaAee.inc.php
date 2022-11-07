<?php

use iEducar\Legacy\Model;

class clsModulesPlanejamentoAulaAee extends Model
{
    public $id;
    public $data_inicial;
    public $data_final;
    public $ref_cod_turma;
    public $ref_cod_matricula;
    public $etapa_sequencial;
    public $ddp;
    public $conteudos;
    public $ref_componente_curricular_array;
    public $bnccs;
    public $bncc_especificacoes;
    public $recursos_didaticos;
    public $outros;
    public $servidor_id;

    public function __construct(
        $id = null,
        $data_inicial = null,
        $data_final = null,
        $ref_cod_turma = null,
        $ref_cod_matricula = null,
        $etapa_sequencial = null,
        $ddp = null,
        $conteudos = null,
        $ref_componente_curricular_array = null,
        $bnccs = null,
        $bncc_especificacoes = null,
        $recursos_didaticos = null,
        $outros = null,
        $servidor_id = null
    ) {
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}planejamento_aula_aee";

        $this->_from = '
                modules.planejamento_aula_aee as pa
            JOIN modules.planejamento_aula_componente_curricular_aee as pacc
                ON (pacc.planejamento_aula_aee_id = pa.id)
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
            JOIN pmieducar.turma_turno u
                ON (u.id = t.turma_turno_id)
            LEFT JOIN pmieducar.turma_modulo q
                ON (q.ref_cod_turma = t.cod_turma AND q.sequencial = 1)
            JOIN modules.professor_turma as pt
                ON (pt.turma_id = pa.ref_cod_turma)
            JOIN cadastro.pessoa AS pe
                ON ( pe.idpes = pt.servidor_id )
            JOIN pmieducar.matricula m
                ON (m.cod_matricula = pa.ref_cod_matricula)  
            JOIN pmieducar.aluno a
                ON (a.cod_aluno = m.ref_cod_aluno)             
            JOIN cadastro.pessoa p
                ON (p.idpes = a.ref_idpes)         
            LEFT JOIN modules.componente_curricular k
                ON (k.id = pacc.componente_curricular_id)  
                                         
        ';

        $this->_campos_lista = $this->_todos_campos = '
            pa.id,
            pa.data_inicial,
            pa.data_final,
            pa.ref_cod_matricula,
            pa.ref_cod_turma,
            pa.ddp,
            pa.recursos_didaticos,
            pa.outros,
            pa.etapa_sequencial AS fase_etapa,
            i.nm_instituicao AS instituicao,
            p.nome as aluno,
            j.fantasia AS escola,
            c.nm_curso AS curso,
            t.nm_turma AS turma,
            pe.nome as professor,
            s.nm_serie AS serie
        ';

        if (is_numeric($id)) {
            $this->id = $id;
        }

        $this->data_inicial = $data_inicial;

        $this->data_final = $data_final;

        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }

        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }

        if (is_numeric($etapa_sequencial)) {
            $this->etapa_sequencial = $etapa_sequencial;
        }

        if (is_string($ddp)) {
            $this->ddp = $ddp;
        }

        if (is_array($conteudos)) {
            $this->conteudos = $conteudos;
        }

        if (is_array($ref_componente_curricular_array)) {
            $this->ref_componente_curricular_array = $ref_componente_curricular_array;
        }

        if (is_array($bnccs)) {
            $this->bnccs = $bnccs;
        }

        if (is_array($bncc_especificacoes)) {
            $this->bncc_especificacoes = $bncc_especificacoes;
        }

        if (is_string($recursos_didaticos)) {
            $this->recursos_didaticos = $recursos_didaticos;
        }

        if (is_string($outros)) {
            $this->outros = $outros;
        }

        if (is_numeric($servidor_id)) {
            $this->servidor_id = $servidor_id;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (
            is_numeric($this->ref_cod_turma)
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_numeric($this->etapa_sequencial)
            && is_numeric($this->ref_cod_matricula)
            && is_array($this->ref_componente_curricular_array)
            && is_string($this->ddp)
            && is_array($this->bnccs)
            && is_array($this->bncc_especificacoes)
            && is_string($this->recursos_didaticos)
            && is_string($this->outros)
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

            $campos .= "{$gruda}ref_cod_matricula";
            $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
            $gruda = ', ';

            $campos .= "{$gruda}etapa_sequencial";
            $valores .= "{$gruda}'{$this->etapa_sequencial}'";
            $gruda = ', ';

            $campos .= "{$gruda}ddp";
            $valores .= "{$gruda}'{$db->escapeString($this->ddp)}'";
            $gruda = ', ';

            $campos .= "{$gruda}recursos_didaticos";
            $valores .= "{$gruda}'{$db->escapeString($this->recursos_didaticos)}'";
            $gruda = ', ';

            $campos .= "{$gruda}outros";
            $valores .= "{$gruda}'{$db->escapeString($this->outros)}'";
            $gruda = ', ';

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}(NOW() - INTERVAL '3 HOURS')";
            $gruda = ', ';

            $campos .= "{$gruda}created_at";
            $valores .= "{$gruda}(NOW() - INTERVAL '3 HOURS')";
            $gruda = ', ';

            $campos .= "{$gruda}servidor_id";
            $valores .= "{$gruda}'{$this->servidor_id}'";
            $gruda = ', ';

            $db->Consulta("
                INSERT INTO
                    {$this->_tabela} ( $campos )
                    VALUES ( $valores )
            ");

            $id = $db->InsertId("{$this->_tabela}_id_seq");

            foreach ($this->ref_componente_curricular_array as $key => $ref_componente_curricular) {
                $obj = new clsModulesPlanejamentoAulaComponenteCurricularAee(null, $id, $ref_componente_curricular[1]);
                $obj->cadastra();
            }

            foreach ($this->bnccs as $key => $bncc_array) {
                foreach ($bncc_array[1] as $key => $bncc_id) {
                    $obj = new clsModulesPlanejamentoAulaBNCCAee(null, $id, $bncc_id);
                    $obj->cadastra();
                }
            }

            foreach ($this->conteudos as $key => $conteudo) {
                $obj = new clsModulesPlanejamentoAulaConteudoAee(null, $id, $conteudo[1]);
                $obj->cadastra();
            }

            foreach ($this->bncc_especificacoes as $key => $bncc_especificacoes_array) {
                foreach ($bncc_especificacoes_array[1] as $key => $bncc_especificacao_id) {
                    $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_id);
                    $bncc_id = $obj->detalhe()['bncc_id'];

                    $obj = new clsModulesPlanejamentoAulaBNCCAee(null, $id, $bncc_id);
                    $planejamento_aula_bncc_aee_id = $obj->detalhe2()['id'];

                    $obj = new clsModulesPlanejamentoAulaBNCCEspecificacaoAee(null, $planejamento_aula_bncc_aee_id, $bncc_especificacao_id);
                    $obj->cadastra();
                }
            }

            return $id;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (
            is_numeric($this->id)
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_string($this->ddp)
            && is_array($this->bnccs)
            && is_array($this->bncc_especificacoes)
            && is_array($this->ref_componente_curricular_array)
            && is_string($this->recursos_didaticos)
            && is_string($this->outros)
        ) {
            $db = new clsBanco();

            $set = "  
                data_inicial = '{$this->data_inicial}',
                data_final = '{$this->data_final}',
                ddp = '{$db->escapeString($this->ddp)}',
                recursos_didaticos = '{$db->escapeString($this->recursos_didaticos)}',
                outros = '{$db->escapeString($this->outros)}',
                data_atualizacao = (NOW() - INTERVAL '3 HOURS'),
                updated_at = (NOW() - INTERVAL '3 HOURS')
            ";

            $db->Consulta("
                UPDATE
                    {$this->_tabela}
                SET
                    $set
                WHERE
                    id = '{$this->id}'
            ");

            $obj = new clsModulesPlanejamentoAulaComponenteCurricularAee();
            foreach ($obj->lista($this->id) as $key => $componenteCurriculares) {
                $cc_atuais[] = $componenteCurriculares;
            }

            $obj = new clsModulesComponenteCurricular();
            $cc_diferenca = $obj->retornaDiferencaEntreConjuntosCC($cc_atuais, $this->ref_componente_curricular_array);

            foreach ($cc_diferenca['adicionar'] as $ccAdicionarArray) {
                if (isset($ccAdicionarArray[1]) && !empty($ccAdicionarArray[1])) {
                    $obj = new clsModulesPlanejamentoAulaComponenteCurricularAee(null, $this->id, $ccAdicionarArray[1]);
                    $obj->cadastra();
                }
            }

            foreach ($cc_diferenca['remover'] as $cc_remover) {
                $obj = new clsModulesPlanejamentoAulaComponenteCurricularAee(null, $this->id, $cc_remover);
                $obj->excluir();
            }

            $obj = new clsModulesPlanejamentoAulaBNCCAee();
            foreach ($obj->lista($this->id) as $key => $bncc) {
                $bnccs_atuais[] = $bncc;
            }

            $obj = new clsModulesBNCC(null, $this->id);
            $bncc_diferenca = $obj->retornaDiferencaEntreConjuntosBNCC($bnccs_atuais, $this->bnccs);

            foreach ($bncc_diferenca['adicionar'] as $bnccAdicionarArray) {
                foreach ($bnccAdicionarArray[1] as $bncc_id) {
                    if (empty($bncc_id)) {
                        continue;
                    }
                    $obj = new clsModulesPlanejamentoAulaBNCCAee(null, $this->id, $bncc_id);
                    $obj->cadastra();
                }
            }

            foreach ($bncc_diferenca['remover'] as $bncc_remover) {
                $obj = new clsModulesPlanejamentoAulaBNCCAee(null, $this->id, $bncc_remover);
                $obj->excluir();
            }

            $obj = new clsModulesPlanejamentoAulaBNCCEspecificacaoAee();
            foreach ($obj->lista($this->id) as $key => $bncc_especificacao) {
                $bnccs_especificacoes_atuais[] = $bncc_especificacao;
            }

            $obj = new clsModulesBNCCEspecificacao();
            $bncc_especificacao_diferenca = $obj->retornaDiferencaEntreConjuntosBNCC($bnccs_especificacoes_atuais, $this->bncc_especificacoes);

            foreach ($bncc_especificacao_diferenca['adicionar'] as $bnccEspecificacaoAdicionarArray) {
                foreach ($bnccEspecificacaoAdicionarArray[1] as $bncc_especificacao_id) {
                    if (empty($bncc_especificacao_id)) {
                        continue;
                    }

                    $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_id);
                    $bncc_id = $obj->detalhe()['bncc_id'];

                    $obj = new clsModulesPlanejamentoAulaBNCCAee(null, $this->id, $bncc_id);
                    $planejamento_aula_bncc_id = $obj->detalhe2()['id'];

                    $obj = new clsModulesPlanejamentoAulaBNCCEspecificacaoAee(null, $planejamento_aula_bncc_id, $bncc_especificacao_id);
                    $obj->cadastra();
                }
            }

            foreach ($bncc_especificacao_diferenca['remover'] as $bncc_especificacao_remover) {
                $obj = new clsModulesBNCCEspecificacao($bncc_especificacao_remover);
                $bncc_id = $obj->detalhe()['bncc_id'];

                $obj = new clsModulesPlanejamentoAulaBNCC(null, $this->id, $bncc_id);
                $planejamento_aula_bncc_id = $obj->detalhe2()['id'];

                $obj = new clsModulesPlanejamentoAulaBNCCEspecificacaoAee(null, $planejamento_aula_bncc_id, $bncc_especificacao_remover);
                $obj->excluir();
            }

            $obj = new clsModulesPlanejamentoAulaConteudoAee();
            $conteudos_atuais = $obj->lista($this->id);
            $conteudo_diferenca = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $this->conteudos);

            foreach ($conteudo_diferenca['adicionar'] as $key => $conteudo_adicionar) {
                $obj = new clsModulesPlanejamentoAulaConteudoAee(null, $this->id, $conteudo_adicionar[1]);
                $obj->cadastra();
            }

            foreach ($conteudo_diferenca['remover'] as $key => $conteudo_remover) {
                $obj = new clsModulesPlanejamentoAulaConteudoAee(null, $this->id, $conteudo_remover[2]);
                $obj->excluir();
            }

            foreach ($conteudo_diferenca['editar'] as $key => $conteudo_editar) {
                $obj = new clsModulesPlanejamentoAulaConteudoAee($conteudo_editar[0], null, $conteudo_editar[1]);
                $obj->edita();
            }

            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros.
     *
     * @return array
     */
    public function lista(
        $int_ano = null,
        $int_ref_cod_ins = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_turma = null,
        $int_ref_cod_matricula = null,
        $int_ref_cod_componente_curricular = null,
        $int_ref_cod_turno = null,
        $time_data_inicial = null,
        $time_data_final = null,
        $int_etapa = null,
        $int_servidor_id = null,
        $time_data = null
    ) {
        $sql = "
            SELECT DISTINCT
                {$this->_campos_lista}
            FROM
                {$this->_from}
        ";

        $whereAnd = ' AND ';
        $filtros = ' WHERE TRUE ';

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

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} pa.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if ($time_data_inicial) {
            $time_data_inicial = Portabilis_Date_Utils::brToPgSQL($time_data_inicial);
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

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros.$this->getOrderby().$this->getLimite();

        //dump($sql);

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
     * Retorna uma lista filtrados de acordo com os parametros.
     *
     * @return array
     */
    public function lista_conteudos(
        $int_ano = null,
        $int_ref_cod_turma = null,
        $int_ref_cod_matricula = null,
        $int_ref_cod_componente_curricular = null,
        $int_etapa = null
    ) {
        $sql = '
            SELECT DISTINCT
            pa.id,
            pa.data_inicial,
            pa.data_final,
            pa.ref_cod_matricula,
            pa.ref_cod_turma,
            pa.etapa_sequencial AS fase_etapa,
            t.nm_turma AS turma
            FROM
            modules.planejamento_aula_aee as pa
            JOIN modules.planejamento_aula_componente_curricular_aee as pacc
                ON (pacc.planejamento_aula_aee_id = pa.id)
            JOIN pmieducar.turma t
                ON (t.cod_turma = pa.ref_cod_turma)
            JOIN pmieducar.matricula m
                ON (m.cod_matricula = pa.ref_cod_matricula)  
            JOIN pmieducar.aluno a
                ON (a.cod_aluno = m.ref_cod_aluno)             
            JOIN cadastro.pessoa p
                ON (p.idpes = a.ref_idpes)         
            LEFT JOIN modules.componente_curricular k
                ON (k.id = pacc.componente_curricular_id)
        ';

        $whereAnd = ' AND ';
        $filtros = ' WHERE TRUE ';

        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} (EXTRACT(YEAR FROM pa.data_inicial) = '{$int_ano}' OR EXTRACT(YEAR FROM pa.data_final) = '{$int_ano}')";
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

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} pa.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_etapa)) {
            $filtros .= "{$whereAnd} pa.etapa_sequencial = '{$int_etapa}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros.$this->getOrderby().$this->getLimite();

        //dump($sql);

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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
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

            $obj = new clsModulesPlanejamentoAulaComponenteCurricularAee();
            $data['componentesCurriculares'] = $obj->lista($this->id);

            $obj = new clsModulesPlanejamentoAulaBNCCAee();
            $data['bnccs'] = $obj->lista($this->id);

            $obj = new clsModulesPlanejamentoAulaBNCCEspecificacaoAee();
            $data['especificacoes'] = $obj->lista($this->id);

            $obj = new clsModulesPlanejamentoAulaConteudoAee();
            $data['conteudos'] = $obj->lista($this->id);

            return $data;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
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

    public function existeComponentePeriodo()
    {
        if ($this->data_inicial && $this->data_final && is_array($this->ref_componente_curricular_array)) {
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
                     modules.planejamento_aula_aee as pa
                 JOIN modules.planejamento_aula_componente_curricular as pacc
                    ON (pacc.planejamento_aula_id = pa.id)
                 WHERE
                     pa.data_inicial >= '{$this->data_inicial}'
                     AND pa.data_final <= '{$this->data_final}'
                     AND pacc.componente_curricular_id IN (".implode(',', $refsComponentes).')
             ';

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna array com registro(s) de aula com ligação com o plano de aula informado ou no caso de erro, false.
     *
     * @return false|array
     */
    public function existeLigacaoRegistroAula()
    {
        if (is_numeric($this->id)) {
            $data = [];

            $db = new clsBanco();
            $db->Consulta("
                SELECT
                conteudo_ministrado_aee_id as id, COUNT(conteudo_ministrado_aee_id)
                FROM
                    modules.planejamento_aula_conteudo_aee as pac
                CROSS JOIN LATERAL (
                    SELECT
                        cmc.conteudo_ministrado_aee_id
                    FROM
                        modules.conteudo_ministrado_conteudo_aee as cmc
                    WHERE
                        cmc.planejamento_aula_conteudo_aee_id = pac.id
                ) sub
                WHERE
                    pac.planejamento_aula_aee_id = '{$this->id}'
                GROUP BY
                    conteudo_ministrado_aee_id
            ");

            while ($db->ProximoRegistro()) {
                $data[] = [$db->Campo('id'), $db->Campo('count')];
            }

            return $data;
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
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
