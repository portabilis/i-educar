<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'intranet/include/clsBanco.inc.php';

class EducacensoAnaliseController extends ApiCoreController
{

  protected function analisaEducacensoRegistro00() {

    $escola = $this->getRequest()->escola;
    $ano    = $this->getRequest()->ano;

    $sql = "SELECT educacenso_cod_escola.cod_escola_inep AS inep,
                   fisica_gestor.cpf AS cpf_gestor_escolar,
                   pessoa_gestor.nome AS nome_gestor_escolar,
                   escola.cargo_gestor AS cargo_gestor_escolar,
                   modulo1.data_inicio AS data_inicio,
                   modulo2.data_fim AS data_fim,
                   escola.latitude AS latitude,
                   escola.longitude AS logitude,
                   municipio.nome AS municipio,
                   municipio.sigla_uf AS uf_municipio,
                   distrito.nome AS distrito
              FROM pmieducar.escola
             INNER JOIN pmieducar.escola_ano_letivo ON (escola_ano_letivo.ref_cod_escola = escola.cod_escola)
             INNER JOIN pmieducar.ano_letivo_modulo modulo1 ON (modulo1.ref_ref_cod_escola = escola.cod_escola
                          AND modulo1.ref_ano = escola_ano_letivo.ano
                          AND modulo1.sequencial = 1)
             INNER JOIN pmieducar.ano_letivo_modulo modulo2 ON (modulo2.ref_ref_cod_escola = escola.cod_escola
                          AND modulo2.ref_ano = escola_ano_letivo.ano
                          AND modulo2.sequencial = (SELECT MAX(sequencial)
                                                      FROM pmieducar.ano_letivo_modulo
                                                     WHERE ref_ano = escola_ano_letivo.ano
                                                       AND ref_ref_cod_escola = escola.cod_escola))
              LEFT JOIN cadastro.pessoa pessoa_gestor ON (pessoa_gestor.idpes = escola.ref_idpes_gestor)
              LEFT JOIN cadastro.fisica fisica_gestor ON (fisica_gestor.idpes = escola.ref_idpes_gestor)
              LEFT JOIN modules.educacenso_cod_escola ON (educacenso_cod_escola.cod_escola = escola.cod_escola)
              LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = escola.ref_idpes)
              LEFT JOIN public.bairro ON (bairro.idbai = endereco_pessoa.idbai)
              LEFT JOIN public.municipio ON (municipio.idmun = bairro.idmun)
              LEFT JOIN public.distrito ON (distrito.idmun = bairro.idmun)
             WHERE escola.cod_escola = $1
               AND escola_ano_letivo.ano = $2";

    $escola = $this->fetchPreparedQuery($sql, array($escola, $ano));

    if(empty($escola)){
      $this->messenger->append("O ano letivo {$ano} não foi definido.");
      return array( 'escolas' => 0);
    }
    else{
      $attrs = array('inep', 'cpf_gestor_escolar', 'nome_gestor_escolar', 'cargo_gestor_escolar', 'data_inicio', 'data_fim', 'latitude', 'longitude', 'municipio', 'uf_municipio', 'distrito');
      return array( 'escola' => Portabilis_Array_Utils::filterSet($escola, $attrs));
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'registro-00'))
      $this->appendResponse($this->analisaEducacensoRegistro00());
    else
      $this->notImplementedOperationError();
  }
}
