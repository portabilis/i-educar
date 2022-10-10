<?php

use iEducar\Legacy\Model;

class clsModulesParecerComponenteCurricular extends Model
{
    public $id;
    public $parecer_aluno_id;
    public $componente_curricular_id;
    public $parecer;
    public $etapa;

    public function __construct(
        $id = null,
        $parecer_aluno_id = null,
        $componente_curricular_id = null,
        $parecer = null,
        $etapa = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}parecer_componente_curricular";

        $this->_campos_lista = $this->_todos_campos = '
        id, 
        parecer_aluno_id, 
        componente_curricular_id, 
        parecer, 
        etapa';

        if (is_numeric($id)) {
            $this->id = $id;
        }
        if (is_numeric($parecer_aluno_id)) {
            $this->parecer_aluno_id = $parecer_aluno_id;
        }
        if (is_numeric($componente_curricular_id)) {
            $this->componente_curricular_id = $componente_curricular_id;
        }
        if (is_numeric($parecer)) {
            $this->parecer = $parecer;
        }
        if (is_string($etapa)) {
            $this->etapa = $etapa;
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
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE parecer_aluno_id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }   
}
