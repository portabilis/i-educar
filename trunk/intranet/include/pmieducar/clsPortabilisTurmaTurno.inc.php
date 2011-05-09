<?php

require_once( "include/pmieducar/geral.inc.php" );

class ClsPortabilisTurmaTurno
{

  function __construct($instituicaoId)
  {
    $this->instituicaoId = $instituicaoId;
		$this->db = new clsBanco();
  }

  function select()
  {

		$this->db->Consulta( "SELECT * FROM pmieducar.turma_turno WHERE instituicao_id = '{$this->instituicaoId}'" );
    
    $turnos = array();
    while($this->db->ProximoRegistro())
    {
      $turnos[] = $this->db->Tupla();
    }
    return $turnos;

  } 

}


?>
