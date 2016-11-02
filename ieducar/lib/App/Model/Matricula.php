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

require_once 'CoreExt/Entity.php';
require_once 'App/Model/MatriculaSituacao.php';

/**
 * App_Model_Matricula class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class App_Model_Matricula
{
  /**
   * Atualiza os dados da matrícula do aluno, promovendo-o ou retendo-o. Usa
   * uma instância da classe legada clsPmieducarMatricula para tal.
   *
   * @param int $matricula
   * @param int $usuario
   * @param bool $aprovado
   * @return bool
   */
  public static function atualizaMatricula($matricula, $usuario, $aprovado = TRUE)
  {
    $instance = CoreExt_Entity::addClassToStorage('clsPmieducarMatricula', NULL,
      'include/pmieducar/clsPmieducarMatricula.inc.php');

    $instance->cod_matricula   = $matricula;
    $instance->ref_usuario_cad = $usuario;
    $instance->ref_usuario_exc = $usuario;

    if (is_int($aprovado))
      $instance->aprovado = $aprovado;
    else
    {
      $instance->aprovado        = ($aprovado == TRUE) ?
        App_Model_MatriculaSituacao::APROVADO :
        App_Model_MatriculaSituacao::REPROVADO;
    }

    return $instance->edita();
  }
}
