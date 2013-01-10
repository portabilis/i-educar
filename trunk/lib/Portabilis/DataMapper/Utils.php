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
 * Portabilis_DataMapper_Utils class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_DataMapper_Utils {

  /*
    this method returns a data mapper loaded by module_package and model_name, eg:

    $resourceDataMapper = $this->getDataMapperFor('module_package', 'model_name');
    $columns            = array('col_1', 'col_2');
    $where              = array('col_3' => 'val_1', 'ativo' => '1');
    $orderBy            = array('col_4' => 'ASC');

    $resources = $resourceDataMapper->findAll($columns, $where, $orderBy, $addColumnIdIfNotSet = false);

  */
  public function getDataMapperFor($packageName, $modelName){
    $dataMapperClassName = ucfirst($packageName) . "_Model_" . ucfirst($modelName) . "DataMapper";
    $classPath           = str_replace('_', '/', $dataMapperClassName) . '.php';

    // don't raise any error if the file to be included not exists or it already included.
    include_once $classPath;

    if (! class_exists($dataMapperClassName))
      throw new CoreExt_Exception("Class '$dataMapperClassName' not found in path $classPath.");

    return new $dataMapperClassName();
  }

}