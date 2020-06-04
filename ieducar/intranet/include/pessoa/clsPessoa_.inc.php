<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

require_once 'include/clsBanco.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsPessoa_
{
    public $idpes;
    public $nome;
    public $idpes_cad;
    public $data_cad;
    public $url;
    public $tipo;
    public $idpes_rev;
    public $data_rev;
    public $situacao;
    public $origem_gravacao;
    public $email;
    public $pessoa_logada;
    public $banco = 'gestao_homolog';
    public $schema_cadastro = 'cadastro';
    public $tabela_pessoa = 'pessoa';
    public $tabela_endereco = 'endereco_pessoa';
    public $tabela_telefone = 'fone_pessoa';

    public function __construct($int_idpes = false, $str_nome = false, $int_idpes_cad = false, $str_url = false, $int_tipo = false, $int_idpes_rev = false, $str_data_rev = false, $str_email = false)
    {
        $this->pessoa_logada = Session::get('id_pessoa');

        $this->idpes = $int_idpes;
        $this->nome = $str_nome;
        $this->idpes_cad = $int_idpes_cad ? $int_idpes_cad : Session::get('id_pessoa');
        $this->url = $str_url;
        $this->tipo = $int_tipo;
        $this->idpes_rev = is_numeric($int_idpes_rev) ? $int_idpes_rev : Session::get('id_pessoa');
        $this->data_rev = $str_data_rev;
        $this->email = $str_email;
    }

    public function cadastra()
    {
        if ($this->nome && $this->tipo) {
            $this->nome = $this->cleanUpName($this->nome);
            $this->nome = str_replace('\'', '\'\'', $this->nome);
            $campos = '';
            $valores = '';
            if ($this->url) {
                $campos .= ', url';
                $valores .= ", '$this->url' ";
            }
            if ($this->email) {
                $campos .= ', email';
                $valores .= ", '$this->email' ";
            }
            if ($this->idpes_cad) {
                $campos .= ', idpes_cad';
                $valores .= ", '$this->idpes_cad' ";
            }

            $db = new clsBanco();

            $slug = Str::lower(Str::slug($this->nome, ' '));

            $db->Consulta("INSERT INTO {$this->schema_cadastro}.{$this->tabela_pessoa} (nome, slug, data_cad,tipo,situacao,origem_gravacao, operacao $campos) VALUES ('$this->nome', '{$slug}', NOW(), '$this->tipo', 'P', 'U', 'I' $valores)");
            $this->idpes = $db->InsertId("{$this->schema_cadastro}.seq_pessoa");
            if ($this->idpes) {
                $detalhe = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('pessoa', $this->pessoa_logada, $this->idpes);
                $auditoria->inclusao($detalhe);
            }

            return $this->idpes;
        }
    }

    public function edita()
    {
        if ($this->idpes) {
            $set = '';
            $gruda = '';

            if ($this->url || $this->url === '') {
                $set .= " url =  '$this->url' ";
                $gruda = ', ';
            }
            if ($this->email || $this->email === '') {
                $set .= "$gruda email = '$this->email' ";
                $gruda = ', ';
            }
            if ($this->nome || $this->nome === '') {
                $this->nome = $this->cleanUpName($this->nome);
                $this->nome = str_replace('\'', '\'\'', $this->nome);

                $slug = Str::lower(Str::slug($this->nome, ' '));

                $set .= "$gruda nome = '$this->nome', slug = '{$slug}' ";
                $gruda = ', ';
            }

            if ($this->idpes_rev) {
                $set .= "$gruda idpes_rev = '$this->idpes_rev'";
            }
            if ($set) {
                $db = new clsBanco();
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela_pessoa} SET $set, data_rev = 'NOW()' WHERE idpes = $this->idpes");
                $auditoria = new clsModulesAuditoriaGeral('pessoa', $this->pessoa_logada, $this->idpes);
                $auditoria->alteracao($detalheAntigo, $this->detalhe());

                return true;
            }
        }
    }

    public function exclui()
    {
        if ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_pessoa WHERE idpes = $this->idpes");

            return true;
        }

        return false;
    }

    public function lista($str_nome = false, $inicio_limite = false, $qtd_registros = false, $str_orderBy = false, $arrayint_idisin = false, $arrayint_idnotin = false, $str_tipo_pessoa = false, $str_email = false, $str_data_cad_ini = false, $str_data_cad_fim = false)
    {
        $whereAnd = 'WHERE ';
        $where = '';
        if (is_string($str_nome)) {
            $str_nome = str_replace(' ', '%', $str_nome);
            $where .= "{$whereAnd} nome ILIKE '%{$str_nome}%' ";
            $whereAnd = ' AND ';
        }
        if (is_string($str_tipo_pessoa)) {
            $where .= "{$whereAnd}tipo = '$str_tipo_pessoa' ";
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
        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        if (is_string($str_data_cad_ini)) {
            if (!$str_data_edicao_fim) {
                $where .= "{$whereAnd}data_cad >= '$str_data_cad_ini 00:00:00' AND data_cad <= '$str_data_cad_ini 23:59:59'";
                $whereAnd = ' AND ';
            } else {
                $where .= "{$whereAnd}data_cad >= '$str_data_cad_ini'";
                $whereAnd = ' AND ';
            }
        }

        if (is_string($str_data_cad_fim)) {
            $where .= "{$whereAnd}data_cad <= '$str_data_cad_fim'";
            $whereAnd = ' AND ';
        }

        $orderBy = ' ORDER BY ';
        if ($str_orderBy) {
            $orderBy .= "$str_orderBy ";
        } else {
            $orderBy .= 'nome ';
        }

        if (is_string($str_email)) {
            $where .= "{$whereAnd}email ILIKE '%$str_email%' ";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco($this->banco);
        $total = $db->UnicoCampo("SELECT count(0) FROM cadastro.pessoa $where");

        $db->Consulta("SELECT idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, situacao, origem_gravacao, email FROM cadastro.pessoa $where $orderBy $limite");

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $nome = mb_strtolower($tupla['nome']);
            $arrayNome = explode(' ', $nome);
            $nome = '';
            foreach ($arrayNome as $parte) {
                if ($parte != 'de' && $parte != 'da' && $parte != 'dos' && $parte != 'do' && $parte != 'das' && $parte != 'e') {
                    $nome .= mb_strtoupper(mb_substr($parte, 0, 1)) . mb_substr($parte, 1) . ' ';
                } else {
                    $nome .= $parte . ' ';
                }
            }
            $tupla['nome'] = $nome;
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
        if (is_numeric($this->idpes)) {
            $db = new clsBanco($this->banco);
            $db->Consulta("SELECT idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, situacao, origem_gravacao, email FROM cadastro.pessoa WHERE idpes = $this->idpes ");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $nome = mb_strtolower($tupla['nome']);
                $arrayNome = explode(' ', $nome);
                $arrNovoNome = [];
                foreach ($arrayNome as $parte) {
                    if ($parte != 'de' && $parte != 'da' && $parte != 'dos' && $parte != 'do' && $parte != 'das' && $parte != 'e') {
                        if ($parte != 's.a' && $parte != 'ltda') {
                            $arrNovoNome[] = mb_strtoupper(mb_substr($parte, 0, 1)) . mb_substr($parte, 1);
                        } else {
                            $arrNovoNome[] = mb_strtoupper($parte);
                        }
                    } else {
                        $arrNovoNome[] = $parte;
                    }
                }
                $nome = implode(' ', $arrNovoNome);
                $tupla['nome'] = $nome;
                list($this->idpes, $this->nome, $this->idpes_cad, $this->data_cad, $this->url, $this->tipo, $this->idpes_rev, $this->data_rev, $this->situacao, $this->origem_gravacao, $this->email) = $tupla;

                return $tupla;
            }
        }

        return false;
    }

    protected function cleanUpName($name)
    {
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }
}
