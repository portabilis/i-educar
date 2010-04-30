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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Enum.php';

/**
 * App_Model_MatriculaSituacao class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class App_Model_MatriculaSituacao extends CoreExt_Enum
{
  const APROVADO            = 1;
  const REPROVADO           = 2;
  const EM_ANDAMENTO        = 3;
  const TRANSFERIDO         = 4;
  const RECLASSIFICADO      = 5;
  const ABANDONO            = 6;
  const EM_EXAME            = 7;
  const APROVADO_APOS_EXAME = 8;
  const RETIDO_FALTA        = 9;

  protected $_data = array(
    self::APROVADO            => 'Aprovado',
    self::REPROVADO           => 'Retido',
    self::EM_ANDAMENTO        => 'Em andamento',
    self::TRANSFERIDO         => 'Transferido',
    self::RECLASSIFICADO      => 'Reclassificado',
    self::ABANDONO            => 'Abandono',
    self::EM_EXAME            => 'Em exame',
    self::APROVADO_APOS_EXAME => 'Aprovado após exame',
    self::RETIDO_FALTA        => 'Retido por falta'
  );

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}