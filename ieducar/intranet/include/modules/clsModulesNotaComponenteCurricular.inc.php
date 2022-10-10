<?php

use iEducar\Legacy\Model;

class clsModulesNotaComponenteCurricular extends Model
{
    public $id;
    public $nota_aluno_id;
    public $componente_curricular_id;
    public $nota;
    public $nota_arredondada;
    public $etapa;
    public $nota_recuperacao;
    public $nota_original;
    public $nota_recuperacao_especifica;

    public function __construct(
        $id = null,
        $nota_aluno_id = null,
        $componente_curricular_id = null,
        $nota = null,
        $nota_arredondada = null,
        $etapa = null,
        $nota_recuperacao = null,
        $nota_original = null,
        $nota_recuperacao_especifica = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}nota_componente_curricular";

        $this->_campos_lista = $this->_todos_campos = '
        id, 
        nota_aluno_id, 
        componente_curricular_id, 
        nota,
        nota_arredondada, 
        etapa,
        nota_recuperacao, 
        nota_original,
        nota_recuperacao_especifica';

        if (is_numeric($id)) {
            $this->id = $id;
        }
        if (is_numeric($nota_aluno_id)) {
            $this->nota_aluno_id = $nota_aluno_id;
        }
        if (is_numeric($componente_curricular_id)) {
            $this->componente_curricular_id = $componente_curricular_id;
        }
        if (is_numeric($nota)) {
            $this->nota = $nota;
        }
        if (is_string($nota_arredondada)) {
            $this->nota_arredondada = $nota_arredondada;
        }
        if (is_string($etapa)) {
            $this->etapa = $etapa;
        }
        if (is_string($nota_recuperacao)) {
            $this->nota_recuperacao = $nota_recuperacao;
        }
        if (is_string($nota_original)) {
            $this->nota_original = $nota_original;
        }
        if (is_string($nota_recuperacao_especifica)) {
            $this->nota_recuperacao_especifica = $nota_recuperacao_especifica;
        }
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE nota_aluno_id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();            
        }

        return false;
    }
}
