<?php

use iEducar\Legacy\Model;

class clsPmieducarAlunoHistoricoAlturaPeso extends Model
{
    public $ref_cod_aluno;
    public $data_historico;
    public $altura;
    public $peso;

    /**
     * Constructor
     */
    public function __construct(
        $ref_cod_aluno = null,
        $data_historico = null,
        $altura = null,
        $peso = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}aluno_historico_altura_peso";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_aluno, data_historico, altura, peso ';

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }
        if (is_string($data_historico)) {
            $this->data_historico = $data_historico;
        }
        if (is_numeric($altura)) {
            $this->altura = $altura;
        }
        if (is_numeric($peso)) {
            $this->peso = $peso;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}ref_cod_aluno";
            $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
            $gruda = ', ';

            if (is_string($this->data_historico)) {
                $campos .= "{$gruda}data_historico";
                $valores .= "{$gruda}'{$this->data_historico}'";
                $gruda = ', ';
            }
            if (is_numeric($this->altura)) {
                $campos .= "{$gruda}altura";
                $valores .= "{$gruda}'{$this->altura}'";
                $gruda = ', ';
            }
            if (is_numeric($this->peso)) {
                $campos .= "{$gruda}peso";
                $valores .= "{$gruda}'{$this->peso}'";
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_string($this->data_historico)) {
                $set .= "{$gruda}data_historico = '{$this->data_historico}'";
                $gruda = ', ';
            }
            if (is_numeric($this->altura)) {
                $set .= "{$gruda}altura = '{$this->altura}'";
                $gruda = ', ';
            }
            if (is_numeric($this->peso)) {
                $set .= "{$gruda}peso = '{$this->peso}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");

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
    public function lista($ref_cod_aluno = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ref_cod_aluno)) {
            $filtros .= "{$whereAnd} ref_co$ref_cod_aluno = '{$ref_cod_aluno}'";
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");

            return true;
        }

        return false;
    }
}
