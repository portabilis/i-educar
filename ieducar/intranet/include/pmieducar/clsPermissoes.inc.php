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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsMenuFuncionario.inc.php';

/**
 * clsPermissoes class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @todo      Eliminar a lógica duplicada dos métodos permissao_*
 * @version   @@package_version@@
 */
class clsPermissoes
{
  function clsPermissoes()
  {
  }

  /**
   * Verifica se um usuário tem permissão para cadastrar baseado em um
   * identificador de processo.
   *
   * @param int $int_processo_ap Identificador de processo
   * @param int $int_idpes_usuario Identificador do usuário
   * @param int $int_soma_nivel_acesso
   * @param string $str_pagina_redirecionar Caminho para o qual a requisição será
   *   encaminhada caso o usuário não tenha privilégios suficientes para a
   *   operação de cadastro
   * @param bool $super_usuario TRUE para verificar se o usuário é super usuário
   * @param bool $int_verifica_usuario_biblioteca TRUE para verificar se o
   *   usuário possui cadastro em alguma biblioteca
   * @return bool|void
   */
  function permissao_cadastra($int_processo_ap, $int_idpes_usuario,
    $int_soma_nivel_acesso, $str_pagina_redirecionar = NULL,
    $super_usuario = NULL, $int_verifica_usuario_biblioteca = FALSE)
  {
    $obj_usuario = new clsFuncionario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    // Verifica se é super usuário
    if ($super_usuario != NULL && $detalhe_usuario['ativo']) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario, FALSE, FALSE, 0);
      $detalhe_super_usuario = $obj_menu_funcionario->detalhe();
    }

    if (!$detalhe_super_usuario) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,
        FALSE, FALSE, $int_processo_ap);
      $detalhe = $obj_menu_funcionario->detalhe();
    }

    $nivel = $this->nivel_acesso($int_idpes_usuario);
    $ok = FALSE;

    if (($super_usuario && $detalhe_super_usuario) || $nivel & $int_soma_nivel_acesso) {
      $ok = TRUE;
    }

    if ((!$detalhe['cadastra'] && !$detalhe_super_usuario)) {
      $ok = FALSE;
    }

    /*
     * Se for usuario tipo biblioteca ou escola
     * ($int_verifica_usuario_biblioteca = true), verifica se possui cadastro na
     * tabela usuario biblioteca
     */
    if (($nivel == 8 ||
        ($nivel == 4 && $int_verifica_usuario_biblioteca == TRUE)
      ) && $int_soma_nivel_acesso > 3 && !$detalhe_super_usuario
    ) {
      $ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? FALSE : TRUE;

      if (!$ok && $nivel == 8) {
        header("Location: index.php?negado=1");
        echo("Usuário não adicionado (ao cadastro da) biblioteca. <a href='/intranet'>Inicio</a>");
        die();
      }
    }

    if (!$ok) {
      if ($str_pagina_redirecionar) {
        header("Location: $str_pagina_redirecionar");
        die();
      }
      else {
        return FALSE;
      }
    }

    return  TRUE;
  }

  /**
   * Verifica se um usuário tem permissão para cadastrar baseado em um
   * identificador de processo.
   *
   * @param int $int_processo_ap Identificador de processo
   * @param int $int_idpes_usuario Identificador do usuário
   * @param int $int_soma_nivel_acesso
   * @param string $str_pagina_redirecionar Caminho para o qual a requisição será
   *   encaminhada caso o usuário não tenha privilégios suficientes para a
   *   operação de cadastro
   * @param bool $super_usuario TRUE para verificar se o usuário é super usuário
   * @param bool $int_verifica_usuario_biblioteca TRUE para verificar se o
   *   usuário possui cadastro em alguma biblioteca
   * @return bool|void
   */
  function permissao_excluir($int_processo_ap, $int_idpes_usuario,
    $int_soma_nivel_acesso, $str_pagina_redirecionar = NULL,
    $super_usuario = NULL,$int_verifica_usuario_biblioteca = FALSE)
  {
    $obj_usuario = new clsFuncionario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    // Verifica se é super usuário
    if ($super_usuario != NULL && $detalhe_usuario['ativo']) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario, FALSE, FALSE, 0);
      $detalhe_super_usuario = $obj_menu_funcionario->detalhe();
    }

    if (!$detalhe_super_usuario) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,
        FALSE, FALSE, $int_processo_ap);
      $detalhe = $obj_menu_funcionario->detalhe();
    }

    $nivel = $this->nivel_acesso($int_idpes_usuario);
    $ok = FALSE;

    if (($super_usuario && $detalhe_super_usuario) || $nivel & $int_soma_nivel_acesso) {
      $ok = TRUE;
    }

    if ((!$detalhe['exclui'] && ! $detalhe_super_usuario)) {
      $ok = FALSE;
    }

    /*
     * Se for usuario tipo biblioteca ou escola
     * ($int_verifica_usuario_biblioteca = true), verifica se possui cadastro na
     * tabela usuario biblioteca
     */
    if (($nivel == 8 ||
        ($nivel == 4 && $int_verifica_usuario_biblioteca == TRUE)
      ) && $int_soma_nivel_acesso > 3 && !$detalhe_super_usuario
    ) {
      $ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? FALSE : TRUE;

      if (!$ok && $nivel == 8) {
        header("Location: index.php?negado=1");
        die();
      }
    }

    if (! $ok) {
      if($str_pagina_redirecionar) {
        header("Location: $str_pagina_redirecionar");
        die();
      }
      else {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Retorna o nível de acesso do usuário, podendo ser:
   *
   * - 1: Poli-institucional
   * - 2: Institucional
   * - 4: Escola
   * - 8: Biblioteca
   *
   * @param int $int_idpes_usuario
   * @return bool|int Retorna FALSE caso o usuário não exista
   */
  function nivel_acesso($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      $obj_tipo_usuario = new clsPmieducarTipoUsuario($detalhe_usuario['ref_cod_tipo_usuario']);
      $detalhe_tipo_usuario = $obj_tipo_usuario->detalhe();
      return $detalhe_tipo_usuario['nivel'];
    }

    return FALSE;
  }

  /**
   * Retorna o código identificador da instituição ao qual o usuário está
   * vinculado.
   *
   * @param int $int_idpes_usuario
   * @return bool|int Retorna FALSE caso o usuário não exista
   */
  function getInstituicao($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      return $detalhe_usuario['ref_cod_instituicao'];
    }

    return FALSE;
  }

  /**
   * Retorna o código identificador da escola ao qual o usuário está vinculado.
   *
   * @param int $int_idpes_usuario
   * @return bool|int Retorna FALSE caso o usuário não exista
   */
  function getEscola($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      return $detalhe_usuario['ref_cod_escola'];
    }

    return FALSE;
  }

  /**
   * Retorna um array associativo com os códigos identificadores da escola e
   * da instituição ao qual o usuário está vinculado.
   *
   * @param $int_idpes_usuario
   * @return array|bool Retorna FALSE caso o usuário não exista
   */
  function getInstituicaoEscola($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      return array(
        "instituicao" => $detalhe_usuario['ref_cod_instituicao'],
        "escola" => $detalhe_usuario['ref_cod_escola']
      );
    }

    return FALSE;
  }

  /**
   * Retorna um array com os códigos identificadores das bibliotecas aos quais
   * o usuário está vinculado.
   *
   * @param int $int_idpes_usuario
   * @return array|int Retorna o inteiro "0" caso o usuário não esteja vinculado
   *   a uma biblioteca
   */
  function getBiblioteca($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarBibliotecaUsuario();
    $lst_usuario_biblioteca = $obj_usuario->lista(NULL, $int_idpes_usuario);

    if ($lst_usuario_biblioteca) {
      return $lst_usuario_biblioteca;
    }
    else {
      return 0;
    }
  }
}
