<?php

use iEducar\Legacy\Model;

class clsModulesNotaExame extends Model
{
    public $ref_cod_matricula;
    public $ref_cod_componente_curricular;
    public $nota_exame;

    public function __construct($ref_cod_matricula = null, $ref_cod_componente_curricular = null, $nota_exame = null)
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}nota_exame";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_matricula, ref_cod_componente_curricular, nota_exame';

        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }
        if (is_numeric($ref_cod_componente_curricular)) {
            $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
        }
        if (is_numeric($nota_exame)) {
            $this->nota_exame = $nota_exame;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular) && is_numeric($this->nota_exame)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_componente_curricular)) {
                $campos .= "{$gruda}ref_cod_componente_curricular";
                $valores .= "{$gruda}'{$this->ref_cod_componente_curricular}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nota_exame)) {
                $campos .= "{$gruda}nota_exame";
                $valores .= "{$gruda}'{$this->nota_exame}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $this->ref_cod_matricula;
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular) && is_numeric($this->nota_exame)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->nota_exame)) {
                $set .= "{$gruda}nota_exame = '{$this->nota_exame}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");

                return true;
            }
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_componente_curricular)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'");

            return true;
        }

        return false;
    }
}
