<?php

use Illuminate\Support\Facades\Session;

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsEnderecoExterno
{
    public $idpes;
    public $idpes_cad;
    public $idpes_rev;
    public $tipo;
    public $idtlog;
    public $logradouro;
    public $numero;
    public $letra;
    public $complemento;
    public $bairro;
    public $cep;
    public $cidade;
    public $sigla_uf;
    public $reside_desde;
    public $bloco;
    public $apartamento;
    public $andar;
    public $zona_localizacao;
    public $tabela;
    public $schema = 'cadastro';

    public function __construct(
        $idpes = false,
        $tipo = false,
        $idtlog = false,
        $logradouro = false,
        $numero = false,
        $letra = false,
        $complemento = false,
        $bairro = false,
        $cep = false,
        $cidade = false,
        $sigla_uf = false,
        $reside_desde = false,
        $bloco = false,
        $apartamento = false,
        $andar = false,
        $idpes_cad = false,
        $idpes_rev = false,
        $zona_localizacao = 1
    ) {
        $idtlog = urldecode($idtlog);

        $objPessoa = new clsPessoa_($idpes);
        if ($objPessoa->detalhe()) {
            $this->idpes = $idpes;
        }

        $this->tipo = $tipo;

        $objTipoLog = new clsTipoLogradouro($idtlog);
        if ($objTipoLog->detalhe()) {
            $this->idtlog = $idtlog;
        }

        $this->logradouro = $logradouro;
        $this->numero = $numero;
        $this->letra = $letra;
        $this->complemento = $complemento;
        $this->bairro = $bairro;
        $this->cep = $cep;
        $this->cidade = $cidade;

        $objSiglaUf = new clsUf($sigla_uf);
        if ($objPessoa->detalhe()) {
            $this->sigla_uf = $sigla_uf;
        }

        $this->idpes_cad = $idpes_cad ? $idpes_cad : Session::get('id_pessoa');
        $this->idpes_rev = $idpes_rev ? $idpes_rev : Session::get('id_pessoa');
        $this->reside_desde = $reside_desde;
        $this->bloco = $bloco;
        $this->apartamento = $apartamento;
        $this->andar = $andar;
        $this->zona_localizacao = $zona_localizacao;

        $this->tabela = 'endereco_externo';
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (
            is_numeric($this->tipo) && is_string($this->idtlog) &&
            is_string($this->logradouro) && is_string($this->cidade) &&
            is_string($this->sigla_uf) && is_numeric($this->idpes_cad)) {
            $campos = '';
            $values = '';

            if (is_numeric($this->numero)) {
                $campos .= ', numero';
                $values .= ", '{$this->numero}'";
            }

            if (is_string($this->letra)) {
                $campos .= ', letra';
                $values .= ", '{$this->letra}'";
            }

            if (is_string($this->complemento)) {
                $campos .= ', complemento';
                $values .= ", '{$this->complemento}'";
            }

            if (is_string($this->bairro)) {
                $campos .= ', bairro';
                $values .= ", '{$this->bairro}'";
            }

            if ($this->cep) {
                $campos .= ', cep';
                $values .= ", '{$this->cep}'";
            }

            if (is_string($this->reside_desde)) {
                $campos .= ', reside_desde';
                $values .= ", '{$this->reside_desde}'";
            }

            if (is_string($this->bloco)) {
                $campos .= ', bloco';
                $values .= ", '{$this->bloco}'";
            }

            if (is_numeric($this->apartamento)) {
                $campos .= ', apartamento';
                $values .= ", '{$this->apartamento}'";
            }

            if (is_numeric($this->andar)) {
                $campos .= ', andar';
                $values .= ", '{$this->andar}'";
            }

            if (is_numeric($this->zona_localizacao)) {
                $campos .= ', zona_localizacao';
                $values .= ", '{$this->zona_localizacao}'";
            }

            $sql = sprintf(
                'INSERT INTO %s.%s (idpes, tipo, idtlog, logradouro, cidade, sigla_uf, ' .
                'origem_gravacao, data_cad, operacao, idpes_cad %s) ' .
                'VALUES (\'%d\', 1, \'%s\', \'%s\', \'%s\', \'%s\', \'M\', NOW(), \'I\', \'%d\' %s)',
                $this->schema,
                $this->tabela,
                $campos,
                $this->idpes,
                $this->idtlog,
                $this->logradouro,
                $this->cidade,
                $this->sigla_uf,
                $this->idpes_cad,
                $values
            );

            $db->Consulta($sql);

            return true;
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
        if (
            is_numeric($this->tipo) && is_string($this->idtlog) &&
            is_string($this->logradouro) && is_string($this->cidade) &&
            is_string($this->sigla_uf) && is_numeric($this->idpes_rev)
        ) {
            $set = sprintf(
                'SET tipo = \'%d\', idtlog = \'%s\', logradouro = \'%s\', cidade = \'%s\', sigla_uf = \'%s\'',
                $this->tipo,
                $this->idtlog,
                $this->logradouro,
                $this->cidade,
                $this->sigla_uf
            );

            if (is_numeric($this->numero)) {
                $set .= ", numero = '{$this->numero}'";
            } else {
                $set .= ', numero = NULL';
            }

            if (is_string($this->letra)) {
                $set .= ", letra = '{$this->letra}'";
            } else {
                $set .= ', letra = NULL';
            }

            if (is_string($this->complemento)) {
                $set .= ", complemento = '{$this->complemento}'";
            } else {
                $set .= ', complemento = NULL';
            }

            if (is_string($this->bairro)) {
                $set .= ", bairro = '{$this->bairro}'";
            } else {
                $set .= ', bairro = NULL';
            }

            if (is_numeric($this->cep)) {
                $set .= ", cep = '{$this->cep}'";
            } else {
                $set .= ', cep = NULL';
            }

            if (is_string($this->reside_desde)) {
                $set .= ", reside_desde = '{$this->reside_desde}'";
            } else {
                $set .= ', reside_desde = NULL';
            }

            if (is_string($this->bloco)) {
                $set .= ", bloco = '{$this->bloco}'";
            } else {
                $set .= ', bloco = NULL';
            }

            if (is_numeric($this->apartamento)) {
                $set .= ", apartamento = '{$this->apartamento}'";
            } else {
                $set .= ', apartamento = NULL';
            }

            if (is_numeric($this->andar)) {
                $set .= ", andar = '{$this->andar}'";
            } else {
                $set .= ', andar = NULL';
            }

            if (is_numeric($this->zona_localizacao)) {
                $set .= ", zona_localizacao = '{$this->zona_localizacao}'";
            }

            if (is_numeric($this->idpes_rev)) {
                $set .= ", idpes_rev = '$this->idpes_rev'";
            }

            $sql = sprintf(
                'UPDATE %s.%s %s WHERE idpes = \'%d\' AND tipo = 1',
                $this->schema,
                $this->tabela,
                $set,
                $this->idpes
            );

            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function exclui()
    {
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();

            $db->Consulta(sprintf(
                'DELETE FROM %s.%s WHERE idpes = \'%d\'',
                $this->schema,
                $this->tabela,
                $this->idpes
            ));

            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $str_idtlog = false,
        $str_logradouro = false,
        $int_numero = false,
        $str_letra = false,
        $str_complemento = false,
        $str_bairro = false,
        $int_cep = false,
        $str_cidade = false,
        $sigla_uf = false,
        $str_reside_desde = false,
        $str_bloco = false,
        $int_apartamento = false,
        $int_andar = false,
        $int_limite_ini = 0,
        $int_limite_qtd = 20,
        $str_orderBy = false,
        $int_idpes = false,
        $zona_localizacao = null
    ) {
        $whereAnd = ' WHERE ';

        if (is_string($int_idpes)) {
            $where .= "{$whereAnd}idpes IN ({$int_idpes})";
            $whereAnd = ' AND ';
        }

        if (is_string($str_idtlog)) {
            $where .= "{$whereAnd}idtlog LIKE '%$str_idtlog%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_logradouro)) {
            $where .= "{$whereAnd}logradouro LIKE '%$str_logradouro%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_numero)) {
            $where .= "{$whereAnd}numero = '$int_numero'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_letra)) {
            $where .= "{$whereAnd}letra LIKE '%$str_letra%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_complemento)) {
            $where .= "{$whereAnd}complemento LIKE '%$str_complemento%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_bairro)) {
            $where .= "{$whereAnd}bairro LIKE '%$str_bairro%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cep)) {
            $where .= "{$whereAnd}cep = '$int_cep'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_cidade)) {
            $where .= "{$whereAnd}cidade LIKE '%$str_cidade%'";
            $whereAnd = ' AND ';
        }

        if (is_string($sigla_uf)) {
            $where .= "{$whereAnd}sigla_uf LIKE '%$sigla_uf%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_reside_desde)) {
            $where .= "{$whereAnd}reside_desde LIKE '%$str_reside_desde%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_bloco)) {
            $where .= "{$whereAnd}bloco = '$str_bloco'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_apartamento)) {
            $where .= "{$whereAnd}apartamento = '$int_apartamento'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_andar)) {
            $where .= "{$whereAnd}andar = '$int_andar'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($zona_localizacao)) {
            $where .= "{$whereAnd}zona_localizacao = '$zona_localizacao'";
            $whereAnd = ' AND ';
        }

        if ($str_orderBy) {
            $orderBy = "ORDER BY $str_orderBy";
        }

        $limit = '';

        if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_ini, $int_limite_qtd";
        }

        $db = new clsBanco();

        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');

        $sql = sprintf(
            'SELECT idpes, tipo, idtlog, logradouro, numero, letra, complemento, ' .
            'bairro, cep, cidade, sigla_uf, reside_desde, bloco, apartamento, ' .
            'andar, zona_localizacao FROM %s.%s %s %s %s',
            $this->schema,
            $this->tabela,
            $where,
            $orderBy,
            $limit
        );

        $db->Consulta($sql);

        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            $tupla['idpes'] = new clsPessoa_($tupla['idpes']);
            $tupla['idtlog'] = new clsTipoLogradouro($tupla['idtlog']);
            $tupla['sigla_uf'] = new clsUf($tupla['sigla_uf']);
            $tupla['total'] = $total;

            $resultado[] = $tupla;
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
        if ($this->idpes) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT idpes, tipo, idtlog, logradouro, numero, letra, complemento, ' .
                'bairro, cep, cidade, sigla_uf, reside_desde, bloco, apartamento, ' .
                'andar, zona_localizacao FROM %s.%s WHERE idpes = %d',
                $this->schema,
                $this->tabela,
                $this->idpes
            );

            $db->Consulta($sql);

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->idpes = $tupla['idpes'];
                $this->tipo = $tupla['tipo'];
                $this->idtlog = $tupla['idtlog'];
                $this->logradouro = $tupla['logradouro'];
                $this->numero = $tupla['numero'];
                $this->letra = $tupla['letra'];
                $this->complemento = $tupla['complemento'];
                $this->bairro = $tupla['bairro'];
                $this->cep = $tupla['cep'];
                $this->cidade = $tupla['cidade'];
                $this->sigla_uf = $tupla['sigla_uf'];
                $this->reside_desde = $tupla['reside_desde'];
                $this->bloco = $tupla['bloco'];
                $this->apartamento = $tupla['apartamento'];
                $this->andar = $tupla['andar'];
                $this->zona_localizacao = $tupla['zona_localizacao'];

                $tupla['idpes'] = new clsPessoa_($tupla['idpes']);
                $tupla['idtlog'] = new clsTipoLogradouro($tupla['idtlog']);
                $tupla['sigla_uf'] = new clsUf($tupla['sigla_uf']);

                return $tupla;
            }
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
        if (is_numeric($this->idpes)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT 1 FROM %s.%s WHERE idpes = %d',
                $this->schema,
                $this->tabela,
                $this->idpes
            );

            $db->Consulta($sql);

            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
