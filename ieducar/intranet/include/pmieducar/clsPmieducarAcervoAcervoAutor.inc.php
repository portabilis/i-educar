<?php

use iEducar\Legacy\Model;

class clsPmieducarAcervoAcervoAutor extends Model
{
    public $ref_cod_acervo_autor;
    public $ref_cod_acervo;
    public $principal;

    public function __construct($ref_cod_acervo_autor = null, $ref_cod_acervo = null, $principal = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}acervo_acervo_autor";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_acervo_autor, ref_cod_acervo, principal';

        if (is_numeric($ref_cod_acervo)) {
            $this->ref_cod_acervo = $ref_cod_acervo;
        }
        if (is_numeric($ref_cod_acervo_autor)) {
            $this->ref_cod_acervo_autor = $ref_cod_acervo_autor;
        }

        if (is_numeric($principal)) {
            $this->principal = $principal;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_acervo_autor) && is_numeric($this->ref_cod_acervo) && is_numeric($this->principal)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_acervo_autor)) {
                $campos .= "{$gruda}ref_cod_acervo_autor";
                $valores .= "{$gruda}'{$this->ref_cod_acervo_autor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_acervo)) {
                $campos .= "{$gruda}ref_cod_acervo";
                $valores .= "{$gruda}'{$this->ref_cod_acervo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->principal)) {
                $campos .= "{$gruda}principal";
                $valores .= "{$gruda}'{$this->principal}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;
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
        if (is_numeric($this->ref_cod_acervo_autor) && is_numeric($this->ref_cod_acervo)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->principal)) {
                $set .= "{$gruda}principal = '{$this->principal}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'");

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
    public function lista($int_ref_cod_acervo_autor = null, $int_ref_cod_acervo = null, $int_principal = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_acervo_autor)) {
            $filtros .= "{$whereAnd} ref_cod_acervo_autor = '{$int_ref_cod_acervo_autor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_acervo)) {
            $filtros .= "{$whereAnd} ref_cod_acervo = '{$int_ref_cod_acervo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_principal)) {
            $filtros .= "{$whereAnd} principal = '{$int_principal}'";
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
        if (is_numeric($this->ref_cod_acervo_autor) && is_numeric($this->ref_cod_acervo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'");
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
        if (is_numeric($this->ref_cod_acervo_autor) && is_numeric($this->ref_cod_acervo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'");
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
        if (is_numeric($this->ref_cod_acervo_autor) && is_numeric($this->ref_cod_acervo)) {
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_acervo)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_acervo = '{$this->ref_cod_acervo}'");

            return true;
        }

        return false;
    }

    public function listaAutoresPorObra($acervoId)
    {
        $db = new clsBanco();
        $db->Consulta("SELECT acervo_acervo_autor.ref_cod_acervo_autor as id,
                            acervo_autor.nm_autor as nome
                         FROM pmieducar.acervo_acervo_autor
             INNER JOIN pmieducar.acervo_autor
              ON acervo_acervo_autor.ref_cod_acervo_autor = acervo_autor.cod_acervo_autor
                         WHERE acervo_acervo_autor.ref_cod_acervo IN ($acervoId)
                         ORDER BY acervo_acervo_autor.principal");

        while ($db->ProximoRegistro()) {
            $resultado[] = $db->Tupla();
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function cadastraAutorParaObra($acervoId, $autorId, $principal)
    {
        $db = new clsBanco();
        $db->Consulta("INSERT INTO pmieducar.acervo_acervo_autor (ref_cod_acervo, ref_cod_acervo_autor, principal) VALUES ({$acervoId},{$autorId}, {$principal})");

        return true;
    }

    public function deletaAutoresDaObra($acervoId)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM pmieducar.acervo_acervo_autor WHERE ref_cod_acervo = {$acervoId}");

        return true;
    }
}
