<?php

use iEducar\Legacy\Model;

class clsPmieducarServidorCurso extends Model
{
    public $cod_servidor_curso;
    public $ref_cod_formacao;
    public $data_conclusao;
    public $data_registro;
    public $diplomas_registros;

    public function __construct($cod_servidor_curso = null, $ref_cod_formacao = null, $data_conclusao = null, $data_registro = null, $diplomas_registros = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}servidor_curso";

        $this->_campos_lista = $this->_todos_campos = 'cod_servidor_curso, ref_cod_formacao, data_conclusao, data_registro, diplomas_registros';

        if (is_numeric($ref_cod_formacao)) {
            $this->ref_cod_formacao = $ref_cod_formacao;
        }

        if (is_numeric($cod_servidor_curso)) {
            $this->cod_servidor_curso = $cod_servidor_curso;
        }
        if (is_string($data_conclusao)) {
            $this->data_conclusao = $data_conclusao;
        }
        if (is_string($data_registro)) {
            $this->data_registro = $data_registro;
        }
        if (is_string($diplomas_registros)) {
            $this->diplomas_registros = $diplomas_registros;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_formacao) && is_string($this->data_conclusao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_formacao)) {
                $campos .= "{$gruda}ref_cod_formacao";
                $valores .= "{$gruda}'{$this->ref_cod_formacao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_conclusao)) {
                $campos .= "{$gruda}data_conclusao";
                $valores .= "{$gruda}'{$this->data_conclusao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_registro)) {
                $campos .= "{$gruda}data_registro";
                $valores .= "{$gruda}'{$this->data_registro}'";
                $gruda = ', ';
            }
            if (is_string($this->diplomas_registros)) {
                $campos .= "{$gruda}diplomas_registros";
                $valores .= "{$gruda}'{$this->diplomas_registros}'";
                $gruda = ', ';
            }
            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_servidor_curso_seq");
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
        if (is_numeric($this->cod_servidor_curso)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_cod_formacao)) {
                $set .= "{$gruda}ref_cod_formacao = '{$this->ref_cod_formacao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_conclusao)) {
                $set .= "{$gruda}data_conclusao = '{$this->data_conclusao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_registro)) {
                $set .= "{$gruda}data_registro = '{$this->data_registro}'";
                $gruda = ', ';
            }
            if (is_string($this->diplomas_registros)) {
                $set .= "{$gruda}diplomas_registros = '{$this->diplomas_registros}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'");

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
    public function lista($int_cod_servidor_curso = null, $int_ref_cod_formacao = null, $date_data_conclusao_ini = null, $date_data_conclusao_fim = null, $date_data_registro_ini = null, $date_data_registro_fim = null, $str_diplomas_registros = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_servidor_curso)) {
            $filtros .= "{$whereAnd} cod_servidor_curso = '{$int_cod_servidor_curso}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_formacao)) {
            $filtros .= "{$whereAnd} ref_cod_formacao = '{$int_ref_cod_formacao}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_conclusao_ini)) {
            $filtros .= "{$whereAnd} data_conclusao >= '{$date_data_conclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_conclusao_fim)) {
            $filtros .= "{$whereAnd} data_conclusao <= '{$date_data_conclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_registro_ini)) {
            $filtros .= "{$whereAnd} data_registro >= '{$date_data_registro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_registro_fim)) {
            $filtros .= "{$whereAnd} data_registro <= '{$date_data_registro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_diplomas_registros)) {
            $filtros .= "{$whereAnd} diplomas_registros LIKE '%{$str_diplomas_registros}%'";
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
        if (is_numeric($this->cod_servidor_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif ($this->ref_cod_formacao) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_formacao = '{$this->ref_cod_formacao}'");
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
        if (is_numeric($this->cod_servidor_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'");
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
        if (is_numeric($this->cod_servidor_curso)) {
        }

        return false;
    }
}
