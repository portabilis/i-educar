<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarServidorFuncao extends Model
{
    public $cod_servidor_funcao;
    public $ref_ref_cod_instituicao;
    public $ref_cod_servidor;
    public $ref_cod_funcao;
    public $matricula;

    public function __construct($ref_ref_cod_instituicao = null, $ref_cod_servidor = null, $ref_cod_funcao = null, $matricula = null, $cod_servidor_funcao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}servidor_funcao";

        $this->_campos_lista = $this->_todos_campos = ' cod_servidor_funcao, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_funcao, matricula';

        if (is_numeric($ref_cod_funcao)) {
                    $this->ref_cod_funcao = $ref_cod_funcao;
        }
        if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
                    $this->ref_cod_servidor = $ref_cod_servidor;
                    $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;

            if (is_string($matricula)) {
                $this->matricula = $matricula;
            }
        }

        if (is_numeric($cod_servidor_funcao)) {
            $this->cod_servidor_funcao = $cod_servidor_funcao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_cod_funcao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_funcao)) {
                $campos .= "{$gruda}ref_cod_funcao";
                $valores .= "{$gruda}'{$this->ref_cod_funcao}'";
                $gruda = ', ';
            }
            if (is_string($this->matricula)) {
                $campos .= "{$gruda}matricula";
                $valores .= "{$gruda}'{$this->matricula}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId('pmieducar.servidor_funcao_seq');
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
        $set = [];

        if (empty($this->matricula)) {
            $set[] = 'matricula = NULL';
        } elseif (is_string($this->matricula)) {
            $set[] = "matricula = '". $this->matricula ."'";
        }

        if (is_numeric($this->ref_cod_funcao)) {
            $set[] = 'ref_cod_funcao = ' . $this->ref_cod_funcao;
        }

        $where = [];

        if (is_numeric($this->cod_servidor_funcao)) {
            $where[] = 'cod_servidor_funcao = ' . $this->cod_servidor_funcao;
        } elseif (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_cod_funcao)) {
            $where[] = 'ref_ref_cod_instituicao = ' . $this->ref_ref_cod_instituicao;
            $where[] = 'ref_cod_servidor = ' . $this->ref_cod_servidor;
            $where[] = 'ref_cod_funcao = ' . $this->ref_cod_funcao;
        }

        if (empty($set) || empty($where)) {
            return false;
        }

        $db = new clsBanco();
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s;',
            $this->_tabela,
            join(', ', $set),
            join(' AND ', $where)
        );

        $db->Consulta($sql);

        return true;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     *
     * @return array
     */
    public function lista($int_ref_ref_cod_instituicao = null, $int_ref_cod_servidor = null, $int_ref_cod_funcao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_funcao)) {
            $filtros .= "{$whereAnd} ref_cod_funcao = '{$int_ref_cod_funcao}'";
            $whereAnd = ' AND ';
        }

        if (is_string($matricula)) {
            $filtros .= "{$whereAnd} matricula = '{$matricula}'";
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
        if (is_numeric($this->cod_servidor_funcao)) {
            $sql = sprintf(
                'SELECT %s FROM %s WHERE cod_servidor_funcao = \'%d\'',
                $this->_todos_campos,
                $this->_tabela,
                $this->cod_servidor_funcao
            );
            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor)) {
            $sql = sprintf(
                'SELECT %s FROM %s WHERE ref_ref_cod_instituicao = \'%d\' AND ref_cod_servidor = \'%d\'',
                $this->_todos_campos,
                $this->_tabela,
                $this->ref_ref_cod_instituicao,
                $this->ref_cod_servidor
            );

            if (is_numeric($this->ref_cod_funcao)) {
                $sql .= sprintf(' AND ref_cod_funcao = \'%d\'', $this->ref_cod_funcao);
            }

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna true se o registro existir. Caso contrário retorna false.
     *
     * @return bool
     */
    public function existe()
    {
        $sql = '';

        if (is_numeric($this->cod_servidor_funcao)) {
            $sql = sprintf(
                'SELECT 1 FROM %s WHERE cod_servidor_funcao = \'%d\'',
                $this->_tabela,
                $this->cod_servidor_funcao
            );
        } elseif (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_cod_funcao)) {
            $sql = sprintf(
                'SELECT 1 FROM %s WHERE ref_ref_cod_instituicao = \'%d\' AND ref_cod_servidor = \'%d\' AND ref_cod_funcao = \'%d\'',
                $this->_tabela,
                $this->ref_ref_cod_instituicao,
                $this->ref_cod_servidor,
                $this->ref_cod_funcao
            );
        }

        if ($sql === '') {
            return false;
        }

        $db = new clsBanco();

        $db->Consulta($sql);

        if ($db->ProximoRegistro()) {
            return true;
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
        if (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_cod_funcao)) {
        }

        return false;
    }

    /**
     * Exclui todos registros
     *
     * @return bool
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'");

            return true;
        }

        return false;
    }

    /**
     * Exclui vinculos das funções removidas
     *
     * @return bool
     */
    public function excluirFuncoesRemovidas($funcoes)
    {
        if (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_cod_servidor) && is_array($funcoes)) {
            $delete = "DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'";
            if (!empty($funcoes)) {
                $delete .= ' AND cod_servidor_funcao NOT IN (' . implode($funcoes, ',') . ')';
            }

            $db = new clsBanco();
            $db->Consulta($delete);

            return true;
        }

        return false;
    }

    public function funcoesDoServidor($int_ref_ref_cod_instituicao, $int_ref_cod_servidor)
    {
        $sql = ' SELECT sf.cod_servidor_funcao, f.nm_funcao as funcao, sf.matricula
                            FROM pmieducar.servidor_funcao sf
                            INNER JOIN pmieducar.funcao f ON f.cod_funcao = sf.ref_cod_funcao ';
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} sf.ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} sf.ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.servidor_funcao sf INNER JOIN pmieducar.funcao f ON f.cod_funcao = sf.ref_cod_funcao {$filtros}");

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
}
