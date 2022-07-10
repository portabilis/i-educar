<?php

class clsPessoaTelefone
{
    public $idpes;
    public $ddd;
    public $fone;
    public $tipo;
    public $idpes_cad;
    public $idpes_rev;
    public $banco = 'gestao_homolog';
    public $schema_cadastro = 'cadastro';
    public $tabela_telefone = 'fone_pessoa';

    public function __construct($int_idpes = false, $int_tipo = false, $str_fone = false, $str_ddd = false, $idpes_cad = false, $idpes_rev = false)
    {
        $this->idpes = $int_idpes;
        $this->ddd = $str_ddd;
        $this->fone = $str_fone;
        $this->tipo = $int_tipo;
        $this->idpes_cad = $idpes_cad ? $idpes_cad : \Illuminate\Support\Facades\Auth::id();
        $this->idpes_rev = $idpes_rev ? $idpes_rev : \Illuminate\Support\Facades\Auth::id();
    }

    public function cadastra()
    {
        // Cadastro do telefone da pessoa na tabela fone_pessoa
        if ($this->idpes && $this->tipo && $this->idpes_cad) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->schema_cadastro}.{$this->tabela_telefone} WHERE idpes = '$this->idpes' AND tipo = '$this->tipo'");
            // Verifica se ja existe um telefone desse tipo cadastrado para essa pessoa
            if (!$db->numLinhas()) {
                // nao tem, cadastra 1 novo
                if (!empty($this->ddd) && !empty($this->fone)) {
                    $ddd = preg_replace('/\D/', '', $this->ddd);
                    $fone = preg_replace('/\D/', '', $this->fone);
                    $db->Consulta("INSERT INTO {$this->schema_cadastro}.{$this->tabela_telefone} (idpes, tipo, ddd, fone,origem_gravacao, data_cad, operacao, idpes_cad) VALUES ('$this->idpes', '$this->tipo', '$ddd', '$fone','M', NOW(), 'I', '$this->idpes_cad')");

                    return true;
                }
            } else {
                // jah tem, edita
                $this->edita();

                return true;
            }
        }

        return false;
    }

    public function edita()
    {
        // Cadastro do telefone da pessoa na tabela fone_pessoa
        if ($this->idpes && $this->tipo && $this->idpes_rev) {
            $set = false;
            $gruda = '';
            if ($this->ddd) {
                $set = "ddd = $this->ddd";
                $gruda = ', ';
            }
            if ($this->fone) {
                $set .= "$gruda fone = $this->fone";
                $gruda = ', ';
            } elseif ($this->fone == '') {
                $set .= "$gruda fone = NULL";
                $gruda = ', ';
            }
            if ($this->idpes_rev) {
                $set .= "$gruda idpes_rev = '$this->idpes_rev'";
                $gruda = ', ';
            }
            if ($set && $this->ddd != '' && $this->fone != '') {
                $db = new clsBanco();
                $db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela_telefone} SET $set WHERE idpes = $this->idpes AND tipo = $this->tipo");

                return true;
            } else {
                if ($this->ddd == '' && $this->fone == '') {
                    $this->exclui();
                }
            }
        }

        return false;
    }

    public function exclui()
    {
        if ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_telefone WHERE idpes = $this->idpes AND tipo = $this->tipo");

            return true;
        }

        return false;
    }

    public function excluiTodos()
    {
        // exclui todos os telefones da pessoa, nao importa o tipo
        if ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_telefone WHERE idpes = $this->idpes");

            return true;
        }

        return false;
    }

    public function lista($int_idpes = false, $str_ordenacao = false, $int_inicio_limite = false, $int_qtd_registros = false, $int_ddd = false, $int_fone = false)
    {
        $whereAnd = 'WHERE ';
        $where = '';
        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd}idpes = '$int_idpes'";
            $whereAnd = ' AND ';
        } elseif (is_string($int_idpes)) {
            $where .= "{$whereAnd}idpes IN ($int_idpes)";
            $whereAnd = ' AND ';
        }

        if (isset($str_tipo_pessoa) && is_string($str_tipo_pessoa)) {
            $where .= "{$whereAnd}tipo = '$str_tipo_pessoa' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ddd)) {
            $where .= "{$whereAnd}ddd = '$int_ddd' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_fone)) {
            $where .= "{$whereAnd}fone = '$int_fone' ";
            $whereAnd = ' AND ';
        }

        $limite = '';
        if ($int_inicio_limite !== false && $int_qtd_registros) {
            $limite = "LIMIT $int_qtd_registros OFFSET $int_inicio_limite ";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM $this->schema_cadastro.$this->tabela_telefone $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');

        $db = new clsBanco($this->banco);
        $db = new clsBanco();

        $db->Consulta("SELECT idpes, tipo, ddd, fone FROM $this->schema_cadastro.$this->tabela_telefone $where $limite");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
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
        if ($this->idpes && $this->tipo) {
            $db = new clsBanco();
            $db->Consulta("SELECT tipo, ddd, fone FROM cadastro.fone_pessoa WHERE idpes = $this->idpes AND tipo = '$this->tipo' ");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        } elseif ($this->idpes && !$this->tipo) {
            $db = new clsBanco();
            $db->Consulta("SELECT tipo, ddd, fone FROM cadastro.fone_pessoa WHERE idpes = $this->idpes ");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }
}
