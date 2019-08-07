<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarServidorAlocacao extends Model
{
    public $cod_servidor_alocacao;
    public $ref_ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_escola;
    public $ref_cod_servidor;
    public $ref_cod_servidor_funcao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $carga_horaria;
    public $periodo;
    public $ref_cod_funcionario_vinculo;
    public $ano;
    public $codUsuario;
    public $dataAdmissao;
    public $dataSaida;
    public $hora_inicial;
    public $hora_final;
    public $hora_atividade;
    public $horas_excedentes;

    /**
     * Carga horária máxima para um período de alocação (em horas).
     *
     * @var float
     */
    public static $cargaHorariaMax = 36.0;

    /**
     * Define o campo para ser usado como padrão de agrupamento no método lista().
     *
     * @var string
     */
    public $_campo_group_by;

    /**
     * @param null $cod_servidor_alocacao
     * @param null $ref_ref_cod_instituicao
     * @param null $ref_usuario_exc
     * @param null $ref_usuario_cad
     * @param null $ref_cod_escola
     * @param null $ref_cod_servidor
     * @param null $data_cadastro
     * @param null $data_exclusao
     * @param null $ativo
     * @param null $carga_horaria
     * @param null $periodo
     * @param null $ref_cod_servidor_funcao
     * @param null $ref_cod_funcionario_vinculo
     * @param null $ano
     * @param null $dataAdmissao
     * @param null $hora_inicial
     * @param null $hora_final
     * @param null $hora_atividade
     * @param null $horas_excedentes
     * @param null $dataSaida
     */
    public function __construct(
        $cod_servidor_alocacao = null,
        $ref_ref_cod_instituicao = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_escola = null,
        $ref_cod_servidor = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $carga_horaria = null,
        $periodo = null,
        $ref_cod_servidor_funcao = null,
        $ref_cod_funcionario_vinculo = null,
        $ano = null,
        $dataAdmissao = null,
        $hora_inicial = null,
        $hora_final = null,
        $hora_atividade = null,
        $horas_excedentes = null,
        $dataSaida = null
    ) {
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'servidor_alocacao';

        $this->_campos_lista = $this->_todos_campos = 'cod_servidor_alocacao, ref_ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, data_cadastro, data_exclusao, ativo, carga_horaria, periodo, ref_cod_servidor_funcao, ref_cod_funcionario_vinculo, ano, data_admissao, hora_inicial, hora_final, hora_atividade, horas_excedentes, data_saida ';

        if (is_numeric($ref_usuario_cad)) {
            $usuario = new clsPmieducarUsuario($ref_usuario_cad);
            if ($usuario->existe()) {
                $this->ref_usuario_cad = $ref_usuario_cad;
            }
        }

        if (is_numeric($ref_usuario_exc)) {
            $usuario = new clsPmieducarUsuario($ref_usuario_exc);
            if ($usuario->existe()) {
                $this->ref_usuario_exc = $ref_usuario_exc;
            }
        }

        if (is_numeric($ref_cod_escola)) {
            $escola = new clsPmieducarEscola($ref_cod_escola);
            if ($escola->existe()) {
                $this->ref_cod_escola = $ref_cod_escola;
            }
        }

        if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
            $servidor = new clsPmieducarServidor(
                $ref_cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                $ref_ref_cod_instituicao
            );

            if ($servidor->existe()) {
                $this->ref_cod_servidor = $ref_cod_servidor;
                $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
            }
        }

        if (is_numeric($cod_servidor_alocacao)) {
            $this->cod_servidor_alocacao = $cod_servidor_alocacao;
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

        if (is_numeric($ref_cod_servidor_funcao)) {
            $this->ref_cod_servidor_funcao = $ref_cod_servidor_funcao;
        }

        if (is_numeric($ref_cod_funcionario_vinculo)) {
            $this->ref_cod_funcionario_vinculo = $ref_cod_funcionario_vinculo;
        }

        // Valida a carga horária
        if (is_string($carga_horaria)) {
            $datetime = explode(':', $carga_horaria);
            $minutos = (((int) $datetime[0]) * 60) + (int) $datetime[1];

            if (self::$cargaHorariaMax * 60 >= $minutos) {
                $this->carga_horaria = $carga_horaria;
            }
        }

        if ($hora_inicial) {
            $this->hora_inicial = $hora_inicial;
        }

        if ($hora_final) {
            $this->hora_final = $hora_final;
        }

        if ($hora_atividade) {
            $this->hora_atividade = $hora_atividade;
        }

        if ($horas_excedentes) {
            $this->horas_excedentes = $horas_excedentes;
        }

        if (is_numeric($periodo)) {
            $this->periodo = $periodo;
        }

        if (is_numeric($ano)) {
            $this->ano = $ano;
        }

        if (is_string($dataAdmissao)) {
            $this->dataAdmissao = $dataAdmissao;
        }

        if (is_string($dataSaida)) {
            $this->dataSaida = $dataSaida;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_ref_cod_instituicao)
            && is_numeric($this->ref_usuario_cad)
            && is_numeric($this->ref_cod_escola)
            && is_numeric($this->ref_cod_servidor)
            && is_string($this->carga_horaria)
            && $this->periodo
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor_funcao)) {
                $campos .= "{$gruda}ref_cod_servidor_funcao";
                $valores .= "{$gruda}'{$this->ref_cod_servidor_funcao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_funcionario_vinculo)) {
                $campos .= "{$gruda}ref_cod_funcionario_vinculo";
                $valores .= "{$gruda}'{$this->ref_cod_funcionario_vinculo}'";
                $gruda = ', ';
            }

            if (is_string($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}'{$this->carga_horaria}'";
                $gruda = ', ';
            }

            if ($this->hora_inicial) {
                $campos .= "{$gruda}hora_inicial";
                $valores .= "{$gruda}'{$this->hora_inicial}'";
                $gruda = ', ';
            }

            if ($this->hora_final) {
                $campos .= "{$gruda}hora_final";
                $valores .= "{$gruda}'{$this->hora_final}'";
                $gruda = ', ';
            }

            if ($this->hora_atividade) {
                $campos .= "{$gruda}hora_atividade";
                $valores .= "{$gruda}'{$this->hora_atividade}'";
                $gruda = ', ';
            }

            if ($this->horas_excedentes) {
                $campos .= "{$gruda}horas_excedentes";
                $valores .= "{$gruda}'{$this->horas_excedentes}'";
                $gruda = ', ';
            }

            if (($this->periodo)) {
                $campos .= "{$gruda}periodo";
                $valores .= "{$gruda}'{$this->periodo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_string($this->dataAdmissao) && !empty($this->dataAdmissao)) {
                $campos .= "{$gruda}data_admissao";
                $valores .= "{$gruda}'{$this->dataAdmissao}'";
                $gruda = ', ';
            }

            if (is_string($this->dataSaida) && !empty($this->dataSaida)) {
                $campos .= "{$gruda}data_saida";
                $valores .= "{$gruda}'{$this->dataSaida}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            return $db->InsertId("{$this->_tabela}_cod_servidor_alocacao_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function edita()
    {
        if (!is_numeric($this->cod_servidor_alocacao) || !is_numeric($this->ref_usuario_exc)) {
            return false;
        }

        $db = new clsBanco();
        $set = '';
        $gruda = '';

        if (is_numeric($this->ref_ref_cod_instituicao)) {
            $set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
            $gruda = ', ';
        }

        if (is_numeric($this->ref_usuario_exc)) {
            $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
            $gruda = ', ';
        }

        if (is_numeric($this->ref_usuario_cad)) {
            $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
            $gruda = ', ';
        }

        if (is_numeric($this->ref_cod_escola)) {
            $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
            $gruda = ', ';
        }

        if (is_numeric($this->ref_cod_servidor)) {
            $set .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
            $gruda = ', ';
        }

        if (is_numeric($this->carga_horaria)) {
            $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
            $gruda = ', ';
        }

        if ($this->hora_inicial) {
            $set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
            $gruda = ', ';
        }

        if ($this->hora_final) {
            $set .= "{$gruda}hora_final = '{$this->hora_final}'";
            $gruda = ', ';
        }

        if ($this->hora_atividade) {
            $set .= "{$gruda}hora_atividade = '{$this->hora_atividade}'";
            $gruda = ', ';
        }

        if ($this->horas_excedentes) {
            $set .= "{$gruda}horas_excedentes = '{$this->horas_excedentes}'";
            $gruda = ', ';
        }

        if (is_numeric($this->ref_cod_servidor_funcao)) {
            $set .= "{$gruda}ref_cod_servidor_funcao = '{$this->ref_cod_servidor_funcao}'";
            $gruda = ', ';
        }

        if (is_numeric($this->ref_cod_funcionario_vinculo)) {
            $set .= "{$gruda}ref_cod_funcionario_vinculo = '{$this->ref_cod_funcionario_vinculo}'";
            $gruda = ', ';
        }

        if (($this->periodo)) {
            $set .= "{$gruda}periodo = '{$this->periodo}'";
            $gruda = ', ';
        }

        if (is_string($this->data_cadastro)) {
            $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
            $gruda = ', ';
        }

        $set .= "{$gruda}data_exclusao = NOW()";
        $gruda = ', ';

        if (is_numeric($this->ativo)) {
            $set .= "{$gruda}ativo = '{$this->ativo}'";
            $gruda = ', ';
        }

        if (is_string($this->dataAdmissao) && !empty($this->dataAdmissao)) {
            $set .= "{$gruda}data_admissao = '{$this->dataAdmissao}'";
        } else {
            $set .= "{$gruda}data_admissao = NULL ";
        }

        if (is_string($this->dataSaida) && !empty($this->dataSaida)) {
            $set .= "{$gruda}data_saida = '{$this->dataSaida}'";
        } else {
            $set .= "{$gruda}data_saida = NULL ";
        }

        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");

        return true;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     *
     * @throws Exception
     */
    public function lista(
        $int_cod_servidor_alocacao = null,
        $int_ref_ref_cod_instituicao = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_servidor = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_carga_horaria = null,
        $int_periodo = null,
        $bool_busca_nome = false,
        $boo_professor = null,
        $ano = null
    ) {
        $filtros = '';
        $whereAnd = ' WHERE ';

        if (is_bool($bool_busca_nome) && $bool_busca_nome == true) {
            $join = ', cadastro.pessoa p ';
            $filtros .= $whereAnd . ' sa.ref_cod_servidor = p.idpes';
            $whereAnd = ' AND ';
            $this->_campos_lista .= ',p.nome';
        }

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} sa{$join}";

        if (is_numeric($int_cod_servidor_alocacao)) {
            $filtros .= "{$whereAnd} sa.cod_servidor_alocacao = '{$int_cod_servidor_alocacao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} sa.ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} sa.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} sa.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} sa.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = sa.ref_cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} sa.ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_carga_horaria)) {
            $filtros .= "{$whereAnd} sa.carga_horaria = '{$int_carga_horaria}'";
            $whereAnd = ' AND ';
        }

        if ($int_periodo) {
            $filtros .= "{$whereAnd} sa.periodo = '{$int_periodo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} sa.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} sa.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} sa.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} sa.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} sa.ano = '{$ano}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} sa.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} sa.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_bool($boo_professor)) {
            $not = $boo_professor ? '=' : '!=';
            $filtros .= "{$whereAnd} EXISTS(SELECT 1 FROM pmieducar.servidor_funcao,pmieducar.funcao WHERE ref_cod_servidor_funcao = cod_funcao AND ref_cod_servidor = sa.ref_cod_servidor AND sa.ref_ref_cod_instituicao = ref_ref_cod_instituicao AND professor $not 1)";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getGroupBy() . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} sa {$join} {$filtros}");

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            if ($countCampos > 1) {
                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;

                continue;
            }

            $resultado[] = $tupla[$this->_campos_lista];
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function listaEscolas($int_ref_ref_cod_instituicao = null)
    {
        if (!is_numeric($int_ref_ref_cod_instituicao)) {
            return false;
        }

        $sql = "SELECT DISTINCT ref_cod_escola FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}' AND ativo = '1'";

        $db = new clsBanco();
        $resultado = [];

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla;
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
     *
     * @throws Exception
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_servidor_alocacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     *
     * @throws Exception
     */
    public function existe()
    {
        if (is_numeric($this->cod_servidor_alocacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function excluir()
    {
        if (is_numeric($this->cod_servidor_alocacao)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");

            return true;
        }

        return false;
    }

    /**
     * Exclui um registro baseado no período da alocação.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function excluir_horario()
    {
        if (is_numeric($this->ref_cod_servidor)
            && is_numeric($this->ref_ref_cod_instituicao)
            && is_numeric($this->ref_cod_escola)
            && is_numeric($this->periodo)
        ) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND periodo = '$this->periodo'");

            return true;
        }

        return false;
    }

    public function excluiAlocacoesServidor($ref_cod_servidor)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$ref_cod_servidor}'");

        return true;
    }

    /**
     * Substitui a alocação entre servidores
     *
     * Substitui a alocação entre servidores, atualizando a tabela
     * pmieducar.servidor_alocacao. A única atualização na tabela ocorre no
     * identificador do servidor, o campo ref_cod_servidor. Para usar este
     * método, um objeto desta classe precisa estar instanciado com os atributos
     * do servidor a ser substituido.
     *
     * @param int $int_ref_cod_servidor_substituto Código do servidor que substituirá o atual
     *
     * @return bool TRUE em caso de sucesso, FALSE caso contrário
     *
     * @throws Exception
     */
    public function substituir_servidor($int_ref_cod_servidor_substituto)
    {
        if (is_numeric($int_ref_cod_servidor_substituto)
            && is_numeric($this->ref_ref_cod_instituicao)
        ) {
            $servidor = new clsPmieducarServidor(
                $int_ref_cod_servidor_substituto,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_ref_cod_instituicao
            );

            if (!$servidor->existe()) {
                return false;
            }
        }

        if (is_numeric($this->ref_cod_servidor)
            && is_numeric($this->ref_ref_cod_instituicao)
            && is_numeric($this->ref_cod_escola)
            && is_numeric($this->periodo)
            && is_string($this->carga_horaria)
        ) {
            $sql = 'UPDATE %s SET ref_cod_servidor=\'%d\' WHERE ref_cod_servidor = \'%d\' ';
            $sql .= 'AND ref_ref_cod_instituicao = \'%d\' AND ref_cod_escola = \'%d\' AND ';
            $sql .= 'carga_horaria = \'%s\' AND periodo = \'%d\'';

            $sql = sprintf(
                $sql,
                $this->_tabela,
                $int_ref_cod_servidor_substituto,
                $this->ref_cod_servidor,
                $this->ref_ref_cod_instituicao,
                $this->ref_cod_escola,
                $this->carga_horaria,
                $this->periodo
            );

            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    /**
     * Define o campo para ser utilizado na agrupação no método Lista().
     */
    public function setGroupby($strNomeCampo)
    {
        if (is_string($strNomeCampo) && $strNomeCampo) {
            $this->_campo_group_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query responsável pelo Agrupamento dos
     * registros.
     *
     * @return string
     */
    public function getGroupBy()
    {
        if (is_string($this->_campo_group_by)) {
            return " GROUP BY {$this->_campo_group_by} ";
        }

        return '';
    }

    /**
     * Retorna a string com a soma da carga horária já alocada do servidor em determinado ano
     *
     * @return string
     *
     * @throws Exception
     */
    public function getCargaHorariaAno()
    {
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ano)) {
            $db = new clsBanco();

            $sql = "SELECT SUM(carga_horaria::interval)
                FROM pmieducar.servidor_alocacao
               WHERE ref_cod_servidor = {$this->ref_cod_servidor}
                 AND ano = {$this->ano}";

            if ($this->cod_servidor_alocacao) {
                $sql .= "AND cod_servidor_alocacao != {$this->cod_servidor_alocacao}";
            }

            $db->Consulta($sql);
            $db->ProximoRegistro();
            $registro = $db->Tupla();

            return $registro[0];
        }

        return '';
    }

    public function periodoAlocado()
    {
        if (is_numeric($this->ref_cod_escola)
            && is_numeric($this->periodo)
            && is_numeric($this->ano)
            && is_numeric($this->ref_cod_servidor)
        ) {
            $db = new clsBanco();

            $sql = "SELECT *
                FROM pmieducar.servidor_alocacao
               WHERE ref_cod_escola = {$this->ref_cod_escola}
                 AND ref_cod_servidor = {$this->ref_cod_servidor}
                 AND ano = {$this->ano}
                 AND periodo = {$this->periodo}
                 AND ativo = 1";

            if (is_numeric($this->cod_servidor_alocacao)) {
                $sql .= " AND cod_servidor_alocacao <> {$this->cod_servidor_alocacao}";
            }

            $db->Consulta($sql);
            $db->ProximoRegistro();
            $registro = $db->Tupla();

            return $registro ? true : false;
        }

        return false;
    }
}
