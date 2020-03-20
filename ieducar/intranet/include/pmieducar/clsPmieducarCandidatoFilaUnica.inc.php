<?php

use iEducar\Legacy\Model;

class clsPmieducarCandidatoFilaUnica extends Model
{
    public $cod_candidato_fila_unica;
    public $ref_cod_aluno;
    public $ref_cod_serie;
    public $ref_cod_turno;
    public $ref_cod_pessoa_cad;
    public $ref_cod_pessoa_exc;
    public $ref_cod_matricula;
    public $ano_letivo;
    public $data_nasc;
    public $data_cadastro;
    public $data_exclusao;
    public $data_solicitacao;
    public $hora_solicitacao;
    public $horario_inicial;
    public $horario_final;
    public $situacao;
    public $via_judicial;
    public $via_judicial_doc;
    public $protocolo;
    public $ativo;
    public $sexo;
    public $ideciv;
    public $comments;

    public function __construct(
        $cod_candidato_fila_unica = null,
        $ref_cod_aluno = null,
        $ref_cod_serie = null,
        $ref_cod_turno = null,
        $ref_cod_pessoa_cad = null,
        $ref_cod_pessoa_exc = null,
        $ref_cod_matricula = null,
        $ano_letivo = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $data_solicitacao = null,
        $hora_solicitacao = null,
        $horario_inicial = null,
        $horario_final = null,
        $situacao = null,
        $via_judicial = null,
        $via_judicial_doc = null,
        $ativo = null
    ) {
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}candidato_fila_unica";

        $this->_campos_lista = $this->_todos_campos = 'cfu.cod_candidato_fila_unica,
                                                       cfu.ref_cod_aluno,
                                                       cfu.ref_cod_serie,
                                                       cfu.ref_cod_turno,
                                                       cfu.ref_cod_pessoa_cad,
                                                       cfu.ref_cod_pessoa_exc,
                                                       cfu.ref_cod_matricula,
                                                       cfu.ano_letivo,
                                                       cfu.data_cadastro,
                                                       cfu.data_exclusao,
                                                       cfu.data_solicitacao,
                                                       cfu.hora_solicitacao,
                                                       cfu.horario_inicial,
                                                       cfu.horario_final,
                                                       cfu.situacao,
                                                       cfu.historico,
                                                       cfu.motivo,
                                                       cfu.data_situacao,
                                                       cfu.via_judicial,
                                                       cfu.via_judicial_doc,
                                                       cfu.ativo,
                                                       cfu.comments';

