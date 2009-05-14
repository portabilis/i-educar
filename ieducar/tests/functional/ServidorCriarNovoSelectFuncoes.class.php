<?php

/*
 * i-Educar - Sistema de gestão de escolas
 *
 * Copyright (c) 2006   Prefeitura Municipal de Itajaí
 *                                 <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuÃí-lo e/ou modificá-lo
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
 */


require_once realpath(dirname(__FILE__) . '/../') . '/FunctionalBaseTest.class.php';

/**
 * ServidorCriarNovoSelectFuncoes class
 *
 * Testa se a condição de carregamento de funções via XMLHttpRequest do select
 * de funções está funcionando corretamente. Esse comportamento é testado para
 * assegurar a correção do bug do {@link http://svn.softwarepublico.gov.br/trac/ieducar/ticket/25 ticket 25}
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Test
 * @since    Classe disponível desde a versão 1.0.2
 * @version  $Id$
 */
class ServidorCriarNovoSelectFuncoes extends FunctionalBaseTest {

  /**
   * Testa se o select de funções do cadastro de servidor contém mais de uma
   * opção
   *
   * Esse teste espera que a primeira instituição lista no select
   * ref_cod_instituicao tenha funções cadastradas
   *
   * @see educar_servidor_cad.php
   */
  public function testSelectFuncoesWithOptions() {
    $this->doLogin();
    $this->open('/intranet/educar_servidor_cad.php');

    $items   = 0;
    $timeout = 60;
    $this->select("//*[@id=\"ref_cod_instituicao\"]", "index=1");

    for ($second = 0; ; $second++) {
      if ($second >= $timeout) {
        $this->fail("Requisição XMLHTTPRequest demorou mais que " . $timeout .
          " segundos.");
      }

      try {
        $items = count($this->getSelectOptions("//*[@id=\"ref_cod_funcao[0]\"]"));
        if ($items > 1) {
          $this->assertTrue($items > 1);
          break;
        }
      } catch (Exception $e) {
        $this->fail("Exception: " . $e);
      }
      sleep(1);
    }
  }

}