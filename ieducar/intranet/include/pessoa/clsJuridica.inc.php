<?php

use Illuminate\Support\Facades\Session;

require_once('include/clsBanco.inc.php');
require_once('include/Geral.inc.php');
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsJuridica
{
    public $idpes;
    public $idpes_cad;
    public $idpes_rev;
    public $cnpj;
    public $fantasia;
    public $insc_estadual;
    public $capital_social;
    public $codUsuario;
    public $tabela;
    public $schema;

    /**
     * Construtor
     *
     * @return Object:clsEstadoCivil
     */
    public function __construct($idpes = false, $cnpj = false, $fantasia = false, $insc_estadual = false, $capital_social = false, $idpes_cad = false, $idpes_rev = false)
    {
        $this->pessoa_logada = Session::get('id_pessoa');

        $objPessoa = new clsPessoa_($idpes);
        if ($objPessoa->detalhe()) {
            $this->idpes = $idpes;
        }

        if (config('legacy.app.uppercase_names')) {
            $fantasia = Str::upper($fantasia);
        }

        $this->fantasia = $fantasia;
        $this->cnpj = $cnpj;
        $this->insc_estadual = $insc_estadual;
        $this->capital_social = $capital_social;
        $this->idpes_cad = $idpes_cad ? $idpes_cad : Session::get('id_pessoa');
        $this->idpes_rev = $idpes_rev ? $idpes_rev : Session::get('id_pessoa');

        $this->tabela = 'juridica';
        $this->schema = 'cadastro';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idpes) && is_numeric($this->cnpj) && is_numeric($this->idpes_cad)) {
            $campos = '';
            $valores = '';
            if ($this->fantasia) {
                $campos .= ', fantasia';
                $valores .= ", '$this->fantasia'";
            }
            if (is_numeric($this->insc_estadual)) {
                $campos .= ', insc_estadual';
                $valores .= ", '$this->insc_estadual' ";
            }
            if (is_string($this->capital_social)) {
                $campos .= ', capital_social';
                $valores .= ", '{$this->capital_social}' ";
            }

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (idpes, cnpj, origem_gravacao, data_cad, operacao, idpes_cad $campos) VALUES ($this->idpes, '$this->cnpj', 'M', NOW(), 'I', '$this->idpes_cad' $valores)");

            if ($this->idpes) {
                $detalhe = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('juridica', $this->pessoa_logada, $this->idpes);
                $auditoria->inclusao($detalhe);
            }

            return true;
        }

        return false;
    }

    /**
     * Edita o registro atual
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->idpes) && is_numeric($this->idpes_rev)) {
            $set = [];
            if (is_string($this->fantasia)) {
                $set[] = " fantasia = '$this->fantasia' ";
            }

            if (is_numeric($this->insc_estadual)) {
                if ($this->insc_estadual) {
                    $set[] = " insc_estadual = '$this->insc_estadual' ";
                } else {
                    $set[] = ' insc_estadual = NULL ';
                }
            } else {
                $set[] = ' insc_estadual = NULL ';
            }

            if (is_string($this->capital_social)) {
                $set[] = " capital_social = '$this->capital_social' ";
            }

            if ($this->idpes_rev) {
                $set[] = " idpes_rev = '$this->idpes_rev' ";
            }

            if (is_numeric($this->cnpj)) {
                $set[] = " cnpj = '$this->cnpj' ";
            }

            if ($set) {
                $campos = implode(', ', $set);
                $db = new clsBanco();
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->schema}.{$this->tabela} SET $campos WHERE idpes = '$this->idpes' ");

                $auditoria = new clsModulesAuditoriaGeral('juridica', $this->pessoa_logada, $this->idpes);
                $auditoria->alteracao($detalheAntigo, $this->detalhe());

                return true;
            }
        }

        return false;
    }

    /**
     * Remove o registro atual
     *
     * @return bool
     */
    public function exclui()
    {
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();
            $detalheAntigo = $this->detalhe();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes}");
            $auditoria = new clsModulesAuditoriaGeral('juridica', $this->pessoa_logada, $this->idpes);
            $auditoria->exclusao($detalheAntigo, $this->detalhe());

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($str_fantasia = false, $str_insc_estadual = false, $int_cnpj = false, $str_ordenacao = false, $int_limite_ini = false, $int_limite_qtd = false, $arrayint_idisin = false, $arrayint_idnotin = false, $int_idpes = false)
    {
        $whereAnd = 'WHERE ';
        $join = '';
        if (is_string($str_fantasia)) {
            $where .= "{$whereAnd} (fcn_upper_nrm(fantasia) LIKE fcn_upper_nrm('%$str_fantasia%') OR fcn_upper_nrm(nome) LIKE fcn_upper_nrm('%$str_fantasia%'))";
            $whereAnd = ' AND ';
        }
        if (is_string($str_insc_estadual)) {
            $where .= "{$whereAnd}insc_estadual ILIKE  '%$str_insc_estadual%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd}idpes = '$int_idpes'";
            $whereAnd = ' AND ';
        }
        if ($this->codUsuario) {
            $where .= "{$whereAnd}idpes IN (SELECT ref_idpes
                                              FROM pmieducar.escola
                                             INNER JOIN pmieducar.escola_usuario ON (escola_usuario.ref_cod_escola = escola.cod_escola)
                                             WHERE ref_cod_usuario = $this->codUsuario
                                               AND escola.ativo = 1)";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cnpj)) {
            $i = 0;
            while (substr($int_cnpj, $i, 1) == 0) {
                $i++;
            }
            if ($i > 0) {
                $int_cnpj = substr($int_cnpj, $i);
            }
            $where .= "{$whereAnd} cnpj::varchar ILIKE  '%$int_cnpj%' ";
            $whereAnd = ' AND ';
        }

        if (is_array($arrayint_idisin)) {
            $ok = true;
            foreach ($arrayint_idisin as $val) {
                if (!is_numeric($val)) {
                    $ok = false;
                }
            }
            if ($ok) {
                $where .= "{$whereAnd}idpes IN ( " . implode(',', $arrayint_idisin) . ' )';
                $whereAnd = ' AND ';
            }
        }

        if (is_array($arrayint_idnotin)) {
            $ok = true;
            foreach ($arrayint_idnotin as $val) {
                if (!is_numeric($val)) {
                    $ok = false;
                }
            }
            if ($ok) {
                $where .= "{$whereAnd}idpes NOT IN ( " . implode(',', $arrayint_idnotin) . ' )';
                $whereAnd = ' AND ';
            }
        }

        $orderBy = '';
        if (is_string($str_ordenacao)) {
            $orderBy = "ORDER BY $str_ordenacao";
        }
        $limit = '';
        if ($int_limite_ini !== false && $int_limite_qtd !== false) {
            $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.v_pessoa_juridica $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT idpes, cnpj, fantasia, insc_estadual, capital_social FROM {$this->schema}.v_pessoa_juridica $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['total'] = $total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os detalhes do objeto
     *
     * @return Array
     */
    public function detalhe()
    {
        if ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpes, cnpj, fantasia, insc_estadual, capital_social FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        } elseif ($this->cnpj) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpes, cnpj, fantasia, insc_estadual, capital_social FROM {$this->schema}.{$this->tabela} WHERE cnpj = {$this->cnpj}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
