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

class FilaUnicaController extends ApiCoreController
{
  
    protected function getDadosAlunoByCertidao()
    {
        $tipoCertidao = $this->getRequest()->tipo_certidao;
        $numNovaCeridao = $this->getRequest()->certidao_nascimento;
        $numTermo = $this->getRequest()->num_termo ? $this->getRequest()->num_termo : 0;
        $numLivro = $this->getRequest()->num_livro ? $this->getRequest()->num_livro : 0;
        $numFolha = $this->getRequest()->num_folha ? $this->getRequest()->num_folha : 0;

        $sql = "SELECT cod_aluno,
                       pessoa.nome,
                       pessoa.idpes,
                       to_char(fisica.data_nasc, 'dd/mm/yyyy') AS data_nasc,
                       documento.num_termo,
                       documento.num_folha,
                       documento.num_livro,
                       documento.certidao_nascimento,
                       to_char(endereco_pessoa.cep, '99999-999') AS cep,
                       endereco_pessoa.numero,
                       endereco_pessoa.letra,
                       endereco_pessoa.complemento,
                       endereco_pessoa.bloco,
                       endereco_pessoa.andar,
                       endereco_pessoa.apartamento
                  FROM pmieducar.aluno
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.fisica ON (fisica.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.documento ON (documento.idpes = aluno.ref_idpes)
                  LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = aluno.ref_idpes)
                 WHERE CASE WHEN {$tipoCertidao} != 1
                                 THEN num_termo = '{$numTermo}'
                                  AND num_livro = '{$numLivro}'
                                  AND num_folha = '{$numFolha}'
                            ELSE certidao_nascimento = '{$numNovaCeridao}'
                        END";

        $attrs = array(
            'cod_aluno',
            'nome',
            'idpes',
            'data_nasc',
            'num_termo',
            'num_folha',
            'num_livro',
            'certidao_nascimento',
            'cep',
            'numero',
            'letra',
            'complemento',
            'bloco',
            'andar',
            'apartamento'
        );
        $aluno = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql), $attrs);
        return array('aluno' => $aluno[0]);
    }

    protected function getMatriculaAlunoAndamento() {
        $anoLetivo = $this->getRequest()->ano_letivo;
        $aluno = $this->getRequest()->aluno_id;
        
        if($aluno && $anoLetivo){
            $sql = "SELECT cod_matricula,
                           ref_cod_aluno AS cod_aluno
                      FROM pmieducar.matricula
                     WHERE ativo = 1
                       AND aprovado = 3
                       AND ano = $1
                       AND ref_cod_aluno = $2";
            $matricula = $this->fetchPreparedQuery($sql, array($anoLetivo, $aluno), false, 'first-line');
            return $matricula;
        }
        return false;
    }

    protected function getSolicitacaoAndamento() {
        $anoLetivo = $this->getRequest()->ano_letivo;
        $aluno = $this->getRequest()->aluno_id;

        if($aluno && $anoLetivo){
            $sql = "SELECT ref_cod_aluno AS cod_aluno,
                           cod_candidato_fila_unica AS cod_candidato
                      FROM pmieducar.candidato_fila_unica
                     WHERE ativo = 1
                       AND ano_letivo = $1
                       AND ref_cod_aluno = $2";
            $matricula = $this->fetchPreparedQuery($sql, array($anoLetivo, $aluno), false, 'first-line');
            return $matricula;
        }
        return false;
    }

    protected function getSeriesSugeridas() {
        $idade = $this->getRequest()->idade;
        if($idade){
            $sql = "SELECT nm_serie
                      FROM pmieducar.serie
                     WHERE ativo = 1
                       AND $1 BETWEEN idade_inicial AND idade_final";
            $series = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql, $idade), 'nm_serie'); 
            return array('series' => $series);
        }
        return false;
    }

  public function Gerar() {
    if ($this->isRequestFor('get', 'get-aluno-by-certidao')) {
        $this->appendResponse($this->getDadosAlunoByCertidao());
    }else if ($this->isRequestFor('get', 'matricula-andamento')){
        $this->appendResponse($this->getMatriculaAlunoAndamento());
    }else if ($this->isRequestFor('get', 'solicitacao-andamento')){
        $this->appendResponse($this->getSolicitacaoAndamento());
    }else if ($this->isRequestFor('get', 'series-sugeridas')){
        $this->appendResponse($this->getSeriesSugeridas());
    }
    else{
      $this->notImplementedOperationError();
    }
  }
}