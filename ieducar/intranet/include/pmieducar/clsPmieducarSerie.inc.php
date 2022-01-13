<?php

use iEducar\Legacy\Model;

class clsPmieducarSerie extends Model
{
    public $cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_curso;
    public $nm_serie;
    public $etapa_curso;
    public $concluinte;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $regra_avaliacao_id;
    public $regra_avaliacao_diferenciada_id;
    public $idade_inicial;
    public $idade_final;
    public $idade_ideal;
    public $alerta_faixa_etaria;
    public $bloquear_matricula_faixa_etaria;
    public $exigir_inep;
    public $importar_serie_pre_matricula;
    public $descricao;

    public function __construct(
        $cod_serie = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_curso = null,
        $nm_serie = null,
        $etapa_curso = null,
        $concluinte = null,
        $carga_horaria = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $idade_inicial = null,
        $idade_final = null,
        $regra_avaliacao_id = null,
        $observacao_historico = null,
        $dias_letivos = null,
        $regra_avaliacao_diferenciada_id = null,
        $alerta_faixa_etaria = false,
        $bloquear_matricula_faixa_etaria = false,
        $idade_ideal = null,
        $exigir_inep = false,
        $importar_serie_pre_matricula = false,
        $descricao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}serie";
        $this->_campos_lista = $this->_todos_campos = 's.cod_serie, s.ref_usuario_exc, s.ref_usuario_cad, s.ref_cod_curso, s.nm_serie, s.etapa_curso, s.concluinte, s.carga_horaria, s.data_cadastro, s.data_exclusao, s.ativo, s.idade_inicial, s.idade_final, s.regra_avaliacao_id, s.observacao_historico, s.dias_letivos, s.regra_avaliacao_diferenciada_id, s.alerta_faixa_etaria, s.bloquear_matricula_faixa_etaria, s.idade_ideal, s.exigir_inep, s.importar_serie_pre_matricula, s.descricao';

        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        // Atribuibui a identificação de regra de avaliação
        if (!is_null($regra_avaliacao_id) && is_numeric($regra_avaliacao_id)) {
            $mapper = new RegraAvaliacao_Model_RegraDataMapper();

            if (isset($curso)) {
                $regras = $mapper->findAll(
                    [],
                    ['id' => $regra_avaliacao_id, 'instituicao' => $curso['ref_cod_instituicao']]
                );

                if (1 == count($regras)) {
                    $regra = $regras[0];
                }
            } else {
                $regra = $mapper->find($regra_avaliacao_id);
            }

            // Verificação fraca pois deixa ser uma regra de outra instituição
            if (isset($regra)) {
                $this->regra_avaliacao_id = $regra->id;
            }
        }

        if (!is_null($regra_avaliacao_diferenciada_id) && is_numeric($regra_avaliacao_diferenciada_id)) {
            $mapper = new RegraAvaliacao_Model_RegraDataMapper();

            if (isset($curso)) {
                $regras = $mapper->findAll(
                    [],
                    ['id' => $regra_avaliacao_diferenciada_id, 'instituicao' => $curso['ref_cod_instituicao']]
                );

                if (1 == count($regras)) {
                    $regra = $regras[0];
                }
            } else {
                $regra = $mapper->find($regra_avaliacao_diferenciada_id);
            }

            // Verificação fraca pois deixa ser uma regra de outra instituição
            if (isset($regra)) {
                $this->regra_avaliacao_diferenciada_id = $regra->id;
            }
        }

        if (is_numeric($cod_serie)) {
            $this->cod_serie = $cod_serie;
        }

