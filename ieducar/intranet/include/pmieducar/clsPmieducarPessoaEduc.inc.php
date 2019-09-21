<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarPessoaEduc extends Model
{
    public $cod_pessoa_educ;
    public $ref_idpes_responsavel;
    public $ref_idpais_origem;
    public $ref_idmun_natural;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nome;
    public $url;
    public $email;
    public $data_nascimento;
    public $sexo;
    public $nacionalidade;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_pessoa_educ = null, $ref_idpes_responsavel = null, $ref_idpais_origem = null, $ref_idmun_natural = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nome = null, $url = null, $email = null, $data_nascimento = null, $sexo = null, $nacionalidade = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}pessoa_educ";

        $this->_campos_lista = $this->_todos_campos = 'cod_pessoa_educ, ref_idpes_responsavel, ref_idpais_origem, ref_idmun_natural, ref_usuario_exc, ref_usuario_cad, nome, url, email, data_nascimento, sexo, nacionalidade, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_idpes_responsavel)) {
                    $this->ref_idpes_responsavel = $ref_idpes_responsavel;
        }
        if (is_numeric($ref_idpais_origem)) {
                    $this->ref_idpais_origem = $ref_idpais_origem;
        }
        if (is_numeric($ref_idmun_natural)) {
                    $this->ref_idmun_natural = $ref_idmun_natural;
        }
        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_pessoa_educ)) {
            $this->cod_pessoa_educ = $cod_pessoa_educ;
        }
        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_string($url)) {
            $this->url = $url;
        }
        if (is_string($email)) {
            $this->email = $email;
        }
        if (is_string($data_nascimento)) {
            $this->data_nascimento = $data_nascimento;
        }
        if (is_string($sexo)) {
            $this->sexo = $sexo;
        }
        if (is_numeric($nacionalidade)) {
            $this->nacionalidade = $nacionalidade;
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
        echo "is_numeric( $this->cod_pessoa_educ ) && is_numeric( $this->ref_idpais_origem ) && is_numeric( $this->ref_idmun_natural ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->nome ) && is_string( $this->data_nascimento ) && is_string( $this->sexo ) && is_numeric( $this->nacionalidade )";
        if (is_numeric($this->cod_pessoa_educ) && is_numeric($this->ref_idpais_origem) && is_numeric($this->ref_idmun_natural) && is_numeric($this->ref_usuario_cad) && is_string($this->nome) && is_string($this->data_nascimento) && is_string($this->sexo) && is_numeric($this->nacionalidade)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->cod_pessoa_educ)) {
                $campos .= "{$gruda}cod_pessoa_educ";
                $valores .= "{$gruda}'{$this->cod_pessoa_educ}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpes_responsavel)) {
                $campos .= "{$gruda}ref_idpes_responsavel";
                $valores .= "{$gruda}'{$this->ref_idpes_responsavel}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpais_origem)) {
                $campos .= "{$gruda}ref_idpais_origem";
                $valores .= "{$gruda}'{$this->ref_idpais_origem}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idmun_natural)) {
                $campos .= "{$gruda}ref_idmun_natural";
                $valores .= "{$gruda}'{$this->ref_idmun_natural}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}'{$this->nome}'";
                $gruda = ', ';
            }
            if (is_string($this->url)) {
                $campos .= "{$gruda}url";
                $valores .= "{$gruda}'{$this->url}'";
                $gruda = ', ';
            }
            if (is_string($this->email)) {
                $campos .= "{$gruda}email";
                $valores .= "{$gruda}'{$this->email}'";
                $gruda = ', ';
            }
            if (is_string($this->data_nascimento)) {
                $campos .= "{$gruda}data_nascimento";
                $valores .= "{$gruda}'{$this->data_nascimento}'";
                $gruda = ', ';
            }
            if (is_string($this->sexo)) {
                $campos .= "{$gruda}sexo";
                $valores .= "{$gruda}'{$this->sexo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nacionalidade)) {
                $campos .= "{$gruda}nacionalidade";
                $valores .= "{$gruda}'{$this->nacionalidade}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_pessoa_educ_seq");
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
        if (is_numeric($this->cod_pessoa_educ) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_idpes_responsavel)) {
                $set .= "{$gruda}ref_idpes_responsavel = '{$this->ref_idpes_responsavel}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpais_origem)) {
                $set .= "{$gruda}ref_idpais_origem = '{$this->ref_idpais_origem}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idmun_natural)) {
                $set .= "{$gruda}ref_idmun_natural = '{$this->ref_idmun_natural}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '{$this->nome}'";
                $gruda = ', ';
            }
            if (is_string($this->url)) {
                $set .= "{$gruda}url = '{$this->url}'";
                $gruda = ', ';
            }
            if (is_string($this->email)) {
                $set .= "{$gruda}email = '{$this->email}'";
                $gruda = ', ';
            }
            if (is_string($this->data_nascimento)) {
                $set .= "{$gruda}data_nascimento = '{$this->data_nascimento}'";
                $gruda = ', ';
            }
            if (is_string($this->sexo)) {
                $set .= "{$gruda}sexo = '{$this->sexo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nacionalidade)) {
                $set .= "{$gruda}nacionalidade = '{$this->nacionalidade}'";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_pessoa_educ = '{$this->cod_pessoa_educ}'");

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
    public function lista($int_cod_pessoa_educ = null, $int_ref_idpes_responsavel = null, $int_ref_idpais_origem = null, $int_ref_idmun_natural = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nome = null, $str_url = null, $str_email = null, $date_data_nascimento_ini = null, $date_data_nascimento_fim = null, $str_sexo = null, $int_nacionalidade = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_pessoa_educ)) {
            $filtros .= "{$whereAnd} cod_pessoa_educ = '{$int_cod_pessoa_educ}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idpes_responsavel)) {
            $filtros .= "{$whereAnd} ref_idpes_responsavel = '{$int_ref_idpes_responsavel}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idpais_origem)) {
            $filtros .= "{$whereAnd} ref_idpais_origem = '{$int_ref_idpais_origem}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idmun_natural)) {
            $filtros .= "{$whereAnd} ref_idmun_natural = '{$int_ref_idmun_natural}'";
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
        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_url)) {
            $filtros .= "{$whereAnd} url LIKE '%{$str_url}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_email)) {
            $filtros .= "{$whereAnd} email LIKE '%{$str_email}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_nascimento_ini)) {
            $filtros .= "{$whereAnd} data_nascimento >= '{$date_data_nascimento_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_nascimento_fim)) {
            $filtros .= "{$whereAnd} data_nascimento <= '{$date_data_nascimento_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sexo)) {
            $filtros .= "{$whereAnd} sexo LIKE '%{$str_sexo}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nacionalidade)) {
            $filtros .= "{$whereAnd} nacionalidade = '{$int_nacionalidade}'";
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
        if (is_numeric($this->cod_pessoa_educ)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_pessoa_educ = '{$this->cod_pessoa_educ}'");
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
        if (is_numeric($this->cod_pessoa_educ)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_pessoa_educ = '{$this->cod_pessoa_educ}'");
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
        if (is_numeric($this->cod_pessoa_educ) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
