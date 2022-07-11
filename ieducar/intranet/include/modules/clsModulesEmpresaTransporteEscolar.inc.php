<?php

use iEducar\Legacy\Model;

class clsModulesEmpresaTransporteEscolar extends Model
{
    public $cod_empresa_transporte_escolar;
    public $ref_idpes;
    public $ref_resp_idpes;
    public $observacao;

    public function __construct(
        $cod_empresa_transporte_escolar = null,
        $ref_idpes = null,
        $ref_resp_idpes = null,
        $observacao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}empresa_transporte_escolar";

        $this->_campos_lista = $this->_todos_campos = ' cod_empresa_transporte_escolar, ref_idpes, ref_resp_idpes, observacao ';

        if (is_numeric($cod_empresa_transporte_escolar)) {
            $this->cod_empresa_transporte_escolar = $cod_empresa_transporte_escolar;
        }

        if (is_numeric($ref_idpes)) {
            $this->ref_idpes = $ref_idpes;
        }

        if (is_numeric($ref_resp_idpes)) {
            $this->ref_resp_idpes = $ref_resp_idpes;
        }

        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_resp_idpes)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_idpes)) {
                $campos .= "{$gruda}ref_idpes";
                $valores .= "{$gruda}'{$this->ref_idpes}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_resp_idpes)) {
                $campos .= "{$gruda}ref_resp_idpes";
                $valores .= "{$gruda}'{$this->ref_resp_idpes}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$observacao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $this->cod_empresa_transporte_escolar = $db->InsertId("{$this->_tabela}_seq");

            if ($this->cod_empresa_transporte_escolar) {
                $detalhe = $this->detalhe();
            }

            return $this->cod_empresa_transporte_escolar;
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
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_idpes)) {
                $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_resp_idpes)) {
                $set .= "{$gruda}ref_resp_idpes = '{$this->ref_resp_idpes}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $set .= "{$gruda}observacao = '{$observacao}'";
                $gruda = ', ';
            }
            if ($set) {
                $this->detalhe();
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
    public function lista(
        $cod_empresa_transporte_escolar = null,
        $ref_idpes = null,
        $ref_resp_idpes = null,
        $nm_idpes = null,
        $nm_resp_idpes = null
    ) {
        $db = new clsBanco();
        $sql = "SELECT {$this->_campos_lista}, (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes
         ) AS nome_empresa , (SELECT nome FROM cadastro.pessoa WHERE idpes = ref_resp_idpes) AS nome_responsavel, (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes
         ) AS nome_empresa , (SELECT '(' || ddd || ')' || fone  FROM cadastro.fone_pessoa WHERE idpes = ref_idpes limit 1) AS telefone  FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_empresa_transporte_escolar)) {
            $filtros .= "{$whereAnd} cod_empresa_transporte_escolar = '{$cod_empresa_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_idpes)) {
            $filtros .= "{$whereAnd} ref_idpes = '{$ref_idpes}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_resp_idpes)) {
            $filtros .= "{$whereAnd} ref_resp_idpes = '{$ref_resp_idpes}'";
            $whereAnd = ' AND ';
        }

        if (is_string($nm_idpes)) {
            $nome_idpes = $db->escapeString($nm_idpes);
            $filtros .= "
        {$whereAnd} exists (
          SELECT
            1
          FROM
            cadastro.pessoa
          WHERE
            cadastro.pessoa.idpes = ref_idpes
            AND unaccent(nome) ILIKE unaccent('%{$nome_idpes}%')
        )";

            $whereAnd = ' AND ';
        }

        if (is_string($nm_resp_idpes)) {
            $nome_resp_idpes = $db->escapeString($nm_resp_idpes);
            $filtros .= "{$whereAnd} exists(SELECT 1 FROM cadastro.pessoa WHERE unaccent(nome) ILIKE unaccent('%{$nome_resp_idpes}%'))";
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
            $detalhe = $this->detalhe();

            $sql = "DELETE FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
