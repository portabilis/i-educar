<?php

use iEducar\Legacy\Model;

class clsPmieducarInfraPredio extends Model
{
    public $cod_infra_predio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_escola;
    public $nm_predio;
    public $desc_predio;
    public $endereco;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $codUsuario;

    public function __construct($cod_infra_predio = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_escola = null, $nm_predio = null, $desc_predio = null, $endereco = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}infra_predio";

        $this->_campos_lista = $this->_todos_campos = 'cod_infra_predio, ref_usuario_exc, ref_usuario_cad, ref_cod_escola, nm_predio, desc_predio, endereco, data_cadastro, data_exclusao, ativo';

        $this->_campos_lista = "predio.{$this->_campos_lista}";
        $this->_campos_lista = str_replace(',', ', predio.', $this->_campos_lista);

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_infra_predio)) {
            $this->cod_infra_predio = $cod_infra_predio;
        }
        if (is_string($nm_predio)) {
            $this->nm_predio = $nm_predio;
        }
        if (is_string($desc_predio)) {
            $this->desc_predio = $desc_predio;
        }
        if (is_string($endereco)) {
            $this->endereco = $endereco;
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
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_escola) && is_string($this->nm_predio) && is_string($this->endereco)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_predio)) {
                $nm_predio = $db->escapeString($this->nm_predio);
                $campos .= "{$gruda}nm_predio";
                $valores .= "{$gruda}'{$nm_predio}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_predio)) {
                $desc_predio = $db->escapeString($this->desc_predio);
                $campos .= "{$gruda}desc_predio";
                $valores .= "{$gruda}'{$desc_predio}'";
                $gruda = ', ';
            }
            if (is_string($this->endereco)) {
                $endereco = $db->escapeString($this->endereco);
                $campos .= "{$gruda}endereco";
                $valores .= "{$gruda}'{$endereco}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            if (is_string($this->data_exclusao)) {
                $campos .= "{$gruda}data_exclusao";
                $valores .= "{$gruda}'{$this->data_exclusao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_infra_predio_seq");
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
        if (is_numeric($this->cod_infra_predio) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_predio)) {
                $nm_predio = $db->escapeString($this->nm_predio);
                $set .= "{$gruda}nm_predio = '{$nm_predio}'";
                $gruda = ', ';
            }
            if (is_string($this->desc_predio)) {
                $desc_predio = $db->escapeString($this->desc_predio);
                $set .= "{$gruda}desc_predio = '{$desc_predio}'";
                $gruda = ', ';
            }
            if (is_string($this->endereco)) {
                $endereco = $db->escapeString($this->endereco);
                $set .= "{$gruda}endereco = '{$endereco}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            if (is_string($this->data_exclusao)) {
                $set .= "{$gruda}data_exclusao = '{$this->data_exclusao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_infra_predio = '{$this->cod_infra_predio}'");

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
    public function lista($int_cod_infra_predio = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_escola = null, $str_nm_predio = null, $str_desc_predio = null, $str_endereco = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_escola_in = null, $int_cod_instituicao = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} predio,pmieducar.escola escola";

        $whereAnd = ' AND ';
        $filtros = ' WHERE escola.cod_escola = predio.ref_cod_escola ';

        if (is_numeric($int_cod_infra_predio)) {
            $filtros .= "{$whereAnd} predio.cod_infra_predio = '{$int_cod_infra_predio}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} predio.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} predio.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} predio.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_predio)) {
            $nm_predio = $db->escapeString($str_nm_predio);
            $filtros .= "{$whereAnd} predio.nm_predio LIKE '%{$nm_predio}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_desc_predio)) {
            $filtros .= "{$whereAnd} predio.desc_predio LIKE '%{$str_desc_predio}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_endereco)) {
            $filtros .= "{$whereAnd} predio.endereco LIKE '%{$str_endereco}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} predio.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} predio.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} predio.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} predio.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} predio.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} predio.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_escola_in)) {
            $filtros .= "{$whereAnd} predio.ref_cod_escola in ($str_escola_in)";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                              WHERE escola_usuario.ref_cod_escola = predio.ref_cod_escola
                                                AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_instituicao)) {
            $filtros .= "{$whereAnd} predio.ref_cod_escola in (SELECT cod_escola from pmieducar.escola where ref_cod_instituicao =$int_cod_instituicao)";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} predio,pmieducar.escola escola {$filtros}");

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
        if (is_numeric($this->cod_infra_predio)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_infra_predio = '{$this->cod_infra_predio}'");
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
        if (is_numeric($this->cod_infra_predio)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_infra_predio = '{$this->cod_infra_predio}'");
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
        if (is_numeric($this->cod_infra_predio) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
