<?php

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarAlunoCMF
{
    /**
     * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
     *
     * @var int
     */
    public $_total;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo metodo lista
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no metodo lista
     *
     * @var int
     */
    public $_limite_offset;

    public function __construct()
    {
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($nome_aluno = null, $cpf_aluno = null, $nome_responsavel = null, $cpf_responsavel = null, $cod_sistema = 1)
    {
        $where_aluno = '';
        $where_responsavel = '';
        $where_sistema = '';

        if (is_numeric($cod_sistema)) {
            $where_sistema .= "AND (cpf_aluno.cpf is not null OR cpf_aluno.ref_cod_sistema = {$cod_sistema} )";
        } else {
            $where_sistema .= 'cpf_aluno is not null';
        }

        if (is_string($nome_aluno)) {
            $table_join = ',cadastro.pessoa       pessoa_aluno';
            $where_join = 'AND cpf_aluno.idpes    = pessoa_aluno.idpes';
            $where_aluno .= "AND (lower(pessoa_aluno.nome)) like  (lower('%{$nome_aluno}%')) ";
        }
        if (is_numeric($cpf_aluno)) {
            $where_aluno .= "AND cpf_aluno.cpf like '%{$cpf_aluno}%' ";
        }

        if (is_string($nome_responsavel)) {
            $where_responsavel .= "AND (lower(pessoa_resp.nome)) like  (lower('%{$nome_responsavel}%')) ";
        }
        if (is_numeric($cpf_responsavel)) {
            $where_responsavel .= "AND cpf_resp.cpf like '%{$cpf_responsavel}%' ";
        }
        if (!empty($where_responsavel)) {
            $where_responsavel = " AND EXISTS (SELECT 1
                                                 FROM cadastro.pessoa       pessoa_resp
                                                      ,cadastro.fisica  cpf_resp
                                                      ,cadastro.fisica     fisica_resp
                                                WHERE cpf_resp.idpes    = pessoa_resp.idpes
                                                  AND pessoa_resp.idpes = fisica_resp.idpes
                                                  AND fisica_aluno.idpes_responsavel = pessoa_resp.idpes
                                                  {$where_responsavel}
                                                  AND cpf_resp.cpf is not null
                                             )";
        }

        $campos_select = 'SELECT pessoa_aluno.idpes as cod_aluno
                      ,pessoa_aluno.nome as nome_aluno
                      ,lower(trim((pessoa_aluno.nome))) as nome_ascii
                      ,cpf_aluno.cpf as cpf_aluno
                      ,cpf_aluno.idpes_responsavel as idpes_responsavel';

        $sql = "
                 FROM cadastro.pessoa       pessoa_aluno
                      ,cadastro.fisica      cpf_aluno
                WHERE cpf_aluno.idpes    = pessoa_aluno.idpes
                  AND cpf_aluno.cpf is not null
                  {$where_sistema}
                  {$where_aluno}
                  {$where_responsavel}";

        $sql_count = "
                 FROM cadastro.fisica      cpf_aluno
                     $table_join
                WHERE cpf_aluno.cpf is not null
                  $where_join
                  {$where_sistema}
                  {$where_aluno}
                  {$where_responsavel}";

        $db = new clsBanco();

        $this->_total = $total = $db->CampoUnico("SELECT COUNT(1) {$sql_count}");

        $db->Consulta("{$campos_select} {$sql} ORDER BY nome_aluno " . $this->getLimite());

        $resultado = [];

        if ($total >= 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $resultado[] = ['nome_aluno' => $tupla['nome_aluno'], 'cpf_aluno' => $tupla['cpf_aluno'], 'cod_aluno' => $tupla['cod_aluno'], 'idpes_responsavel' => $tupla['idpes_responsavel']];
            }

            return $resultado;
        }

        return false;
    }

    /**
     * Define limites de retorno para o metodo lista
     *
     * @return null
     */
    public function setLimite($intLimiteQtd, $intLimiteOffset = null)
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query resposavel pelo Limite de registros
     *
     * @return string
     */
    public function getLimite()
    {
        if (is_numeric($this->_limite_quantidade)) {
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if (is_numeric($this->_limite_offset)) {
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }

            return $retorno;
        }

        return '';
    }
}
