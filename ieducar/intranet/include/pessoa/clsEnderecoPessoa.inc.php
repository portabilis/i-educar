<?php

use Illuminate\Support\Facades\Session;

require_once 'include/clsBanco.inc.php';

class clsPessoaEndereco
{
    public $idpes;
    public $idpes_cad;
    public $idpes_rev;
    public $tipo;
    public $cep;
    public $idlog;
    public $idbai;
    public $numero;
    public $complemento;
    public $reside_desde;
    public $letra;
    public $bloco;
    public $apartamento;
    public $andar;
    public $observacoes;

    public $banco = 'gestao_homolog';
    public $schema_cadastro = 'cadastro';
    public $tabela = 'endereco_pessoa';

    public function __construct(
        $int_idpes = false,
        $numeric_cep = false,
        $int_idlog = false,
        $int_idbai = false,
        $numeric_numero = false,
        $str_complemento = false,
        $date_reside_desde = false,
        $str1_letra = false,
        $str_bloco = false,
        $int_apartamento = false,
        $int_andar = false,
        $idpes_cad = false,
        $idpes_rev = false,
        $observacoes = null
    ) {
        $this->idpes = $int_idpes;
        $numeric_cep = idFederal2Int($numeric_cep);

        $obj = new clsCepLogradouroBairro($int_idlog, $numeric_cep, $int_idbai);

        if ($obj->detalhe()) {
            $this->idbai = $int_idbai;
            $this->idlog = $int_idlog;
            $this->cep = $numeric_cep;
        }

        $this->numero = $numeric_numero;
        $this->complemento = $str_complemento;
        $this->reside_desde = $date_reside_desde;
        $this->letra = $str1_letra;
        $this->bloco = $str_bloco;
        $this->apartamento = $int_apartamento;
        $this->andar = $int_andar;
        $this->idpes_cad = $idpes_cad ? $idpes_cad : Session::get('id_pessoa');
        $this->idpes_rev = $idpes_rev ? $idpes_rev : Session::get('id_pessoa');
        $this->observacoes = $observacoes;
    }

    public function cadastra()
    {
        if ($this->idpes && $this->cep && $this->idlog && $this->idbai &&
            $this->idpes_cad) {
            $campos = '';
            $valores = '';

            if ($this->numero) {
                $campos .= ', numero';
                $valores .= ", '$this->numero' ";
            }

            if ($this->letra) {
                $campos .= ', letra';
                $valores .= ", '$this->letra' ";
            }

            if ($this->complemento) {
                $campos .= ', complemento';
                $valores .= ", '$this->complemento' ";
            }

            if ($this->reside_desde) {
                $campos .= ', reside_desde';
                $valores .= ", '$this->reside_desde' ";
            }

            if ($this->bloco) {
                $campos .= ', bloco';
                $valores .= ", '$this->bloco' ";
            }

            if ($this->apartamento) {
                $campos .= ', apartamento';
                $valores .= ", '$this->apartamento' ";
            }

            if ($this->andar) {
                $campos .= ', andar';
                $valores .= ", '$this->andar' ";
            }

            if ($this->observacoes) {
                $observacoes = pg_escape_string($this->observacoes);

                $campos .= ', observacoes';
                $valores .= ", '$observacoes' ";
            }

            $sql = sprintf(
                'INSERT INTO %s.%s (idpes, tipo, cep, idlog, idbai, origem_gravacao, ' .
                'data_cad, operacao, idpes_cad %s) VALUES (\'%d\', \'1\', ' .
                '\'%s\', \'%s\', \'%d\', \'M\', NOW(), \'I\', \'%d\' %s)',
                $this->schema_cadastro,
                $this->tabela,
                $campos,
                $this->idpes,
                $this->cep,
                $this->idlog,
                $this->idbai,
                $this->idpes_cad,
                $valores
            );

            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function edita()
    {
        if ($this->idpes && $this->idpes_rev) {
            $setVir = ' SET ';
            $set = '';

            if ($this->numero) {
                $set .= "$setVir numero = '$this->numero' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir numero = NULL ";
                $setVir = ', ';
            }

            if ($this->letra) {
                $set .= "$setVir letra = '$this->letra' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir letra = NULL ";
                $setVir = ', ';
            }

            if ($this->complemento) {
                $set .= "$setVir complemento = '$this->complemento' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir complemento = NULL ";
                $setVir = ', ';
            }

            if ($this->reside_desde) {
                $set .= "$setVir reside_desde = '$this->reside_desde' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir reside_desde = NULL ";
                $setVir = ', ';
            }

            if ($this->bloco) {
                $set .= "$setVir bloco = '$this->bloco' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir bloco = NULL ";
                $setVir = ', ';
            }

            if ($this->apartamento) {
                $set .= "$setVir apartamento = '$this->apartamento' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir apartamento = NULL ";
                $setVir = ', ';
            }

            if ($this->andar) {
                $set .= "$setVir andar = '$this->andar' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir andar = NULL ";
                $setVir = ', ';
            }

            if ($this->observacoes) {
                $observacoes = pg_escape_string($this->observacoes);

                $set .= "$setVir observacoes = '$observacoes' ";
                $setVir = ', ';
            } else {
                $set .= "$setVir observacoes = NULL ";
                $setVir = ', ';
            }

            if ($this->cep && $this->idbai && $this->idlog) {
                $set .= "$setVir cep = '$this->cep', idbai = '$this->idbai', idlog = '$this->idlog'";
                $setVir = ', ';
            }

            if ($this->idpes_rev) {
                $set .= "$setVir idpes_rev ='$this->idpes_rev'";
            }

            if ($set) {
                $db = new clsBanco();
                $db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela} $set WHERE idpes = $this->idpes");

                return true;
            }
        }

        return false;
    }

    public function exclui()
    {
        if ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta(sprintf(
                'DELETE FROM %s.%s WHERE idpes = %d',
                $this->schema_cadastro,
                $this->tabela,
                $this->idpes
            ));
        }
    }

