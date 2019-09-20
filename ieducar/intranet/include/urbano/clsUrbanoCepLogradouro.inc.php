<?php

use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Session;

require_once 'include/urbano/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsUrbanoCepLogradouro extends Model
{
    public $cep;
    public $idlog;
    public $nroini;
    public $nrofin;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $pessoa_logada;

    public function __construct($cep = null, $idlog = null, $nroini = null, $nrofin = null, $idpes_rev = null, $data_rev = null, $origem_gravacao = null, $idpes_cad = null, $data_cad = null, $operacao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'urbano.';
        $this->_tabela = "{$this->_schema}cep_logradouro";

        $this->pessoa_logada = Session::get('id_pessoa');

        $this->_campos_lista = $this->_todos_campos = 'cl.cep, cl.idlog, cl.nroini, cl.nrofin, cl.idpes_rev, cl.data_rev, cl.origem_gravacao, cl.idpes_cad, cl.data_cad, cl.operacao';
        if (is_numeric($idpes_rev)) {
                    $this->idpes_rev = $idpes_rev;
        }
        if (is_numeric($idpes_cad)) {
                    $this->idpes_cad = $idpes_cad;
        }
        if (is_numeric($idlog)) {
                    $this->idlog = $idlog;
        }
        if (is_numeric($cep)) {
            $this->cep = $cep;
        }
        if (is_numeric($nroini)) {
            $this->nroini = $nroini;
        }
        if (is_numeric($nrofin)) {
            $this->nrofin = $nrofin;
        }
        if (is_string($data_rev)) {
            $this->data_rev = $data_rev;
        }
        if (is_string($origem_gravacao)) {
            $this->origem_gravacao = $origem_gravacao;
        }
        if (is_string($data_cad)) {
            $this->data_cad = $data_cad;
        }
        if (is_string($operacao)) {
            $this->operacao = $operacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog) && is_string($this->origem_gravacao) && is_string($this->operacao)) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';
            if (is_numeric($this->cep)) {
                $campos .= "{$gruda}cep";
                $valores .= "{$gruda}'{$this->cep}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idlog)) {
                $campos .= "{$gruda}idlog";
                $valores .= "{$gruda}'{$this->idlog}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nroini)) {
                $campos .= "{$gruda}nroini";
                $valores .= "{$gruda}'{$this->nroini}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nrofin)) {
                $campos .= "{$gruda}nrofin";
                $valores .= "{$gruda}'{$this->nrofin}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_rev)) {
                $campos .= "{$gruda}idpes_rev";
                $valores .= "{$gruda}'{$this->idpes_rev}'";
                $gruda = ', ';
            }
            if (is_string($this->data_rev)) {
                $campos .= "{$gruda}data_rev";
                $valores .= "{$gruda}'{$this->data_rev}'";
                $gruda = ', ';
            }
            if (is_string($this->origem_gravacao)) {
                $campos .= "{$gruda}origem_gravacao";
                $valores .= "{$gruda}'{$this->origem_gravacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $campos .= "{$gruda}idpes_cad";
                $valores .= "{$gruda}'{$this->idpes_cad}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cad";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_string($this->operacao)) {
                $campos .= "{$gruda}operacao";
                $valores .= "{$gruda}'{$this->operacao}'";
                $gruda = ', ';
            }
            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $detalhe = $this->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de CEP', $this->pessoa_logada, $this->cep);
            $auditoria->inclusao($detalhe);

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
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $db = new clsBanco();
            $set = '';
            if (is_numeric($this->nroini)) {
                $set .= "{$gruda}nroini = '{$this->nroini}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nrofin)) {
                $set .= "{$gruda}nrofin = '{$this->nrofin}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_rev)) {
                $set .= "{$gruda}idpes_rev = '{$this->idpes_rev}'";
                $gruda = ', ';
            }
            if (is_string($this->data_rev)) {
                $set .= "{$gruda}data_rev = '{$this->data_rev}'";
                $gruda = ', ';
            }
            if (is_string($this->origem_gravacao)) {
                $set .= "{$gruda}origem_gravacao = '{$this->origem_gravacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_cad)) {
                $set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cad)) {
                $set .= "{$gruda}data_cad = '{$this->data_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->operacao)) {
                $set .= "{$gruda}operacao = '{$this->operacao}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cep = '{$this->cep}' AND idlog = '{$this->idlog}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_nroini
     * @param integer int_nrofin
     * @param integer int_idpes_rev
     * @param string date_data_rev_ini
     * @param string date_data_rev_fim
     * @param string str_origem_gravacao
     * @param integer int_idpes_cad
     * @param string date_data_cad_ini
     * @param string date_data_cad_fim
     * @param string str_operacao
     *
     * @return array
     */
    public function lista($int_nroini = null, $int_nrofin = null, $int_idpes_rev = null, $date_data_rev_ini = null, $date_data_rev_fim = null, $str_origem_gravacao = null, $int_idpes_cad = null, $date_data_cad_ini = null, $date_data_cad_fim = null, $str_operacao = null, $int_idsis_rev = null, $int_idsis_cad = null, $int_idpais = null, $str_sigla_uf = null, $int_idmun = null, $int_idlog = null, $int_cep = null)
    {
        $select = ', l.nome AS nm_logradouro, l.idmun, m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ';
        $from = 'cl, public.logradouro l, public.municipio m, public.uf u, public.pais p ';

        $sql = "SELECT {$this->_campos_lista}{$select} FROM {$this->_tabela} {$from}";
        $whereAnd = ' AND ';
        $filtros = ' WHERE cl.idlog = l.idlog AND l.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais ';
        if (is_numeric($int_cep)) {
            $filtros .= "{$whereAnd} cl.cep = '{$int_cep}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idlog)) {
            $filtros .= "{$whereAnd} cl.idlog = '{$int_idlog}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nroini)) {
            $filtros .= "{$whereAnd} cl.nroini = '{$int_nroini}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nrofin)) {
            $filtros .= "{$whereAnd} cl.nrofin = '{$int_nrofin}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_rev)) {
            $filtros .= "{$whereAnd} cl.idpes_rev = '{$int_idpes_rev}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_rev_ini)) {
            $filtros .= "{$whereAnd} cl.data_rev >= '{$date_data_rev_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_rev_fim)) {
            $filtros .= "{$whereAnd} cl.data_rev <= '{$date_data_rev_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_origem_gravacao)) {
            $filtros .= "{$whereAnd} cl.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} cl.idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cad_ini)) {
            $filtros .= "{$whereAnd} cl.data_cad >= '{$date_data_cad_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cad_fim)) {
            $filtros .= "{$whereAnd} cl.data_cad <= '{$date_data_cad_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_operacao)) {
            $filtros .= "{$whereAnd} cl.operacao LIKE '%{$str_operacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpais)) {
            $filtros .= "{$whereAnd} p.idpais = '{$int_idpais}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla_uf)) {
            $filtros .= "{$whereAnd} u.sigla_uf = '{$str_sigla_uf}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmun)) {
            $filtros .= "{$whereAnd} m.idmun = '{$int_idmun}'";
            $whereAnd = ' AND ';
        }
        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$from}{$filtros}");
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
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_nroini
     * @param integer int_nrofin
     * @param integer int_idpes_rev
     * @param string date_data_rev_ini
     * @param string date_data_rev_fim
     * @param string str_origem_gravacao
     * @param integer int_idpes_cad
     * @param string date_data_cad_ini
     * @param string date_data_cad_fim
     * @param string str_operacao
     *
     * @return array
     */
    public function lista_($int_nroini = null, $int_nrofin = null, $int_idpes_rev = null, $date_data_rev_ini = null, $date_data_rev_fim = null, $str_origem_gravacao = null, $int_idpes_cad = null, $date_data_cad_ini = null, $date_data_cad_fim = null, $str_operacao = null, $int_idsis_rev = null, $int_idsis_cad = null, $int_idpais = null, $str_sigla_uf = null, $int_idmun = null, $int_idlog = null, $int_cep = null)
    {
        $select = ' cl.idlog, l.nome AS nm_logradouro, l.idmun, m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ';
        $from = 'cl, public.logradouro l, public.municipio m, public.uf u, public.pais p ';

        $sql = "SELECT distinct{$select} FROM {$this->_tabela} {$from}";
        $whereAnd = ' AND ';
        $filtros = ' WHERE cl.idlog = l.idlog AND l.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais ';
        if (is_numeric($int_cep)) {
            $filtros .= "{$whereAnd} cl.cep = '{$int_cep}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idlog)) {
            $filtros .= "{$whereAnd} cl.idlog = '{$int_idlog}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nroini)) {
            $filtros .= "{$whereAnd} cl.nroini = '{$int_nroini}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nrofin)) {
            $filtros .= "{$whereAnd} cl.nrofin = '{$int_nrofin}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_rev)) {
            $filtros .= "{$whereAnd} cl.idpes_rev = '{$int_idpes_rev}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_rev_ini)) {
            $filtros .= "{$whereAnd} cl.data_rev >= '{$date_data_rev_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_rev_fim)) {
            $filtros .= "{$whereAnd} cl.data_rev <= '{$date_data_rev_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_origem_gravacao)) {
            $filtros .= "{$whereAnd} cl.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} cl.idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cad_ini)) {
            $filtros .= "{$whereAnd} cl.data_cad >= '{$date_data_cad_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cad_fim)) {
            $filtros .= "{$whereAnd} cl.data_cad <= '{$date_data_cad_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_operacao)) {
            $filtros .= "{$whereAnd} cl.operacao LIKE '%{$str_operacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpais)) {
            $filtros .= "{$whereAnd} p.idpais = '{$int_idpais}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla_uf)) {
            $filtros .= "{$whereAnd} u.sigla_uf = '{$str_sigla_uf}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmun)) {
            $filtros .= "{$whereAnd} m.idmun = '{$int_idmun}'";
            $whereAnd = ' AND ';
        }
        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$from}{$filtros}");
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
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} cl WHERE cl.cep = '{$this->cep}' AND cl.idlog = '{$this->idlog}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna true se o registro existir. Caso contrário retorna false.
     *
     * @return bool
     */
    public function existe()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cep = '{$this->cep}' AND idlog = '{$this->idlog}'");
            if ($db->ProximoRegistro()) {
                return true;
            }
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
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
        }

        return false;
    }
}
