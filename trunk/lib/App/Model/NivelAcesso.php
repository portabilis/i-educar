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
 * App_Model_NivelAcesso class.
 *
 * Define os valores inteiros usados nas comparações das verificações de
 * acesso da classe clsPermissoes.
 *
 * Esses valores são verificados com o uso do operador binário &, resultando
 * na seguinte tabela verdade:
 *
 * <code>
 * +------------------------+---+---+---+----+
 * | Nível acessos          | 1 | 3 | 7 | 11 |
 * +------------------------+---+---+---+----+
 * | Poli-institucional (1) | T | T | T |  T |
 * +------------------------+---+---+---+----+
 * | Institucional      (2) | F | T | T |  T |
 * +------------------------+---+---+---+----+
 * | Escola             (4) | F | F | T |  F |
 * +------------------------+---+---+---+----+
 * | Biblioteca         (8) | F | F | F |  T |
 * +------------------------+---+---+---+----+
 *
 * Onde, T = TRUE; F = FALSE
 * </code>
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @see       clsPermissoes#permissao_cadastra
 * @see       clsPermissoes#permissao_excluir
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class App_Model_NivelAcesso extends CoreExt_Enum
{
  const POLI_INSTITUCIONAL = 1;
  const INSTITUCIONAL      = 3;
  const SOMENTE_ESCOLA     = 7;
  const SOMENTE_BIBLIOTECA = 11;

  protected $_data = array(
    self::POLI_INSTITUCIONAL => 'Poli-institucional',
    self::INSTITUCIONAL      => 'Institucional',
    self::SOMENTE_ESCOLA     => 'Somente escola',
    self::SOMENTE_BIBLIOTECA => 'Somente biblioteca'
  );

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}