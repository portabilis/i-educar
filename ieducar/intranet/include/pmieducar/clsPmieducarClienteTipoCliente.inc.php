<?php

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarClienteTipoCliente
{
    public $ref_cod_cliente_tipo;
    public $ref_cod_cliente;
    public $data_cadastro;
    public $data_exclusao;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ativo;
    public $ref_cod_biblioteca;

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

    public function __construct(
        $ref_cod_cliente_tipo = null,
        $ref_cod_cliente = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ref_usuario_cad = null,
        $ref_usuario_exc = null,
        $ativo = 1,
        $ref_cod_biblioteca = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}cliente_tipo_cliente";

        $this->_campos_lista = $this->_todos_campos = 'ctc.ref_cod_cliente_tipo, ctc.ref_cod_cliente, ctc.data_cadastro, ctc.data_exclusao, ctc.ref_usuario_cad, ctc.ref_usuario_exc, ctc.ativo';

        if (is_numeric($ref_cod_cliente_tipo)) {
            if (class_exists('clsPmieducarClienteTipo')) {
                $tmp_obj = new clsPmieducarClienteTipo($ref_cod_cliente_tipo);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.cliente_tipo WHERE cod_cliente_tipo = '{$ref_cod_cliente_tipo}'")) {
                    $this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
                }
            }
        }

        if (is_numeric($ref_cod_cliente)) {
            if (class_exists('clsPmieducarCliente')) {
                $tmp_obj = new clsPmieducarCliente($ref_cod_cliente);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_cliente = $ref_cod_cliente;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_cliente = $ref_cod_cliente;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.cliente WHERE cod_cliente = '{$ref_cod_cliente}'")) {
                    $this->ref_cod_cliente = $ref_cod_cliente;
                }
            }
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_usuario_cad)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_cliente_tipo)) {
                $campos .= "{$gruda}ref_cod_cliente_tipo";
                $valores .= "{$gruda}'{$this->ref_cod_cliente_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $campos .= "{$gruda}ref_cod_cliente";
                $valores .= "{$gruda}'{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}1";
            $gruda = ', ';
            $sql = "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )";
            $db->Consulta($sql);

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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ativo)) {
            $db = new clsBanco();
            $set = '';

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_cliente = '{$this->ref_cod_cliente}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Edita o tipo do cliente
     *
     * @return bool
     */
    public function trocaTipo()
    {
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ativo)) {
            $db = new clsBanco();
            $set = '';

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_cliente_tipo)) {
                $set .= "{$gruda}ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_cliente = '{$this->ref_cod_cliente}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Recebe valor antigo para utilizar na cláusula WHERE e atualiza o registro com os novos dados,
     * vindos dos atributos.
     */
    public function trocaTipoBiblioteca($ref_cod_biblioteca_atual)
    {
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_usuario_exc) && is_numeric($this->ativo) && is_numeric($this->ref_cod_biblioteca) && $ref_cod_biblioteca_atual) {
            $db = new clsBanco();
            $set = '';

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_cliente_tipo)) {
                $set .= "{$gruda}ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_biblioteca)) {
                $set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_biblioteca = {$ref_cod_biblioteca_atual}");

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
    public function lista($int_ref_cod_cliente_tipo = null, $int_ref_cod_cliente = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ref_usuario_cad = null, $int_ref_usuario_exc = null, $int_ref_cod_biblioteca = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ctc, {$this->_schema}cliente_tipo ct";

        $whereAnd = ' AND ';
        $filtros = ' WHERE ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo';

        if (is_numeric($int_ref_cod_cliente_tipo)) {
            $filtros .= "{$whereAnd} ctc.ref_cod_cliente_tipo = '{$int_ref_cod_cliente_tipo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_cliente)) {
            $filtros .= "{$whereAnd} ctc.ref_cod_cliente = '{$int_ref_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} ctc.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} ctc.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} ctc.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} ctc.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ctc.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ctc.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ct.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} ctc.ativo = '{$int_ativo}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ctc, {$this->_schema}cliente_tipo ct {$filtros}");

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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ctc WHERE ctc.ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ctc.ref_cod_cliente = '{$this->ref_cod_cliente}'");
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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_cliente = '{$this->ref_cod_cliente}'");
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
    public function existeCliente()
    {
        if (is_numeric($this->ref_cod_cliente)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_cliente = '{$this->ref_cod_cliente}'");
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
    public function existeClienteBiblioteca($ref_cod_biblioteca_atual)
    {
        if (is_numeric($this->ref_cod_cliente) && is_numeric($ref_cod_biblioteca_atual)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_cliente = '{$this->ref_cod_cliente}' AND ref_cod_biblioteca = {$ref_cod_biblioteca_atual}");
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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_cliente)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_cliente = '{$this->ref_cod_cliente}'");

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
