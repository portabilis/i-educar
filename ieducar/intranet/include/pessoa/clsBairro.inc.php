<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsBairro
{
    public $idbai;
    public $idmun;
    public $geom;
    public $nome;
    public $idpes_cad;
    public $idpes_rev;
    public $origem_gravacao;
    public $operacao;
    public $zona_localizacao;
    public $iddi;
    public $tabela;
    public $schema = 'public';

    public function __construct($int_idbai = false, $int_idmun = false, $str_geom = false, $str_nome = false, $int_idpes_cad = false, $int_idpes_rev = false, $str_origem_gravacao = false, $str_operacao = false, $int_idsis_cad = false, $int_idsis_rev = false, $zona_localizacao = null)
    {
        $this->idbai = $int_idbai;

        $objMun = new clsMunicipio($int_idmun);

        if ($objMun->detalhe()) {
            $this->idmun = $int_idmun;
        }

        $this->geom = $str_geom;
        $this->nome = $str_nome;
        $this->idpes_cad = $int_idpes_cad;
        $this->idpes_rev = $int_idpes_rev;
        $this->operacao = $str_operacao;
        $this->origem_gravacao = $str_origem_gravacao;
        $this->zona_localizacao = $zona_localizacao;

        $this->tabela = 'bairro';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idmun) && is_numeric($this->iddis) && is_string($this->nome)) {
            $campos = '';
            $values = '';

            if (is_string($this->geom)) {
                $campos .= ', geom';
                $values .= ", '{$this->geom}'";
            }
            if (is_numeric($this->idpes_cad)) {
                $campos .= ', idpes_cad';
                $values .= ", '{$this->idpes_cad}'";
            }
            if (is_string($this->operacao)) {
                $campos .= ', operacao';
                $values .= ", '{$this->operacao}'";
            }
            if (is_string($this->origem_gravacao)) {
                $campos .= ', origem_gravacao';
                $values .= ", '{$this->origem_gravacao}'";
            }
            if (is_numeric($this->zona_localizacao)) {
                $campos .= ', zona_localizacao';
                $values .= ", '{$this->zona_localizacao}' ";
            }
            if (is_numeric($this->iddis)) {
                $campos .= ', iddis';
                $values .= ", '{$this->iddis}' ";
            }

            $this->checkSequenceofBairro();

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( idmun, origem_gravacao, operacao, nome, data_cad$campos ) VALUES ( '{$this->idmun}', 'U', 'I', '{$this->nome}', NOW()$values )");

            return $db->InsertId("{$this->schema}.seq_bairro");
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
        if (is_numeric($this->idmun) && is_string($this->nome)) {
            $set = "SET idmun = '{$this->idmun}', nome = '{$this->nome}'";
            if (is_string($this->geom)) {
                $set .= ", geom = '{$this->geom}'";
            } else {
                $set .= ', geom = NULL';
            }

            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idbai = '$this->idbai'");

            return true;
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
        if (is_numeric($this->idbai)) {
            $objEndPes = new clsEnderecoPessoa();
            $listaEndPes = $objEndPes->lista(false, false, false, false, false, false, $this->idbai);

            $objCepLogBai = new clsCepLogradouroBairro();
            $listaCepLogBai = $objCepLogBai->lista(false, false, $this->idbai);

            if (!count($listaEndPes) && !count($listaCepLogBai)) {
                $db = new clsBanco();
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idmun = false, $str_geom = false, $str_nome = false, $int_limite_ini = false, $int_limite_qtd = false, $str_orderBy = false, $array_idbai_notin = false, $id_bairro = false)
    {
        $whereAnd = 'WHERE ';
        if (is_numeric($int_idmun)) {
            $where .= "{$whereAnd}idmun = '$int_idmun'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $where .= "{$whereAnd}geom LIKE '%$str_geom%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $where .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_array($array_idbai_notin)) {
            $implode = implode(',', $array_idbai_notin);
            $where .= "{$whereAnd}idbai NOT IN ($implode)";
            $whereAnd = ' AND ';
        }

        if (is_numeric($id_bairro)) {
            $where .= "{$whereAnd}idbai = '$id_bairro'";
            $whereAnd = ' AND ';
        }

        if ($str_orderBy) {
            $orderBy = "ORDER BY $str_orderBy";
        }
        $limit = '';
        if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
        }
        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT idbai,idmun, geom, nome FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['idmun'] = new clsMunicipio($tupla['idmun']);
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
        if ($this->idbai) {
            $db = new clsBanco();
            $db->Consulta("SELECT idbai, idmun, geom, nome FROM {$this->schema}.{$this->tabela} WHERE idbai='{$this->idbai}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->idbai = $tupla['idbai'];
                $this->idmun = $tupla['idmun'];
                $this->geom = $tupla['geom'];
                $this->nome = $tupla['nome'];
                $tupla['idmun'] = new clsMunicipio($tupla['idmun']);

                return $tupla;
            }
        }

        return false;
    }

    /**
     * Retorna o ultimo registro cadastrado
     *
     * @return integer
     */
    public function UltimaChave()
    {
        $db = new clsBanco();
        $db->Consulta("SELECT MAX(idbai) FROM {$this->schema}.{$this->tabela}");
        if ($db->ProximoRegistro()) {
            list($chave) = $db->Tupla();

            return $chave;
        }

        return false;
    }

    /**
     * Altera o sequencial do bairro caso o mesmo esteja irregular
     */
    public function checkSequenceofBairro()
    {
        $db = new clsBanco();
        $db->Consulta('SELECT last_value AS seq_bairro FROM public.seq_bairro;');
        $db->ProximoRegistro();
        $sequencial = $db->Tupla();
        $sequencial = $sequencial['seq_bairro'];

        $ultimoId = $this->UltimaChave();

        if ($ultimoId >= $sequencial) {
            $proximoId = $ultimoId + 1;
            $db->Consulta("SELECT setval('public.seq_bairro',{$proximoId},false);");
        }
    }
}
