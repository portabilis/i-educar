<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarDocumentos extends Model
{
    public $ref_cod_pessoa_educ;
    public $ref_idorg_rg;
    public $sigla_uf_cert_civil;
    public $sigla_uf_exp_rg;
    public $rg;
    public $data_expedicao_rg;
    public $num_titulo_eleitor;
    public $zona_titulo_eleitor;
    public $secao_titulo_eleitor;
    public $tipo_certidao_civil;
    public $num_termo;
    public $num_folha;
    public $num_livro;
    public $data_emissao_certidao_civil;

    public function __construct($ref_cod_pessoa_educ = null, $ref_idorg_rg = null, $sigla_uf_cert_civil = null, $sigla_uf_exp_rg = null, $rg = null, $data_expedicao_rg = null, $num_titulo_eleitor = null, $zona_titulo_eleitor = null, $secao_titulo_eleitor = null, $tipo_certidao_civil = null, $num_termo = null, $num_folha = null, $num_livro = null, $data_emissao_certidao_civil = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}documentos";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_pessoa_educ, ref_idorg_rg, sigla_uf_cert_civil, sigla_uf_exp_rg, rg, data_expedicao_rg, num_titulo_eleitor, zona_titulo_eleitor, secao_titulo_eleitor, tipo_certidao_civil, num_termo, num_folha, num_livro, data_emissao_certidao_civil';

        if (is_numeric($ref_idorg_rg)) {
                    $this->ref_idorg_rg = $ref_idorg_rg;
        }
        if (is_string($sigla_uf_cert_civil)) {
                    $this->sigla_uf_cert_civil = $sigla_uf_cert_civil;
        }
        if (is_string($sigla_uf_exp_rg)) {
                    $this->sigla_uf_exp_rg = $sigla_uf_exp_rg;
        }
        if (is_numeric($ref_cod_pessoa_educ)) {
                    $this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
        }

        if (is_numeric($rg)) {
            $this->rg = $rg;
        }
        if (is_string($data_expedicao_rg)) {
            $this->data_expedicao_rg = $data_expedicao_rg;
        }
        if (is_numeric($num_titulo_eleitor)) {
            $this->num_titulo_eleitor = $num_titulo_eleitor;
        }
        if (is_numeric($zona_titulo_eleitor)) {
            $this->zona_titulo_eleitor = $zona_titulo_eleitor;
        }
        if (is_numeric($secao_titulo_eleitor)) {
            $this->secao_titulo_eleitor = $secao_titulo_eleitor;
        }
        if (is_numeric($tipo_certidao_civil)) {
            $this->tipo_certidao_civil = $tipo_certidao_civil;
        }
        if (is_numeric($num_termo)) {
            $this->num_termo = $num_termo;
        }
        if (is_numeric($num_folha)) {
            $this->num_folha = $num_folha;
        }
        if (is_numeric($num_livro)) {
            $this->num_livro = $num_livro;
        }
        if (is_string($data_emissao_certidao_civil)) {
            $this->data_emissao_certidao_civil = $data_emissao_certidao_civil;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_pessoa_educ) && is_numeric($this->ref_idorg_rg) && is_string($this->sigla_uf_cert_civil) && is_string($this->sigla_uf_exp_rg)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_pessoa_educ)) {
                $campos .= "{$gruda}ref_cod_pessoa_educ";
                $valores .= "{$gruda}'{$this->ref_cod_pessoa_educ}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idorg_rg)) {
                $campos .= "{$gruda}ref_idorg_rg";
                $valores .= "{$gruda}'{$this->ref_idorg_rg}'";
                $gruda = ', ';
            }
            if (is_string($this->sigla_uf_cert_civil)) {
                $campos .= "{$gruda}sigla_uf_cert_civil";
                $valores .= "{$gruda}'{$this->sigla_uf_cert_civil}'";
                $gruda = ', ';
            }
            if (is_string($this->sigla_uf_exp_rg)) {
                $campos .= "{$gruda}sigla_uf_exp_rg";
                $valores .= "{$gruda}'{$this->sigla_uf_exp_rg}'";
                $gruda = ', ';
            }
            if (is_numeric($this->rg)) {
                $campos .= "{$gruda}rg";
                $valores .= "{$gruda}'{$this->rg}'";
                $gruda = ', ';
            }
            if (is_string($this->data_expedicao_rg)) {
                $campos .= "{$gruda}data_expedicao_rg";
                $valores .= "{$gruda}'{$this->data_expedicao_rg}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_titulo_eleitor)) {
                $campos .= "{$gruda}num_titulo_eleitor";
                $valores .= "{$gruda}'{$this->num_titulo_eleitor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->zona_titulo_eleitor)) {
                $campos .= "{$gruda}zona_titulo_eleitor";
                $valores .= "{$gruda}'{$this->zona_titulo_eleitor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->secao_titulo_eleitor)) {
                $campos .= "{$gruda}secao_titulo_eleitor";
                $valores .= "{$gruda}'{$this->secao_titulo_eleitor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tipo_certidao_civil)) {
                $campos .= "{$gruda}tipo_certidao_civil";
                $valores .= "{$gruda}'{$this->tipo_certidao_civil}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_termo)) {
                $campos .= "{$gruda}num_termo";
                $valores .= "{$gruda}'{$this->num_termo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_folha)) {
                $campos .= "{$gruda}num_folha";
                $valores .= "{$gruda}'{$this->num_folha}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_livro)) {
                $campos .= "{$gruda}num_livro";
                $valores .= "{$gruda}'{$this->num_livro}'";
                $gruda = ', ';
            }
            if (is_string($this->data_emissao_certidao_civil)) {
                $campos .= "{$gruda}data_emissao_certidao_civil";
                $valores .= "{$gruda}'{$this->data_emissao_certidao_civil}'";
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

            if (is_numeric($this->ref_idorg_rg)) {
                $set .= "{$gruda}ref_idorg_rg = '{$this->ref_idorg_rg}'";
                $gruda = ', ';
            }
            if (is_string($this->sigla_uf_cert_civil)) {
                $set .= "{$gruda}sigla_uf_cert_civil = '{$this->sigla_uf_cert_civil}'";
                $gruda = ', ';
            }
            if (is_string($this->sigla_uf_exp_rg)) {
                $set .= "{$gruda}sigla_uf_exp_rg = '{$this->sigla_uf_exp_rg}'";
                $gruda = ', ';
            }
            if (is_numeric($this->rg)) {
                $set .= "{$gruda}rg = '{$this->rg}'";
                $gruda = ', ';
            }
            if (is_string($this->data_expedicao_rg)) {
                $set .= "{$gruda}data_expedicao_rg = '{$this->data_expedicao_rg}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_titulo_eleitor)) {
                $set .= "{$gruda}num_titulo_eleitor = '{$this->num_titulo_eleitor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->zona_titulo_eleitor)) {
                $set .= "{$gruda}zona_titulo_eleitor = '{$this->zona_titulo_eleitor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->secao_titulo_eleitor)) {
                $set .= "{$gruda}secao_titulo_eleitor = '{$this->secao_titulo_eleitor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tipo_certidao_civil)) {
                $set .= "{$gruda}tipo_certidao_civil = '{$this->tipo_certidao_civil}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_termo)) {
                $set .= "{$gruda}num_termo = '{$this->num_termo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_folha)) {
                $set .= "{$gruda}num_folha = '{$this->num_folha}'";
                $gruda = ', ';
            }
            if (is_numeric($this->num_livro)) {
                $set .= "{$gruda}num_livro = '{$this->num_livro}'";
                $gruda = ', ';
            }
            if (is_string($this->data_emissao_certidao_civil)) {
                $set .= "{$gruda}data_emissao_certidao_civil = '{$this->data_emissao_certidao_civil}'";
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
    public function lista($int_ref_cod_pessoa_educ = null, $int_ref_idorg_rg = null, $str_sigla_uf_cert_civil = null, $str_sigla_uf_exp_rg = null, $int_rg = null, $date_data_expedicao_rg_ini = null, $date_data_expedicao_rg_fim = null, $int_num_titulo_eleitor = null, $int_zona_titulo_eleitor = null, $int_secao_titulo_eleitor = null, $int_tipo_certidao_civil = null, $int_num_termo = null, $int_num_folha = null, $int_num_livro = null, $date_data_emissao_certidao_civil_ini = null, $date_data_emissao_certidao_civil_fim = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_pessoa_educ)) {
            $filtros .= "{$whereAnd} ref_cod_pessoa_educ = '{$int_ref_cod_pessoa_educ}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idorg_rg)) {
            $filtros .= "{$whereAnd} ref_idorg_rg = '{$int_ref_idorg_rg}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla_uf_cert_civil)) {
            $filtros .= "{$whereAnd} sigla_uf_cert_civil LIKE '%{$str_sigla_uf_cert_civil}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla_uf_exp_rg)) {
            $filtros .= "{$whereAnd} sigla_uf_exp_rg LIKE '%{$str_sigla_uf_exp_rg}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_rg)) {
            $filtros .= "{$whereAnd} rg = '{$int_rg}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_expedicao_rg_ini)) {
            $filtros .= "{$whereAnd} data_expedicao_rg >= '{$date_data_expedicao_rg_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_expedicao_rg_fim)) {
            $filtros .= "{$whereAnd} data_expedicao_rg <= '{$date_data_expedicao_rg_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_num_titulo_eleitor)) {
            $filtros .= "{$whereAnd} num_titulo_eleitor = '{$int_num_titulo_eleitor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_zona_titulo_eleitor)) {
            $filtros .= "{$whereAnd} zona_titulo_eleitor = '{$int_zona_titulo_eleitor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_secao_titulo_eleitor)) {
            $filtros .= "{$whereAnd} secao_titulo_eleitor = '{$int_secao_titulo_eleitor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_tipo_certidao_civil)) {
            $filtros .= "{$whereAnd} tipo_certidao_civil = '{$int_tipo_certidao_civil}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_num_termo)) {
            $filtros .= "{$whereAnd} num_termo = '{$int_num_termo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_num_folha)) {
            $filtros .= "{$whereAnd} num_folha = '{$int_num_folha}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_num_livro)) {
            $filtros .= "{$whereAnd} num_livro = '{$int_num_livro}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_emissao_certidao_civil_ini)) {
            $filtros .= "{$whereAnd} data_emissao_certidao_civil >= '{$date_data_emissao_certidao_civil_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_emissao_certidao_civil_fim)) {
            $filtros .= "{$whereAnd} data_emissao_certidao_civil <= '{$date_data_emissao_certidao_civil_fim}'";
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
