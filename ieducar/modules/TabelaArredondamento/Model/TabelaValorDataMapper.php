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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'App/Model/IedFinder.php';

/**
 * TabelaArredondamento_Model_TabelaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaArredondamento_Model_TabelaValorDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'TabelaArredondamento_Model_TabelaValor';
  protected $_tableName   = 'tabela_arredondamento_valor';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'tabelaArredondamento' => 'tabela_arredondamento_id',
    'valorMinimo'          => 'valor_minimo',
    'valorMaximo'          => 'valor_maximo'
  );
}