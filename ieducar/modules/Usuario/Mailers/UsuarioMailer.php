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

require_once 'lib/Portabilis/Mailer.php';

class UsuarioMailer extends Portabilis_Mailer
{

  static function updatedPassword($user, $link) {
    $to = $user->email;

    $subject = "Sua senha foi alterada - i-Educar - " . self::host();
    $message = "Olá!\n\n" .
               "A senha da matrícula '{$user->matricula}' foi alterada recentemente.\n\n" .
               "Caso você não tenha feito esta alteração, por favor, tente alterar sua senha acessando o link $link ou entre em contato com o administrador do sistema (solicitando mudança da sua senha), pois sua conta pode estar sendo usada por alguma pessoa não autorizada.";

    return self::mail($to, $subject, $message);
  }

  static function passwordReset($user, $link) {
    $to = $user->email;

    $subject = "Redefinição de senha - i-Educar - " . self::host();
    $message = "Olá!\n\n" .
               "Recebemos uma solicitação de redefinição de senha para a matrícula {$user->matricula}.\n\n" .
               "Para redefinir sua senha acesse o link: $link\n\n" .
               "Caso você não tenha feito esta solicitação, por favor, ignore esta mensagem.";

    return self::mail($to, $subject, $message);
  }
}