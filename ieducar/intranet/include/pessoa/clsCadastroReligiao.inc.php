<?php

class clsCadastroReligiao
{
    public $cod_religiao;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_religiao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    /**
     * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
     *
     * @var int
     */
    public $_total;

    /**
     * Nome do schema
     *
     * @var string
     */
    public $_schema;

    /**
     * Nome da tabela
     *
     * @var string
     */
    public $_tabela;

    /**
     * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
     *
     * @var string
     */
    public $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
     *
     * @var string
     */
    public $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo metodo lista
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no metodo lista
     *
     * @var int
     */
    public $_limite_offset;

    /**
     * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
     *
     * @var string
     */
    public $_campo_order_by;

    /**
     * Construtor (PHP 4)
     *
     * @param integer cod_religiao
     * @param integer idpes_exc
     * @param integer idpes_cad
     * @param string nm_religiao
     * @param string data_cadastro
     * @param string data_exclusao
     * @param bool ativo
     *
     * @return object
     */
    public function __construct($cod_religiao = null, $idpes_exc = null, $idpes_cad = null, $nm_religiao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'cadastro.';
        $this->_tabela = "{$this->_schema}religiao";

        $this->_campos_lista = $this->_todos_campos = 'cod_religiao, idpes_exc, idpes_cad, nm_religiao, data_cadastro, data_exclusao, ativo';

        if (is_numeric($idpes_exc)) {
            if (class_exists('clsCadastroFisica')) {
                $tmp_obj = new clsCadastroFisica($idpes_exc);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->idpes_exc = $idpes_exc;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->idpes_exc = $idpes_exc;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM cadastro.fisica WHERE idpes = '{$idpes_exc}'")) {
                    $this->idpes_exc = $idpes_exc;
                }
            }
        }
        if (is_numeric($idpes_cad)) {
            if (class_exists('clsCadastroFisica')) {
                $tmp_obj = new clsCadastroFisica($idpes_cad);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->idpes_cad = $idpes_cad;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->idpes_cad = $idpes_cad;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM cadastro.fisica WHERE idpes = '{$idpes_cad}'")) {
                    $this->idpes_cad = $idpes_cad;
                }
            }
        }

        if (is_numeric($cod_religiao)) {
            $this->cod_religiao = $cod_religiao;
        }
        if (is_string($nm_religiao)) {
            $this->nm_religiao = $nm_religiao;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (! is_null($ativo)) {
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
        if (is_numeric($this->idpes_cad) && is_string($this->nm_religiao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->idpes_exc)) {
                $campos .= "{$gruda}idpes_exc";
                $valores .= "{$gruda}'{$this->idpes_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $campos .= "{$gruda}idpes_cad";
                $valores .= "{$gruda}'{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_religiao)) {
                $campos .= "{$gruda}nm_religiao";
                $valores .= "{$gruda}'{$this->nm_religiao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_religiao_seq");
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
        if (is_numeric($this->cod_religiao)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->idpes_exc)) {
                $set .= "{$gruda}idpes_exc = '{$this->idpes_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_religiao)) {
                $set .= "{$gruda}nm_religiao = '{$this->nm_religiao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (! is_null($this->ativo)) {
                $val = dbBool($this->ativo) ? 'TRUE': 'FALSE';
                $set .= "{$gruda}ativo = {$val}";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_religiao = '{$this->cod_religiao}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_idpes_exc
     * @param integer int_idpes_cad
     * @param string str_nm_religiao
     * @param string date_data_cadastro_ini
     * @param string date_data_cadastro_fim
     * @param string date_data_exclusao_ini
     * @param string date_data_exclusao_fim
     * @param bool bool_ativo
     *
     * @return array
     */
    public function lista($int_idpes_exc = null, $int_idpes_cad = null, $str_nm_religiao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $bool_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_religiao)) {
            $filtros .= "{$whereAnd} cod_religiao = '{$int_cod_religiao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_exc)) {
            $filtros .= "{$whereAnd} idpes_exc = '{$int_idpes_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_religiao)) {
            $filtros .= "{$whereAnd} nm_religiao LIKE '%{$str_nm_religiao}%'";
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
        if (! is_null($bool_ativo)) {
            if (dbBool($bool_ativo)) {
                $filtros .= "{$whereAnd} ativo = TRUE";
            } else {
                $filtros .= "{$whereAnd} ativo = FALSE";
            }
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
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
        if (is_numeric($this->cod_religiao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_religiao = '{$this->cod_religiao}'");
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
        if (is_numeric($this->cod_religiao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_religiao = '{$this->cod_religiao}'");
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
        if (is_numeric($this->cod_religiao)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Define quais campos da tabela serao selecionados na invocacao do metodo lista
     *
     * @return null
     */
    public function setCamposLista($str_campos)
    {
        $this->_campos_lista = $str_campos;
    }

    /**
     * Define que o metodo Lista devera retornoar todos os campos da tabela
     *
     * @return null
     */
    public function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    /**
     * Define limites de retorno para o metodo lista
     *
     * @return null
     */
    public function setLimite($intLimiteQtd, $intLimiteOffset = null)
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query resposavel pelo Limite de registros
     *
     * @return string
     */
    public function getLimite()
    {
        if (is_numeric($this->_limite_quantidade)) {
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if (is_numeric($this->_limite_offset)) {
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }

            return $retorno;
        }

        return '';
    }

    /**
     * Define campo para ser utilizado como ordenacao no metolo lista
     *
     * @return null
     */
    public function setOrderby($strNomeCampo)
    {
        if (is_string($strNomeCampo) && $strNomeCampo) {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
     *
     * @return string
     */
    public function getOrderby()
    {
        if (is_string($this->_campo_order_by)) {
            return " ORDER BY {$this->_campo_order_by} ";
        }

        return '';
    }
}
