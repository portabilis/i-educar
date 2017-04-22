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
require_once 'include/pessoa/clsPessoa_.inc.php';
require_once 'include/pessoa/clsFisica.inc.php';
require_once 'intranet/include/funcoes.inc.php';

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
            ideciv as estadocivil, sexo,
            coalesce((select nome from cadastro.pessoa where idpes = fisica.idpes_pai),
            (select nm_pai from pmieducar.aluno where cod_aluno = $1)) as nome_pai,
            coalesce((select nome from cadastro.pessoa where idpes = fisica.idpes_mae),
            (select nm_mae from pmieducar.aluno where cod_aluno = $1)) as nome_mae,
            (select nome from cadastro.pessoa where idpes = fisica.idpes_responsavel) as nome_responsavel,
            (select rg from cadastro.documento where documento.idpes = fisica.idpes) as rg,
            (SELECT COALESCE((SELECT cep FROM cadastro.endereco_pessoa WHERE idpes = $2),
            (SELECT cep FROM cadastro.endereco_externo WHERE idpes = $2))) as cep,

             (SELECT COALESCE((SELECT l.nome FROM public.logradouro l, cadastro.endereco_pessoa ep WHERE l.idlog = ep.idlog and ep.idpes = $2),
             (SELECT logradouro FROM cadastro.endereco_externo WHERE idpes = $2))) as logradouro,

             (SELECT COALESCE((SELECT l.idtlog FROM public.logradouro l, cadastro.endereco_pessoa ep WHERE l.idlog = ep.idlog and ep.idpes = $2),
             (SELECT idtlog FROM cadastro.endereco_externo WHERE idpes = $2))) as idtlog,

           (SELECT COALESCE((SELECT b.nome FROM public.bairro b, cadastro.endereco_pessoa ep WHERE b.idbai = ep.idbai and ep.idpes = $2),
             (SELECT bairro FROM cadastro.endereco_externo WHERE idpes = $2))) as bairro,

             (SELECT COALESCE((SELECT b.zona_localizacao FROM public.bairro b, cadastro.endereco_pessoa ep WHERE b.idbai = ep.idbai and ep.idpes = $2),
             (SELECT zona_localizacao FROM cadastro.endereco_externo WHERE idpes = $2))) as zona_localizacao,

             (SELECT COALESCE((SELECT l.idmun FROM public.logradouro l, cadastro.endereco_pessoa ep WHERE l.idlog = ep.idlog and ep.idpes = $2),
             (SELECT idmun FROM public.logradouro l, urbano.cep_logradouro cl, cadastro.endereco_externo ee
              WHERE cl.idlog = l.idlog AND cl.cep = ee.cep and ee.idpes = $2 order by 1 desc limit 1))) as idmun,

              idmun_nascimento,


              (SELECT COALESCE((SELECT numero FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT numero FROM cadastro.endereco_externo WHERE idpes = $2))) as numero,

              (SELECT COALESCE((SELECT letra FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT letra FROM cadastro.endereco_externo WHERE idpes = $2))) as letra,

              (SELECT COALESCE((SELECT complemento FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT complemento FROM cadastro.endereco_externo WHERE idpes = $2))) as complemento,

              (SELECT COALESCE((SELECT andar FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT andar FROM cadastro.endereco_externo WHERE idpes = $2))) as andar,

              (SELECT COALESCE((SELECT bloco FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT bloco FROM cadastro.endereco_externo WHERE idpes = $2))) as bloco,

              (SELECT COALESCE((SELECT apartamento FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT apartamento FROM cadastro.endereco_externo WHERE idpes = $2))) as apartamento,

             (SELECT idbai FROM cadastro.endereco_pessoa WHERE idpes = $2) as idbai,

             (SELECT idlog FROM cadastro.endereco_pessoa WHERE idpes = $2) as idlog
            from cadastro.fisica where idpes = $2";

    $details = $this->fetchPreparedQuery($sql, array($alunoId, $pessoaId), false, 'first-row');

    $attrs   = array('cpf', 'rg', 'data_nascimento', 'pai_id', 'mae_id', 'responsavel_id', 'nome_pai', 'nome_mae',
                       'nome_responsavel','sexo','estadocivil', 'cep', 'logradouro', 'idtlog', 'bairro',
                       'zona_localizacao', 'idbai', 'idlog', 'idmun', 'idmun_nascimento', 'complemento',
                       'apartamento', 'andar', 'bloco', 'numero' , 'letra');
    $details = Portabilis_Array_Utils::filter($details, $attrs);

    $details['aluno_id']         = $alunoId;
    $details['nome_mae']         = $this->toUtf8($details['nome_mae'], array('transform' => true));
    $details['nome_pai']         = $this->toUtf8($details['nome_pai'], array('transform' => true));
    $details['nome_responsavel'] = $this->toUtf8($details['nome_responsavel'], array('transform' => true));
    $details['cep']              = int2CEP($details['cep']);

    $details['bairro']           = $this->toUtf8($details['bairro']);
    $details['logradouro']       = $this->toUtf8($details['logradouro']);
    $detaihandleGetPersonls['complemento']      = $this->toUtf8($details['complemento']);
    $details['letra']            = $this->toUtf8($details['letra']);
    $details['bloco']            = $this->toUtf8($details['bloco']);

    if($details['idmun']){

      $_sql = " SELECT nome, sigla_uf FROM public.municipio WHERE idmun = $1; ";

      $mun = $this->fetchPreparedQuery($_sql, $details['idmun'], false, 'first-row');

      $details['municipio'] = $this->toUtf8($mun['nome']);

      $details['sigla_uf'] = $mun['sigla_uf'];

    }

    if ($details['idmun_nascimento']){

      $_sql = " SELECT nome, sigla_uf FROM public.municipio WHERE idmun = $1; ";

      $mun = $this->fetchPreparedQuery($_sql, $details['idmun_nascimento'], false, 'first-row');

      $details['municipio_nascimento'] = $this->toUtf8($mun['nome']);

      $details['sigla_uf_nascimento'] = $mun['sigla_uf'];

    }

    if ($details['pai_id']){

      $_sql = " SELECT ideciv as estadocivil, sexo FROM cadastro.fisica WHERE idpes = $1; ";

      $pai = $this->fetchPreparedQuery($_sql, $details['pai_id'], false, 'first-row');

      $paiDetails['estadocivil'] = $pai['estadocivil'];

      $paiDetails['sexo'] = $pai['sexo'];

      $details['pai_details'] = $paiDetails;

    }

    if ($details['mae_id']){

      $_sql = " SELECT ideciv as estadocivil, sexo FROM cadastro.fisica WHERE idpes = $1; ";

      $mae = $this->fetchPreparedQuery($_sql, $details['mae_id'], false, 'first-row');

      $maeDetails['estadocivil'] = $mae['estadocivil'];

      $maeDetails['sexo'] = $mae['sexo'];

      $details['mae_details'] = $maeDetails;

    }

    $details['data_nascimento']  = Portabilis_Date_Utils::pgSQLToBr($details['data_nascimento']);

    return $details;
  }

  protected function loadPessoaParent(){

      $_sql = " SELECT (select nome from cadastro.pessoa where pessoa.idpes = fisica.idpes) as nome ,ideciv as estadocivil, sexo FROM cadastro.fisica WHERE idpes = $1; ";

      $details = $this->fetchPreparedQuery($_sql, $this->getRequest()->id, false, 'first-row');

      $details['nome'] = Portabilis_String_Utils::toUtf8($details['nome']);

      $details['id'] = $this->getRequest()->id;

      return $details;;
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
               cadastro.fisica where fisica.idpes = pessoa.idpes and (pessoa.idpes::varchar like $1||'%' or
               trim(leading '0' from fisica.cpf::varchar) like trim(leading '0' from $1::varchar)||'%' or
               fisica.cpf::varchar like $1||'%') order by id limit 15";

    // search by rg
    $sqls[] = "select distinct pessoa.idpes as id, pessoa.nome as name from cadastro.pessoa, cadastro.documento
               where pessoa.idpes = documento.idpes and ((documento.rg::varchar like $1||'%') or
               trim(leading '0' from documento.rg::varchar) like trim(leading '0' from $1::varchar)||'%') order by id limit 15";

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

  protected function post(){

    $pessoaId = $this->getRequest()->pessoa_id;

    $pessoaId = $this->createOrUpdatePessoa($pessoaId);
    $this->createOrUpdatePessoaFisica($pessoaId);

    $this->appendResponse('pessoa_id', $pessoaId);
  }

  protected function createOrUpdatePessoa($pessoaId = null) {
    $pessoa        = new clsPessoa_();
    $pessoa->idpes = $pessoaId;
    $pessoa->nome  = Portabilis_String_Utils::toLatin1($this->getRequest()->nome);

    $sql = "select 1 from cadastro.pessoa WHERE idpes = $1 limit 1";

    if (! $pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
      $pessoa->tipo      = 'F';
      $pessoa->idpes_cad = $this->currentUserId();
      $pessoaId          = $pessoa->cadastra();
    }
    else {
      $pessoa->idpes_rev = $this->currentUserId();
      $pessoa->data_rev  = date('Y-m-d H:i:s', time());
      $pessoa->edita();
    }

    return $pessoaId;
  }

  protected function createOrUpdatePessoaFisica($pessoaId) {

    $fisica                     = new clsFisica();
    $fisica->idpes              = $pessoaId;
    $fisica->data_nasc          = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->datanasc);
    $fisica->sexo               = $this->getRequest()->sexo;
    $fisica->ref_cod_sistema    = 'NULL';
    $fisica->ideciv             = $this->getRequest()->estadocivil;
    $fisica->idpes_pai          = "NULL";
    $fisica->idpes_mae          = "NULL";
    $fisica->idmun_nascimento   = $this->getRequest()->naturalidade;

    $sql = "select 1 from cadastro.fisica WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $fisica->cadastra();
    else
      $fisica->edita();

  }

  protected function _createOrUpdatePessoaEndereco($pessoaId) {

    $cep = idFederal2Int($this->getRequest()->cep);

    $objCepLogradouro = new ClsCepLogradouro($cep, $this->getRequest()->logradouro_id);

    if (! $objCepLogradouro->existe())
      $objCepLogradouro->cadastra();

    $objCepLogradouroBairro = new ClsCepLogradouroBairro();
    $objCepLogradouroBairro->cep = $cep;
    $objCepLogradouroBairro->idbai = $this->getRequest()->bairro_id;
    $objCepLogradouroBairro->idlog = $this->getRequest()->logradouro_id;

    if (! $objCepLogradouroBairro->existe())
      $objCepLogradouroBairro->cadastra();

    $endereco = new clsPessoaEndereco(
      $this->getRequest()->pessoa_id,
      $cep,
      $this->getRequest()->logradouro_id,
      $this->getRequest()->bairro_id,
      $this->getRequest()->numero,
      Portabilis_String_Utils::toLatin1($this->getRequest()->complemento),
      FALSE,
      Portabilis_String_Utils::toLatin1($this->getRequest()->letra),
      Portabilis_String_Utils::toLatin1($this->getRequest()->bloco),
      $this->getRequest()->apartamento,
      $this->getRequest()->andar
    );

    // forçado exclusão, assim ao cadastrar endereco_pessoa novamente,
    // será excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function createOrUpdateEndereco() {

    $pessoaId = $this->getRequest()->pessoa_id;

    if ($this->getRequest()->cep && is_numeric($this->getRequest()->bairro_id) && is_numeric($this->getRequest()->logradouro_id))
      $this->_createOrUpdatePessoaEndereco($pessoaId);
    else if($this->getRequest()->cep && is_numeric($this->getRequest()->municipio_id)){

      if (!is_numeric($this->bairro_id)){

        if ($this->canCreateBairro())
          $this->getRequest()->bairro_id = $this->createBairro();
        else
          return;
      }

      if (!is_numeric($this->logradouro_id)){
        if($this->canCreateLogradouro())
          $this->getRequest()->logradouro_id = $this->createLogradouro();
        else
          return;
      }

      $this->_createOrUpdatePessoaEndereco($pessoaId);

    }else{
      $endereco = new clsPessoaEndereco($pessoaId);
      $endereco->exclui();
    }

  }


  protected function canCreateBairro(){
    return !empty($this->getRequest()->bairro) && !empty($this->getRequest()->zona_localizacao);
  }

  protected function canCreateLogradouro(){
    return !empty($this->getRequest()->logradouro) && !empty($this->getRequest()->idtlog);
  }

  protected function createBairro(){

    $objBairro = new clsBairro(null,$this->getRequest()->municipio_id,null,Portabilis_String_Utils::toLatin1($this->getRequest()->bairro), $this->currentUserId());
    $objBairro->zona_localizacao = $this->getRequest()->zona_localizacao;

    return $objBairro->cadastra();
  }

  protected function createLogradouro(){
    $objLogradouro = new clsLogradouro(null,$this->getRequest()->idtlog, Portabilis_String_Utils::toLatin1($this->getRequest()->logradouro), $this->getRequest()->municipio_id,
                                           null, 'S', $this->currentUserId());
    return $objLogradouro->cadastra();
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'pessoa-search'))
      $this->appendResponse($this->search());

    elseif ($this->isRequestFor('get', 'pessoa'))
      $this->appendResponse($this->get());
    elseif ($this->isRequestFor('post', 'pessoa'))
      $this->appendResponse($this->post());
    elseif ($this->isRequestFor('post', 'pessoa-endereco'))
      $this->appendResponse($this->createOrUpdateEndereco());
    elseif ($this->isRequestFor('get', 'pessoa-parent'))
      $this->appendResponse($this->loadPessoaParent());
    else
      $this->notImplementedOperationError();
  }
}
