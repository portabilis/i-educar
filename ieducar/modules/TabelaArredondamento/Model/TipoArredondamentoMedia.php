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
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Enum.php';

/**
 * TabelaArredondamento_Model_TipoArredondamentoMedia class.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaArredondamento_Model_TipoArredondamentoMedia extends CoreExt_Enum
{
  const NAO_ARREDONDAR = 0;
  const ARREDONDAR_PARA_NOTA_INFERIOR = 1;
  const ARREDONDAR_PARA_NOTA_SUPERIOR = 2;
  const ARREDONDAR_PARA_NOTA_ESPECIFICA = 3;

  protected $_data = array(
    self::NAO_ARREDONDAR => 'N&atilde;o utilizar arredondamento para esta casa decimal',
    self::ARREDONDAR_PARA_NOTA_INFERIOR => 'Arredondar para o n&uacute;mero inteiro imediatamente inferior',
    self::ARREDONDAR_PARA_NOTA_SUPERIOR => 'Arredondar para o n&uacute;mero inteiro imediatamente superior',
    self::ARREDONDAR_PARA_NOTA_ESPECIFICA => 'Arredondar para a casa decimal espec&iacute;fica'
  );

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}