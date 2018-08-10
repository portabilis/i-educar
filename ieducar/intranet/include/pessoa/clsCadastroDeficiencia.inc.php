<?php
class clsCadastroDeficiencia
{
    public $cod_deficiencia;
    public $nm_deficiencia;
    public $deficiencia_educacenso;
    public $desconsidera_regra_diferenciada;

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

    /**
     * Construtor.
     */
    public function __construct($cod_deficiencia = null, $nm_deficiencia = null, $deficiencia_educacenso = null, $desconsidera_regra_diferenciada = null)
    {
        $db = new clsBanco();
        $this->_schema = 'cadastro.';
        $this->_tabela = "{$this->_schema}deficiencia";

        $this->_campos_lista = $this->_todos_campos = 'cod_deficiencia, nm_deficiencia, deficiencia_educacenso, desconsidera_regra_diferenciada ';

        if (is_numeric($cod_deficiencia)) {
            $this->cod_deficiencia = $cod_deficiencia;
        }

        if (is_string($nm_deficiencia)) {
            $this->nm_deficiencia = $nm_deficiencia;
        }

        if (is_numeric($deficiencia_educacenso)) {
            $this->deficiencia_educacenso = $deficiencia_educacenso;
        }

        if (is_bool($desconsidera_regra_diferenciada)) {
            $this->desconsidera_regra_diferenciada = $desconsidera_regra_diferenciada;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->nm_deficiencia)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->cod_deficiencia)) {
                $campos .= "{$gruda}cod_deficiencia";
                $valores .= "{$gruda}'{$this->cod_deficiencia}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_deficiencia)) {
                $campos .= "{$gruda}nm_deficiencia";
                $valores .= "{$gruda}'{$this->nm_deficiencia}'";
                $gruda = ', ';
            }

            if (is_numeric($this->deficiencia_educacenso)) {
                $campos .= "{$gruda}deficiencia_educacenso";
                $valores .= "{$gruda}'{$this->deficiencia_educacenso}'";
                $gruda = ', ';
            }

            if (is_bool($this->desconsidera_regra_diferenciada)) {
                $desconsidera_regra_diferenciada = $this->desconsidera_regra_diferenciada ? 'true' : 'false';
                $campos .= "{$gruda}desconsidera_regra_diferenciada";
                $valores .= "{$gruda}'{$desconsidera_regra_diferenciada}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_deficiencia_seq");
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
        if (is_numeric($this->cod_deficiencia)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->nm_deficiencia)) {
                $set .= "{$gruda}nm_deficiencia = '{$this->nm_deficiencia}'";
                $gruda = ', ';
            }

            if (is_numeric($this->deficiencia_educacenso)) {
                $set .= "{$gruda}deficiencia_educacenso = '{$this->deficiencia_educacenso}'";
                $gruda = ', ';
            }

            if (is_bool($this->desconsidera_regra_diferenciada)) {
                $desconsidera_regra_diferenciada = $this->desconsidera_regra_diferenciada ? 'true' : 'false';
                $set .= "{$gruda}desconsidera_regra_diferenciada = '{$desconsidera_regra_diferenciada}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_deficiencia = '{$this->cod_deficiencia}'");

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
    public function lista($int_cod_deficiencia = null, $str_nm_deficiencia = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_deficiencia)) {
            $filtros .= "{$whereAnd} cod_deficiencia = '{$int_cod_deficiencia}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_deficiencia)) {
            $filtros .= "{$whereAnd} nm_deficiencia ILIKE '%{$str_nm_deficiencia}%'";
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_deficiencia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
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
        if (is_numeric($this->cod_deficiencia)) {
            $this->excluiVinculosDeficiencia($this->cod_deficiencia);
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_deficiencia = '{$this->cod_deficiencia}'");

            return true;
        }

        return false;
    }

    public function excluiVinculosDeficiencia($deficienciaId)
    {
        $db = new clsBanco();
        $db->Consulta("  DELETE FROM cadastro.fisica_deficiencia WHERE ref_cod_deficiencia = {$deficienciaId};");

        return true;
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
