<?php

require_once("include/clsBanco.inc.php");

#wrapper to clsBanco
class Db
{
  function __construct()
  {
    $this->_db = new clsBanco();
  }

  function select($sql)
  {
    #try{
  $this->_db->Consulta($sql);
    $rows = array();
    while ($this->_db->ProximoRegistro())
      $rows[] = $this->_db->Tupla();
    return $rows;
    #}
    #catch(Exception $e)
    #{
    #  echo $e->getMessage();
    #}
  }

  function selectField($sql)
  {
    return $this->_db->UnicoCampo($sql);
  }
}

?>
