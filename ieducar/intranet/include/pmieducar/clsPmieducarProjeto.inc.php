<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarProjeto extends Model
{
    public $cod_projeto;
    public $nome;
    public $observacao;

    public function __construct($cod_projeto = null, $nome = null, $observacao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}projeto";

        $this->_campos_lista = $this->_todos_campos = 'cod_projeto, nome, observacao ';

        if (is_numeric($cod_projeto)) {
            $this->cod_projeto = $cod_projeto;
        }
        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->nome)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}'{$this->nome}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$this->observacao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_projeto) && is_string($this->nome)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '{$this->nome}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $set .= "{$gruda}observacao = '{$this->observacao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_projeto = '{$this->cod_projeto}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($cod_projeto = null, $nome = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_projeto)) {
            $filtros .= "{$whereAnd} cod_projeto = '{$cod_projeto}'";
            $whereAnd = ' AND ';
        }
        if (is_string($nome)) {
            $filtros .= "{$whereAnd} nome ILIKE '%{$nome}%'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
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
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_projeto)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_projeto = '{$this->cod_projeto}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_projeto)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_projeto = '{$this->cod_projeto}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_projeto)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_projeto = '{$this->cod_projeto}'");

            return true;
        }

        return false;
    }

    public function deletaProjetosDoAluno($alunoId)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM pmieducar.projeto_aluno WHERE ref_cod_aluno = {$alunoId}");

        return true;
    }

    public function cadastraProjetoDoAluno($alunoId, $projetoId, $dataInclusao, $dataDesligamento, $turnoId)
    {
        if ($this->alunoPossuiProjeto($alunoId, $projetoId)) {
            return false;
        }
        $dataInclusao = '\'' . $dataInclusao . '\'';
        $dataDesligamento = !empty($dataDesligamento) ? '\'' . $dataDesligamento . '\'' : 'NULL';
        $db = new clsBanco();
        $db->Consulta("INSERT INTO pmieducar.projeto_aluno (ref_cod_aluno, ref_cod_projeto, data_inclusao, data_desligamento, turno) VALUES ({$alunoId},{$projetoId}, $dataInclusao, $dataDesligamento, $turnoId)");

        return true;
    }

    public function listaProjetosPorAluno($alunoId)
    {
        $db = new clsBanco();
        $db->Consulta("SELECT nome as projeto,
                                   data_inclusao,
                                   data_desligamento,
                                   turno
                              FROM  pmieducar.projeto_aluno,
                                    pmieducar.projeto
                              WHERE ref_cod_projeto = cod_projeto
                              AND ref_cod_aluno = {$alunoId} ");

        while ($db->ProximoRegistro()) {
            $resultado[] = $db->Tupla();
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function alunoPossuiProjeto($alunoId, $projetoId)
    {
        $db = new clsBanco();
        $db->Consulta("SELECT 1
                          FROM  pmieducar.projeto_aluno,
                                pmieducar.projeto
                          WHERE ref_cod_projeto = cod_projeto
                          AND ref_cod_aluno = {$alunoId}
                          AND ref_cod_projeto = {$projetoId}");

        return $db->ProximoRegistro();
    }
}
