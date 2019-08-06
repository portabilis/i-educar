<?php

require_once 'include/pmieducar/geral.inc.php';

class clsModulesNotaExame
{
    public $ref_cod_matricula;
    public $ref_cod_componente_curricular;
    public $nota_exame;

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

    public function __construct($ref_cod_matricula = null, $ref_cod_componente_curricular = null, $nota_exame = null)
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}nota_exame";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_matricula, ref_cod_componente_curricular, nota_exame';

        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }
        if (is_numeric($ref_cod_componente_curricular)) {
            $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
        }
        if (is_numeric($nota_exame)) {
            $this->nota_exame = $nota_exame;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular) && is_numeric($this->nota_exame)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_componente_curricular)) {
                $campos .= "{$gruda}ref_cod_componente_curricular";
                $valores .= "{$gruda}'{$this->ref_cod_componente_curricular}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nota_exame)) {
                $campos .= "{$gruda}nota_exame";
                $valores .= "{$gruda}'{$this->nota_exame}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $this->ref_cod_matricula;
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular) && is_numeric($this->nota_exame)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->nota_exame)) {
                $set .= "{$gruda}nota_exame = '{$this->nota_exame}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");

                return true;
            }
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");

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
