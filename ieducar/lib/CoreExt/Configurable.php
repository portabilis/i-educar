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
 * @package   CoreExt_Configurable
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * CoreExt_Configurable interface.
 *
 * Essa interface tem como objetivo prover uma API uniforme para classes que
 * definem parâmetros de configuração. Basicamente provê apenas o método
 * público setOptions, que recebe um array de parâmetros. Como o PHP não
 * permite herança múltipla, essa API apenas reforça a idéia de se criar uma
 * uniformidade entre as diferentes classes configuráveis do i-Educar.
 *
 * Uma sugestão de implementação do método setOptions é dada pelo exemplo a
 * seguir:
 * <code>
 * <?php
 * protected $_options = array(
 *   'option1' => NULL,
 *   'option2' => NULL
 * );
 *
 * public function setOptions(array $options = array())
 * {
 *   $defaultOptions = array_keys($this->getOptions());
 *   $passedOptions  = array_keys($options);
 *
 *   if (0 < count(array_diff($passedOptions, $defaultOptions))) {
 *     throw new InvalidArgumentException(
 *       sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
 *     );
 *   }
 *
 *   $this->_options = array_merge($this->getOptions(), $options);
 *   return $this;
 * }
 *
 * public function getOptions()
 * {
 *   return $this->_options;
 * }
 * </code>
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Configurable
 * @since     Interface disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
interface CoreExt_Configurable
{
  /**
   * Setter.
   * @param  array $options
   * @return CoreExt_Configurable Provê interface fluída
   */
  public function setOptions(array $options = array());

  /**
   * Getter.
   * @return array
   */
  public function getOptions();
}