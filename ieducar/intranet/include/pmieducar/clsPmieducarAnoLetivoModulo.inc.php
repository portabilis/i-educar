<?php

use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Session;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarAnoLetivoModulo extends Model
{
    public $ref_ano;
    public $ref_ref_cod_escola;
    public $sequencial;
    public $ref_cod_modulo;
    public $data_inicio;
    public $data_fim;
    public $dias_letivos;

    /**
     * Construtor
     *
     * @return object
     */
    public function __construct(
        $ref_ano = null,
        $ref_ref_cod_escola = null,
        $sequencial = null,
        $ref_cod_modulo = null,
        $data_inicio = null,
        $data_fim = null,
        $dias_letivos = null
    ) {
        $db = new clsBanco();

        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}ano_letivo_modulo";

        $this->_campos_lista = $this->_todos_campos = 'ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo, data_inicio, data_fim, dias_letivos';

        if (is_numeric($ref_cod_modulo)) {
                    $this->ref_cod_modulo = $ref_cod_modulo;
        }

        if (is_numeric($ref_ref_cod_escola) && is_numeric($ref_ano)) {
                    $this->ref_ref_cod_escola = $ref_ref_cod_escola;
                    $this->ref_ano = $ref_ano;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }

        if (is_string($data_inicio)) {
            $this->data_inicio = $data_inicio;
        }

        if (is_string($data_fim)) {
            $this->data_fim = $data_fim;
        }

        if (is_numeric($dias_letivos)) {
            $this->dias_letivos = $dias_letivos;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (
            is_numeric($this->ref_ano)
            && is_numeric($this->ref_ref_cod_escola)
            && is_numeric($this->sequencial)
            && is_numeric($this->ref_cod_modulo)
            && is_string($this->data_inicio)
            && is_string($this->data_fim)
            && is_numeric($this->dias_letivos)
        ) {
            $db = new clsBanco();

            $campos = [];
            $valores = [];

            if (is_numeric($this->ref_ano)) {
                $campos[] = 'ref_ano';
                $valores[] = "'{$this->ref_ano}'";
            }

            if (is_numeric($this->ref_ref_cod_escola)) {
                $campos[] = 'ref_ref_cod_escola';
                $valores[] = "'{$this->ref_ref_cod_escola}'";
            }

            if (is_numeric($this->sequencial)) {
                $campos[] = 'sequencial';
                $valores[] = "'{$this->sequencial}'";
            }

            if (is_numeric($this->ref_cod_modulo)) {
                $campos[] = 'ref_cod_modulo';
                $valores[] = "'{$this->ref_cod_modulo}'";
            }

            if (is_string($this->data_inicio)) {
                $campos[] = 'data_inicio';
                $valores[] = "'{$this->data_inicio}'";
            }

            if (is_string($this->data_fim)) {
                $campos[] = 'data_fim';
                $valores[] = "'{$this->data_fim}'";
            }

            if (is_numeric($this->dias_letivos)) {
                $campos[] = 'dias_letivos';
                $valores[] = "'{$this->dias_letivos}'";
            }

            // ativa escolaAnoLetivo se estiver desativado
            // (quando o escolaAnoLetivo é 'excluido' o registro não é removido)
            $escolaAnoLetivo = new clsPmieducarEscolaAnoLetivo(
                $this->ref_ref_cod_escola,
                $this->ref_ano,
                null,
                Session::get('id_pessoa'),
                null,
                null,
                null,
                1
            );

            $escolaAnoLetivoDetalhe = $escolaAnoLetivo->detalhe();

            if (isset($escolaAnoLetivoDetalhe['ativo']) and $escolaAnoLetivoDetalhe['ativo'] != '1') {
                $escolaAnoLetivo->edita();
            }

            $campos = join(', ', $campos);
            $valores = join(', ', $valores);

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
        if (
            is_numeric($this->ref_ano)
            && is_numeric($this->ref_ref_cod_escola)
            && is_numeric($this->sequencial)
            && is_numeric($this->ref_cod_modulo)
        ) {
            $db = new clsBanco();
            $set = [];

            if (is_string($this->data_inicio)) {
                $set[] = "data_inicio = '{$this->data_inicio}'";
            }

            if (is_string($this->data_fim)) {
                $set[] = "data_fim = '{$this->data_fim}'";
            }

            if (is_numeric($this->dias_letivos)) {
                $set[] = "dias_letivos = '{$this->dias_letivos}'";
            }

            if ($set) {
                $set = join(', ', $set);

                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'");

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
    public function lista(
        $int_ref_ano = null,
        $int_ref_ref_cod_escola = null,
        $int_sequencial = null,
        $int_ref_cod_modulo = null,
        $date_data_inicio_ini = null,
        $date_data_inicio_fim = null,
        $date_data_fim_ini = null,
        $date_data_fim_fim = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} WHERE ";
        $filtros = [];

        if (is_numeric($int_ref_ano)) {
            $filtros[] = "ref_ano = '{$int_ref_ano}'";
        }

        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros[] = "ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
        }

        if (is_numeric($int_sequencial)) {
            $filtros[] = "sequencial = '{$int_sequencial}'";
        }

        if (is_numeric($int_ref_cod_modulo)) {
            $filtros[] = "ref_cod_modulo = '{$int_ref_cod_modulo}'";
        }

        if (is_string($date_data_inicio_ini)) {
            $filtros[] = "data_inicio >= '{$date_data_inicio_ini}'";
        }

        if (is_string($date_data_inicio_fim)) {
            $filtros[] = "data_inicio <= '{$date_data_inicio_fim}'";
        }

        if (is_string($date_data_fim_ini)) {
            $filtros[] = "data_fim >= '{$date_data_fim_ini}'";
        }

        if (is_string($date_data_fim_fim)) {
            $filtros[] = "data_fim <= '{$date_data_fim_fim}'";
        }

        if (empty($filtros)) {
            return false;
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $filtros = join(' AND ', $filtros);

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} WHERE {$filtros}");

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
        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola) && is_numeric($this->sequencial) && is_numeric($this->ref_cod_modulo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'");
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
        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola) && is_numeric($this->sequencial) && is_numeric($this->ref_cod_modulo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND sequencial = '{$this->sequencial}' AND ref_cod_modulo = '{$this->ref_cod_modulo}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro (mantido apenas por questão de BC)
     *
     * @return bool
     */
    public function excluir()
    {
        return false;
    }

    /**
     * Exclui todos os registros referentes a uma escola e a um ano
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_ano = '{$this->ref_ano}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'");

            return true;
        }

        return false;
    }

    /**
     * Retorna a menor data dos modulos de uma escola e ano
     *
     * @return array
     */
    public function menorData($ref_ano, $ref_ref_cod_escola)
    {
        if (is_numeric($ref_ano) && is_numeric($ref_ref_cod_escola)) {
            $db = new clsBanco();
            $resultado = $db->CampoUnico("SELECT
                    MIN( data_inicio )
                FROM
                    pmieducar.ano_letivo_modulo
                WHERE
                    ref_ano = '{$ref_ano}'
                    AND ref_ref_cod_escola = '{$ref_ref_cod_escola}'");

            return $resultado;
        }

        return false;
    }

    /**
     * Retorna a maior data dos modulos de uma escola e ano
     *
     * @return array
     */
    public function maiorData($ref_ano, $ref_ref_cod_escola)
    {
        if (is_numeric($ref_ano) && is_numeric($ref_ref_cod_escola)) {
            $db = new clsBanco();
            $resultado = $db->CampoUnico("SELECT
                    MAX( data_fim )
                FROM
                    pmieducar.ano_letivo_modulo
                WHERE
                    ref_ano = '{$ref_ano}'
                    AND ref_ref_cod_escola = '{$ref_ref_cod_escola}'");

            return $resultado;
        }

        return false;
    }

    /**
     * Retorna o nome do módulo de acordo com o ano, escola e sequencial
     *
     * @return string
     */
    public function getNomeModulo()
    {
        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $resultado = $db->CampoUnico("SELECT sequencial || 'º ' || nm_tipo AS nome_modulo
                                            FROM pmieducar.ano_letivo_modulo
                                           INNER JOIN pmieducar.modulo ON (modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo)
                                           WHERE ref_ano = {$this->ref_ano}
                                             AND ref_ref_cod_escola = {$this->ref_ref_cod_escola}
                                             AND sequencial = {$this->sequencial}");

            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com id e nome das etapas da escola
     *
     * @return array
     */
    public function getEtapas()
    {
        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
            $db = new clsBanco();
            $sql = "SELECT sequencial AS id, sequencial || 'º ' || nm_tipo AS nome
                      FROM pmieducar.ano_letivo_modulo
                     INNER JOIN pmieducar.modulo ON (modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo)
                     WHERE ref_ano = {$this->ref_ano}
                       AND ref_ref_cod_escola = {$this->ref_ref_cod_escola}";

            $db->Consulta($sql);

            while ($db->ProximoRegistro()) {
                $resultado[] = $db->Tupla();
            }

            return $resultado;
        }

        return false;
    }
}
