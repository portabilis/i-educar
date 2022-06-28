<?php

use iEducar\Legacy\Model;

class clsPmieducarBiblioteca extends Model
{
    public $cod_biblioteca;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $nm_biblioteca;
    public $valor_multa;
    public $max_emprestimo;
    public $valor_maximo_multa;
    public $data_cadastro;
    public $data_exclusao;
    public $requisita_senha;
    public $ativo;
    public $dias_espera;
    public $tombo_automatico;
    public $bloqueia_emprestimo_em_atraso;
    public $codUsuario;

    /**
     * Construtor (PHP 4)
     *
     * @return object
     */
    public function __construct(
        $cod_biblioteca = null,
        $ref_cod_instituicao = null,
        $ref_cod_escola = null,
        $nm_biblioteca = null,
        $valor_multa = null,
        $max_emprestimo = null,
        $valor_maximo_multa = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $requisita_senha = null,
        $ativo = null,
        $dias_espera = null,
        $tombo_automatico = null,
        $bloqueia_emprestimo_em_atraso = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}biblioteca";

        $this->_campos_lista = $this->_todos_campos = 'cod_biblioteca,
                                                       ref_cod_instituicao,
                                                                                                     ref_cod_escola,
                                                                                                     nm_biblioteca,
                                                                                                     valor_multa,
                                                                                                     max_emprestimo,
                                                                                                     valor_maximo_multa,
                                                                                                     data_cadastro,
                                                                                                     data_exclusao,
                                                                                                     requisita_senha,
                                                                                                     ativo,
                                                                                                     dias_espera,
                                                                                                     tombo_automatico,
                                                                                                     bloqueia_emprestimo_em_atraso';