        if (is_string($nm_serie)) {
            $this->nm_serie = $nm_serie;
        }

        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }

        if (is_numeric($etapa_curso)) {
            $this->etapa_curso = $etapa_curso;
        }

        if (is_numeric($concluinte)) {
            $this->concluinte = $concluinte;
        }

        if (is_numeric($carga_horaria)) {
            $this->carga_horaria = $carga_horaria;
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }

        if (is_numeric($idade_inicial)) {
            $this->idade_inicial = $idade_inicial;
        }

        if (is_numeric($idade_final)) {
            $this->idade_final = $idade_final;
        }

        if (dbBool($alerta_faixa_etaria)) {
            $this->alerta_faixa_etaria = $alerta_faixa_etaria;
        }

        if (dbBool($bloquear_matricula_faixa_etaria)) {
            $this->bloquear_matricula_faixa_etaria = $bloquear_matricula_faixa_etaria;
        }

        if (is_numeric($idade_ideal)) {
            $this->idade_ideal = $idade_ideal;
        }

        if (dbBool($exigir_inep)) {
            $this->exigir_inep = $exigir_inep;
        }

        if (dbBool($importar_serie_pre_matricula)) {
            $this->importar_serie_pre_matricula = $importar_serie_pre_matricula;
        }

        $this->observacao_historico = $observacao_historico;
        $this->dias_letivos = $dias_letivos;
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (
            is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_curso) &&
            is_string($this->nm_serie) && is_numeric($this->etapa_curso) &&
            is_numeric($this->concluinte) && is_numeric($this->carga_horaria) &&
            is_numeric($this->dias_letivos)
        ) {
            $db = new clsBanco();

            $campos = [];
            $valores = [];

            if (is_numeric($this->ref_usuario_cad)) {
                $campos[] = 'ref_usuario_cad';
                $valores[] = "'{$this->ref_usuario_cad}'";
            }

            if (is_numeric($this->ref_cod_curso)) {
                $campos[] = 'ref_cod_curso';
                $valores[] = "'{$this->ref_cod_curso}'";
            }

            if (is_string($this->nm_serie)) {
                $nm_serie = $db->escapeString($this->nm_serie);
                $campos[] = 'nm_serie';
                $valores[] = "'{$nm_serie}'";
            }

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos[] = 'descricao';
                $valores[] = "'{$descricao}'";
            }

            if (is_numeric($this->etapa_curso)) {
                $campos[] = 'etapa_curso';
                $valores[] = "'{$this->etapa_curso}'";
            }

            if (is_numeric($this->concluinte)) {
                $campos[] = 'concluinte';
                $valores[] = "'{$this->concluinte}'";
            }

            if (is_numeric($this->carga_horaria)) {
                $campos[] = 'carga_horaria';
                $valores[] = "'{$this->carga_horaria}'";
            }

            if (is_numeric($this->idade_inicial)) {
                $campos[] = 'idade_inicial';
                $valores[] = "'{$this->idade_inicial}'";
            }

            if (is_numeric($this->idade_final)) {
                $campos[] = 'idade_final';
                $valores[] = "'{$this->idade_final}'";
            }

            if (is_numeric($this->regra_avaliacao_id)) {
                $campos[] = 'regra_avaliacao_id';
                $valores[] = "'{$this->regra_avaliacao_id}'";
            }

            if (is_numeric($this->regra_avaliacao_diferenciada_id)) {
                $campos[] = 'regra_avaliacao_diferenciada_id';
                $valores[] = "'{$this->regra_avaliacao_diferenciada_id}'";
            }

            $campos[] = 'data_cadastro';
            $valores[] = 'NOW()';

            $campos[] = 'ativo';
            $valores[] = '\'1\'';

            if (is_string($this->observacao_historico)) {
                $observacao_historico = $db->escapeString($this->observacao_historico);
                $campos[] = 'observacao_historico';
                $valores[] = "'{$observacao_historico}'";
            }

            if (is_numeric($this->dias_letivos)) {
                $campos[] = 'dias_letivos';
                $valores[] = "'{$this->dias_letivos}'";
            }

            if (is_numeric($this->idade_ideal)) {
                $campos[] = 'idade_ideal';
                $valores[] = "'{$this->idade_ideal}'";
            }

            if (dbBool($this->alerta_faixa_etaria)) {
                $campos[] = 'alerta_faixa_etaria';
                $valores[] = ' true ';
            } else {
                $campos[] = 'alerta_faixa_etaria';
                $valores[] = ' false ';
            }

            if (dbBool($this->bloquear_matricula_faixa_etaria)) {
                $campos[] = 'bloquear_matricula_faixa_etaria';
                $valores[] = ' true ';
            } else {
                $campos[] = 'bloquear_matricula_faixa_etaria';
                $valores[] = ' false ';
            }

            if (dbBool($this->exigir_inep)) {
                $campos[] = 'exigir_inep';
                $valores[] = ' true ';
            } else {
                $campos[] = 'exigir_inep';
                $valores[] = ' false ';
            }

            if (dbBool($this->importar_serie_pre_matricula)) {
                $campos[] = 'importar_serie_pre_matricula';
                $valores[] = ' true ';
            } else {
                $campos[] = 'importar_serie_pre_matricula';
                $valores[] = ' false ';
            }

            $campos = join(', ', $campos);
            $valores = join(', ', $valores);

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_serie_seq");
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
        if (is_numeric($this->cod_serie) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = [];

            if (is_numeric($this->ref_usuario_exc)) {
                $set[] = "ref_usuario_exc = '{$this->ref_usuario_exc}'";
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $set[] = "ref_usuario_cad = '{$this->ref_usuario_cad}'";
            }

            if (is_numeric($this->ref_cod_curso)) {
                $set[] = "ref_cod_curso = '{$this->ref_cod_curso}'";
            }

            if (is_string($this->nm_serie)) {
                $nm_serie = $db->escapeString($this->nm_serie);
                $set[] = "nm_serie = '{$nm_serie}'";
            }

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set[] = "descricao = '{$descricao}'";
            }

            if (is_numeric($this->etapa_curso)) {
                $set[] = "etapa_curso = '{$this->etapa_curso}'";
            }

            if (is_numeric($this->concluinte)) {
                $set[] = "concluinte = '{$this->concluinte}'";
            }

            if (is_numeric($this->carga_horaria)) {
                $set[] = "carga_horaria = '{$this->carga_horaria}'";
            }

            if (is_string($this->data_cadastro)) {
                $set[] = "data_cadastro = '{$this->data_cadastro}'";
            }

            $set[] = 'data_exclusao = NOW()';

            if (is_numeric($this->ativo)) {
                $set[] = "ativo = '{$this->ativo}'";
            }

            if (is_numeric($this->idade_inicial)) {
                $set[] = "idade_inicial = '{$this->idade_inicial}'";
            } else {
                $set[] = 'idade_inicial = NULL';
            }

            if (is_numeric($this->idade_final)) {
                $set[] = "idade_final = '{$this->idade_final}'";
            } else {
                $set[] = 'idade_final = NULL';
            }

            if (is_numeric($this->regra_avaliacao_id)) {
                $set[] = "regra_avaliacao_id = '{$this->regra_avaliacao_id}'";
            }

            if (is_numeric($this->regra_avaliacao_diferenciada_id)) {
                $set[] = "regra_avaliacao_diferenciada_id = '{$this->regra_avaliacao_diferenciada_id}' ";
            } else {
                $set[] = 'regra_avaliacao_diferenciada_id = NULL ';
            }

            if (is_string($this->observacao_historico)) {
                $observacao_historico = $db->escapeString($this->observacao_historico);
                $set[] = "observacao_historico = '{$observacao_historico}'";
            }

            if (is_numeric($this->dias_letivos)) {
                $set[] = "dias_letivos = '{$this->dias_letivos}'";
            }

            if (is_numeric($this->idade_ideal)) {
                $set[] = "idade_ideal = '{$this->idade_ideal}'";
            } else {
                $set[] = 'idade_ideal = NULL';
            }

            if (dbBool($this->alerta_faixa_etaria)) {
                $set[] = 'alerta_faixa_etaria = true ';
            } else {
                $set[] = 'alerta_faixa_etaria = false ';
            }

            if (dbBool($this->bloquear_matricula_faixa_etaria)) {
                $set[] = 'bloquear_matricula_faixa_etaria = true ';
            } else {
                $set[] = 'bloquear_matricula_faixa_etaria = false ';
            }

            if (dbBool($this->exigir_inep)) {
                $set[] = 'exigir_inep = true ';
            } else {
                $set[] = 'exigir_inep = false ';
            }

            if (dbBool($this->importar_serie_pre_matricula)) {
                $set[] = 'importar_serie_pre_matricula = true ';
            } else {
                $set[] = 'importar_serie_pre_matricula = false ';
            }

            $set = join(', ', $set);

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_serie = '{$this->cod_serie}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $int_cod_serie = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_curso = null,
        $str_nm_serie = null,
        $int_etapa_curso = null,
        $int_concluinte = null,
        $int_carga_horaria = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_cod_instituicao = null,
        $int_idade_inicial = null,
        $int_idade_final = null,
        $int_ref_cod_escola = null,
        $regra_avaliacao_id = null,
        $int_idade_ideal = null,
        $ano = null
    ) {
        $db = new clsBanco();
        $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao FROM {$this->_tabela} s, {$this->_schema}curso c";

        $filtros = [' WHERE s.ref_cod_curso = c.cod_curso'];

        if (is_numeric($int_cod_serie)) {
            $filtros[] = "s.cod_serie = '{$int_cod_serie}'";
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros[] = "s.ref_usuario_exc = '{$int_ref_usuario_exc}'";
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros[] = "s.ref_usuario_cad = '{$int_ref_usuario_cad}'";
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros[] = " s.ref_cod_curso = '{$int_ref_cod_curso}'";
        }

        if (is_string($str_nm_serie)) {
            $nm_serie = $db->escapeString($str_nm_serie);
            $filtros[] = "EXISTS (SELECT 1 FROM pmieducar.serie WHERE unaccent(s.nm_serie) ILIKE unaccent('%{$nm_serie}%'))";
        }

        if (is_numeric($int_etapa_curso)) {
            $filtros[] = "s.etapa_curso = '{$int_etapa_curso}'";
        }

        if (is_numeric($int_concluinte)) {
            $filtros[] = "s.concluinte = '{$int_concluinte}'";
        }

        if (is_numeric($int_carga_horaria)) {
            $filtros[] = "s.carga_horaria = '{$int_carga_horaria}'";
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros[] = "s.data_cadastro >= '{$date_data_cadastro_ini}'";
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros[] = "s.data_cadastro <= '{$date_data_cadastro_fim}'";
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros[] = "s.data_exclusao >= '{$date_data_exclusao_ini}'";
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros[] = "s.data_exclusao <= '{$date_data_exclusao_fim}'";
        }

        if (is_numeric($regra_avaliacao_id)) {
            $filtros[] = "s.regra_avaliacao_id = '{$regra_avaliacao_id}'";
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros[] = 's.ativo = \'1\'';
        } else {
            $filtros[] = 's.ativo = \'0\'';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros[] = "c.ref_cod_instituicao = '$int_ref_cod_instituicao'";
        }

        if (is_numeric($int_idade_inicial)) {
            $filtros[] = "idade_inicial = '{$int_idade_inicial}'";
        }

        if (is_numeric($int_idade_ideal)) {
            $filtros[] = "idade_ideal = '{$int_idade_ideal}'";
        }

        if (is_numeric($int_idade_final)) {
            $filtros[] = "idade_final= '{$int_idade_final}'";
        }

        if (isset($int_ref_cod_escola)) {
            $condicao = " EXISTS (SELECT
                    1
                FROM
                    pmieducar.escola_serie es
                WHERE
                    s.cod_serie = es.ref_cod_serie
                    AND es.ativo = 1
                    AND es.ref_cod_escola = '{$int_ref_cod_escola}' ";

            if (isset($ano)) {
                $condicao .= " AND {$ano} = ANY(es.anos_letivos) ";
            }
            $condicao .= ' ) ';

            $filtros[] = $condicao;
        } elseif (isset($ano)) {
            $filtros[] = "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_serie es
                                        WHERE s.cod_serie = es.ref_cod_serie
                                          AND es.ativo = 1
                                          AND {$ano} = ANY(es.anos_letivos)) ";
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $filtros = join(' AND ', $filtros);

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} s, "
            . "{$this->_schema}curso c {$filtros}");

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

        return $resultado;
    }

    public function listaSeriesComComponentesVinculados(
        $int_cod_serie = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_instituicao = null,
        $int_ativo = null
    ) {
        $sql = "SELECT {$this->_campos_lista}, s.descricao,
            c.ref_cod_instituicao FROM {$this->_tabela} s,
            {$this->_schema}curso c";

        $filtros = [' WHERE s.ref_cod_curso = c.cod_curso'];

        if (is_numeric($int_cod_serie)) {
            $filtros[] = "s.cod_serie = '{$int_cod_serie}'";
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros[] = "s.ref_cod_curso = '{$int_ref_cod_curso}'";
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros[] = "c.ref_cod_instituicao = '$int_ref_cod_instituicao'";
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros[] = 's.ativo = \'1\'';
        }

        $filtros[] = 's.cod_serie IN (SELECT DISTINCT ano_escolar_id FROM modules.componente_curricular_ano_escolar)';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $filtros = join(' AND ', $filtros);

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} s, "
            . "{$this->_schema}curso c {$filtros}");

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
        if (is_numeric($this->cod_serie) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_serie = '{$this->cod_serie}' AND s.ref_cod_curso = '{$this->ref_cod_curso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif (is_numeric($this->cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_serie = '{$this->cod_serie}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro ou FALSE caso não exista.
     *
     * @return array|bool
     */
    public function existe()
    {
        if (is_numeric($this->cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_serie = '{$this->cod_serie}'");
            $db->ProximoRegistro();

            return $db->Tupla();
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
        if (is_numeric($this->cod_serie) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Seleciona as série que não estejam cadastradas na escola.
     *
     * @param int $ref_cod_curso
     * @param int $ref_cod_escola
     *
     * @return array
     */
    public function getNotEscolaSerie($ref_cod_curso, $ref_cod_escola)
    {
        $db = new clsBanco();
        $sql = "SELECT *
            FROM
              pmieducar.serie s
            WHERE s.ref_cod_curso = '{$ref_cod_curso}'
            AND s.cod_serie NOT IN
            (
              SELECT es.ref_cod_serie
              FROM pmieducar.escola_serie es
              WHERE es.ref_cod_escola = '{$ref_cod_escola}'
            )";

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla;
        }

        return $resultado;
    }

    /**
     * Verifica se a data de nascimento enviada por parâmetro está dentro do período de corte etário pré-definido.
     *
     * @param int $dataNascimento
     *
     * @return boolean
     */
    public function verificaPeriodoCorteEtarioDataNascimento($dataNascimento, $ano)
    {
        $detSerie = $this->detalhe();
        $idadeInicial = $detSerie['idade_inicial'];
        $idadeFinal = $detSerie['idade_final'];

        $instituicaoId = $this->getInstituicaoByCurso($detSerie['ref_cod_curso']);
        $objInstituicao = new clsPmieducarInstituicao($instituicaoId);
        $detInstituicao = $objInstituicao->detalhe();
        $dataBaseMatricula = $detInstituicao['data_base_matricula'];

        //Caso não tenha data base na matricula, não verifica se está dentro do periodo
        if (!is_string($dataBaseMatricula)) {
            return true;
        }

        $dataBaseMatricula = explode('-', $dataBaseMatricula);

        $anoLimite = $ano;
        $mesLimite = $dataBaseMatricula[1];
        $diaLimite = $dataBaseMatricula[2];

        $dataLimite = $anoLimite . '-' . $mesLimite . '-' . $diaLimite;

        $dataNascimento = new DateTime($dataNascimento);
        $dataLimite = new DateTime($dataLimite);

        $diferencaDatas = $dataNascimento->diff($dataLimite);

        $idadeNaData = $diferencaDatas->y;
        $idadesPermitidas = range($idadeInicial, $idadeFinal);

        $idadeCompativel = false;

        foreach ($idadesPermitidas as $idade) {
            if ($idade == $idadeNaData) {
                $idadeCompativel = true;
            }
        }

        return $idadeCompativel;
    }

    public function getInstituicaoByCurso($codCurso)
    {
        $objCurso = new clsPmieducarCurso($codCurso);
        $detCurso = $objCurso->detalhe();

        return $detCurso['ref_cod_instituicao'];
    }

    public function possuiTurmasVinculadas()
    {
        $sql = 'SELECT
            1
        FROM
            pmieducar.turma
        WHERE TRUE
            AND turma.ref_ref_cod_serie = $1
            AND turma.ativo = 1';

        $params = [
            'params' => $this->cod_serie,
            'return_only' => 'first-field'
        ];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $params);
    }
}
