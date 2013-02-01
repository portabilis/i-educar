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
require_once 'lib/Portabilis/Utils/Database.php';

/**
 * Portabilis_Validator class.
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
  }


  // TODO refatorar todos metodos, para não receber mais argumento $raiseException*

  /* TODO refatorar todos metodos, para não receber mais argumento $addMsg*
          caso $msg falso pode-se disparar erro */

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

  public function validatesValueIsInBd($fieldName, &$value, $schemaName, $tableName, $raiseExceptionOnFail = true, $addMsgOnError = true){
    $sql = "select 1 from $schemaName.$tableName where $fieldName = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $value) != 1){
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

  public function validatesValueNotInBd($fieldName, &$value, $schemaName, $tableName, $raiseExceptionOnFail = true, $addMsgOnError = true){
    $sql = "select 1 from $schemaName.$tableName where $fieldName = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $value) == 1) {
      if ($addMsgOnError) {
        $msg = "O valor informado {$value} para $tableName já existe no banco de dados.";
        $this->messenger->append($msg);
      }

      if ($raiseExceptionOnFail)
        throw new CoreExt_Exception($msg);

      return false;
    }

    return true;
  }
}