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
 * @package   CoreExt_View
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * CoreExt_View_Abstract abstract class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_View
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class CoreExt_View_Abstract
{
  /**
   * Conteúdo gerado pela execução de um controller.
   * @var string
   */
  protected $_contents = NULL;

  /**
   * Setter.
   *
   * @param  string $contents
   * @return CoreExt_View_Abstract Provê interface fluída.
   */
  public function setContents($contents)
  {
    $this->_contents = $contents;
    return $this;
  }

  /**
   * Getter.
   * @return string
   */
  public function getContents()
  {
    return $this->_contents;
  }

  /**
   * Implementação do método mágico __toString(). Retorna o valor de $contents.
   * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
   * @return string
   */
  public function __toString()
  {
    return $this->getContents();
  }
}