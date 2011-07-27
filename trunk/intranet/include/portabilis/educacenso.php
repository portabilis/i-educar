<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include_paths.php");
require_once("dal.php");

  class Educacenso
  {

    function __construct($codIeducar, $codInep, $nomeInep, $fonte)
    {
      $this->codIeducar = $codIeducar;
      $this->codInep = $codInep;
      $this->fonte = $fonte;
      $this->db = new Db();
    }

    function _getEntityName()
    {
      throw new Exception("The method '_setEntityName' from class Educacenso must be overridden!");
    }

    function _getEntityTableName()
    {
      return "modules.educacenso_cod_" . $this->_getEntityName();
    }

    function save()
    {
      throw new Exception("The method 'save' from class Educacenso must be overridden!");
    }

    function setCodIeducar($cod)
    {
      $this->codIeducar = $cod;
    }

    function setCodInep($cod)
    {
      $this->codInep = $cod;
    }

    function getCodInep($codIeducar)
    {
      $codInep = $this->db->selectField("select cod_{$this->_getEntityName()}_inep from modules.educacenso_cod_{$this->_getEntityName()} where cod_{$this->_getEntityName()} = $codIeducar");
      return $codInep;
    }

    function getNomeIeducar($codIeducar)
    {
      throw new Exception("The method 'getNomeIeducar' from class Educacenso must be overridden!");
    }

    function setFonte($fonte)
    {
      $this->fonte = fonte;
    }

    function getFonte()
    {
      return 'null';
    }

    function exists()
    {
      $exists = $this->db->selectField("select 1 from modules.educacenso_cod_escola where cod_escola = {$this->codIeducar}");
      return $exists;
    }

  }

  class EducacensoEscola extends Educacenso
  {

    function _getEntityName()
    {
      return 'escola';
    }

    function getNomeIeducar($codIeducar)
    {

      $nome = $this->db->selectField("select fantasia from cadastro.juridica where idpes = (select ref_idpes from pmieducar.escola where cod_escola = $codIeducar)");
      return $nome;

    }

    function save()
    {
      $nomeIeducar = pg_escape_string($this->getNomeIeducar($codIeducar = $this->codIeducar));
      $exists = $this->exists();

      #TODO criar metodo para cada operacao
      if (! $exists and ! is_numeric($this->codInep))
        return True;
      else if (! $exists)
        $sql = "insert into {$this->_getEntityTableName()} (cod_escola, cod_escola_inep, nome_inep, fonte, created_at, updated_at) values ($this->codIeducar, $this->codInep, '$nomeIeducar', {$this->getFonte()}, now(), null)";
      else if ($exists and ! is_numeric($this->codInep))
        $sql = "delete from {$this->_getEntityTableName()} where cod_escola = $this->codIeducar";
      else if ($exists)
        $sql = "update {$this->_getEntityTableName()} set cod_escola_inep = $this->codInep, nome_inep = '$nomeIeducar', fonte = {$this->getFonte()}, updated_at = now() where cod_escola = $this->codIeducar";
      else
        throw new Exception("Educacenso record can not be saved!");

      return $this->db->select($sql);
    }
  }

?>
