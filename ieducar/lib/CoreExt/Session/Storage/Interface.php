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
 * @package   CoreExt_Session
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Configurable.php';

/**
 * CoreExt_Session_Storage_Interface interface.
 *
 * Interface mínima para que um storage de session possa ser criado. Define
 * os métodos básicos de escrita e inicialização/destruição de uma session.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Session
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
interface CoreExt_Session_Storage_Interface extends CoreExt_Configurable
{
  /**
   * Inicializa a session.
   */
  public function start();

  /**
   *
   * @param string $key
   * @return mixed
   */
  public function read($key);

  /**
   * Persiste um dado valor na session.
   * @param string $key
   * @param mixed $value
   */
  public function write($key, $value);

  /**
   * Remove/apaga um dado na session.
   * @param string $key
   */
  public function remove($key);

  /**
   * Destrói os dados de uma session.
   */
  public function destroy();

  /**
   * Gera um novo id para a session.
   */
  public function regenerate($destroy = FALSE);

  /**
   * Persiste os dados da session no storage definido ao final da execução
   * do script PHP.
   */
  public function shutdown();
}