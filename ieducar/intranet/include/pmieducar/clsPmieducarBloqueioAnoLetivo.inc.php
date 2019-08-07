<?php

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarBloqueioAnoLetivo
{
    public $ref_cod_instituicao;
    public $ref_ano;
    public $data_inicio;
    public $data_fim;

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

    public function __construct($ref_cod_instituicao = null, $ref_ano = null, $data_inicio = null, $data_fim = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}bloqueio_ano_letivo";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_instituicao, ref_ano, data_inicio, data_fim ';

        if (is_numeric($ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
        if (is_numeric($ref_ano)) {
            $this->ref_ano = $ref_ano;
        }
        if (is_string($data_inicio)) {
            $this->data_inicio = $data_inicio;
        }
        if (is_string($data_fim)) {
            $this->data_fim = $data_fim;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_ano) && is_string($this->data_inicio) && is_string($this->data_fim)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ano)) {
                $campos .= "{$gruda}ref_ano";
                $valores .= "{$gruda}'{$this->ref_ano}'";
                $gruda = ', ';
            }
            if (is_string($this->data_inicio)) {
                $campos .= "{$gruda}data_inicio";
                $valores .= "{$gruda}'{$this->data_inicio}'";
                $gruda = ', ';
            }
            if (is_string($this->data_fim)) {
                $campos .= "{$gruda}data_fim";
                $valores .= "{$gruda}'{$this->data_fim}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;
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
        if (is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_ano) && is_string($this->data_inicio) && is_string($this->data_fim)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->data_inicio)) {
                $set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
                $gruda = ', ';
            }
            if (is_string($this->data_fim)) {
                $set .= "{$gruda}data_fim = '{$this->data_fim}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'");

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
    public function lista($ref_cod_instituicao = null, $ref_ano = null)
    {
        $sql = "SELECT {$this->_campos_lista}, instituicao.nm_instituicao as instituicao FROM {$this->_tabela} INNER JOIN pmieducar.instituicao ON (ref_cod_instituicao = cod_instituicao) ";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($ref_ano)) {
            $filtros .= "{$whereAnd} ref_ano = '{$ref_ano}'";
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
        if (is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_ano)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_campos_lista}, instituicao.nm_instituicao as instituicao FROM {$this->_tabela} INNER JOIN pmieducar.instituicao ON (ref_cod_instituicao = cod_instituicao)  WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'");
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
        if (is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_ano)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'");
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
        if (is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_ano)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'");

            return true;
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
