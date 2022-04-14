<?php

use iEducar\Legacy\Model;

class clsPmieducarBloqueioLancamentoFaltasNotas extends Model
{
    public $cod_bloqueio;
    public $ano;
    public $ref_cod_escola;
    public $etapa;
    public $data_inicio;
    public $data_fim;

    public function __construct(
        $cod_bloqueio = null,
        $ano = null,
        $ref_cod_escola = null,
        $etapa = null,
        $data_inicio = null,
        $data_fim = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'bloqueio_lancamento_faltas_notas';

        $this->_campos_lista = $this->_todos_campos = ' cod_bloqueio, ano, ref_cod_escola, etapa, data_inicio, data_fim ';

        if (is_numeric($cod_bloqueio)) {
            $this->cod_bloqueio = $cod_bloqueio;
        }
        if (is_numeric($ano)) {
            $this->ano = $ano;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($etapa)) {
            $this->etapa = $etapa;
        }
        if (is_string($data_inicio)) {
            $this->data_inicio = $data_inicio;
        }
        if (is_string($data_fim)) {
            $this->data_fim = $data_fim;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ano) && is_numeric($this->ref_cod_escola) && is_numeric($this->etapa) &&
            is_string($this->data_inicio) && is_string($this->data_fim)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa)) {
                $campos .= "{$gruda}etapa";
                $valores .= "{$gruda}'{$this->etapa}'";
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

            $sql = "INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)";

            $db->Consulta($sql);

            return true;
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
        if (is_numeric($this->cod_bloqueio)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa)) {
                $set .= "{$gruda}etapa = '{$this->etapa}'";
                $gruda = ', ';
            }

            if (is_string($this->data_inicio)) {
                $set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
                $gruda = ', ';
            }

            if (is_string($this->data_fim)) {
                $set .= "{$gruda}data_fim = '{$this->data_fim}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_bloqueio = '{$this->cod_bloqueio}' ");

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
    public function lista($ano = null, $ref_cod_escola = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} ano = '{$ano}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$ref_cod_escola}'";
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
        if (is_numeric($this->cod_bloqueio)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_bloqueio = '{$this->cod_bloqueio}' ");
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
        if (is_numeric($this->cod_bloqueio)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_bloqueio = '{$this->cod_bloqueio}' ");
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif (is_numeric($this->ano) && is_numeric($this->ref_cod_escola) && is_numeric($this->etapa)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1
                       FROM pmieducar.bloqueio_lancamento_faltas_notas
                      WHERE ref_cod_escola = {$this->ref_cod_escola}
                        AND ano = {$this->ano}
                        AND etapa = {$this->etapa}");
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
        if (is_numeric($this->cod_bloqueio)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_bloqueio = '{$this->cod_bloqueio}' ");

            return true;
        }

        return false;
    }

    /**
     * Retorna um boleano identificando se está atualmente dentro do periodo para lançamento de faltas notas
     * registros.
     *
     * @return bool
     */
    public function verificaPeriodo()
    {
        if (is_numeric($this->ano) && is_numeric($this->ref_cod_escola)) {
            if (!$this->existe()) {
                return true;
            }
            $db = new clsBanco();

            $db->Consulta("SELECT 1
                       FROM pmieducar.bloqueio_lancamento_faltas_notas
                      WHERE ref_cod_escola = {$this->ref_cod_escola}
                        AND ano = {$this->ano}
                        AND etapa = {$this->etapa}
                        AND data_inicio <= now()::date
                        AND data_fim >= now()::date");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
