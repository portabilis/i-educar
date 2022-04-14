<?php

use iEducar\Legacy\Model;

class clsModulesMotorista extends Model
{
    public $cod_motorista;
    public $ref_idpes;
    public $cnh;
    public $tipo_cnh;
    public $dt_habilitacao;
    public $vencimento_cnh;
    public $ref_cod_empresa_transporte_escolar;
    public $observacao;

    /**
     * Construtor.
     */
    public function __construct(
        $cod_motorista = null,
        $ref_idpes = null,
        $cnh = null,
        $tipo_cnh = null,
        $dt_habilitacao = null,
        $vencimento_cnh = null,
        $ref_cod_empresa_transporte_escolar = null,
        $observacao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}motorista";

        $this->_campos_lista = $this->_todos_campos = ' cod_motorista, ref_idpes, cnh, tipo_cnh, dt_habilitacao, vencimento_cnh, ref_cod_empresa_transporte_escolar, observacao';

        if (is_numeric($cod_motorista)) {
            $this->cod_motorista = $cod_motorista;
        }

        if (is_numeric($ref_idpes)) {
            $this->ref_idpes = $ref_idpes;
        }

        if (is_string($cnh)) {
            $this->cnh = $cnh;
        }

        if (is_string($tipo_cnh)) {
            $this->tipo_cnh = $tipo_cnh;
        }

        if (is_string($dt_habilitacao)) {
            $this->dt_habilitacao = $dt_habilitacao;
        }

        if (is_string($vencimento_cnh)) {
            $this->vencimento_cnh = $vencimento_cnh;
        }

        if (is_numeric($ref_cod_empresa_transporte_escolar)) {
            $this->ref_cod_empresa_transporte_escolar = $ref_cod_empresa_transporte_escolar;
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
        if (is_numeric($this->ref_cod_empresa_transporte_escolar)
            && is_numeric($this->ref_idpes)) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->cod_motorista)) {
                $campos .= "{$gruda}cod_motorista";
                $valores .= "{$gruda}'{$this->cod_motorista}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_idpes)) {
                $campos .= "{$gruda}ref_idpes";
                $valores .= "{$gruda}'{$this->ref_idpes}'";
                $gruda = ', ';
            }

            if (is_string($this->cnh)) {
                $campos .= "{$gruda}cnh";
                $valores .= "{$gruda}'{$this->cnh}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo_cnh)) {
                $campos .= "{$gruda}tipo_cnh";
                $valores .= "{$gruda}'{$this->tipo_cnh}'";
                $gruda = ', ';
            }

            if (is_string($this->dt_habilitacao) && trim($this->dt_habilitacao) != '') {
                $campos .= "{$gruda}dt_habilitacao";
                $valores .= "{$gruda}'{$this->dt_habilitacao}'";
                $gruda = ', ';
            }

            if (is_string($this->vencimento_cnh) && trim($this->vencimento_cnh) != '') {
                $campos .= "{$gruda}vencimento_cnh";
                $valores .= "{$gruda}'{$this->vencimento_cnh}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
                $campos .= "{$gruda}ref_cod_empresa_transporte_escolar";
                $valores .= "{$gruda}'{$this->ref_cod_empresa_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$observacao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $this->cod_motorista = $db->InsertId("{$this->_tabela}_seq");

            if ($this->cod_motorista) {
                $detalhe = $this->detalhe();
            }

            return $this->cod_motorista;
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
        if (is_numeric($this->cod_motorista)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->cod_motorista)) {
                $set .= "{$gruda}cod_motorista = '{$this->cod_motorista}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_idpes)) {
                $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
                $gruda = ', ';
            }

            if (is_string($this->cnh)) {
                $set .= "{$gruda}cnh = '{$this->cnh}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo_cnh)) {
                $set .= "{$gruda}tipo_cnh = '{$this->tipo_cnh}'";
                $gruda = ', ';
            }

            if (is_string($this->dt_habilitacao) && trim($this->dt_habilitacao) != '') {
                $set .= "{$gruda}dt_habilitacao = '{$this->dt_habilitacao}'";
                $gruda = ', ';
            }

            if (is_string($this->vencimento_cnh) && trim($this->vencimento_cnh) != '') {
                $set .= "{$gruda}vencimento_cnh = '{$this->vencimento_cnh}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
                $set .= "{$gruda}ref_cod_empresa_transporte_escolar = '{$this->ref_cod_empresa_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $set .= "{$gruda}observacao = '{$observacao}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_motorista = '{$this->cod_motorista}'");

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
        $cod_motorista = null,
        $nome_motorista = null,
        $cnh = null,
        $tipo_cnh = null,
        $ref_cod_empresa_transporte_escolar = null,
        $ref_idpes = null
    ) {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista}, (
          SELECT
            nome
          FROM
            modules.empresa_transporte_escolar emp,cadastro.pessoa p
          WHERE
            ref_cod_empresa_transporte_escolar = cod_empresa_transporte_escolar AND p.idpes = emp.ref_idpes
         ) AS nome_empresa , (SELECT nome FROM cadastro.pessoa WHERE idpes = ref_idpes) AS nome_motorista  FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';
        if (is_numeric($cod_motorista)) {
            $filtros .= "{$whereAnd} cod_motorista = '{$cod_motorista}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_idpes)) {
            $filtros .= "{$whereAnd} ref_idpes = '{$ref_idpes}'";
            $whereAnd = ' AND ';
        }

        if (is_string($nome_motorista)) {
            $nm_motorista = $db->escapeString($nome_motorista);
            $filtros .= "
        {$whereAnd} exists(SELECT 1 FROM cadastro.pessoa WHERE unaccent(nome) ILIKE unaccent('%{$nm_motorista}%'))";

            $whereAnd = ' AND ';
        }

        if (is_string($cnh)) {
            $filtros .= "{$whereAnd} cnh = '{$cnh}'";
            $whereAnd = ' AND ';
        }

        if (is_string($tipo_cnh)) {
            $filtros .= "{$whereAnd} tipo_cnh = '{$tipo_cnh}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_empresa_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_empresa_transporte_escolar = '{$ref_cod_empresa_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

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
        if (is_numeric($this->cod_motorista)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos}, (
          SELECT
            nome
          FROM
            modules.empresa_transporte_escolar emp,cadastro.pessoa p
          WHERE
            ref_cod_empresa_transporte_escolar = cod_empresa_transporte_escolar AND p.idpes = emp.ref_idpes
         ) AS nome_empresa , (SELECT nome FROM cadastro.pessoa WHERE idpes = ref_idpes) AS nome_motorista  FROM {$this->_tabela} WHERE cod_motorista = '{$this->cod_motorista}'");
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
        if (is_numeric($this->cod_motorista)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_motorista = '{$this->cod_motorista}'");
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
        if (is_numeric($this->cod_motorista)) {
            $detalhe = $this->detalhe();

            $sql = "DELETE FROM {$this->_tabela} WHERE cod_motorista = '{$this->cod_motorista}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