        if (is_numeric($cod_candidato_fila_unica)) {
            $this->cod_candidato_fila_unica = $cod_candidato_fila_unica;
        }

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }

        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }

        if (is_numeric($ref_cod_turno)) {
            $this->ref_cod_turno = $ref_cod_turno;
        }

        if (is_numeric($ref_cod_pessoa_cad)) {
            $this->ref_cod_pessoa_cad = $ref_cod_pessoa_cad;
        }

        if (is_numeric($ref_cod_pessoa_exc)) {
            $this->ref_cod_pessoa_exc = $ref_cod_pessoa_exc;
        }

        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }

        if (is_numeric($ano_letivo)) {
            $this->ano_letivo = $ano_letivo;
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }

        if (is_string($data_solicitacao)) {
            $this->data_solicitacao = $data_solicitacao;
        }

        if (is_string($hora_solicitacao)) {
            $this->hora_solicitacao = $hora_solicitacao;
        }

        if (is_string($horario_inicial)) {
            $this->horario_inicial = $horario_inicial;
        }

        if (is_string($horario_final)) {
            $this->horario_final = $horario_final;
        }

        if (is_string($situacao)) {
            $this->situacao = $situacao;
        }

        if (is_bool($via_judicial)) {
            $this->via_judicial = $via_judicial;
        }

        if (is_string($via_judicial_doc)) {
            $this->via_judicial_doc = $via_judicial_doc;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (
            is_numeric($this->ref_cod_aluno)
            && is_numeric($this->ref_cod_serie)
            && is_numeric($this->ref_cod_turno)
            && is_numeric($this->ref_cod_pessoa_cad)
            && is_numeric($this->ano_letivo)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}ref_cod_aluno";
            $valores .= "{$gruda}{$this->ref_cod_aluno}";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_serie";
            $valores .= "{$gruda}{$this->ref_cod_serie}";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_turno";
            $valores .= "{$gruda}{$this->ref_cod_turno}";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_pessoa_cad";
            $valores .= "{$gruda}{$this->ref_cod_pessoa_cad}";
            $gruda = ', ';

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ano_letivo";
            $valores .= "{$gruda}{$this->ano_letivo}";
            $gruda = ', ';

            if (is_string($this->data_solicitacao)) {
                $campos .= "{$gruda}data_solicitacao";
                $valores .= "{$gruda}'{$this->data_solicitacao}'";
                $gruda = ', ';
            }

            if (is_string($this->hora_solicitacao)) {
                $campos .= "{$gruda}hora_solicitacao";
                $valores .= "{$gruda}'{$this->hora_solicitacao}'";
                $gruda = ', ';
            }

            if (is_string($this->horario_inicial) && !empty($this->horario_inicial)) {
                $campos .= "{$gruda}horario_inicial";
                $valores .= "{$gruda}'{$this->horario_inicial}'";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}horario_inicial";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }

            if (is_string($this->horario_final) && !empty($this->horario_final)) {
                $campos .= "{$gruda}horario_final";
                $valores .= "{$gruda}'{$this->horario_final}'";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}horario_final";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }

            if (is_string($this->situacao)) {
                $campos .= "{$gruda}situacao";
                $valores .= "{$gruda}'{$this->situacao}'";
                $gruda = ', ';
            }

            if (dbBool($this->via_judicial)) {
                $campos .= "{$gruda}via_judicial";
                $valores .= "{$gruda}true";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}via_judicial";
                $valores .= "{$gruda}false";
                $gruda = ', ';
            }

            if (is_string($this->via_judicial_doc)) {
                $campos .= "{$gruda}via_judicial_doc";
                $valores .= "{$gruda}'{$this->via_judicial_doc}'";
                $gruda = ', ';
            }

            if (is_string($this->comments)) {
                $campos .= "{$gruda}comments";
                $valores .= "{$gruda}'{$this->comments}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            return $db->campoUnico("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores ) RETURNING cod_candidato_fila_unica");
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (
            is_numeric($this->cod_candidato_fila_unica)
            && is_numeric($this->ref_cod_aluno)
        ) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_serie)) {
                $set .= "{$gruda}ref_cod_serie = {$this->ref_cod_serie}";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_turno)) {
                $set .= "{$gruda}ref_cod_turno = {$this->ref_cod_turno}";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_pessoa_exc)) {
                $set .= "{$gruda}ref_cod_pessoa_exc = {$this->ref_cod_pessoa_exc}";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_matricula)) {
                $set .= "{$gruda}ref_cod_matricula = {$this->ref_cod_matricula}";
                $gruda = ', ';
            }

            if (is_numeric($this->ano_letivo)) {
                $set .= "{$gruda}ano_letivo = {$this->ano_letivo}";
                $gruda = ', ';
            }

            if (is_string($this->data_exclusao)) {
                $set .= "{$gruda}data_exclusao = '{$this->data_exclusao}'";
                $gruda = ', ';
            }

            if (is_string($this->data_solicitacao) && !empty($this->data_solicitacao)) {
                $set .= "{$gruda}data_solicitacao = '{$this->data_solicitacao}'";
                $gruda = ', ';
            }

            if (is_string($this->hora_solicitacao) && !empty($this->hora_solicitacao)) {
                $set .= "{$gruda}hora_solicitacao = '{$this->hora_solicitacao}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}hora_solicitacao = NULL";
                $gruda = ', ';
            }

            if (is_string($this->horario_inicial) && !empty($this->horario_inicial)) {
                $set .= "{$gruda}horario_inicial = '{$this->horario_inicial}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}horario_inicial = NULL";
                $gruda = ', ';
            }

            if (is_string($this->horario_final) && !empty($this->horario_final)) {
                $set .= "{$gruda}horario_final = '{$this->horario_final}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}horario_final = NULL";
                $gruda = ', ';
            }

            if (is_string($this->situacao)) {
                $set .= "{$gruda}situacao = '{$this->situacao}'";
                $gruda = ', ';
            }

            if (dbBool($this->via_judicial)) {
                $set .= "{$gruda}via_judicial = true";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}via_judicial = false";
                $gruda = ', ';
            }

            if (is_string($this->via_judicial_doc)) {
                $set .= "{$gruda}via_judicial_doc = '{$this->via_judicial_doc}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}via_judicial_doc = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = {$this->ativo}";
                $gruda = ', ';
            }

            if (is_string($this->comments)) {
                $set .= "{$gruda}comments = '{$this->comments}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}comments = NULL";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_candidato_fila_unica = {$this->cod_candidato_fila_unica}");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista(
        $nome = null,
        $nome_responsavel = null,
        $ref_cod_escola = null,
        $getEscolas = false
    ) {
        $sqlEscolas = 'null';

        if ($getEscolas) {
            $sqlEscolas = " (SELECT string_agg(j.fantasia, ', ')
                          FROM pmieducar.escola_candidato_fila_unica ecfu
                    INNER JOIN pmieducar.escola e ON e.cod_escola = ecfu.ref_cod_escola
                    INNER JOIN cadastro.juridica j ON j.idpes = e.ref_idpes
                         WHERE ecfu.ref_cod_candidato_fila_unica = cfu.cod_candidato_fila_unica
                      GROUP BY ecfu.ref_cod_candidato_fila_unica) AS escolas";
        }

        $sql = "SELECT {$this->_campos_lista},
                       p.nome,
                       f.data_nasc,
                       d.certidao_nascimento,
                       d.num_termo,
                       d.num_livro,
                       d.num_folha,
                       d.comprovante_residencia,
                       f.data_nasc,
                       f.sexo,
                       f.ideciv,
                       s.nm_serie,
                       cfu.comments AS observacoes,
                       (cfu.ano_letivo || to_char(cfu.cod_candidato_fila_unica, 'fm00000000')) AS protocolo,
                       (CASE cfu.situacao
                        WHEN 'A' THEN 'Atendida'
                        WHEN 'I' THEN 'Indeferida'
                        WHEN 'D' THEN 'Desistente'
                        ELSE 'Em espera' END) AS situacao_desc,
                        {$sqlEscolas}
                  FROM {$this->_tabela} cfu
            INNER JOIN pmieducar.aluno a ON (a.cod_aluno = cfu.ref_cod_aluno)
            INNER JOIN cadastro.pessoa p ON (p.idpes = a.ref_idpes)
            INNER JOIN cadastro.fisica f ON (f.idpes = a.ref_idpes)
            INNER JOIN pmieducar.serie s ON (s.cod_serie = cfu.ref_cod_serie)
             LEFT JOIN cadastro.documento d ON (d.idpes = a.ref_idpes)";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($this->cod_candidato_fila_unica) && empty($this->protocolo)) {
            $filtros .= "{$whereAnd} cod_candidato_fila_unica = {$this->cod_candidato_fila_unica}";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ref_cod_aluno)) {
            $filtros .= "{$whereAnd} ref_cod_aluno = {$this->ref_cod_aluno}";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = {$this->ref_cod_serie}";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ref_cod_turno)) {
            $filtros .= "{$whereAnd} ref_cod_turno = {$this->ref_cod_turno}";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ref_cod_pessoa_exc)) {
            $filtros .= "{$whereAnd} ref_cod_pessoa_exc = {$this->ref_cod_pessoa_exc}";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = {$this->ref_cod_matricula}";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ano_letivo) && empty($this->protocolo)) {
            $filtros .= "{$whereAnd} ano_letivo = {$this->ano_letivo}";
            $whereAnd = ' AND ';
        }

        if (is_string($this->data_exclusao)) {
            $filtros .= "{$whereAnd} data_exclusao = '{$this->data_exclusao}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->data_solicitacao)) {
            $filtros .= "{$whereAnd} data_solicitacao = '{$this->data_solicitacao}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->hora_solicitacao)) {
            $filtros .= "{$whereAnd} hora_solicitacao = '{$this->hora_solicitacao}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->data_nasc)) {
            $filtros .= "{$whereAnd} f.data_nasc = '{$this->data_nasc}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->sexo)) {
            $filtros .= "{$whereAnd} f.sexo = '{$this->sexo}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ideciv)) {
            $filtros .= "{$whereAnd} f.ideciv = '{$this->ideciv}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->horario_inicial)) {
            $filtros .= "{$whereAnd} horario_inicial = '{$this->horario_inicial}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->horario_final)) {
            $filtros .= "{$whereAnd} horario_final = '{$this->horario_final}'";
            $whereAnd = ' AND ';
        }

        if (is_string($this->situacao)) {
            if ($this->situacao === 'E') {
                $filtros .= "{$whereAnd} cfu.situacao IS NULL";
            } else {
                $filtros .= "{$whereAnd} cfu.situacao = '{$this->situacao}'";
            }

            $whereAnd = ' AND ';
        }

        if (dbBool($this->via_judicial)) {
            $filtros .= "{$whereAnd} via_judicial = true";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd}via_judicial = false";
            $whereAnd = ' AND ';
        }

        if (is_string($this->via_judicial_doc)) {
            $filtros .= "{$whereAnd} via_judicial_doc = '{$this->via_judicial_doc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($this->ativo)) {
            $filtros .= "{$whereAnd} cfu.ativo = '{$this->ativo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($nome)) {
            $nome = str_replace('\'', '\'\'', $nome);
            $filtros .= "{$whereAnd} upper(nome) LIKE upper('%{$nome}%')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_candidato_fila_unica
                                              WHERE ref_cod_candidato_fila_unica = cod_candidato_fila_unica
                                                AND ref_cod_escola = {$ref_cod_escola})";
            $whereAnd = ' AND ';
        }

        if (is_string($nome_responsavel)) {
            $nome_responsavel = str_replace('\'', '\'\'', $nome_responsavel);
            $filtros .= "{$whereAnd} (SELECT upper(replace(textcat_all(nome),' <br>',','))
                                        FROM (SELECT p.nome
                                                FROM pmieducar.responsaveis_aluno ra
                                               INNER JOIN cadastro.pessoa p ON (p.idpes = ra.ref_idpes)
                                               WHERE ref_cod_aluno = cfu.ref_cod_aluno
                                               ORDER BY vinculo_familiar
                                               LIMIT 3) r) LIKE upper('%{$nome_responsavel}%')";
        }

        if (is_numeric($this->protocolo)) {
            $protocolo = $this->protocolo;
            $ano_letivo = substr($protocolo, 0, 4);
            $cod_candidato_fila_unica = substr_replace($protocolo, '', 0, 4) + 0;
            $filtros .= "{$whereAnd} cod_candidato_fila_unica = {$cod_candidato_fila_unica}";
            $filtros .= "{$whereAnd} ano_letivo = {$ano_letivo}";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0)
                                           FROM {$this->_tabela} cfu
                                     INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = cfu.ref_cod_aluno)
                                     INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                                     INNER JOIN cadastro.fisica f ON (f.idpes = aluno.ref_idpes) {$filtros}");

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
    public function detalhe()
    {
        if (is_numeric($this->cod_candidato_fila_unica)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos},
                                  (cfu.ano_letivo || to_char(cfu.cod_candidato_fila_unica, 'fm00000000')) AS protocolo,
                                  p.nome,
                                  f.data_nasc,
                                  f.sexo,
                                  f.ideciv,
                                  s.nm_serie,
                                  d.certidao_nascimento,
                                  d.num_termo,
                                  d.num_folha,
                                  d.num_livro,
                                  d.comprovante_residencia,
                                  fisica_responsavel.sexo AS sexo_responsavel,
                                  fisica_responsavel.ideciv AS ideciv_responsavel,
                                  replace(string_agg(pessoa_responsavel.nome, ' '),' ',',') AS responsaveis,
                                  (SELECT textcat_all(relatorio.get_nome_escola(ref_cod_escola))
                                     FROM (SELECT ref_cod_escola
                                             FROM pmieducar.escola_candidato_fila_unica ecfu
                                            WHERE ref_cod_candidato_fila_unica = cfu.cod_candidato_fila_unica
                                            ORDER BY sequencial) e) AS escolas
                             FROM {$this->_tabela} cfu
                       INNER JOIN pmieducar.aluno a ON (a.cod_aluno = cfu.ref_cod_aluno)
                       INNER JOIN cadastro.pessoa p ON (p.idpes = a.ref_idpes)
                       INNER JOIN cadastro.fisica f ON (f.idpes = a.ref_idpes)
                       INNER JOIN pmieducar.serie s ON (s.cod_serie = cfu.ref_cod_serie)
                        LEFT JOIN pmieducar.responsaveis_aluno ra ON (ra.ref_cod_aluno= cfu.ref_cod_aluno)
                        LEFT JOIN cadastro.pessoa pessoa_responsavel ON (pessoa_responsavel.idpes = ra.ref_idpes)
                        LEFT JOIN cadastro.fisica fisica_responsavel ON (fisica_responsavel.idpes = pessoa_responsavel.idpes)
                        LEFT JOIN cadastro.documento d ON (d.idpes = a.ref_idpes)
                            WHERE cod_candidato_fila_unica = {$this->cod_candidato_fila_unica}
                            GROUP BY cfu.cod_candidato_fila_unica,
                                     cfu.ref_cod_aluno,
                                     cfu.ref_cod_serie,
                                     cfu.ref_cod_turno,
                                     cfu.ref_cod_pessoa_cad,
                                     cfu.ref_cod_pessoa_exc,
                                     cfu.ref_cod_matricula,
                                     cfu.ano_letivo,
                                     cfu.data_cadastro,
                                     cfu.data_exclusao,
                                     cfu.data_solicitacao,
                                     cfu.hora_solicitacao,
                                     cfu.horario_inicial,
                                     cfu.horario_final,
                                     cfu.situacao,
                                     cfu.motivo,
                                     cfu.via_judicial,
                                     cfu.via_judicial_doc,
                                     cfu.ativo,
                                     p.nome,
                                     f.data_nasc,
                                     f.sexo,
                                     f.ideciv,
                                     s.nm_serie,
                                     d.certidao_nascimento,
                                     d.num_termo,
                                     d.num_folha,
                                     d.num_livro,
                                     d.comprovante_residencia,
                                     fisica_responsavel.sexo,
                                     fisica_responsavel.ideciv");

            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_candidato_fila_unica)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_canddidato_fila_unica = '{$this->cod_canddidato_fila_unica}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_canddidato_fila_unica) && is_numeric($this->ref_cod_pessoa_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    protected function montaHistorico()
    {
        $detalhes = $this->detalhe();
        $historico = $detalhes['historico'];

        if (is_null($historico)) {
            $historico = [];
        } else {
            $historico = json_decode($historico, true);
        }

        $mapaSituacao = [
            null => 'Em espera',
            'I' => 'Indeferida',
            'A' => 'Atendida',
        ];

        $data = $detalhes['data_situacao'] ?? $detalhes['data_solicitacao'];
        $data = date('d/m/Y', strtotime($data));

        $historico[] = [
            'situacao' => $mapaSituacao[$detalhes['situacao']] ?? 'Desconhecida',
            'motivo' => trim($detalhes['motivo']),
            'data' => $data,
        ];

        return json_encode($historico);
    }

    public function indefereCandidatura($motivo = null)
    {
        $motivo = $motivo == null ? 'null' : '\'' . $motivo . '\'';

        if (is_numeric($this->cod_candidato_fila_unica)) {
            $historico = $this->montaHistorico();
            $db = new clsBanco();
            $db->Consulta("UPDATE pmieducar.candidato_fila_unica
                              SET situacao = 'I',
                                  motivo = $motivo,
                                  historico = '$historico',
                                  data_situacao = NOW()
                            WHERE cod_candidato_fila_unica = '{$this->cod_candidato_fila_unica}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function vinculaMatricula($ref_cod_matricula)
    {
        if (is_numeric($ref_cod_matricula)) {
            $historico = $this->montaHistorico();

            $db = new clsBanco();
            $db->Consulta("UPDATE pmieducar.candidato_fila_unica
                              SET ref_cod_matricula = '{$ref_cod_matricula}',
                                  situacao = 'A',
                                  data_situacao = NOW(),
                                  historico = '{$historico}'
                            WHERE cod_candidato_fila_unica = '{$this->cod_candidato_fila_unica}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function alteraSituacao($situacao, $motivo = null, $data = null)
    {
        if (!$this->cod_candidato_fila_unica) {
            return false;
        }

        $situacao = $situacao ?: 'NULL';
        $motivo = str_replace("\'", "''", $motivo) ?: 'NULL';
        $historico = $this->montaHistorico();
        $data = $data ?: 'NOW()';

        $db = new clsBanco();
        $db->Consulta("UPDATE pmieducar.candidato_fila_unica
                          SET situacao = {$situacao},
                              motivo = '{$motivo}',
                              data_situacao = NOW(),
                              data_solicitacao = '{$data}',
                              hora_solicitacao = NOW(),
                              historico = '{$historico}'
                        WHERE cod_candidato_fila_unica = '{$this->cod_candidato_fila_unica}'");

        return true;
    }

    /**
     * Retorna um array com os códigos das escolas em que o aluno está
     * aguardando na fila.
     *
     * @param int $cod_candidato_fila_unica
     *
     * @return array
     *
     * @throws Exception
     */
    public function getOpcoesDeEscolas($cod_candidato_fila_unica)
    {
        $db = new clsBanco();

        $db->Consulta(
            "
                SELECT ref_cod_escola
                FROM pmieducar.escola_candidato_fila_unica
                WHERE ref_cod_candidato_fila_unica = {$cod_candidato_fila_unica}
                ORDER BY sequencial;
            "
        );

        $escolas = [];

        while ($db->ProximoRegistro()) {
            $escolas[] = $db->Tupla()['ref_cod_escola'];
        }

        return $escolas;
    }
}
