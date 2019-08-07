<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarEscolaComplemento extends Model
{
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $cep;
    public $numero;
    public $complemento;
    public $email;
    public $nm_escola;
    public $municipio;
    public $bairro;
    public $logradouro;
    public $ddd_telefone;
    public $telefone;
    public $ddd_fax;
    public $fax;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($ref_cod_escola = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $cep = null, $numero = null, $complemento = null, $email = null, $nm_escola = null, $municipio = null, $bairro = null, $logradouro = null, $ddd_telefone = null, $telefone = null, $ddd_fax = null, $fax = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}escola_complemento";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_escola, ref_usuario_exc, ref_usuario_cad, cep, numero, complemento, email, nm_escola, municipio, bairro, logradouro, ddd_telefone, telefone, ddd_fax, fax, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_cad)) {
            if (class_exists('clsPmieducarUsuario')) {
                $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_usuario_cad = $ref_usuario_cad;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_usuario_cad = $ref_usuario_cad;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
                }
            }
        }
        if (is_numeric($ref_usuario_exc)) {
            if (class_exists('clsPmieducarUsuario')) {
                $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_usuario_exc = $ref_usuario_exc;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_usuario_exc = $ref_usuario_exc;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
                }
            }
        }

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($cep)) {
            $this->cep = $cep;
        }
        if (is_numeric($numero)) {
            $this->numero = $numero;
        }
        if (is_string($complemento)) {
            $this->complemento = $complemento;
        }
        if (is_string($email)) {
            $this->email = $email;
        }
        if (is_string($nm_escola)) {
            $this->nm_escola = $nm_escola;
        }
        if (is_string($municipio)) {
            $this->municipio = $municipio;
        }
        if (is_string($bairro)) {
            $this->bairro = $bairro;
        }
        if (is_string($logradouro)) {
            $this->logradouro = $logradouro;
        }
        if (is_numeric($ddd_telefone)) {
            $this->ddd_telefone = $ddd_telefone;
        }
        if (is_numeric($telefone)) {
            $this->telefone = $telefone;
        }
        if (is_numeric($ddd_fax)) {
            $this->ddd_fax = $ddd_fax;
        }
        if (is_numeric($fax)) {
            $this->fax = $fax;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_usuario_cad) && is_numeric($this->cep) && is_numeric($this->numero) && is_string($this->nm_escola) && is_string($this->municipio) && is_string($this->bairro) && is_string($this->logradouro)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cep)) {
                $campos .= "{$gruda}cep";
                $valores .= "{$gruda}'{$this->cep}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero)) {
                $campos .= "{$gruda}numero";
                $valores .= "{$gruda}'{$this->numero}'";
                $gruda = ', ';
            }
            if (is_string($this->complemento)) {
                $campos .= "{$gruda}complemento";
                $valores .= "{$gruda}'{$this->complemento}'";
                $gruda = ', ';
            }
            if (is_string($this->email)) {
                $campos .= "{$gruda}email";
                $valores .= "{$gruda}'{$this->email}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_escola)) {
                $campos .= "{$gruda}nm_escola";
                $valores .= "{$gruda}'{$this->nm_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->municipio)) {
                $campos .= "{$gruda}municipio";
                $valores .= "{$gruda}'{$this->municipio}'";
                $gruda = ', ';
            }
            if (is_string($this->bairro)) {
                $campos .= "{$gruda}bairro";
                $valores .= "{$gruda}'{$this->bairro}'";
                $gruda = ', ';
            }
            if (is_string($this->logradouro)) {
                $campos .= "{$gruda}logradouro";
                $valores .= "{$gruda}'{$this->logradouro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ddd_telefone)) {
                $campos .= "{$gruda}ddd_telefone";
                $valores .= "{$gruda}'{$this->ddd_telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->telefone)) {
                $campos .= "{$gruda}telefone";
                $valores .= "{$gruda}'{$this->telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ddd_fax)) {
                $campos .= "{$gruda}ddd_fax";
                $valores .= "{$gruda}'{$this->ddd_fax}'";
                $gruda = ', ';
            }
            if (is_numeric($this->fax)) {
                $campos .= "{$gruda}fax";
                $valores .= "{$gruda}'{$this->fax}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cep)) {
                $set .= "{$gruda}cep = '{$this->cep}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero)) {
                $set .= "{$gruda}numero = '{$this->numero}'";
                $gruda = ', ';
            }
            if (is_string($this->complemento)) {
                $set .= "{$gruda}complemento = '{$this->complemento}'";
                $gruda = ', ';
            }
            if (is_string($this->email)) {
                $set .= "{$gruda}email = '{$this->email}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_escola)) {
                $set .= "{$gruda}nm_escola = '{$this->nm_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->municipio)) {
                $set .= "{$gruda}municipio = '{$this->municipio}'";
                $gruda = ', ';
            }
            if (is_string($this->bairro)) {
                $set .= "{$gruda}bairro = '{$this->bairro}'";
                $gruda = ', ';
            }
            if (is_string($this->logradouro)) {
                $set .= "{$gruda}logradouro = '{$this->logradouro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ddd_telefone)) {
                $set .= "{$gruda}ddd_telefone = '{$this->ddd_telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->telefone)) {
                $set .= "{$gruda}telefone = '{$this->telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ddd_fax)) {
                $set .= "{$gruda}ddd_fax = '{$this->ddd_fax}'";
                $gruda = ', ';
            }
            if (is_numeric($this->fax)) {
                $set .= "{$gruda}fax = '{$this->fax}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_escola = '{$this->ref_cod_escola}'");

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
    public function lista($int_ref_cod_escola = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_cep = null, $int_numero = null, $str_complemento = null, $str_email = null, $str_nm_escola = null, $str_municipio = null, $str_bairro = null, $str_logradouro = null, $int_ddd_telefone = null, $int_telefone = null, $int_ddd_fax = null, $int_fax = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cep)) {
            $filtros .= "{$whereAnd} cep = '{$int_cep}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_numero)) {
            $filtros .= "{$whereAnd} numero = '{$int_numero}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_complemento)) {
            $filtros .= "{$whereAnd} complemento LIKE '%{$str_complemento}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_email)) {
            $filtros .= "{$whereAnd} email LIKE '%{$str_email}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_escola)) {
            $filtros .= "{$whereAnd} nm_escola LIKE '%{$str_nm_escola}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_municipio)) {
            $filtros .= "{$whereAnd} municipio LIKE '%{$str_municipio}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_bairro)) {
            $filtros .= "{$whereAnd} bairro LIKE '%{$str_bairro}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_logradouro)) {
            $filtros .= "{$whereAnd} logradouro LIKE '%{$str_logradouro}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ddd_telefone)) {
            $filtros .= "{$whereAnd} ddd_telefone = '{$int_ddd_telefone}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_telefone)) {
            $filtros .= "{$whereAnd} telefone = '{$int_telefone}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ddd_fax)) {
            $filtros .= "{$whereAnd} ddd_fax = '{$int_ddd_fax}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_fax)) {
            $filtros .= "{$whereAnd} fax = '{$int_fax}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
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
        if (is_numeric($this->ref_cod_escola)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}'");
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
        if (is_numeric($this->ref_cod_escola)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}'");
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
