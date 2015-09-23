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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
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

class PessoaController extends ApiCoreController
{

  protected function canGet() {
    $can = true;

    if (! $this->getRequest()->id && ! $this->getRequest()->cpf) {
      $can = false;
      $this->messenger->append("É necessário receber uma variavel 'id' ou 'cpf'");
    }

    elseif ($this->getRequest()->id)
      $can = $this->validatesResourceId();

    return $can;
  }

  // validators

  // overwrite api core validator
  protected function validatesResourceId() {
    $existenceOptions = array('schema_name' => 'cadastro', 'field_name' => 'idpes');

    return  $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('fisica', $this->getRequest()->id, $existenceOptions);
  }


  // load resources

  protected function tryLoadAlunoId($pessoaId) {
    $sql = "select cod_aluno as id from pmieducar.aluno where ref_idpes = $1";
    $id  = $this->fetchPreparedQuery($sql, $pessoaId, false, 'first-field');

    // caso um array vazio seja retornado, seta resultado como null,
    // evitando erro em loadDetails
    if (empty($id))
      $id = null;

    return $id;
  }

  protected function loadPessoa($id = null) {
    $sql            = "select idpes as id, nome from cadastro.pessoa where idpes = $1";

    $pessoa         = $this->fetchPreparedQuery($sql, $id, false, 'first-row');
    $pessoa['nome'] = $this->toUtf8($pessoa['nome'], array('transform' => true));

    return $pessoa;
  }

  protected function loadPessoaByCpf($cpf = null) {
    $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);

    if (! $cpf)
      throw new Exception("CPF deve conter caracteres numéricos");

    $sql            = "select pessoa.idpes as id, nome from cadastro.pessoa, fisica
                       where fisica.idpes = pessoa.idpes and cpf = $1 limit 1";

    $pessoa         = $this->fetchPreparedQuery($sql, $cpf, false, 'first-row');
    $pessoa['nome'] = $this->toUtf8($pessoa['nome'], array('transform' => true));

