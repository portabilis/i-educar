<?php

/*
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
 */


/*
 * Faz require do bootstrap para que as mesmas configurações do ambiente
 * (conexão com o banco de dados, títulos, configurações do PHP), sejam
 * utilizadas pelos unit test para evitar discrepâncias no comportamento.
 */
require_once realpath(dirname(__FILE__) . '/../') . '/includes/bootstrap.php';

chdir(realpath(dirname(__FILE__) . '/../') . '/intranet');
require_once 'PHPUnit/Framework.php';
require_once 'include/clsBanco.inc.php';


/**
 * UnitBaseTest abstract class.
 *
 * Muda o diretório atual para que os testes possam ser facilmente invocados
 * em qualquer subdiretório do sistema.
 *
 * Abstrai o PHPUnit, diminuindo a dependência de seu uso.
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Test
 * @since    Classe disponível desde a versão 1.0.1
 * @version  $Id$
 */
abstract class UnitBaseTest extends PHPUnit_Framework_TestCase {}