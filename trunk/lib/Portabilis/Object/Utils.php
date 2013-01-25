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
 * Portabilis_Object_Utils class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Object_Utils {

  public static function filterSet($objects, $attrs = array()){
    if (! is_array($objects))
      $objects = array($objects);

    $objectsFiltered = array();

    foreach($objects as $object)
      $objectsFiltered[] = self::filter($object, $attrs);

    return $objectsFiltered;
  }


  /* Retorna um array {key => value, key => value}
     de atributos filtrados de um objeto, podendo renomear nome dos attrs,
     util para filtrar um objetos a ser retornado por uma api

       $objects - objeto ou array de objetos a ser(em) filtrado(s)
       $attrs    - atributo ou array de atributos para filtrar objeto,
       ex: $attrs = array('cod_escola' => 'id', 'nome')
  */
  public static function filter($object, $attrs = array()){
    if (! is_array($attrs))
      $attrs = array($attrs);

    $objectFiltered = array();

    // apply filter
    foreach($attrs as $keyAttr => $valueAtt) {
      if (! is_string($keyAttr))
        $keyAttr = $valueAtt;

      $objectFiltered[$valueAtt] = $object->$keyAttr;
    }

    return $objectFiltered;
  }


  /* Retorna um array { key => value, key2 => value2 }, filtrados de um array (lista) de objetos,
     util para filtar uma lista de objetos a ser usado para criar um input select.
       $objects         - objeto ou array de objetos a ser(em) filtrado(s)
       $keyAttr   - nome do atributo respectivo a chave, a filtrar no objeto,
       $valueAtt - nome do atributo respectivo ao valor a filtrar no objeto,
  */
  public static function asIdValue($objects, $keyAttr, $valueAtt){
    $objectsFiltered = array();

    if (! is_array($objects))
      $objects = array($objects);

    // apply filter
    foreach($objects as $object)
      $objectsFiltered[$object->$keyAttr] = $object->$valueAtt;

    return $objectsFiltered;
  }
}
