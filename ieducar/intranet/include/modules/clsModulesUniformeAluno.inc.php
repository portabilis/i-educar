<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsModulesUniformeAluno extends Model
{
    public $ref_cod_aluno;
    public $recebeu_uniforme;
    public $quantidade_camiseta;
    public $tamanho_camiseta;
    public $quantidade_blusa_jaqueta;
    public $tamanho_blusa_jaqueta;
    public $quantidade_bermuda;
    public $tamanho_bermuda;
    public $quantidade_calca;
    public $tamanho_calca;
    public $quantidade_saia;
    public $tamanho_saia;
    public $quantidade_calcado;
    public $tamanho_calcado;
    public $quantidade_meia;
    public $tamanho_meia;

    public function __construct(
        $ref_cod_aluno = null,
        $recebeu_uniforme = null,
        $quantidade_camiseta = null,
        $tamanho_camiseta = null,
        $quantidade_blusa_jaqueta = null,
        $tamanho_blusa_jaqueta = null,
        $quantidade_bermuda = null,
        $tamanho_bermuda = null,
        $quantidade_calca = null,
        $tamanho_calca = null,
        $quantidade_saia = null,
        $tamanho_saia = null,
        $quantidade_calcado = null,
        $tamanho_calcado = null,
        $quantidade_meia = null,
        $tamanho_meia = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}uniforme_aluno";

        $this->_campos_lista = $this->_todos_campos = ' ref_cod_aluno, recebeu_uniforme, quantidade_camiseta, 
          tamanho_camiseta, quantidade_blusa_jaqueta, tamanho_blusa_jaqueta, quantidade_bermuda, tamanho_bermuda,
          quantidade_calca, tamanho_calca, quantidade_saia, tamanho_saia, quantidade_calcado, tamanho_calcado,
          quantidade_meia, tamanho_meia';

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }

        if (is_string($recebeu_uniforme)) {
            $this->recebeu_uniforme = $recebeu_uniforme;
        }

        if (is_numeric($quantidade_camiseta)) {
            $this->quantidade_camiseta = $quantidade_camiseta;
        }

        if (is_string($tamanho_camiseta)) {
            $this->tamanho_camiseta = $tamanho_camiseta;
        }

        if (is_numeric($quantidade_blusa_jaqueta)) {
            $this->quantidade_blusa_jaqueta = $quantidade_blusa_jaqueta;
        }

        if (is_string($tamanho_blusa_jaqueta)) {
            $this->tamanho_blusa_jaqueta = $tamanho_blusa_jaqueta;
        }

        if (is_numeric($quantidade_bermuda)) {
            $this->quantidade_bermuda = $quantidade_bermuda;
        }

        if (is_string($tamanho_bermuda)) {
            $this->tamanho_bermuda = $tamanho_bermuda;
        }

        if (is_numeric($quantidade_calca)) {
            $this->quantidade_calca = $quantidade_calca;
        }

        if (is_string($tamanho_calca)) {
            $this->tamanho_calca = $tamanho_calca;
        }

        if (is_numeric($quantidade_saia)) {
            $this->quantidade_saia = $quantidade_saia;
        }

        if (is_string($tamanho_saia)) {
            $this->tamanho_saia = $tamanho_saia;
        }

        if (is_numeric($quantidade_calcado)) {
            $this->quantidade_calcado = $quantidade_calcado;
        }

        if (is_string($tamanho_calcado)) {
            $this->tamanho_calcado = $tamanho_calcado;
        }

        if (is_numeric($quantidade_meia)) {
            $this->quantidade_meia = $quantidade_meia;
        }

        if (is_string($tamanho_meia)) {
            $this->tamanho_meia = $tamanho_meia;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}ref_cod_aluno";
            $valores .= "{$gruda}{$this->ref_cod_aluno}";
            $gruda = ', ';

            $campos .= "{$gruda}recebeu_uniforme";
            $valores .= "{$gruda}'{$this->recebeu_uniforme}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_camiseta)) {
                $campos .= "{$gruda}quantidade_camiseta";
                $valores .= "{$gruda}{$this->quantidade_camiseta}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_camiseta";
            $valores .= "{$gruda}'{$this->tamanho_camiseta}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_blusa_jaqueta)) {
                $campos .= "{$gruda}quantidade_blusa_jaqueta";
                $valores .= "{$gruda}{$this->quantidade_blusa_jaqueta}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_blusa_jaqueta";
            $valores .= "{$gruda}'{$this->tamanho_blusa_jaqueta}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_bermuda)) {
                $campos .= "{$gruda}quantidade_bermuda";
                $valores .= "{$gruda}{$this->quantidade_bermuda}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_bermuda";
            $valores .= "{$gruda}'{$this->tamanho_bermuda}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_calca)) {
                $campos .= "{$gruda}quantidade_calca";
                $valores .= "{$gruda}{$this->quantidade_calca}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_calca";
            $valores .= "{$gruda}'{$this->tamanho_calca}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_saia)) {
                $campos .= "{$gruda}quantidade_saia";
                $valores .= "{$gruda}{$this->quantidade_saia}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_saia";
            $valores .= "{$gruda}'{$this->tamanho_saia}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_calcado)) {
                $campos .= "{$gruda}quantidade_calcado";
                $valores .= "{$gruda}{$this->quantidade_calcado}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_calcado";
            $valores .= "{$gruda}'{$this->tamanho_calcado}'";
            $gruda = ', ';

            if (is_numeric($this->quantidade_meia)) {
                $campos .= "{$gruda}quantidade_meia";
                $valores .= "{$gruda}{$this->quantidade_meia}";
                $gruda = ', ';
            }

            $campos .= "{$gruda}tamanho_meia";
            $valores .= "{$gruda}'{$this->tamanho_meia}'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $this->ref_cod_aluno;
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $set = '';

            $set .= "recebeu_uniforme = '{$this->recebeu_uniforme}'";

            if (is_numeric($this->quantidade_camiseta)) {
                $set .= ",quantidade_camiseta = '{$this->quantidade_camiseta}'";
            } else {
                $set .= ',quantidade_camiseta = NULL';
            }

            $set .= ",tamanho_camiseta = '{$this->tamanho_camiseta}'";

            if (is_numeric($this->quantidade_blusa_jaqueta)) {
                $set .= ",quantidade_blusa_jaqueta = '{$this->quantidade_blusa_jaqueta}'";
            } else {
                $set .= ',quantidade_blusa_jaqueta = NULL';
            }

            $set .= ",tamanho_blusa_jaqueta = '{$this->tamanho_blusa_jaqueta}'";

            if (is_numeric($this->quantidade_bermuda)) {
                $set .= ",quantidade_bermuda = '{$this->quantidade_bermuda}'";
            } else {
                $set .= ',quantidade_bermuda = NULL';
            }

            $set .= ",tamanho_bermuda = '{$this->tamanho_bermuda}'";

            if (is_numeric($this->quantidade_calca)) {
                $set .= ",quantidade_calca = '{$this->quantidade_calca}'";
            } else {
                $set .= ',quantidade_calca = NULL';
            }

            $set .= ",tamanho_calca = '{$this->tamanho_calca}'";

            if (is_numeric($this->quantidade_saia)) {
                $set .= ",quantidade_saia = '{$this->quantidade_saia}'";
            } else {
                $set .= ',quantidade_saia = NULL';
            }

            $set .= ",tamanho_saia = '{$this->tamanho_saia}'";

            if (is_numeric($this->quantidade_calcado)) {
                $set .= ",quantidade_calcado = '{$this->quantidade_calcado}'";
            } else {
                $set .= ',quantidade_calcado = NULL';
            }

            $set .= ",tamanho_calcado = '{$this->tamanho_calcado}'";

            if (is_numeric($this->quantidade_meia)) {
                $set .= ",quantidade_meia = '{$this->quantidade_meia}'";
            } else {
                $set .= ',quantidade_meia = NULL';
            }

            $set .= ",tamanho_meia = '{$this->tamanho_meia}'";

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista()
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
            $db->ProximoRegistro();

            return $db->Tupla();
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
