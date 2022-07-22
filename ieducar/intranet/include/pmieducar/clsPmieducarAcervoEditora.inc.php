<?php

use iEducar\Legacy\Model;

class clsPmieducarAcervoEditora extends Model
{
    public $cod_acervo_editora;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $nm_editora;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $telefone;
    public $ddd_telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function __construct($cod_acervo_editora = null, $ref_usuario_cad = null, $ref_usuario_exc = null, $ref_idtlog = null, $ref_sigla_uf = null, $nm_editora = null, $cep = null, $cidade = null, $bairro = null, $logradouro = null, $numero = null, $telefone = null, $ddd_telefone = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_biblioteca = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}acervo_editora";

        $this->_campos_lista = $this->_todos_campos = 'cod_acervo_editora, ref_usuario_cad, ref_usuario_exc, ref_idtlog, ref_sigla_uf, nm_editora, cep, cidade, bairro, logradouro, numero, telefone, ddd_telefone, data_cadastro, data_exclusao, ativo, ref_cod_biblioteca';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_string($ref_idtlog)) {
            $this->ref_idtlog = $ref_idtlog;
        }

        if (is_string($ref_sigla_uf)) {
            $this->ref_sigla_uf = $ref_sigla_uf;
        }

        if (is_numeric($cod_acervo_editora)) {
            $this->cod_acervo_editora = $cod_acervo_editora;
        }
        if (is_string($nm_editora)) {
            $this->nm_editora = $nm_editora;
        }
        if (is_numeric($cep)) {
            $this->cep = $cep;
        }
        if (is_string($cidade)) {
            $this->cidade = $cidade;
        }
        if (is_string($bairro)) {
            $this->bairro = $bairro;
        }
        if (is_string($logradouro)) {
            $this->logradouro = $logradouro;
        }
        if (is_numeric($numero)) {
            $this->numero = $numero;
        }
        if (is_numeric($telefone)) {
            $this->telefone = $telefone;
        }
        if (is_numeric($ddd_telefone)) {
            $this->ddd_telefone = $ddd_telefone;
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
        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_editora) && is_numeric($this->ref_cod_biblioteca)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->ref_idtlog)) {
                $campos .= "{$gruda}ref_idtlog";
                $valores .= "{$gruda}'{$this->ref_idtlog}'";
                $gruda = ', ';
            }
            if (is_string($this->ref_sigla_uf)) {
                $campos .= "{$gruda}ref_sigla_uf";
                $valores .= "{$gruda}'{$this->ref_sigla_uf}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_editora)) {
                $nm_editora = $db->escapeString($this->nm_editora);
                $campos .= "{$gruda}nm_editora";
                $valores .= "{$gruda}'{$nm_editora}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cep)) {
                $campos .= "{$gruda}cep";
                $valores .= "{$gruda}'{$this->cep}'";
                $gruda = ', ';
            }
            if (is_string($this->cidade)) {
                $cidade = $db->escapeString($this->cidade);
                $campos .= "{$gruda}cidade";
                $valores .= "{$gruda}'{$cidade}'";
                $gruda = ', ';
            }
            if (is_string($this->bairro)) {
                $bairro = $db->escapeString($this->bairro);
                $campos .= "{$gruda}bairro";
                $valores .= "{$gruda}'{$bairro}'";
                $gruda = ', ';
            }
            if (is_string($this->logradouro)) {
                $campos .= "{$gruda}logradouro";
                $valores .= "{$gruda}'{$this->logradouro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero)) {
                $campos .= "{$gruda}numero";
                $valores .= "{$gruda}'{$this->numero}'";
                $gruda = ', ';
            }
            if (is_numeric($this->telefone)) {
                $campos .= "{$gruda}telefone";
                $valores .= "{$gruda}'{$this->telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ddd_telefone)) {
                $campos .= "{$gruda}ddd_telefone";
                $valores .= "{$gruda}'{$this->ddd_telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_acervo_editora_seq");
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
        if (is_numeric($this->cod_acervo_editora) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }

            if (is_string($this->ref_idtlog)) {
                $set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ref_idtlog = null";
                $gruda = ', ';
            }

            if (is_string($this->ref_sigla_uf)) {
                $set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ref_sigla_uf = null";
                $gruda = ', ';
            }

            if (is_string($this->nm_editora)) {
                $nm_editora = $db->escapeString($this->nm_editora);
                $set .= "{$gruda}nm_editora = '{$nm_editora}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cep)) {
                $set .= "{$gruda}cep = '{$this->cep}'";
                $gruda = ', ';
            }
            if (is_string($this->cidade)) {
                $cidade = $db->escapeString($this->cidade);
                $set .= "{$gruda}cidade = '{$cidade}'";
                $gruda = ', ';
            }
            if (is_string($this->bairro)) {
                $bairro = $db->escapeString($this->bairro);
                $set .= "{$gruda}bairro = '{$bairro}'";
                $gruda = ', ';
            }
            if (is_string($this->logradouro)) {
                $set .= "{$gruda}logradouro = '{$this->logradouro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero)) {
                $set .= "{$gruda}numero = '{$this->numero}'";
                $gruda = ', ';
            }
            if (is_numeric($this->telefone)) {
                $set .= "{$gruda}telefone = '{$this->telefone}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ddd_telefone)) {
                $set .= "{$gruda}ddd_telefone = '{$this->ddd_telefone}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_biblioteca)) {
                $set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'");

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
    public function lista($int_cod_acervo_editora = null, $int_ref_usuario_cad = null, $int_ref_usuario_exc = null, $str_ref_idtlog = null, $str_ref_sigla_uf = null, $str_nm_editora = null, $int_cep = null, $str_cidade = null, $str_bairro = null, $str_logradouro = null, $int_numero = null, $int_telefone = null, $int_ddd_telefone = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_biblioteca = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_acervo_editora)) {
            $filtros .= "{$whereAnd} cod_acervo_editora = '{$int_cod_acervo_editora}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_ref_idtlog)) {
            $filtros .= "{$whereAnd} ref_idtlog LIKE '%{$str_ref_idtlog}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_ref_sigla_uf)) {
            $filtros .= "{$whereAnd} ref_sigla_uf LIKE '%{$str_ref_sigla_uf}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_editora)) {
            $str_nome_editora = $db->escapeString($str_nm_editora);
            $filtros .= "{$whereAnd} nm_editora LIKE '%{$str_nome_editora}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cep)) {
            $filtros .= "{$whereAnd} cep = '{$int_cep}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_cidade)) {
            $cidade = $db->escapeString($str_cidade);
            $filtros .= "{$whereAnd} cidade LIKE '%{$cidade}%'";
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
        if (is_numeric($int_numero)) {
            $filtros .= "{$whereAnd} numero = '{$int_numero}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_telefone)) {
            $filtros .= "{$whereAnd} telefone = '{$int_telefone}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ddd_telefone)) {
            $filtros .= "{$whereAnd} ddd_telefone = '{$int_ddd_telefone}'";
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
        if (is_array($int_ref_cod_biblioteca)) {
            $bibs = implode(', ', $int_ref_cod_biblioteca);
            $filtros .= "{$whereAnd} (ref_cod_biblioteca IN ($bibs) OR ref_cod_biblioteca IS NULL)";
            $whereAnd = ' AND ';
        } elseif (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }

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
        if (is_numeric($this->cod_acervo_editora)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'");
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
        if (is_numeric($this->cod_acervo_editora)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'");
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
        if (is_numeric($this->cod_acervo_editora) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
