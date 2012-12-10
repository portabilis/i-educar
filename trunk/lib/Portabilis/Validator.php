<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';
require_once 'CoreExt/Exception.php';

/**
 * CoreExt_Session class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Validator {

  public function __construct(&$messenger) {
    $this->messenger = $messenger;
    $this->db        = new clsBanco();
  }


  // TODO refatorar todos metodos, para não receber mais argumento $raiseException*

  /* TODO refatorar todos metodos, para não receber mais argumento $addMsg*
          caso $msg falso, pode-se não add a mensagem */

  public function validatesPresenceOf(&$value, $name, $raiseExceptionOnFail = false, $msg = '', $addMsgOnEmpty = true){
    if (! isset($value) || (empty($value) && !is_numeric($value))){
      if ($addMsgOnEmpty)
      {
        $msg = empty($msg) ? "É necessário receber uma variavel '$name'" : $msg;
        $this->messenger->append($msg);
      }

      if ($raiseExceptionOnFail)
         throw new CoreExt_Exception($msg);

      return false;
    }
    return true;
  }


  public function validatesValueIsNumeric(&$value, $name, $raiseExceptionOnFail = false, $msg = '', $addMsgOnError = true){
    if (! is_numeric($value)){
      if ($addMsgOnError)
      {
        $msg = empty($msg) ? "O valor recebido para variavel '$name' deve ser numerico" : $msg;
        $this->messenger->append($msg);
      }

      if ($raiseExceptionOnFail)
         throw new CoreExt_Exception($msg);

      return false;
    }
    return true;
  }


  public function validatesValueIsArray(&$value, $name, $raiseExceptionOnFail = false, $msg = '', $addMsgOnError = true){

    if (! is_array($value)){
      if ($addMsgOnError) {
        $msg = empty($msg) ? "Deve ser recebido uma lista de '$name'" : $msg;
        $this->messenger->append($msg);
      }

      if ($raiseExceptionOnFail)
         throw new CoreExt_Exception($msg);

      return false;
    }
    return true;
  }


  public function validatesValueInSetOf(&$value, $setExpectedValues, $name, $raiseExceptionOnFail = false, $msg = ''){
    if (! empty($setExpectedValues) && ! in_array($value, $setExpectedValues)){
      $msg = empty($msg) ? "Valor recebido na variavel '$name' é invalido" : $msg;
      $this->messenger->append($msg);

      if ($raiseExceptionOnFail)
         throw new CoreExt_Exception($msg);

      return false;
    }

    return true;
  }


  // TODO alterar para usar modelos
  public function validatesValueIsInBd($fieldName, &$value, $schemaName, $tableName, $raiseExceptionOnFail = true, $addMsgOnError = true){
    $sql = "select 1 as exists from $schemaName.$tableName where $fieldName = $1";
    $exists = $this->fetchPreparedQuery($sql, $value, true, 'first-field');

    if (! $exists == '1'){
      if ($addMsgOnError) {
        $msg = "O valor informado {$value} para $tableName, não esta presente no banco de dados.";
        $this->messenger->append($msg);
      }

      if ($raiseExceptionOnFail)
        throw new CoreExt_Exception($msg);

      return false;
    }
    return true;
  }


  // wrapper para $db->execPreparedQuery($sql, $params)
  protected function fetchPreparedQuery($sql, $params = array(), $hideExceptions = true, $returnOnly = '') {
    try {
      $result = array();
      $db = new clsBanco();
      if ($db->execPreparedQuery($sql, $params) != false) {

        while ($db->ProximoRegistro())
          $result[] = $db->Tupla();

        if ($returnOnly == 'first-line' and isset($result[0]))
          $result = $result[0];
        elseif ($returnOnly == 'first-field' and isset($result[0]) and isset($result[0][0]))
          $result = $result[0][0];
        else
          $result = null;
      }
    }
    catch(Exception $e) {
      if (! $hideExceptions)
        $this->messenger->append($e->getMessage(), "error", true);
    }
    return $result;
  }
}

// deprecated Validator class
class Validator extends Portabilis_Validator {
}
