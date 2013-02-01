<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @package   Usuario
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

class UsuarioValidator
{
  static function validatePassword($messenger,
                                             $oldPassword,
                                             $newPassword,
                                             $confirmation,
                                             $encriptedPassword,
                                             $matricula)
  {
    $newPassword  = strtolower($newPassword);
    $confirmation = strtolower($confirmation);

    if (empty($newPassword))
      $messenger->append('Por favor informe uma senha.', 'error');

    elseif (strlen($newPassword) < 8)
      $messenger->append('Por favor informe uma senha mais segura, com pelo menos 8 caracteres.', 'error');

    elseif ($newPassword != $confirmation)
      $messenger->append('A confirma&ccedil;&atilde;o de senha n&atilde;o confere com a senha.', 'error');

    elseif (strpos($newPassword, $matricula) != false)
      $messenger->append('A senha informada &eacute; similar a sua matricula, informe outra senha.', 'error');

    elseif ($encriptedPassword == $oldPassword)
      $messenger->append('Informe uma senha diferente da atual.', 'error');

    return ! $messenger->hasMsgWithType('error');
  }
}