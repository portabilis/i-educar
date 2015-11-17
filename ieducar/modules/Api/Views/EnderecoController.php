<?php
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */
require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';
require_once 'intranet/include/funcoes.inc.php';
class EnderecoController extends ApiCoreController
{
  protected function getPrimeiroEnderecoCep() {
    
    $cep = idFederal2int($this->getRequest()->cep);
    
    // consulta dados
    $select = "
      SELECT
        c.idlog, c.cep, c.idbai, b.nome as nome_bairro, l.nome as nome_logradouro, u.sigla_uf, m.nome, t.idtlog, t.descricao as tipo_logradouro, m.idmun, b.zona_localizacao 
   
      FROM
        urbano.cep_logradouro_bairro c, public.bairro b, public.logradouro l,
        public.municipio m, public.uf u, urbano.tipo_logradouro t
      WHERE
        c.idlog = l.idlog AND
        c.idbai = b.idbai AND
        l.idmun = b.idmun AND
        l.idmun = m.idmun AND
        l.idtlog = t.idtlog AND
        m.sigla_uf = u.sigla_uf AND 
        c.cep = {$cep} LIMIT 1";
    
    $result = Portabilis_Utils_Database::fetchPreparedQuery($select, array('return_only' => 'first-line'));
    $return;
    if (is_array($result)){      
      $return = array();
      foreach ($result as $name => $value) {
        $return[$name] = Portabilis_String_Utils::toUtf8($value);
      }        
    }
    
    return $return;
  }  
  public function Gerar() {
    if ($this->isRequestFor('get', 'primeiro_endereco_cep'))
      $this->appendResponse($this->getPrimeiroEnderecoCep());
    else
      $this->notImplementedOperationError();
  }
}