    public function lista(
        $int_idpes = false,
        $str_ordenacao = false,
        $int_inicio_limite = false,
        $int_qtd_limite = false,
        $int_cep = false,
        $int_idlog = false,
        $int_idbai = false,
        $int_numero = false,
        $str_bloco = false,
        $int_apartamento = false,
        $int_andar = false,
        $str_letra = false,
        $str_complemento = false
    ) {
        $whereAnd = ' AND ';
        $where = '';

        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd}idpes = '$int_idpes' ";
            $whereAnd = ' AND ';
        } elseif (is_string($int_idpes)) {
            $where .= "{$whereAnd}idpes IN ({$int_idpes}) ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cep)) {
            $where .= "{$whereAnd}cep = '$int_cep' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idlog)) {
            $where .= "{$whereAnd}idlog = '$int_idlog' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idbai)) {
            $where .= "{$whereAnd}idbai = '$int_idbai' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_numero)) {
            $where .= "{$whereAnd}numero = '$int_numero' ";
            $whereAnd = ' AND ';
        }

        if ($str_bloco) {
            $where .= "{$whereAnd}bloco = '$str_bloco' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_apartamento)) {
            $where .= "{$whereAnd}apartamento = '$int_apartamento' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_andar)) {
            $where .= "{$whereAnd}andar = '$int_andar' ";
            $whereAnd = ' AND ';
        }

        if (is_string($str_letra)) {
            $where .= "{$whereAnd}letra = '$str_letra' ";
            $whereAnd = ' AND ';
        }

        if (is_string($str_complemento)) {
            $where .= "{$whereAnd}complemento ILIKE '%$str_complemento%' ";
            $whereAnd = ' AND ';
        }

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        if ($str_orderBy) {
            $orderBy .= " ORDER BY $str_orderBy ";
        }

        $db = new clsBanco();

        $sql = sprintf(
            'SELECT COUNT(0) AS total FROM %s.%s WHERE tipo = 1 %s',
            $this->schema_cadastro,
            $this->tabela,
            $where
        );

        $db->Consulta($sql);
        $db->ProximoRegistro();
        $total = $db->Campo('total');

        $db = new clsBanco($this->banco);

        $sql = sprintf(
            'SELECT idpes, tipo, cep, idlog, numero, letra, complemento, reside_desde, ' .
            'idbai, bloco, apartamento, andar FROM %s.%s WHERE tipo = 1 %s %s %s',
            $this->schema_cadastro,
            $this->tabela,
            $where,
            $orderBy,
            $limite
        );

        $db->Consulta($sql);
        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['cep'] = new clsCepLogradouro($tupla['cep'], $tupla['idlog']);
            $tupla['idlog'] = new clsCepLogradouro($tupla['cep'], $tupla['idlog']);
            $tupla['idbai'] = new clsPublicBairro(null, null, $tupla['idbai']);

            $bairro = $tupla['idbai']->detalhe();

            $tupla['zona_localizacao'] = $bairro['zona_localizacao'];

            $tupla['total'] = $total;

            $resultado[] = $tupla;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    public function detalhe()
    {
        if ($this->idpes) {
            $db = new clsBanco($this->banco);

            $sql = sprintf(
                'SELECT idpes, tipo, cep, idlog, numero, letra, complemento, ' .
                'reside_desde, idbai, bloco, apartamento, andar, observacoes ' .
                'FROM %s.%s WHERE idpes = %d',
                $this->schema_cadastro,
                $this->tabela,
                $this->idpes
            );

            $db->Consulta($sql);

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $cep = $tupla['cep'];

                $tupla['cep'] = new clsCepLogradouro($cep, $tupla['idlog']);
                $tupla['zona_localizacao'] = null;

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
                $this->schema_cadastro,
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
