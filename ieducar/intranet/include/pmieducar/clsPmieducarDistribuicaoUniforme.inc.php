<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarDistribuicaoUniforme extends Model
{
    public $cod_distribuicao_uniforme;

    public $ref_cod_aluno;

    public $ano;

    public $kit_completo;

    public $agasalho_qtd;

    public $camiseta_curta_qtd;

    public $camiseta_longa_qtd;

    public $meias_qtd;

    public $bermudas_tectels_qtd;

    public $bermudas_coton_qtd;

    public $tenis_qtd;

    public $data;

    public $agasalho_tm;

    public $camiseta_curta_tm;

    public $camiseta_longa_tm;

    public $meias_tm;

    public $bermudas_tectels_tm;

    public $bermudas_coton_tm;

    public $tenis_tm;

    public $ref_cod_escola;

    public $camiseta_infantil_qtd;

    public $camiseta_infantil_tm;

    public $calca_jeans_qtd;

    public $calca_jeans_tm;

    public $saia_qtd;

    public $saia_qtm;

    public function __construct(
        $cod_distribuicao_uniforme = null,
        $ref_cod_aluno = null,
        $ano = null,
        $kit_completo = null,
        $agasalho_qtd = null,
        $camiseta_curta_qtd = null,
        $camiseta_longa_qtd = null,
        $meias_qtd = null,
        $bermudas_tectels_qtd = null,
        $bermudas_coton_qtd = null,
        $tenis_qtd = null,
        $data = null,
        $agasalho_tm = null,
        $camiseta_curta_tm = null,
        $camiseta_longa_tm = null,
        $meias_tm = null,
        $bermudas_tectels_tm = null,
        $bermudas_coton_tm = null,
        $tenis_tm = null,
        $ref_cod_escola = null,
        $camiseta_infantil_qtd = null,
        $camiseta_infantil_tm = null,
        $calca_jeans_qtd = null,
        $calca_jeans_tm = null,
        $saia_qtd = null,
        $saia_tm = null
    ) {
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}distribuicao_uniforme";

        $this->_campos_lista = $this->_todos_campos = ' 
            cod_distribuicao_uniforme, ref_cod_aluno, ano, kit_completo, 
            agasalho_qtd, camiseta_curta_qtd, camiseta_longa_qtd, meias_qtd,
            bermudas_tectels_qtd, bermudas_coton_qtd, tenis_qtd, data, 
            agasalho_tm, camiseta_curta_tm, camiseta_longa_tm, meias_tm, 
            bermudas_tectels_tm, bermudas_coton_tm, tenis_tm, ref_cod_escola,
            camiseta_infantil_qtd, camiseta_infantil_tm, calca_jeans_qtd, 
            calca_jeans_tm, saia_qtd, saia_tm
        ';

        if (is_numeric($cod_distribuicao_uniforme)) {
            $this->cod_distribuicao_uniforme = $cod_distribuicao_uniforme;
        }

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }

        if (is_numeric($ano)) {
            $this->ano = $ano;
        }

        $this->kit_completo = $kit_completo;

        if (is_numeric($agasalho_qtd)) {
            $this->agasalho_qtd = $agasalho_qtd;
        }

        if (is_numeric($camiseta_curta_qtd)) {
            $this->camiseta_curta_qtd = $camiseta_curta_qtd;
        }

        if (is_numeric($camiseta_longa_qtd)) {
            $this->camiseta_longa_qtd = $camiseta_longa_qtd;
        }

        if (is_numeric($meias_qtd)) {
            $this->meias_qtd = $meias_qtd;
        }

        if (is_numeric($bermudas_tectels_qtd)) {
            $this->bermudas_tectels_qtd = $bermudas_tectels_qtd;
        }

        if (is_numeric($bermudas_coton_qtd)) {
            $this->bermudas_coton_qtd = $bermudas_coton_qtd;
        }

        if (is_numeric($tenis_qtd)) {
            $this->tenis_qtd = $tenis_qtd;
        }

        if (is_string($data)) {
            $this->data = $data;
        }

        if (is_numeric($camiseta_infantil_qtd)) {
            $this->camiseta_infantil_qtd = $camiseta_infantil_qtd;
        }

        if (is_string($camiseta_infantil_tm)) {
            $this->camiseta_infantil_tm = $camiseta_infantil_tm;
        }

        if (is_numeric($calca_jeans_qtd)) {
            $this->calca_jeans_qtd = $calca_jeans_qtd;
        }

        if (is_numeric($saia_qtd)) {
            $this->saia_qtd = $saia_qtd;
        }

        $this->agasalho_tm = $agasalho_tm;
        $this->camiseta_curta_tm = $camiseta_curta_tm;
        $this->camiseta_longa_tm = $camiseta_longa_tm;
        $this->meias_tm = $meias_tm;
        $this->bermudas_tectels_tm = $bermudas_tectels_tm;
        $this->bermudas_coton_tm = $bermudas_coton_tm;
        $this->tenis_tm = $tenis_tm;
        $this->ref_cod_escola = $ref_cod_escola;
        $this->calca_jeans_tm = $calca_jeans_tm;
        $this->saia_tm = $saia_tm;
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ano)) {
            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            $campos .= "{$gruda}ref_cod_aluno";
            $valores .= "{$gruda}{$this->ref_cod_aluno}";
            $gruda = ', ';

            $campos .= "{$gruda}ano";
            $valores .= "{$gruda}{$this->ano}";
            $gruda = ', ';

            if (dbBool($this->kit_completo)) {
                $campos .= "{$gruda}kit_completo";
                $valores .= "{$gruda} TRUE ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}kit_completo";
                $valores .= "{$gruda} FALSE ";
                $gruda = ', ';
            }

            if (is_numeric($this->agasalho_qtd)) {
                $campos .= "{$gruda}agasalho_qtd";
                $valores .= "{$gruda}{$this->agasalho_qtd}";
                $gruda = ', ';
            }

            if (is_numeric($this->camiseta_curta_qtd)) {
                $campos .= "{$gruda}camiseta_curta_qtd";
                $valores .= "{$gruda}{$this->camiseta_curta_qtd}";
                $gruda = ', ';
            }

            if (is_numeric($this->camiseta_longa_qtd)) {
                $campos .= "{$gruda}camiseta_longa_qtd";
                $valores .= "{$gruda}{$this->camiseta_longa_qtd}";
                $gruda = ', ';
            }

            if (is_numeric($this->meias_qtd)) {
                $campos .= "{$gruda}meias_qtd";
                $valores .= "{$gruda}{$this->meias_qtd}";
                $gruda = ', ';
            }

            if (is_numeric($this->bermudas_tectels_qtd)) {
                $campos .= "{$gruda}bermudas_tectels_qtd";
                $valores .= "{$gruda}{$this->bermudas_tectels_qtd}";
                $gruda = ', ';
            }

            if (is_numeric($this->bermudas_coton_qtd)) {
                $campos .= "{$gruda}bermudas_coton_qtd";
                $valores .= "{$gruda}{$this->bermudas_coton_qtd}";
                $gruda = ', ';
            }

            if (is_numeric($this->tenis_qtd)) {
                $campos .= "{$gruda}tenis_qtd";
                $valores .= "{$gruda}{$this->tenis_qtd}";
                $gruda = ', ';
            }

            if (is_string($this->data)) {
                $campos .= "{$gruda}data";
                $valores .= "{$gruda}'{$this->data}'";
                $gruda = ', ';
            }
            if (is_string($this->agasalho_tm)) {
                $campos .= "{$gruda}agasalho_tm";
                $valores .= "{$gruda}'{$this->agasalho_tm}'";
                $gruda = ', ';
            }

            if (is_string($this->camiseta_curta_tm)) {
                $campos .= "{$gruda}camiseta_curta_tm";
                $valores .= "{$gruda}'{$this->camiseta_curta_tm}'";
                $gruda = ', ';
            }

            if (is_string($this->camiseta_longa_tm)) {
                $campos .= "{$gruda}camiseta_longa_tm";
                $valores .= "{$gruda}'{$this->camiseta_longa_tm}'";
                $gruda = ', ';
            }

            if (is_string($this->meias_tm)) {
                $campos .= "{$gruda}meias_tm";
                $valores .= "{$gruda}'{$this->meias_tm}'";
                $gruda = ', ';
            }

            if (is_string($this->bermudas_tectels_tm)) {
                $campos .= "{$gruda}bermudas_tectels_tm";
                $valores .= "{$gruda}'{$this->bermudas_tectels_tm}'";
                $gruda = ', ';
            }

            if (is_string($this->bermudas_coton_tm)) {
                $campos .= "{$gruda}bermudas_coton_tm";
                $valores .= "{$gruda}'{$this->bermudas_coton_tm}'";
                $gruda = ', ';
            }

            if (is_string($this->tenis_tm)) {
                $campos .= "{$gruda}tenis_tm";
                $valores .= "{$gruda}'{$this->tenis_tm}'";
                $gruda = ', ';
            }

            if ($this->ref_cod_escola) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}{$this->ref_cod_escola}";
                $gruda = ', ';
            }

            if (is_numeric($this->camiseta_infantil_qtd)) {
                $campos .= "{$gruda}camiseta_infantil_qtd";
                $valores .= "{$gruda}{$this->camiseta_infantil_qtd}";
                $gruda = ', ';
            }

            if (is_string($this->camiseta_infantil_tm)) {
                $campos .= "{$gruda}camiseta_infantil_tm";
                $valores .= "{$gruda}'{$this->camiseta_infantil_tm}'";
                $gruda = ', ';
            }

            if (is_numeric($this->calca_jeans_qtd)) {
                $campos .= "{$gruda}calca_jeans_qtd";
                $valores .= "{$gruda}{$this->calca_jeans_qtd}";
                $gruda = ', ';
            }

            if (is_string($this->calca_jeans_tm)) {
                $campos .= "{$gruda}calca_jeans_tm";
                $valores .= "{$gruda}'{$this->calca_jeans_tm}'";
                $gruda = ', ';
            }

            if (is_numeric($this->saia_qtd)) {
                $campos .= "{$gruda}saia_qtd";
                $valores .= "{$gruda}{$this->saia_qtd}";
                $gruda = ', ';
            }

            if (is_string($this->saia_tm)) {
                $campos .= "{$gruda}saia_tm";
                $valores .= "{$gruda}'{$this->saia_tm}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->insertId("{$this->_tabela}_seq");
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
        if (is_numeric($this->cod_distribuicao_uniforme)) {
            $db  = new clsBanco();
            $set = '';

            if (is_numeric($this->ano)) {
                $set .= " ano = '{$this->ano}' ";
            } else {
                return false;
            }

            if (dbBool($this->kit_completo)) {
                $set .= ',kit_completo = TRUE ';
            } else {
                $set .= ',kit_completo = FALSE';
            }

            if (is_numeric($this->agasalho_qtd)) {
                $set .= ",agasalho_qtd = '{$this->agasalho_qtd}'";
            } else {
                $set .= ',agasalho_qtd = NULL';
            }

            if (is_numeric($this->camiseta_curta_qtd)) {
                $set .= ",camiseta_curta_qtd = '{$this->camiseta_curta_qtd}'";
            } else {
                $set .= ',camiseta_curta_qtd = NULL';
            }

            if (is_numeric($this->camiseta_longa_qtd)) {
                $set .= ",camiseta_longa_qtd = '{$this->camiseta_longa_qtd}'";
            } else {
                $set .= ',camiseta_longa_qtd = NULL';
            }

            if (is_numeric($this->meias_qtd)) {
                $set .= ",meias_qtd = '{$this->meias_qtd}'";
            } else {
                $set .= ',meias_qtd = NULL';
            }

            if (is_numeric($this->bermudas_tectels_qtd)) {
                $set .= ",bermudas_tectels_qtd = '{$this->bermudas_tectels_qtd}'";
            } else {
                $set .= ',bermudas_tectels_qtd = NULL';
            }

            if (is_numeric($this->bermudas_coton_qtd)) {
                $set .= ",bermudas_coton_qtd = '{$this->bermudas_coton_qtd}'";
            } else {
                $set .= ',bermudas_coton_qtd = NULL';
            }

            if (is_numeric($this->tenis_qtd)) {
                $set .= ",tenis_qtd = '{$this->tenis_qtd}'";
            } else {
                $set .= ',tenis_qtd = NULL';
            }

            if (is_string($this->data)) {
                $set .= ",data = '{$this->data}'";
            }

            if ($this->agasalho_tm) {
                $set .= ",agasalho_tm = '{$this->agasalho_tm}'";
            } else {
                $set .= ',agasalho_tm = NULL';
            }

            if ($this->camiseta_curta_tm) {
                $set .= ",camiseta_curta_tm = '{$this->camiseta_curta_tm}'";
            } else {
                $set .= ',camiseta_curta_tm = NULL';
            }

            if ($this->camiseta_longa_tm) {
                $set .= ",camiseta_longa_tm = '{$this->camiseta_longa_tm}'";
            } else {
                $set .= ',camiseta_longa_tm = NULL';
            }

            if ($this->meias_tm) {
                $set .= ",meias_tm = '{$this->meias_tm}'";
            } else {
                $set .= ',meias_tm = NULL';
            }

            if ($this->bermudas_tectels_tm) {
                $set .= ",bermudas_tectels_tm = '{$this->bermudas_tectels_tm}'";
            } else {
                $set .= ',bermudas_tectels_tm = NULL';
            }

            if ($this->bermudas_coton_tm) {
                $set .= ",bermudas_coton_tm = '{$this->bermudas_coton_tm}'";
            } else {
                $set .= ',bermudas_coton_tm = NULL';
            }

            if ($this->tenis_tm) {
                $set .= ",tenis_tm = '{$this->tenis_tm}'";
            } else {
                $set .= ',tenis_tm = NULL';
            }
            if ($this->ref_cod_escola) {
                $set .= ",ref_cod_escola = '{$this->ref_cod_escola}'";
            } else {
                $set .= ',ref_cod_escola = NULL';
            }

            if ($this->camiseta_infantil_qtd) {
                $set .= ",camiseta_infantil_qtd = '{$this->camiseta_infantil_qtd}'";
            } else {
                $set .= ',camiseta_infantil_qtd = NULL';
            }

            if ($this->camiseta_infantil_tm) {
                $set .= ",camiseta_infantil_tm = '{$this->camiseta_infantil_tm}'";
            } else {
                $set .= ',camiseta_infantil_tm = NULL';
            }

            if ($this->calca_jeans_qtd) {
                $set .= ",calca_jeans_qtd = '{$this->calca_jeans_qtd}'";
            } else {
                $set .= ',calca_jeans_qtd = NULL';
            }

            if ($this->calca_jeans_tm) {
                $set .= ",calca_jeans_tm = '{$this->calca_jeans_tm}'";
            } else {
                $set .= ',calca_jeans_tm = NULL';
            }

            if ($this->saia_qtd) {
                $set .= ",saia_qtd = '{$this->saia_qtd}'";
            } else {
                $set .= ',saia_qtd = NULL';
            }

            if ($this->saia_tm) {
                $set .= ",saia_tm = '{$this->saia_tm}'";
            } else {
                $set .= ',saia_tm = NULL';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'");

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
    public function lista($ref_cod_aluno = null, $ano = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = ' WHERE TRUE ';

        if (is_numeric($ref_cod_aluno)) {
            $filtros .= " AND ref_cod_aluno = {$ref_cod_aluno} ";
        }

        if (is_numeric($ano)) {
            $filtros .= " AND ano = {$ano} ";
        }

        $db = new clsBanco();
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
        if (is_numeric($this->cod_distribuicao_uniforme)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'");
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
    public function detalhePorAlunoAno()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ano)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ano = '{$this->ano}' AND ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
        if (is_numeric($this->cod_distribuicao_uniforme)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'");
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
        if (is_numeric($this->cod_distribuicao_uniforme)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
