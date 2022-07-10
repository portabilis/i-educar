<?php

use iEducar\Legacy\Model;

class clsPmieducarTipoAvaliacaoValores extends Model
{
    public $ref_cod_tipo_avaliacao;
    public $sequencial;
    public $nome;
    public $valor;
    public $valor_min;
    public $valor_max;
    public $ativo;

    public function __construct($ref_cod_tipo_avaliacao = null, $sequencial = null, $nome = null, $valor = null, $valor_min = null, $valor_max = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}tipo_avaliacao_valores";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_tipo_avaliacao, sequencial, nome, valor, valor_min, valor_max,ativo';

        if (is_numeric($ref_cod_tipo_avaliacao)) {
            $this->ref_cod_tipo_avaliacao = $ref_cod_tipo_avaliacao;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_numeric($valor)) {
            $this->valor = $valor;
        }
        if (is_numeric($valor_min)) {
            $this->valor_min = $valor_min;
        }
        if (is_numeric($valor_max)) {
            $this->valor_max = $valor_max;
        }
        if (!is_null($ativo)) {
            $this->ativo = dbBool($ativo) ? 'true' : 'false';
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_tipo_avaliacao) && is_numeric($this->sequencial) && is_string($this->nome) && is_numeric($this->valor) && is_numeric($this->valor_min) && is_numeric($this->valor_max)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_tipo_avaliacao)) {
                $campos .= "{$gruda}ref_cod_tipo_avaliacao";
                $valores .= "{$gruda}'{$this->ref_cod_tipo_avaliacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}'{$this->nome}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor)) {
                $campos .= "{$gruda}valor";
                $valores .= "{$gruda}'{$this->valor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_min)) {
                $campos .= "{$gruda}valor_min";
                $valores .= "{$gruda}'{$this->valor_min}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_max)) {
                $campos .= "{$gruda}valor_max";
                $valores .= "{$gruda}'{$this->valor_max}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}true";
            $gruda = ', ';

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
        if (is_numeric($this->ref_cod_tipo_avaliacao) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '{$this->nome}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor)) {
                $set .= "{$gruda}valor = '{$this->valor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_min)) {
                $set .= "{$gruda}valor_min = '{$this->valor_min}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_max)) {
                $set .= "{$gruda}valor_max = '{$this->valor_max}'";
                $gruda = ', ';
            }
            if (dbBool($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'");

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
    public function lista($int_ref_cod_tipo_avaliacao = null, $int_sequencial = null, $str_nome = null, $int_valor = null, $int_valor_min = null, $int_valor_max = null, $bool_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_tipo_avaliacao)) {
            $filtros .= "{$whereAnd} ref_cod_tipo_avaliacao = '{$int_ref_cod_tipo_avaliacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor)) {
            $filtros .= "{$whereAnd} valor = '{$int_valor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_min)) {
            $filtros .= "{$whereAnd} valor_min <= '{$int_valor_min}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_max)) {
            $filtros .= "{$whereAnd} valor_max >= '{$int_valor_max}'";
            $whereAnd = ' AND ';
        }

        if (dbBool($bool_ativo)) {
            $bool_ativo = dbBool($bool_ativo) ? 'true' : 'false';
            $filtros .= "{$whereAnd} ativo = $bool_ativo";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = true";
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
        if (is_numeric($this->ref_cod_tipo_avaliacao) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'");
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
        if (is_numeric($this->ref_cod_tipo_avaliacao) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna uma string com o nome da nota em que $nota se encontra, pertencendo essa ao conjunto de notas
     * de $cod_tipo_avaliacao e $sequencial
     *
     * @param int $nota
     *
     * @return string
     */
    public function nomeNota($nota, $cod_tipo_avaliacao)
    {
        if (is_numeric($nota) && is_numeric($cod_tipo_avaliacao)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT nome FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$cod_tipo_avaliacao}' AND valor_min <= '{$nota}' AND valor_max >= '{$nota}' AND ativo = true LIMIT 1");
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
        if (is_numeric($this->ref_cod_tipo_avaliacao) && is_numeric($this->sequencial)) {
            $this->ativo = 'false';
            if ($this->edita()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_tipo_avaliacao)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->_tabela} set ativo = false WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' ");

            return true;
        }

        return false;
    }
}
