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
 * CoreExt_Session class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Object_Utils {

  /* Retorna um array de atributos filtrados de um objeto
       $objects - objeto ou array de objetos a ser(em) filtrado(s)
       $atts    - atributo ou array de atributos para filtrar objeto,
       ex: $atts = array('cod_escola' => 'id', 'nome')
  */
  public static function filter($objects, $atts = array()){
    $objects_filtered = array();

    if (! is_array($objects))
      $objects = array();

    if (! is_array($atts))
      $atts = array();

    // apply filter
    foreach($objects as $object) {
      $object_filtered = array();

      foreach($atts as $att_name => $new_attr_name) {
        if (! $att_name)
          $att_name = $new_attr_name;

        $object_filtered[$new_attr_name] = $object->$att_name;
      }
      $objects_filtered[] = $object_filtered;
    }

    return $objects_filtered;
  }


  /* */
  public static function filter_key_value($objects, $att_key_name, $attr_value_name){
    $objects_filtered = array();

    if (! is_array($objects))
      $objects = array();

    // apply filter
    foreach($objects as $object) {
      $objects_filtered[$object->$att_key_name] = $object->$attr_value_name;
    }

    return $objects_filtered;
  }
}
?>
