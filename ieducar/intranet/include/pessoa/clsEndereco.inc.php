<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsEndereco
{
    public $idpes;
    public $tipo;
    public $idtlog;
    public $logradouro;
    public $idlog;
    public $numero;
    public $letra;
    public $complemento;
    public $bairro;
    public $idbai;
    public $cep;
    public $cidade;
    public $idmun;
    public $sigla_uf;
    public $reside_desde;
    public $bloco;
    public $apartamento;
    public $andar;
    public $zona_localizacao;

    public function __construct($idpes = false)
    {
        $this->idpes = $idpes;
    }

    /**
     * Retorna o endereço da pessoa cadastrada como array associativo.
     *
     * @return array|FALSE caso não haja um endereço cadastrado.
     */
    public function detalhe()
    {
        if ($this->idpes) {
            $db = new clsBanco();

            $sql = sprintf('SELECT
                cep, idlog, numero, letra, complemento, idbai, bloco, andar,
                apartamento, logradouro, bairro, cidade, sigla_uf, idtlog,
                zona_localizacao
              FROM
                cadastro.v_endereco
              WHERE
                idpes = %d', $this->idpes);

            $db->Consulta($sql);

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->bairro = $tupla['bairro'];
                $this->idbai = $tupla['idbai'];
                $this->cidade = $tupla['cidade'];
                $this->sigla_uf = $tupla['sigla_uf'];
                $this->complemento = $tupla['complemento'];
                $this->bloco = $tupla['bloco'];
                $this->apartamento = $tupla['apartamento'];
                $this->andar = $tupla['andar'];
                $this->letra = $tupla['letra'];
                $this->numero = $tupla['numero'];
                $this->logradouro = $tupla['logradouro'];
                $this->idlog = $tupla['idlog'];
                $this->idtlog = $tupla['idtlog'];
                $this->cep = $tupla['cep'];
                $this->zona_localizacao = $tupla['zona_localizacao'];

                return $tupla;
            }
        }

        return false;
    }

    public function edita()
    {
    }
}
