<?php

use iEducar\Legacy\Model;

class clsModulesNotaAluno extends Model
{
    public $id;
    public $matricula_id;

    public function __construct(
        $id = null,
        $matricula_id = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}nota_aluno";

        $this->_campos_lista = $this->_todos_campos = '
        id, 
        matricula_id';

        if (is_numeric($id)) {
            $this->id = $id;
        }
        if (is_numeric($matricula_id)) {
            $this->matricula_id = $matricula_id;
        }        
    }

    public function selectNotaAlunoIdByMatricula($matricula_id)
    {
        if ($matricula_id) {
            $db = new clsBanco();

            $sql = "
                 SELECT id
                 FROM
                     modules.nota_aluno
                 WHERE matricula_id = $matricula_id
             ";

            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
