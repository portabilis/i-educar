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
                   EXTRACT(YEAR FROM modulo1.data_inicio) AS data_inicio,
                   EXTRACT(YEAR FROM modulo2.data_fim) AS data_fim,
                   escola.latitude AS latitude,
                   escola.longitude AS longitude,
                   municipio.cod_ibge AS inep_municipio,
                   uf.cod_ibge AS inep_uf,
                   distrito.cod_ibge AS inep_distrito,
                   pessoa.nome AS nome_escola
              FROM pmieducar.escola
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = escola.ref_idpes)
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
              LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
              LEFT JOIN public.distrito ON (distrito.idmun = bairro.idmun)
             WHERE escola.cod_escola = $1
               AND escola_ano_letivo.ano = $2";

    $escola = $this->fetchPreparedQuery($sql, array($escola, $ano));

    if(empty($escola)){
      $this->messenger->append("O ano letivo {$ano} não foi definido.");
      return null;
    }

    $escola       = $escola[0];
    $nomeEscola   = $escola["nome_escola"];
    $anoAtual     = date("Y");
    $anoAnterior  = $anoAtual-1;
    $anoPosterior = $anoAtual+1;

    $mensagem = array();

    if (!$escola["inep"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se a escola possui o código INEP cadastrado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Código INEP)");
    }
    if (!$escola["cpf_gestor_escolar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o(a) gestor(a) escolar possui o CPF cadastrado.",
                          "path" => "(Pessoa FJ > Pessoa física > Editar > Campo: CPF)");
    }
    if (!$escola["nome_gestor_escolar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o(a) gestor(a) escolar foi informado(a).",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Gestor escolar)");
    }
    if (!$escola["cargo_gestor_escolar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o cargo do(a) gestor(a) escolar foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Campo: Cargo do gestor escolar)");
    }
    if ($escola["data_inicio"] != $anoAtual && $escola["data_inicio"] != $anoAnterior) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verifique se a data inicial da primeira etapa foi cadastrada corretamente.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar ano letivo > Ok > Campo: Data inicial)");
    }
    if ($escola["data_fim"] != $anoAtual && $escola["data_fim"] != $anoPosterior) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verifique se a data final da última etapa foi cadastrada corretamente.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar ano letivo > Ok > Campo: Data final)");
    }
    if ((!$escola["latitude"]) && $escola["longitude"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a longitude foi informada, portanto obrigatoriamente a latitude também deve ser informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Latitude)");
    }
    if ((!$escola["longitude"]) && $escola["latitude"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a latitude foi informada, portanto obrigatoriamente a longitude também deve ser informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Longitude)");
    }
    if (!$escola["inep_uf"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código da UF informada, foi cadastrado conforme a 'Tabela de UF'.",
                          "path" => "(Endereçamento > Estado > Editar > Campo: Código INEP)");
    }
    if (!$escola["inep_municipio"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código do município informado, foi cadastrado conforme a 'Tabela de Municípios'.",
                          "path" => "(Endereçamento > Município > Editar > Campo: Código INEP)");
    }
    if (!$escola["inep_distrito"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código do distrito informado, foi cadastrado conforme a 'Tabela de Distritos'.",
                          "path" => "(Endereçamento > Distrito > Editar > Campo: Código INEP)");
    }

    return array('mensagens' => $mensagem,
                 'title'     => "Análise exportação - Registro 00");
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'registro-00'))
      $this->appendResponse($this->analisaEducacensoRegistro00());
    else
      $this->notImplementedOperationError();
  }
}