        if (is_numeric($ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }

        if (is_numeric($cod_biblioteca)) {
            $this->cod_biblioteca = $cod_biblioteca;
        }
        if (is_string($nm_biblioteca)) {
            $this->nm_biblioteca = $nm_biblioteca;
        }
        if (is_numeric($valor_multa) || $valor_multa == 'NULL') {
            $this->valor_multa = $valor_multa;
        }
        if (is_numeric($max_emprestimo) || $max_emprestimo == 'NULL') {
            $this->max_emprestimo = $max_emprestimo;
        }
        if (is_numeric($valor_maximo_multa) || $valor_maximo_multa == 'NULL') {
            $this->valor_maximo_multa = $valor_maximo_multa;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($requisita_senha) || $requisita_senha == 'NULL') {
            $this->requisita_senha = $requisita_senha;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
        if (is_numeric($dias_espera) || $dias_espera == 'NULL') {
            $this->dias_espera = $dias_espera;
        }
        if (!is_null($tombo_automatico)) {
            $this->tombo_automatico = $tombo_automatico;
        }
        if (is_bool($bloqueia_emprestimo_em_atraso)) {
            $this->bloqueia_emprestimo_em_atraso = $bloqueia_emprestimo_em_atraso;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->nm_biblioteca)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_biblioteca)) {
                $nm_biblioteca = $db->escapeString($this->nm_biblioteca);
                $campos .= "{$gruda}nm_biblioteca";
                $valores .= "{$gruda}'{$nm_biblioteca}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_multa)) {
                $campos .= "{$gruda}valor_multa";
                $valores .= "{$gruda}'{$this->valor_multa}'";
                $gruda = ', ';
            }
            if (is_numeric($this->max_emprestimo)) {
                $campos .= "{$gruda}max_emprestimo";
                $valores .= "{$gruda}'{$this->max_emprestimo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_maximo_multa)) {
                $campos .= "{$gruda}valor_maximo_multa";
                $valores .= "{$gruda}'{$this->valor_maximo_multa}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_numeric($this->requisita_senha)) {
                $campos .= "{$gruda}requisita_senha";
                $valores .= "{$gruda}'{$this->requisita_senha}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->dias_espera)) {
                $campos .= "{$gruda}dias_espera";
                $valores .= "{$gruda}'{$this->dias_espera}'";
                $gruda = ', ';
            }
            if (!is_null($this->tombo_automatico)) {
                $campos .= "{$gruda}tombo_automatico";// = {$this->tombo_automatico}";
                $aux = dbBool($this->tombo_automatico) ? 'TRUE' : 'FALSE';
                $valores .= "{$gruda}{$aux}";
                $gruda = ', ';
            }
            if (is_bool($this->bloqueia_emprestimo_em_atraso)) {
                $campos .= "{$gruda}bloqueia_emprestimo_em_atraso";
                $aux = dbBool($this->bloqueia_emprestimo_em_atraso) ? 'TRUE' : 'FALSE';
                $valores .= "{$gruda}{$aux}";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_biblioteca_seq");
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
        if (is_numeric($this->cod_biblioteca)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_cod_instituicao)) {
                $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_biblioteca)) {
                $nm_biblioteca = $db->escapeString($this->nm_biblioteca);
                $set .= "{$gruda}nm_biblioteca = '{$nm_biblioteca}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_multa)) {
                $set .= "{$gruda}valor_multa = '{$this->valor_multa}'";
                $gruda = ', ';
            } elseif ($this->valor_multa == 'NULL') {
                $set .= "{$gruda}valor_multa = {$this->valor_multa}";
                $gruda = ', ';
            }
            if (is_numeric($this->max_emprestimo)) {
                $set .= "{$gruda}max_emprestimo = '{$this->max_emprestimo}'";
                $gruda = ', ';
            } elseif ($this->max_emprestimo == 'NULL') {
                $set .= "{$gruda}max_emprestimo = {$this->max_emprestimo}";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_maximo_multa)) {
                $set .= "{$gruda}valor_maximo_multa = '{$this->valor_maximo_multa}'";
                $gruda = ', ';
            } elseif ($this->valor_maximo_multa == 'NULL') {
                $set .= "{$gruda}valor_maximo_multa = {$this->valor_maximo_multa}";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->requisita_senha)) {
                $set .= "{$gruda}requisita_senha = '{$this->requisita_senha}'";
                $gruda = ', ';
            } elseif ($this->requisita_senha == 'NULL') {
                $set .= "{$gruda}requisita_senha = {$this->requisita_senha}";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dias_espera)) {
                $set .= "{$gruda}dias_espera = '{$this->dias_espera}'";
                $gruda = ', ';
            } elseif ($this->dias_espera == 'NULL') {
                $set .= "{$gruda}dias_espera = {$this->dias_espera}";
                $gruda = ', ';
            }
            if (!is_null($this->tombo_automatico)) {
                $aux = dbBool($this->tombo_automatico) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}tombo_automatico = {$aux}";
                $gruda = ', ';
            }
            if (is_bool($this->bloqueia_emprestimo_em_atraso)) {
                $aux = dbBool($this->bloqueia_emprestimo_em_atraso) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}bloqueia_emprestimo_em_atraso = {$aux}";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_biblioteca = '{$this->cod_biblioteca}'");

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
    public function lista($int_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $str_nm_biblioteca = null, $int_valor_multa = null, $int_max_emprestimo = null, $int_valor_maximo_multa = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_requisita_senha = null, $int_ativo = null, $int_dias_espera = null, $in_biblioteca = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_biblioteca)) {
            $filtros .= "{$whereAnd} cod_biblioteca = '{$int_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                              WHERE escola_usuario.ref_cod_escola = biblioteca.ref_cod_escola
                                                AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_biblioteca)) {
            $str_nome_biblioteca = $db->escapeString($str_nm_biblioteca);
            $filtros .= "{$whereAnd} nm_biblioteca LIKE '%{$str_nome_biblioteca}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_multa)) {
            $filtros .= "{$whereAnd} valor_multa = '{$int_valor_multa}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_max_emprestimo)) {
            $filtros .= "{$whereAnd} max_emprestimo = '{$int_max_emprestimo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_maximo_multa)) {
            $filtros .= "{$whereAnd} valor_maximo_multa = '{$int_valor_maximo_multa}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_requisita_senha)) {
            $filtros .= "{$whereAnd} requisita_senha = '{$int_requisita_senha}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dias_espera)) {
            $filtros .= "{$whereAnd} dias_espera = '{$int_dias_espera}'";
            $whereAnd = ' AND ';
        }

        if (!empty($in_biblioteca)) {
            $filtros .= "{$whereAnd} cod_biblioteca in ($in_biblioteca)";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
        if (is_numeric($this->cod_biblioteca)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_biblioteca = '{$this->cod_biblioteca}'");
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
        if (is_numeric($this->cod_biblioteca)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_biblioteca = '{$this->cod_biblioteca}'");
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
        if (is_numeric($this->cod_biblioteca)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
