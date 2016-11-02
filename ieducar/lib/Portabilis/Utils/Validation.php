<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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

require_once 'lib/Portabilis/Utils/User.php';

/**
 * Portabilis_Utils_Validation class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Utils_Validation {

  public static function validatesCpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);

    if (strlen($cpf) != 11)
      return false;

    // calcula primeiro dígito verificador
    $soma = 0;

    for ($i = 0; $i < 9; $i++)
      $soma += ((10 - $i) * $cpf[$i]);

    $primeiroDigito = 11 - ($soma % 11);

    if ($primeiroDigito >= 10)
      $primeiroDigito = 0;


    // calcula segundo dígito verificador
    $soma = 0;

    for ($i = 0; $i < 10; $i++)
      $soma += ((11 - $i) * $cpf[$i]);

    $segundoDigito = 11 - ($soma % 11);

    if ($segundoDigito >= 10)
      $segundoDigito = 0;


    return ($primeiroDigito == $cpf[9] && $segundoDigito == $cpf[10]);
  }
}
