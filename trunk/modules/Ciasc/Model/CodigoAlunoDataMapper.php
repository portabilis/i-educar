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
 * @author     Tiago Oliveira Camargo <tiago.camargo@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     serieciasc
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'Ciasc/Model/CodigoAluno.php';
require_once 'CoreExt/DataMapper.php';

class Ciasc_Model_CodigoAlunoDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Ciasc_Model_CodigoAluno';
  protected $_tableSchema = 'serieciasc';
  protected $_tableName   = 'aluno_cod_aluno';

  protected $_primaryKey = array(
    'cod_aluno', 'cod_ciasc'
  );

  public function __construct(clsBanco $db = NULL)
  {
    $this->_attributeMap = array(
        'cod_ciasc'  => 'cod_ciasc',
        'cod_aluno'  => 'cod_aluno',
        'user'    => 'user_id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    );
    parent::__construct($db);
  }
}