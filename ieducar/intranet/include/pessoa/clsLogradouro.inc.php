<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsLogradouro
{
    public $idlog;
    public $idtlog;
    public $nome;
    public $idmun;
    public $geom;
    public $ident_oficial;
    public $idpes_cad;
    public $tabela;
    public $schema = 'public';

    public function __construct($int_idlog = false, $str_idtlog = false, $str_nome = false, $int_idmun = false, $str_geom = false, $str_ident_oficial = false, $idpes_cad = null)
    {
        $this->idlog = $int_idlog;

        $objLog = new clsTipoLogradouro($str_idtlog);
        if ($objLog->detalhe()) {
            $this->idtlog = $str_idtlog;
        }

        $this->nome = $str_nome;
        $this->idmun = $int_idmun;
        $this->geom = $str_geom;
        $this->ident_oficial = $str_ident_oficial;
        $this->idpes_cad = $idpes_cad;

        $this->tabela = 'logradouro';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_string($this->idtlog) && is_string($this->nome) && is_numeric($this->idmun) && is_string($this->ident_oficial)) {
            $campos = '';
            $values = '';

            if (is_string($this->geom)) {
                $campos .= ', geom';
                $values .= ", '{$this->geom}'";
            }

            if (is_string($this->idpes_cad)) {
                $campos .= ', idpes_cad';
                $values .= ", '{$this->idpes_cad}'";
            }

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} ( idtlog, nome, idmun, origem_gravacao, ident_oficial,data_cad, OPERACAO $campos ) VALUES ( '{$this->idtlog}', '{$this->nome}', '{$this->idmun}', 'U', '{$this->ident_oficial}', NOW(), 'I' $values )");

            return $db->InsertId("{$this->schema}.seq_logradouro");
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
        if (is_numeric($this->idlog) && is_string($this->idtlog) && is_string($this->nome) && is_numeric($this->idmun) && is_string($this->ident_oficial)) {
            $set = "SET idtlog = '{$this->idtlog}', nome = '{$this->nome}', idmun = '{$this->idmun}', ident_oficial = '{$this->ident_oficial}'";

            if (is_string($this->geom)) {
                $set .= ", geom = '{$this->geom}'";
            } else {
                $set .= ', geom = NULL';
            }

            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idlog = '$this->idlog'");

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
        if (is_numeric($this->idlog)) {
            $objEndPessoa = new clsEnderecoPessoa();
            $listaEndPessoa = $objEndPessoa->lista(false, false, false, false, false, $this->idlog);

            $objCepLog = new clsCepLogradouro();
            $listaCepLog = $objCepLog->lista(false, $this->idlog);

            $objCepLogBai = new clsCepLogradouroBairro();
            $listaCepLogBai = $objCepLogBai->lista($this->idlog);

            if (!count($listaEndPessoa) && !count($listaCepLog) && !count($listaCepLogBai)) {
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
    public function lista($str_idtlog = false, $str_nome = false, $int_idmun = false, $str_geom = false, $str_ident_oficial = false, $int_limite_ini = 0, $int_limite_qtd = 20, $str_orderBy = false, $int_idlog = false)
    {
        $whereAnd = 'WHERE ';
        if (is_string($str_idtlog)) {
            $where .= "{$whereAnd}fcn_upper_nrm( idtlog ) ILIKE fcn_upper_nrm('%$str_idtlog%')";
            $whereAnd = ' AND ';
        }
        if (is_string($int_idlog)) {
            $where .= "{$whereAnd}idlog  = '$int_idlog'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $str_nome = limpa_acentos($str_nome);
            $where .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmun)) {
            $where .= "{$whereAnd}idmun = '$int_idmun'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $where .= "{$whereAnd}geom LIKE '%$str_geom%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_ident_oficial)) {
            $where .= "{$whereAnd}ident_oficial LIKE '%$str_ident_oficial%'";
            $whereAnd = ' AND ';
        }

        if ($str_orderBy) {
            $orderBy = "ORDER BY $str_orderBy";
        }

        $limit = '';
        if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_qtd OFFSET $int_limite_ini";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');

        $db->Consulta("SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['idtlog'] = new clsTipoLogradouro($tupla['idtlog']);

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
        if ($this->idlog) {
            $db = new clsBanco();
            $db->Consulta("SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} WHERE idlog='{$this->idlog}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->idlog = $tupla['idlog'];
                $this->idtlog = $tupla['idtlog'];
                $this->nome = $tupla['nome'];
                $this->idmun = $tupla['idmun'];
                $this->geom = $tupla['geom'];
                $this->ident_oficial = $tupla['ident_oficial'];

                $tupla['idtlog'] = new clsTipoLogradouro($tupla['idtlog']);

                return $tupla;
            }
        }

        return false;
    }
}
