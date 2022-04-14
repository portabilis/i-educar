<?php

use iEducar\Legacy\Model;

class clsPmieducarCandidatoReservaVaga extends Model
{
    public $cod_candidato_reserva_vaga;
    public $ano_letivo;
    public $data_solicitacao;
    public $ref_cod_aluno;
    public $ref_cod_serie;
    public $ref_cod_turno;
    public $ref_cod_pessoa_cad;
    public $data_cad;
    public $ref_cod_matricula;
    public $situacao;
    public $quantidade_membros;
    public $codUsuario;
    public $membros_trabalham;
    public $mae_fez_pre_natal;
    public $hora_solicitacao;

    public function __construct(
        $cod_candidato_reserva_vaga = null,
        $ano_letivo = null,
        $data_solicitacao = null,
        $ref_cod_aluno = null,
        $ref_cod_serie = null,
        $ref_cod_turno = null,
        $ref_cod_pessoa_cad = null,
        $ref_cod_escola = null,
        $quantidade_membros = null,
        $membros_trabalham = null,
        $mae_fez_pre_natal = null,
        $hora_solicitacao = null
    ) {
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'candidato_reserva_vaga crv ';

        $this->_campos_lista = $this->_todos_campos = ' crv.cod_candidato_reserva_vaga,
                                                    crv.ano_letivo,
                                                    crv.data_solicitacao,
                                                    crv.ref_cod_aluno,
                                                    crv.ref_cod_serie,
                                                    crv.ref_cod_turno,
                                                    crv.ref_cod_pessoa_cad,
                                                    crv.data_cad,
                                                    crv.data_update,
                                                    crv.data_situacao,
                                                    crv.situacao,
                                                    crv.ref_cod_matricula,
                                                    crv.ref_cod_escola,
                                                    crv.quantidade_membros,
                                                    crv.membros_trabalham,
                                                    crv.mae_fez_pre_natal,
                                                    crv.hora_solicitacao,
                                                    crv.historico';

        if (is_numeric($cod_candidato_reserva_vaga)) {
            $this->cod_candidato_reserva_vaga = $cod_candidato_reserva_vaga;
        }

        if (is_numeric($ano_letivo)) {
            $this->ano_letivo = $ano_letivo;
        }

        if (is_string($data_solicitacao)) {
            $this->data_solicitacao = $data_solicitacao;
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

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }

        if (is_numeric($quantidade_membros)) {
            $this->quantidade_membros = $quantidade_membros;
        }

        if (is_numeric($membros_trabalham)) {
            $this->membros_trabalham = $membros_trabalham;
        }

        if (is_bool($mae_fez_pre_natal)) {
            $this->mae_fez_pre_natal = $mae_fez_pre_natal;
        }

        if (is_string($hora_solicitacao)) {
            $this->hora_solicitacao = $hora_solicitacao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ano_letivo) &&
            is_string($this->data_solicitacao) &&
            is_numeric($this->ref_cod_aluno) &&
            is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_pessoa_cad)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->cod_candidato_reserva_vaga)) {
                $campos .= "{$gruda}cod_candidato_reserva_vaga";
                $valores .= "{$gruda}'{$this->cod_candidato_reserva_vaga}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano_letivo)) {
                $campos .= "{$gruda}ano_letivo";
                $valores .= "{$gruda}'{$this->ano_letivo}'";
                $gruda = ', ';
            }

            if (is_string($this->data_solicitacao)) {
                $campos .= "{$gruda}data_solicitacao";
                $valores .= "{$gruda}'{$this->data_solicitacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_turno)) {
                $campos .= "{$gruda}ref_cod_turno";
                $valores .= "{$gruda}'{$this->ref_cod_turno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quantidade_membros)) {
                $campos .= "{$gruda}quantidade_membros";
                $valores .= "{$gruda}'{$this->quantidade_membros}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_pessoa_cad)) {
                $campos .= "{$gruda}ref_cod_pessoa_cad";
                $valores .= "{$gruda}'{$this->ref_cod_pessoa_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->membros_trabalham)) {
                $campos .= "{$gruda}membros_trabalham";
                $valores .= "{$gruda}$this->membros_trabalham";
                $gruda = ', ';
            }

            if (is_bool($this->mae_fez_pre_natal) && $this->mae_fez_pre_natal) {
                $campos .= "{$gruda}mae_fez_pre_natal";
                $valores .= "{$gruda}true";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}mae_fez_pre_natal";
                $valores .= "{$gruda}false";
                $gruda = ', ';
            }

            if (is_string($this->hora_solicitacao) && !empty($this->hora_solicitacao)) {
                $campos .= "{$gruda}hora_solicitacao";
                $valores .= "{$gruda}'$this->hora_solicitacao'";
                $gruda = ', ';
            }
            if (empty($this->hora_solicitacao)) {
                $campos .= "{$gruda}hora_solicitacao";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cad";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}data_update";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_escola";
            $valores .= "{$gruda}'{$this->ref_cod_escola}'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO pmieducar.candidato_reserva_vaga ($campos) VALUES ($valores)");

            return $db->InsertId('pmieducar.candidato_reserva_vaga_seq');
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
        if (is_numeric($this->cod_candidato_reserva_vaga)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';
            $campos = '';

            if (is_numeric($this->ano_letivo)) {
                $set .= "{$gruda}ano_letivo = '{$this->ano_letivo}'";
                $gruda = ', ';
            }

            if (is_string($this->data_solicitacao)) {
                $set .= "{$gruda}data_solicitacao = '{$this->data_solicitacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_aluno)) {
                $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_serie)) {
                $set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_turno)) {
                $set .= "{$gruda}ref_cod_turno = '{$this->ref_cod_turno}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_update = NOW() ";
            $gruda = ', ';

            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quantidade_membros)) {
                $set .= "{$gruda}quantidade_membros = '{$this->quantidade_membros}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}quantidade_membros = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->membros_trabalham)) {
                $set .= "{$gruda}membros_trabalham = $this->membros_trabalham";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}membros_trabalham = NULL";
                $gruda = ', ';
            }

            if (is_bool($this->mae_fez_pre_natal) && $this->mae_fez_pre_natal) {
                $set .= "{$gruda}mae_fez_pre_natal = true";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}mae_fez_pre_natal = false";
                $gruda = ', ';
            }

            if (is_string($this->hora_solicitacao) && !empty($this->hora_solicitacao)) {
                $set .= "{$gruda}hora_solicitacao = '$this->hora_solicitacao'";
            } elseif (empty($this->hora_solicitacao)) {
                $set .= "{$gruda}hora_solicitacao = NULL";
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_candidato_reserva_vaga = '{$this->cod_candidato_reserva_vaga}'");

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
        $ano_letivo = null,
        $nome = null,
        $nome_responsavel = null,
        $ref_cod_escola = null,
        $ref_cod_serie = null,
        $ref_cod_curso = null,
        $ref_cod_turno = null,
        $ref_cod_aluno = null,
        $situacaoEmEspera = false,
        $situacao = null,
        $protocolo = null
    ) {
        $this->resetCamposLista();

        $sql = "SELECT {$this->_campos_lista},
                       resp_pes.nome as nome_responsavel,
                       pes.nome as nome,
                       relatorio.get_nome_escola(crv.ref_cod_escola) as nm_escola
                  FROM {$this->_tabela}
                 INNER JOIN pmieducar.aluno a ON a.cod_aluno = crv.ref_cod_aluno
                 INNER JOIN cadastro.pessoa pes ON pes.idpes = a.ref_idpes
                 INNER JOIN cadastro.fisica fis ON fis.idpes = pes.idpes
                  LEFT JOIN cadastro.pessoa resp_pes ON fis.idpes_responsavel = resp_pes.idpes
                 INNER JOIN pmieducar.serie AS ser ON ser.cod_serie = crv.ref_cod_serie ";
        $whereAnd = ' WHERE ';
        $filtros = '';

        if (is_numeric($ano_letivo)) {
            $filtros .= " {$whereAnd} ano_letivo = {$ano_letivo} ";
            $whereAnd = ' AND ';
        }

        if (is_string($nome)) {
            $nome = pg_escape_string($nome);
            $filtros .= " {$whereAnd} (LOWER(pes.nome)) LIKE (LOWER('%{$nome}%')) ";
            $whereAnd = ' AND ';
        }

        if (is_string($nome_responsavel)) {
            $nome_responsavel = pg_escape_string($nome_responsavel);
            $filtros .= " {$whereAnd} (LOWER(resp_pes.nome)) LIKE (LOWER('%{$nome_responsavel}%')) ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= " {$whereAnd} ref_cod_escola = {$ref_cod_escola} ";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = crv.ref_cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_serie)) {
            $filtros .= " {$whereAnd} crv.ref_cod_serie = {$ref_cod_serie} ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_curso)) {
            $filtros .= " {$whereAnd} ser.ref_cod_curso = {$ref_cod_curso} ";
            $whereAnd = ' AND ';
        }

        if ($ref_cod_turno != 0) {
            $filtros .= " {$whereAnd} crv.ref_cod_turno = {$ref_cod_turno} ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_aluno)) {
            $filtros .= " {$whereAnd} ref_cod_aluno = {$ref_cod_aluno} ";
            $whereAnd = ' AND ';
        }

        if ($situacaoEmEspera) {
            $filtros .= " {$whereAnd} crv.situacao IS NULL";
            $whereAnd = ' AND ';
        }

        if ($situacao) {
            if ($situacao == 'E') {
                $filtros .= " {$whereAnd} crv.situacao IS NULL";
            } else {
                $filtros .= " {$whereAnd} crv.situacao = '{$situacao}'";
            }
            $whereAnd = ' AND ';
        }

        if ($protocolo) {
            $filtros .= " {$whereAnd} crv.cod_candidato_reserva_vaga = {$protocolo}";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0)
                                                   FROM {$this->_tabela}
                                                  INNER JOIN pmieducar.aluno a ON a.cod_aluno = crv.ref_cod_aluno
                                                  INNER JOIN cadastro.pessoa pes ON pes.idpes = a.ref_idpes
                                                  INNER JOIN cadastro.fisica fis ON fis.idpes = pes.idpes
                                                   LEFT JOIN cadastro.pessoa resp_pes ON fis.idpes_responsavel = resp_pes.idpes
                                                  INNER JOIN pmieducar.serie AS ser ON ser.cod_serie = crv.ref_cod_serie {$filtros}");

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
        if (is_numeric($this->cod_candidato_reserva_vaga)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos},
                                          resp_pes.nome as nome_responsavel,
                                          pes.nome as nome,
                                          crv.motivo as motivo,
                                          relatorio.get_nome_escola(crv.ref_cod_escola) as nm_escola,
                                          (SELECT nm_serie FROM pmieducar.serie WHERE cod_serie = ref_cod_serie) as serie,
                                          fis.cpf
                                     FROM {$this->_tabela}
                                    INNER JOIN pmieducar.aluno a ON a.cod_aluno = crv.ref_cod_aluno
                                    INNER JOIN cadastro.pessoa pes ON pes.idpes = a.ref_idpes
                                    INNER JOIN cadastro.fisica fis ON fis.idpes = pes.idpes
                                     LEFT JOIN cadastro.pessoa resp_pes ON fis.idpes_responsavel = resp_pes.idpes
                                    WHERE cod_candidato_reserva_vaga = '{$this->cod_candidato_reserva_vaga}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * @param null $ano_letivo
     * @param null $ref_cod_serie
     * @param null $ref_cod_aluno
     * @param null $ref_cod_escola
     *
     * @return bool
     *
     * @throws Exception
     */
    public function atualizaDesistente($ano_letivo = null, $ref_cod_serie = null, $ref_cod_aluno = null, $ref_cod_escola = null)
    {
        $this->resetCamposLista();

        $sql = "UPDATE {$this->_tabela}
               SET situacao = 'D', data_situacao = NOW()";

        $whereAnd = ' WHERE ';
        $filtros = '';

        if (is_numeric($ano_letivo)) {
            $filtros .= " {$whereAnd} ano_letivo = {$ano_letivo} ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_serie)) {
            $filtros .= " {$whereAnd} ref_cod_serie = {$ref_cod_serie} ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_aluno)) {
            $filtros .= " {$whereAnd} ref_cod_aluno = {$ref_cod_aluno} ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= " {$whereAnd} ref_cod_escola <> {$ref_cod_escola} ";
        }

        $db = new clsBanco();
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $db->Consulta($sql);

        return true;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_candidato_reserva_vaga)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_candidato_reserva_vaga = '{$this->cod_candidato_reserva_vaga}'");
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
        if (is_numeric($this->cod_candidato_reserva_vaga)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_candidato_reserva_vaga = '{$this->cod_candidato_reserva_vaga}'");

            return true;
        }

        return false;
    }

    /**
     * @return false|string
     */
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
        $data = date('d/m/Y', strtotime($data ?? 'now'));

        $historico[] = [
            'situacao' => $mapaSituacao[$detalhes['situacao']] ?? 'Desconhecida',
            'motivo' => trim($detalhes['motivo']),
            'data' => $data,
        ];

        return json_encode($historico);
    }

    /**
     * @param $ref_cod_escola
     * @param $ref_cod_matricula
     * @param $ref_cod_aluno
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function vinculaMatricula($ref_cod_escola, $ref_cod_matricula, $ref_cod_aluno)
    {
        if (is_numeric($ref_cod_escola) &&
            is_numeric($ref_cod_matricula) &&
            is_numeric($ref_cod_aluno)) {
            $historico = $this->montaHistorico();
            $sql = "UPDATE pmieducar.candidato_reserva_vaga
                       SET ref_cod_matricula = '{$ref_cod_matricula}',
                           situacao = 'A',
                           data_situacao = NOW(),
                           historico = '{$historico}'
                     WHERE ref_cod_escola = '{$ref_cod_escola}'
                       AND ref_cod_aluno = '{$ref_cod_aluno}'";
            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * @param null $motivo
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function indefereSolicitacao($motivo = null)
    {
        $motivo = $motivo == null ? 'null' : '\'' . $motivo . '\'';

        if (is_numeric($this->cod_candidato_reserva_vaga)) {
            $historico = $this->montaHistorico();
            $db = new clsBanco();
            $db->Consulta("UPDATE pmieducar.candidato_reserva_vaga
                                      SET situacao = 'N',
                                          motivo = $motivo,
                                          data_situacao = NOW(),
                                          historico = '{$historico}'
                                    WHERE cod_candidato_reserva_vaga = '{$this->cod_candidato_reserva_vaga}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * @param $situacao
     * @param null $motivo
     *
     * @return bool
     *
     * @throws Exception
     */
    public function alteraSituacao($situacao, $motivo = null, $data = null)
    {
        if (!$this->cod_candidato_reserva_vaga) {
            return false;
        }

        $situacao = $situacao ?: 'NULL';
        $motivo = str_replace("\'", '\'\'', addslashes($motivo)) ?: null;
        if ($data) {
            $data = "data_solicitacao = to_date('{$data}','DD-MM-YYYY'),";
        }

        $historico = $this->montaHistorico();

        $db = new clsBanco();
        $db->Consulta("UPDATE pmieducar.candidato_reserva_vaga
                                  SET situacao = {$situacao},
                                      motivo = '{$motivo}',
                                      {$data}
                                      data_situacao = NOW(),
                                      historico = '{$historico}'
                                WHERE cod_candidato_reserva_vaga = '{$this->cod_candidato_reserva_vaga}'");

        return true;
    }
}
