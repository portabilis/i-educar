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
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/funcoes.inc.php';

/**
 * Class ReservavagaController
 * @deprecated Essa versão da API pública será descontinuada
 */
class ReservavagaController extends ApiCoreController
{

  protected function permiteMultiplasReservas() {

    $sql = "SELECT instituicao.multiplas_reserva_vaga
              FROM pmieducar.instituicao
              LIMIT 1 ";

    return dbBool($this->fetchPreparedQuery($sql, array(), true, 'first-field'));
  }

  protected function getCandidato() {
    $nome = $this->getRequest()->nome;
    $anoLetivo = $this->getRequest()->ano;
    $dataNascimento = $this->getRequest()->dataNascimento;
    $escola = $this->getRequest()->escola;

    $codigo = 0;

      if($nome && $anoLetivo && $dataNascimento && $escola){

        $sql = "SELECT candidato_reserva_vaga.cod_candidato_reserva_vaga AS codigo
                  FROM pmieducar.candidato_reserva_vaga
                 INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = candidato_reserva_vaga.ref_cod_aluno)
                 INNER JOIN cadastro.fisica ON (fisica.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                  LEFT JOIN cadastro.pessoa pessoa_responsavel ON (pessoa_responsavel.idpes = fisica.idpes_responsavel)
                  LEFT JOIN cadastro.fisica fisica_responsavel ON (fisica_responsavel.idpes = fisica.idpes_responsavel)
                 WHERE fisica.data_nasc = $3
                   AND candidato_reserva_vaga.ano_letivo = $2
                   AND candidato_reserva_vaga.ref_cod_escola = $4
                   AND ((candidato_reserva_vaga.situacao = 'A') or candidato_reserva_vaga.situacao IS NULL)
                   AND translate(public.fcn_upper(trim(pessoa.nome)),
                       'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                       'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN') = translate(public.fcn_upper(trim($1)),
                       'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                       'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN')";

        $params = array($nome, $anoLetivo, Portabilis_Date_Utils::brToPgSQL($dataNascimento), $escola);

        $candidato = $this->fetchPreparedQuery($sql, $params);

        if(!empty($candidato)){
          $codigo = $candidato[0]['codigo'];
        }
    }elseif ($nome && $anoLetivo && $dataNascimento){
      $sql = "SELECT candidato_reserva_vaga.cod_candidato_reserva_vaga AS codigo
                FROM pmieducar.candidato_reserva_vaga
               INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = candidato_reserva_vaga.ref_cod_aluno)
               INNER JOIN cadastro.fisica ON (fisica.idpes = aluno.ref_idpes)
               INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                LEFT JOIN cadastro.pessoa pessoa_responsavel ON (pessoa_responsavel.idpes = fisica.idpes_responsavel)
                LEFT JOIN cadastro.fisica fisica_responsavel ON (fisica_responsavel.idpes = fisica.idpes_responsavel)
               WHERE fisica.data_nasc = $3
                 AND ((candidato_reserva_vaga.situacao = 'A') or candidato_reserva_vaga.situacao IS NULL)
                 AND candidato_reserva_vaga.ano_letivo = $2
                 AND translate(public.fcn_upper(trim(pessoa.nome)),
                     'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                     'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN') = translate(public.fcn_upper(trim($1)),
                     'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                     'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN')";

      $candidato = $this->fetchPreparedQuery($sql, array($nome, $anoLetivo, Portabilis_Date_Utils::brToPgSQL($dataNascimento)));

      if(!empty($candidato)){
        $codigo = $candidato[0]['codigo'];
      }
    }
    return array('codigo' => $codigo, 'escola' => $escola);
  }

  protected function getAlunoAndamento() {
    $nome = $this->getRequest()->nome;
    $cpfResponsavel = $this->getRequest()->cpf;
    $dataNascimento = $this->getRequest()->dataNascimento;
    $anoReserva = $this->getRequest()->anoReserva;

    if ($nome && $cpfResponsavel && $dataNascimento && $anoReserva){

      $sql = "SELECT aluno.cod_aluno AS codigo
                FROM pmieducar.aluno
               INNER JOIN cadastro.fisica ON (fisica.idpes = aluno.ref_idpes)
               INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                LEFT JOIN cadastro.fisica responsavel ON (fisica.idpes_responsavel = responsavel.idpes)
                LEFT JOIN pmieducar.matricula ON (matricula.ref_cod_aluno = aluno.cod_aluno AND matricula.ativo = 1)
                WHERE fisica.data_nasc = $3
                  AND responsavel.cpf = $2
                  AND matricula.aprovado = 3
                  AND matricula.ano = $4
                  AND translate(public.fcn_upper(trim(pessoa.nome)),
                      'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                      'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN') = translate(public.fcn_upper(trim($1)),
                      'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                      'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN')";

      $aluno = $this->fetchPreparedQuery($sql, array($nome, idFederal2int($cpfResponsavel), Portabilis_Date_Utils::brToPgSQL($dataNascimento), $anoReserva));

      if(!empty($aluno)){
        return array('codigo' => $aluno[0]['codigo']);
      }
    }
    return array('codigo' => 0);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'candidato'))
      $this->appendResponse($this->getCandidato());
    else if ($this->isRequestFor('get', 'aluno-andamento'))
      $this->appendResponse($this->getAlunoAndamento());
    else
      $this->notImplementedOperationError();
  }
}
