<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarEndereco extends Model
{
    public $ref_cod_pessoa_educ;
    public $ref_idbai;
    public $ref_cep;
    public $ref_idlog;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $numero;
    public $complemento;
    public $letra;
    public $andar;
    public $bloco;
    public $apartamento;

    public function __construct($ref_cod_pessoa_educ = null, $ref_idbai = null, $ref_cep = null, $ref_idlog = null, $ref_idtlog = null, $ref_sigla_uf = null, $numero = null, $complemento = null, $letra = null, $andar = null, $bloco = null, $apartamento = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}endereco";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_pessoa_educ, ref_idbai, ref_cep, ref_idlog, ref_idtlog, ref_sigla_uf, numero, complemento, letra, andar, bloco, apartamento';

        if (is_numeric($ref_cod_pessoa_educ)) {
                    $this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
        }
        if (is_numeric($ref_idlog) && is_numeric($ref_cep) && is_numeric($ref_idbai)) {
                    $this->ref_idlog = $ref_idlog;
                    $this->ref_cep = $ref_cep;
                    $this->ref_idbai = $ref_idbai;
        }
        if (is_string($ref_idtlog)) {
                    $this->ref_idtlog = $ref_idtlog;
        }

        if (is_string($ref_sigla_uf)) {
            $this->ref_sigla_uf = $ref_sigla_uf;
        }
        if (is_numeric($numero)) {
            $this->numero = $numero;
        }
        if (is_string($complemento)) {
            $this->complemento = $complemento;
        }
        if (is_string($letra)) {
            $this->letra = $letra;
        }
        if (is_numeric($andar)) {
            $this->andar = $andar;
        }
        if (is_string($bloco)) {
            $this->bloco = $bloco;
        }
        if (is_numeric($apartamento)) {
            $this->apartamento = $apartamento;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_pessoa_educ) && is_numeric($this->ref_idbai) && is_numeric($this->ref_cep) && is_numeric($this->ref_idlog) && is_string($this->ref_idtlog) && is_string($this->ref_sigla_uf) && is_numeric($this->numero)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_pessoa_educ)) {
                $campos .= "{$gruda}ref_cod_pessoa_educ";
                $valores .= "{$gruda}'{$this->ref_cod_pessoa_educ}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idbai)) {
                $campos .= "{$gruda}ref_idbai";
                $valores .= "{$gruda}'{$this->ref_idbai}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cep)) {
                $campos .= "{$gruda}ref_cep";
                $valores .= "{$gruda}'{$this->ref_cep}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idlog)) {
                $campos .= "{$gruda}ref_idlog";
                $valores .= "{$gruda}'{$this->ref_idlog}'";
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
            if (is_string($this->letra)) {
                $campos .= "{$gruda}letra";
                $valores .= "{$gruda}'{$this->letra}'";
                $gruda = ', ';
            }
            if (is_numeric($this->andar)) {
                $campos .= "{$gruda}andar";
                $valores .= "{$gruda}'{$this->andar}'";
                $gruda = ', ';
            }
            if (is_string($this->bloco)) {
                $campos .= "{$gruda}bloco";
                $valores .= "{$gruda}'{$this->bloco}'";
                $gruda = ', ';
            }
            if (is_numeric($this->apartamento)) {
                $campos .= "{$gruda}apartamento";
                $valores .= "{$gruda}'{$this->apartamento}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_ref_cod_pessoa_educ_seq");
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
        if (is_numeric($this->ref_cod_pessoa_educ)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_idbai)) {
                $set .= "{$gruda}ref_idbai = '{$this->ref_idbai}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cep)) {
                $set .= "{$gruda}ref_cep = '{$this->ref_cep}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idlog)) {
                $set .= "{$gruda}ref_idlog = '{$this->ref_idlog}'";
                $gruda = ', ';
            }
            if (is_string($this->ref_idtlog)) {
                $set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
                $gruda = ', ';
            }
            if (is_string($this->ref_sigla_uf)) {
                $set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
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
            if (is_string($this->letra)) {
                $set .= "{$gruda}letra = '{$this->letra}'";
                $gruda = ', ';
            }
            if (is_numeric($this->andar)) {
                $set .= "{$gruda}andar = '{$this->andar}'";
                $gruda = ', ';
            }
            if (is_string($this->bloco)) {
                $set .= "{$gruda}bloco = '{$this->bloco}'";
                $gruda = ', ';
            }
            if (is_numeric($this->apartamento)) {
                $set .= "{$gruda}apartamento = '{$this->apartamento}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'");

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
    public function lista($int_ref_cod_pessoa_educ = null, $int_ref_idbai = null, $int_ref_cep = null, $int_ref_idlog = null, $str_ref_idtlog = null, $str_ref_sigla_uf = null, $int_numero = null, $str_complemento = null, $str_letra = null, $int_andar = null, $str_bloco = null, $int_apartamento = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_pessoa_educ)) {
            $filtros .= "{$whereAnd} ref_cod_pessoa_educ = '{$int_ref_cod_pessoa_educ}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idbai)) {
            $filtros .= "{$whereAnd} ref_idbai = '{$int_ref_idbai}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cep)) {
            $filtros .= "{$whereAnd} ref_cep = '{$int_ref_cep}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idlog)) {
            $filtros .= "{$whereAnd} ref_idlog = '{$int_ref_idlog}'";
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
        if (is_numeric($int_numero)) {
            $filtros .= "{$whereAnd} numero = '{$int_numero}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_complemento)) {
            $filtros .= "{$whereAnd} complemento LIKE '%{$str_complemento}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_letra)) {
            $filtros .= "{$whereAnd} letra LIKE '%{$str_letra}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_andar)) {
            $filtros .= "{$whereAnd} andar = '{$int_andar}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_bloco)) {
            $filtros .= "{$whereAnd} bloco LIKE '%{$str_bloco}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_apartamento)) {
            $filtros .= "{$whereAnd} apartamento = '{$int_apartamento}'";
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
        if (is_numeric($this->ref_cod_pessoa_educ)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'");
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
        if (is_numeric($this->ref_cod_pessoa_educ)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'");
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
        if (is_numeric($this->ref_cod_pessoa_educ)) {
        }

        return false;
    }
}
