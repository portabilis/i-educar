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
 * @package   CoreExt_Validate
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Validate/Choice.php';
require_once 'CoreExt/Validate/ChoiceMultiple.php';
require_once 'CoreExt/Validate/String.php';
require_once 'CoreExt/Validate/Numeric.php';

/**
 * CoreExt_Validatable interface.
 *
 * A classe que implementar essa interface terá definir métodos que permitam
 * relacionar uma propriedade a um CoreExt_Validate_Interface, criando um
 * mecanismo simples e efetivo de validação.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Interface disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
interface CoreExt_Validate_Validatable
{
  /**
   * Retorna TRUE caso a propriedade seja válida.
   *
   * @param  string $key
   * @return bool
   */
  public function isValid($key = '');

  /**
   * Configura um CoreExt_Validate_Interface para uma propriedade da classe.
   *
   * @param  string $key
   * @param  CoreExt_Validate_Interface $validator
   * @return CoreExt_Validate_Validatable Provê interface fluída
   */
  public function setValidator($key, CoreExt_Validate_Interface $validator);

  /**
   * Retorna a instância CoreExt_Validate_Interface para uma propriedade da
   * classe ou NULL caso nenhum validador esteja atribuído.
   *
   * @param  string $key
   * @return CoreExt_Validate_Interface|NULL
   */
  public function getValidator($key);
}