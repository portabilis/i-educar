<?php

require_once 'include/pmieducar/geral.inc.php';

class clsModulesTipoVeiculo
{
    public $cod_tipo_veiculo;
    public $descricao;

    /**
     * Armazena o total de resultados obtidos na última chamada ao método lista().
     *
     * @var int
     */
    public $_total;

    /**
     * Nome do schema.
     *
     * @var string
     */
    public $_schema;

    /**
     * Nome da tabela.
     *
     * @var string
     */
    public $_tabela;

    /**
     * Lista separada por vírgula, com os campos que devem ser selecionados na
     * próxima chamado ao método lista().
     *
     * @var string
     */
    public $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por vírgula, padrão para
     * seleção no método lista.
     *
     * @var string
     */
    public $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo método lista().
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no método lista().
     *
     * @var int
     */
    public $_limite_offset;

    /**
     * Define o campo para ser usado como padrão de ordenação no método lista().
     *
     * @var string
     */
    public $_campo_order_by;

    public function __construct($cod_tipo_veiculo = null, $descricao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}tipo_veiculo";

        $this->_campos_lista = $this->_todos_campos = ' cod_tipo_veiculo, descricao';

        if (is_numeric($cod_tipo_veiculo)) {
            $this->cod_tipo_veiculo = $cod_tipo_veiculo;
        }

        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->descricao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}descricao";
            $valores .= "{$gruda}'{$this->descricao}'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_seq");
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
        if (is_string($this->descricao)) {
            $db = new clsBanco();
            $set = '';

            $set .= "descricao = '{$this->descricao}'";

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'");

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
    public function lista($cod_tipo_veiculo = null, $descricao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_tipo_veiculo)) {
            $filtros .= "{$whereAnd} cod_tipo_veiculo = '{$cod_tipo_veiculo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($descricao)) {
            $filtros .= "{$whereAnd} (LOWER(descricao)) LIKE (LOWER('%{$descricao}%'))";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos}, (
              SELECT
                nome
              FROM
                cadastro.pessoa
              WHERE
                idpes = ref_idpes
             ) AS nome_empresa , (SELECT nome FROM cadastro.pessoa WHERE idpes = ref_resp_idpes) AS nome_responsavel FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'");
            $db->ProximoRegistro();

            return $db->Tupla();
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
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'");
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
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    /**
     * Define quais campos da tabela serão selecionados no método Lista().
     */
    public function setCamposLista($str_campos)
    {
        $this->_campos_lista = $str_campos;
    }

    /**
     * Define que o método Lista() deverpa retornar todos os campos da tabela.
     */
    public function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    /**
     * Define limites de retorno para o método Lista().
     */
    public function setLimite($intLimiteQtd, $intLimiteOffset = null)
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query responsável pelo limite de
     * registros retornados/afetados.
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
     * Define o campo para ser utilizado como ordenação no método Lista().
     */
    public function setOrderby($strNomeCampo)
    {
        if (is_string($strNomeCampo) && $strNomeCampo) {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query responsável pela Ordenação dos
     * registros.
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
