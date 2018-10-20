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
 * @author Rodrigo Rodrigues <rodrigogbgod@gmail.com>
 * @category  i-Educar
 * @license   GPL-2.0+
 * @package   Core_Controller
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'Configuracao/Model/ConfiguracaoMovimentoGeral.php';

class ConfiguracaoMovimentoGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'ConfiguracaoMovimentoGeral';
    protected $_tableName   = 'config_movimento_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'serie'       => 'ref_cod_serie',
        'coluna'      => 'coluna'
    );
}
