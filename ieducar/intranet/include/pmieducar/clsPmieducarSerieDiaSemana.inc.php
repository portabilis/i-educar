<?php

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarSerieDiaSemana
{
    public $dia_semana;
    public $ref_ref_cod_serie;
    public $ref_ref_cod_escola;

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

    public function __construct($dia_semana = null, $ref_ref_cod_serie = null, $ref_ref_cod_escola = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}serie_dia_semana";

        $this->_campos_lista = $this->_todos_campos = 'dia_semana, ref_ref_cod_serie, ref_ref_cod_escola';

        if (is_numeric($ref_ref_cod_escola) && is_numeric($ref_ref_cod_serie)) {
            if (class_exists('clsPmieducarEscolaSerie')) {
                $tmp_obj = new clsPmieducarEscolaSerie($ref_ref_cod_escola, $ref_ref_cod_serie);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_ref_cod_escola = $ref_ref_cod_escola;
                        $this->ref_ref_cod_serie = $ref_ref_cod_serie;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_ref_cod_escola = $ref_ref_cod_escola;
                        $this->ref_ref_cod_serie = $ref_ref_cod_serie;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola_serie WHERE ref_cod_escola = '{$ref_ref_cod_escola}' AND ref_cod_serie = '{$ref_ref_cod_serie}'")) {
                    $this->ref_ref_cod_escola = $ref_ref_cod_escola;
                    $this->ref_ref_cod_serie = $ref_ref_cod_serie;
                }
            }
        }

        if (is_numeric($dia_semana)) {
            $this->dia_semana = $dia_semana;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->dia_semana) && is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->dia_semana)) {
                $campos .= "{$gruda}dia_semana";
                $valores .= "{$gruda}'{$this->dia_semana}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_serie)) {
                $campos .= "{$gruda}ref_ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_escola)) {
                $campos .= "{$gruda}ref_ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_dia_semana_seq");
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
        if (is_numeric($this->dia_semana)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_ref_cod_serie)) {
                $set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_escola)) {
                $set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE dia_semana = '{$this->dia_semana}'");

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
    public function lista($int_dia_semana = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_dia_semana)) {
            $filtros .= "{$whereAnd} dia_semana = '{$int_dia_semana}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
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
        if (is_numeric($this->dia_semana)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE dia_semana = '{$this->dia_semana}'");
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
        if (is_numeric($this->dia_semana)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE dia_semana = '{$this->dia_semana}'");
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
        if (is_numeric($this->dia_semana)) {
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
