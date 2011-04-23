<?php

require_once 'include/clsBanco.inc.php';

class clsPessoaEndereco
{
  var $idpes;
  var $idpes_cad;
  var $idpes_rev;
  var $tipo;
  var $cep;
  var $idlog;
  var $idbai;
  var $numero;
  var $complemento;
  var $reside_desde;
  var $letra;
  var $bloco;
  var $apartamento;
  var $andar;

  var $banco           = 'gestao_homolog';
  var $schema_cadastro = 'cadastro';
  var $tabela          = 'endereco_pessoa';

  function clsPessoaEndereco($int_idpes = FALSE, $numeric_cep = FALSE,
    $int_idlog = FALSE, $int_idbai = FALSE, $numeric_numero = FALSE,
    $str_complemento = FALSE, $date_reside_desde = FALSE, $str1_letra = FALSE,
    $str_bloco = FALSE, $int_apartamento = FALSE, $int_andar = FALSE,
    $idpes_cad = FALSE, $idpes_rev = FALSE)
  {
    $this->idpes = $int_idpes;
    $numeric_cep = idFederal2Int($numeric_cep);

    $obj = new clsCepLogradouroBairro($int_idlog, $numeric_cep, $int_idbai);

    if ($obj->detalhe()) {
      $this->idbai = $int_idbai;
      $this->idlog = $int_idlog;
      $this->cep   = $numeric_cep;
    }

    $this->numero       = $numeric_numero;
    $this->complemento  = $str_complemento;
    $this->reside_desde = $date_reside_desde;
    $this->letra        = $str1_letra;
    $this->bloco        = $str_bloco;
    $this->apartamento  = $int_apartamento;
    $this->andar        = $int_andar;
    $this->idpes_cad    = $idpes_cad ? $idpes_cad : $_SESSION['id_pessoa'];
    $this->idpes_rev    = $idpes_rev ? $idpes_rev : $_SESSION['id_pessoa'];
  }

  function cadastra()
  {
    if ($this->idpes && $this->cep && $this->idlog && $this->idbai &&
      $this->idpes_cad) {

      $campos  = '';
      $valores = '';

      if ($this->numero) {
        $campos  .= ', numero';
        $valores .= ", '$this->numero' ";
      }

      if ($this->letra) {
        $campos  .= ', letra';
        $valores .= ", '$this->letra' ";
      }

      if ($this->complemento) {
        $campos  .= ', complemento';
        $valores .= ", '$this->complemento' ";
      }

      if ($this->reside_desde) {
        $campos  .= ', reside_desde';
        $valores .= ", '$this->reside_desde' ";
      }

      if ($this->bloco) {
        $campos  .= ', bloco';
        $valores .= ", '$this->bloco' ";
      }

      if ($this->apartamento) {
        $campos  .= ', apartamento';
        $valores .= ", '$this->apartamento' ";
      }

      if ($this->andar) {
        $campos  .= ', andar';
        $valores .= ", '$this->andar' ";
      }

      $sql = sprintf(
        'INSERT INTO %s.%s (idpes, tipo, cep, idlog, idbai, origem_gravacao, ' .
        'idsis_cad, data_cad, operacao, idpes_cad %s) VALUES (\'%d\', \'1\', ' .
        '\'%s\', \'%s\', \'%d\', \'M\', 17, NOW(), \'I\', \'%d\' %s)',
        $this->schema_cadastro, $this->tabela, $campos, $this->idpes,
        $this->cep, $this->idlog, $this->idbai, $this->idpes_cad, $valores
      );

      $db = new clsBanco();
      $db->Consulta($sql);

      return TRUE;
    }

    return FALSE;
  }

  function edita()
  {
    if ($this->idpes && $this->idpes_rev) {
      $setVir = ' SET ';
      $set    = '';

      if ($this->numero) {
        $set   .= "$setVir numero = '$this->numero' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir numero = NULL ";
        $setVir = ', ';
      }

      if ($this->letra) {
        $set   .= "$setVir letra = '$this->letra' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir letra = NULL ";
        $setVir = ', ';
      }

      if ($this->complemento) {
        $set .= "$setVir complemento = '$this->complemento' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir complemento = NULL ";
        $setVir = ', ';
      }

      if ($this->reside_desde) {
        $set   .= "$setVir reside_desde = '$this->reside_desde' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir reside_desde = NULL ";
        $setVir = ', ';
      }

      if ($this->bloco) {
        $set   .= "$setVir bloco = '$this->bloco' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir bloco = NULL ";
        $setVir = ', ';
      }

      if ($this->apartamento) {
        $set   .= "$setVir apartamento = '$this->apartamento' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir apartamento = NULL ";
        $setVir = ', ';
      }

      if ($this->andar) {
        $set   .= "$setVir andar = '$this->andar' ";
        $setVir = ', ';
      }
      else {
        $set   .= "$setVir andar = NULL ";
        $setVir = ', ';
      }

      if ($this->cep && $this->idbai && $this->idlog) {
        $set   .= "$setVir cep = '$this->cep', idbai = '$this->idbai', idlog = '$this->idlog'";
        $setVir = ', ';
      }

      if ($this->idpes_rev) {
        $set .= "$setVir idpes_rev ='$this->idpes_rev'";
      }

      if ($set) {
        $db = new clsBanco();
        $db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela} $set WHERE idpes = $this->idpes");
        return TRUE;
      }
    }