    return $pessoa;
  }

  protected function loadDetails($pessoaId = null) {
    $alunoId = $this->tryLoadAlunoId($pessoaId);

    $sql = "select cpf, data_nasc as data_nascimento, idpes_pai as pai_id,
            idpes_mae as mae_id, idpes_responsavel as responsavel_id,
            coalesce((select nome from cadastro.pessoa where idpes = fisica.idpes_pai),
            (select nm_pai from pmieducar.aluno where cod_aluno = $1)) as nome_pai,
            coalesce((select nome from cadastro.pessoa where idpes = fisica.idpes_mae),
            (select nm_mae from pmieducar.aluno where cod_aluno = $1)) as nome_mae,
            (select nome from cadastro.pessoa where idpes = fisica.idpes_responsavel) as nome_responsavel,
            (select rg from cadastro.documento where documento.idpes = fisica.idpes) as rg
            from cadastro.fisica where idpes = $2";

    $details = $this->fetchPreparedQuery($sql, array($alunoId, $pessoaId), false, 'first-row');

    $attrs   = array('cpf', 'rg', 'data_nascimento', 'pai_id', 'mae_id', 'responsavel_id', 'nome_pai', 'nome_mae', 'nome_responsavel');
    $details = Portabilis_Array_Utils::filter($details, $attrs);

    $details['aluno_id']         = $alunoId;
    $details['nome_mae']         = $this->toUtf8($details['nome_mae'], array('transform' => true));
    $details['nome_pai']         = $this->toUtf8($details['nome_pai'], array('transform' => true));
    $details['nome_responsavel'] = $this->toUtf8($details['nome_responsavel'], array('transform' => true));

    $details['data_nascimento']  = Portabilis_Date_Utils::pgSQLToBr($details['data_nascimento']);

    return $details;
  }

  protected function loadDeficiencias($pessoaId) {
    $sql = "select cod_deficiencia as id, nm_deficiencia as nome from cadastro.fisica_deficiencia,
            cadastro.deficiencia where cod_deficiencia = ref_cod_deficiencia and ref_idpes = $1";

    $deficiencias = $this->fetchPreparedQuery($sql, $pessoaId, false);

    // transforma array de arrays em array chave valor
    $_deficiencias = array();

    foreach ($deficiencias as $deficiencia) {
      $nome = $this->toUtf8($deficiencia['nome'], array('transform' => true));
      $_deficiencias[$deficiencia['id']] = $nome;
    }

    return $_deficiencias;
  }

  protected function loadRg($pessoaId) {
    $sql = "select rg from cadastro.documento where idpes = $1";
    $rg  = $this->fetchPreparedQuery($sql, $pessoaId, false, 'first-field');

    // caso um array vazio seja retornado, seta resultado como null
    if (empty($rg))
      $rg = null;

    return $rg;
  }

  protected function loadDataNascimento($pessoaId) {
    $sql        = "select data_nasc from cadastro.fisica where idpes = $1";
    $nascimento = $this->fetchPreparedQuery($sql, $pessoaId, false, 'first-field');

    // caso um array vazio seja retornado, seta resultado como null
    if (empty($nascimento))
      $nascimento = null;

    return $nascimento;
  }


  // search

  protected function searchOptions() {
    return array('namespace' => 'cadastro', 'idAttr' => 'idpes');
  }

  protected function sqlsForNumericSearch() {
    $sqls = array();

    // search by idpes or cpf
    $sqls[] = "select distinct pessoa.idpes as id, pessoa.nome as name from cadastro.pessoa,
               cadastro.fisica where fisica.idpes = pessoa.idpes and (pessoa.idpes like $1||'%' or
               trim(leading '0' from fisica.cpf) like trim(leading '0' from $1)||'%' or
               fisica.cpf like $1||'%') order by id limit 15";

    // search by rg
    $sqls[] = "select distinct pessoa.idpes as id, pessoa.nome as name from cadastro.pessoa, cadastro.documento
               where pessoa.idpes = documento.idpes and ((documento.rg like $1||'%') or
               trim(leading '0' from documento.rg) like trim(leading '0' from $1)||'%') order by id limit 15";

    return $sqls;
  }

  // subscreve formatResourceValue para adicionar o rg da pessoa, ao final do valor,
  // "<id_pessoa> - <nome_pessoa> (RG: <rg>)", ex: "1 - Lucas D'Avila (RG: 1234567)"
  protected function formatResourceValue($resource) {
    $nome       = $this->toUtf8($resource['name'], array('transform' => true));
    $rg         = $this->loadRg($resource['id']);
    $nascimento = $this->loadDataNascimento($resource['id']);

    // Quando informado, inclui detalhes extra sobre a pessoa, como RG e Data nascimento.
    $details = array();

    if ($nascimento)
      $details[] = 'Nascimento: ' . Portabilis_Date_Utils::pgSQLToBr($nascimento);

    if ($rg)
      $details[] = "RG: $rg";

    $details = $details ? ' (' . implode(', ', $details) . ')' : '';

    return $resource['id'] . " - $nome$details";
  }

  // api responders

  protected function get() {
    $pessoa = array();

    if ($this->canGet()) {

      if ($this->getRequest()->id)
        $pessoa  = $this->loadPessoa($this->getRequest()->id);
      else
        $pessoa  = $this->loadPessoaByCpf($this->getRequest()->cpf);

      $attrs   = array('id', 'nome');
      $pessoa  = Portabilis_Array_Utils::filter($pessoa, $attrs);

      $details = $this->loadDetails($this->getRequest()->id);
      $pessoa  = Portabilis_Array_Utils::merge($pessoa, $details);

      $pessoa['deficiencias'] = $this->loadDeficiencias($this->getRequest()->id);
    }

    return $pessoa;
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'pessoa-search'))
      $this->appendResponse($this->search());

    elseif ($this->isRequestFor('get', 'pessoa'))
      $this->appendResponse($this->get());
    else
      $this->notImplementedOperationError();
  }
}
