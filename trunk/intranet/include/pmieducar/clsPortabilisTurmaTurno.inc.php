<?php

require_once( "include/pmieducar/geral.inc.php" );

class ClsPortabilisTurmaTurno
{

  function __construct($instituicaoId, $turmaTurnoId = null)
  {
    $this->instituicaoId = $instituicaoId;
    $this->turmaTurnoId = $turmaTurnoId;
		$this->db = new clsBanco();
  }

  function select()
  {

    $TurmaTurnoId = $this->turmaTurnoId ? "and turma_turno_id = {$this->turmaTurnoId}" : '';
		$this->db->Consulta( "SELECT * FROM pmieducar.turma_turno WHERE instituicao_id = {$this->instituicaoId} $TurmaTurnoId" );
    
    $turnos = array();
    while($this->db->ProximoRegistro())
    {
      $turnos[] = $this->db->Tupla();
    }
    return $turnos;

  } 

}


?>