    return FALSE;
  }

  function exclui()
  {
    if ($this->idpes) {
      $db = new clsBanco();
      $db->Consulta(sprintf(
        'DELETE FROM %s.%s WHERE idpes = %d',
        $this->schema_cadastro, $this->tabela, $this->idpes
      ));
    }
  }

  function lista($int_idpes = FALSE, $str_ordenacao = FALSE,
    $int_inicio_limite = FALSE, $int_qtd_limite = FALSE, $int_cep = FALSE,
    $int_idlog = FALSE, $int_idbai = FALSE, $int_numero = FALSE,
    $str_bloco = FALSE, $int_apartamento = FALSE, $int_andar = FALSE,
    $str_letra = FALSE, $str_complemento = FALSE)
  {
    $whereAnd = ' AND ';
    $where    = '';

    if (is_numeric($int_idpes)) {
      $where   .= "{$whereAnd}idpes = '$int_idpes' ";
      $whereAnd = ' AND ';
    }
    elseif (is_string($int_idpes))
    {
      $where   .= "{$whereAnd}idpes IN ({$int_idpes}) ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_cep)) {
      $where   .= "{$whereAnd}cep = '$int_cep' ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idlog)) {
      $where   .= "{$whereAnd}idlog = '$int_idlog' ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idbai)) {
      $where   .= "{$whereAnd}idbai = '$int_idbai' ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_numero)) {
      $where   .= "{$whereAnd}numero = '$int_numero' ";
      $whereAnd = ' AND ';
    }

    if ($str_bloco) {
      $where   .= "{$whereAnd}bloco = '$str_bloco' ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_apartamento)) {
      $where   .= "{$whereAnd}apartamento = '$int_apartamento' ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_andar)) {
      $where   .= "{$whereAnd}andar = '$int_andar' ";
      $whereAnd = ' AND ';
    }

    if (is_string($str_letra)) {
      $where   .= "{$whereAnd}letra = '$str_letra' ";
      $whereAnd = ' AND ';
    }

    if (is_string($str_complemento)) {
      $where   .= "{$whereAnd}complemento ILIKE '%$str_complemento%' ";
      $whereAnd = ' AND ';
    }

    if ($inicio_limite !== FALSE && $qtd_registros) {
      $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
    }

    if ($str_orderBy) {
      $orderBy .= " ORDER BY $str_orderBy ";
    }

    $db = new clsBanco();

    $sql = sprintf(
      'SELECT COUNT(0) AS total FROM %s.%s WHERE tipo = 1 %s',
      $this->schema_cadastro, $this->tabela, $where
    );

    $db->Consulta($sql);
    $db->ProximoRegistro();
    $total = $db->Campo('total');

    $db = new clsBanco($this->banco);

    $sql = sprintf(
      'SELECT idpes, tipo, cep, idlog, numero, letra, complemento, reside_desde, ' .
      'idbai, bloco, apartamento, andar FROM %s.%s WHERE tipo = 1 %s %s %s',
      $this->schema_cadastro, $this->tabela, $where, $orderBy, $limite
    );

    $db->Consulta($sql);
    $resultado = array();

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $tupla['cep']   = new clsCepLogradouro($tupla['cep'], $tupla['idlog']);
      $tupla['idlog'] = new clsCepLogradouro($tupla['cep'], $tupla['idlog']);
      $tupla['idbai'] = new clsPublicBairro(NULL, NULL, $tupla['idbai']);

      $bairro = $tupla['idbai']->detalhe();

      $tupla['zona_localizacao'] = $bairro['zona_localizacao'];

      $tupla['total'] = $total;

      $resultado[] = $tupla;
    }

    if (count($resultado) > 0) {
      return $resultado;
    }

    return FALSE;
  }

  function detalhe()
  {
    if ($this->idpes) {
      $db = new clsBanco($this->banco);

      $sql = sprintf(
        'SELECT idpes, tipo, cep, idlog, numero, letra, complemento, ' .
        'reside_desde, idbai, bloco, apartamento, andar ' .
        'FROM %s.%s WHERE idpes = %d',
        $this->schema_cadastro, $this->tabela, $this->idpes
      );

      $db->Consulta($sql);

      if ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $cep = $tupla['cep'];

        $tupla['cep']   = new clsCepLogradouro($cep, $tupla['idlog']);
        $tupla['idlog'] = new clsCepLogradouro($cep, $tupla['idlog']);

        $tupla['idbai'] = new clsPublicBairro(NULL, NULL, $tupla['idbai']);

        $bairro = $tupla['idbai']->detalhe();

        $tupla['zona_localizacao'] = $bairro['zona_localizacao'];

        return $tupla;
      }
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->idpes)) {
      $db = new clsBanco();

      $sql = sprintf(
        'SELECT 1 FROM %s.%s WHERE idpes = %d',
        $this->schema_cadastro, $this->tabela, $this->idpes
      );

      $db->Consulta($sql);
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }
}