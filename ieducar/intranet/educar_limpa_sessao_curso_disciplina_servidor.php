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

/**
 * Apaga variáveis de sessão contendo dados de função do servidor
 *
 * Arquivo acessado via XMLHttpRequest
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

session_start();
unset($_SESSION['cursos_disciplina']);
unset($_SESSION['cursos_servidor']);
unset($_SESSION['cod_servidor']);
session_write_close();
echo "";