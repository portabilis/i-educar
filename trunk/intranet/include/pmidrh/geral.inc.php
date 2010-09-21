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

/**
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

// Inclui operações de bootstrap.
require_once '../includes/bootstrap.php';


require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

require_once 'include/pmidrh/clsPmidrhCargos.inc.php';
require_once 'include/pmidrh/clsPmidrhDiaria.inc.php';
require_once 'include/pmidrh/clsPmidrhDiariaGrupo.inc.php';
require_once 'include/pmidrh/clsPmidrhDiariaValores.inc.php';
require_once 'include/pmidrh/clsPmidrhLogVisualizacaoOlerite.inc.php';
require_once 'include/pmidrh/clsPmidrhPortaria.inc.php';
require_once 'include/pmidrh/clsPmidrhPortariaCamposEspeciaisValor.inc.php';
require_once 'include/pmidrh/clsPmidrhPortariaCamposTabela.inc.php';
require_once 'include/pmidrh/clsPmidrhPortariaFuncionario.inc.php';
require_once 'include/pmidrh/clsPmidrhStatus.inc.php';
require_once 'include/pmidrh/clsPmidrhTipoPortaria.inc.php';
require_once 'include/pmidrh/clsPmidrhTipoPortariaCamposEspeciais.inc.php';
require_once 'include/pmidrh/clsPmidrhPortariaCamposTabelaValor.inc.php';
require_once 'include/pmidrh/clsPmidrhPortariaResponsavel.inc.php';
require_once 'include/pmidrh/clsPmidrhPortariaAssinatura.inc.php';
require_once 'include/pmidrh/clsPmidrhUsuario.inc.php';
require_once 'include/pmidrh/clsPmidrhInstituicao.inc.php';
require_once 'include/pmidrh/clsSetor.inc.php';