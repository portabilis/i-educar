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


/**
 * Portabilis_Messenger class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_Messenger {

  public function __construct() {
    $this->_msgs = array();
  }


  public function append($msg, $type="error", $encodeToUtf8 = false, $ignoreIfHasMsgWithType = '') {
    if (empty($ignoreIfHasMsgWithType) || ! $this->hasMsgWithType($ignoreIfHasMsgWithType)) {

      if ($encodeToUtf8)
        $msg = utf8_encode($msg);

      $this->_msgs[] = array('msg' => $msg, 'type' => $type);
    }
  }


  public function hasMsgWithType($type) {
    $hasMsg = false;

    foreach ($this->_msgs as $m){
      if ($m['type'] == $type) {
        $hasMsg = true;
        break;
      }
    }

    return $hasMsg;
  }


  public function toHtml($tag = 'p') {
    $msgs = '';

    foreach($this->getMsgs() as $m)
      $msgs .= "<$tag class='{$m['type']}'>{$m['msg']}</$tag>";

    return $msgs;
  }


  public function getMsgs() {
    $msgs = array();

    // expoe explicitamente apenas as chaves 'msg' e 'type', evitando exposição
    // indesejada de chaves adicionadas futuramente ao array $this->_msgs
    foreach($this->_msgs as $m)
      $msgs[] = array('msg' => $m['msg'], 'type' => $m['type']);

    return $msgs;
  }

  // merge messages from another messenger with it self
  public function merge($anotherMessenger) {
    foreach($anotherMessenger->getMsgs() as $msg) {
      $this->append($msg['msg'], $msg['type']);
    }
  }
}