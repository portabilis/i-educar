<?php

use iEducar\Legacy\Model;

class clsModulesFrequenciaAluno extends Model {
    public $id;
    public $ref_frequencia;
    public $ref_cod_matricula;   
    public $justificativa;
    public $aulas_faltou;

    public function __construct(
        $id = null,
        $ref_frequencia = null,
        $ref_cod_matricula = null,
        $justificativa = null,
        $aulas_faltou = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}frequencia_aluno";   

        $this->_campos_lista = $this->_todos_campos = '
            id,
            ref_frequencia,
            ref_cod_matricula,
            justificativa,
            aulas_faltou
        ';


        if ($id) {
            $this->id = $id;
        }
        if (is_numeric($ref_frequencia)) {
            $this->ref_frequencia = $ref_frequencia;
        }
        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }
        if (is_string($justificativa)) {
            $this->justificativa = $justificativa;
        }
        if (is_array($aulas_faltou)) {
            $this->aulas_faltou = $aulas_faltou;
        }        
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe($ref_cod_matricula)
    {
        if (($ref_cod_matricula)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = $ref_cod_matricula");
            $db->ProximoRegistro();

            return $db->Tupla();            
        }

        return false;
    }

}
