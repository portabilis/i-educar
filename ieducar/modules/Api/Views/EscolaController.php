<?php
// error_reporting(E_ERROR);
// ini_set("display_errors", 1);
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
require_once 'Portabilis/Array/Utils.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'include/pmieducar/clsPmieducarEscolaUsuario.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';

/**
 * Class EscolaController
 * @deprecated Essa versão da API pública será descontinuada
 */
class EscolaController extends ApiCoreController
{
  protected $_processoAp        = 561;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;


  protected function canChange() {
    return true;
  }

  protected function loadEscolaInepId($escolaId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'escola');
    $entity     = $this->tryGetEntityOf($dataMapper, $escolaId);

    return (is_null($entity) ? null : $entity->get('escolaInep'));
  }


  protected function createUpdateOrDestroyEducacensoEscola($escolaId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'escola');

    if (empty($this->getRequest()->escola_inep_id))
      $result = $this->deleteEntityOf($dataMapper, $escolaId);
    else {
      $data = array(
        'escola'      => $escolaId,
        'escolaInep'  => $this->getRequest()->escola_inep_id,

        // campos deprecados?
        'fonte'      => 'fonte',
        'nomeInep'   => '-',

        // always setting now...
        'created_at' => 'NOW()',
      );

      $entity = $this->getOrCreateEntityOf($dataMapper, $escolaId);
      $entity->setOptions($data);

      $result = $this->saveEntity($dataMapper, $entity);
    }

    return $result;
  }

  protected function get() {
    if ($this->canGet()) {
      $id = $this->getRequest()->id;

      $escola = array();
      $escola['escola_inep_id'] = $this->loadEscolaInepId($id);

      return $escola;
    }
  }

  protected function put() {
    $id = $this->getRequest()->id;

    if ($this->canPut()) {
      $this->createUpdateOrDestroyEducacensoEscola($id);

      $this->messenger->append('Cadastro alterado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.',
                               'error', false, 'error');

    return array('id' => $id);
  }

  protected function canGetEscolas(){
    return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('ano')
             && $this->validatesPresenceOf('curso_id')  && $this->validatesPresenceOf('serie_id')
             && $this->validatesPresenceOf('turma_turno_id');
  }

  protected function canGetEtapasPorEscola(){
    return $this->validatesPresenceOf('instituicao_id');
  }

  protected function formatResourceValue($resource) {
    $nome    = $this->toUtf8($resource['name'], array('transform' => true));

    return $nome;
  }

  protected function canGetServidoresDisciplinasTurmas() {
    return  $this->validatesPresenceOf('ano') &&
            $this->validatesPresenceOf('instituicao_id');
  }

  protected function sqlsForStringSearch() {

  $sqls[] = "SELECT e.cod_escola as id, j.fantasia as name
               FROM pmieducar.escola e, cadastro.juridica j
              WHERE j.idpes = e.ref_idpes
                AND e.ativo = 1
                AND j.fantasia ILIKE '%'||$1||'%'
                LIMIT 8";

  $sqls[] = "SELECT e.cod_escola as id, ec.nm_escola as name
               FROM pmieducar.escola e, pmieducar.escola_complemento ec
              WHERE e.cod_escola = ec.ref_cod_escola
                AND e.ativo = 1
                AND ec.nm_escola ILIKE '%'||$1||'%'
                LIMIT 8";


    return $sqls;
  }

  protected function getEtapasPorEscola(){
    if ($this->canGetEtapasPorEscola()){
      $ano = $this->getRequest()->ano ? $this->getRequest()->ano : 0;

      $sql = 'SELECT ref_cod_escola as escola_id,
                     ano as ano
                FROM pmieducar.escola_ano_letivo
               WHERE (CASE WHEN $1 = 0 THEN true ELSE ano = $1 END)
                 AND andamento = 1
               ORDER BY ref_cod_escola, ano';

      $anosLetivos = $this->fetchPreparedQuery($sql, array($ano));

      $attrs = array('escola_id', 'ano');
      $anosLetivos = Portabilis_Array_Utils::filterSet($anosLetivos, $attrs);

      foreach($anosLetivos as $index => $anoLetivo){
        $anosLetivos[$index] = array_merge($anosLetivos[$index], $this->getEtapasAnoEscola($anoLetivo["ano"], $anoLetivo["escola_id"]));
        $anosLetivos[$index] = array_merge($anosLetivos[$index], $this->getEtapasTurmasAnoEscola($anoLetivo["ano"], $anoLetivo["escola_id"]));
      }

      return array('escolas' => $anosLetivos);
    }
  }

  private function getEtapasAnoEscola($ano, $escola){
    $sql = 'SELECT data_inicio,
                   data_fim
              FROM pmieducar.ano_letivo_modulo
             WHERE ref_ano = $1
               AND ref_ref_cod_escola = $2
          ORDER BY sequencial';
    $etapas = array();
    $etapas = $this->fetchPreparedQuery($sql, array($ano, $escola));
    $attrs = array('data_inicio', 'data_fim');
    $etapas = Portabilis_Array_Utils::filterSet($etapas, $attrs);

    return array("etapas" => $etapas);
  }

  private function getEtapasTurmasAnoEscola($ano, $escola)
  {
    $sql_turmas = 'SELECT DISTINCT tm.ref_cod_turma as turma_id
              FROM pmieducar.turma_modulo tm
              INNER JOIN pmieducar.turma t ON (tm.ref_cod_turma = t.cod_turma)
              INNER JOIN pmieducar.curso c on (c.cod_curso = t.ref_cod_curso)
            WHERE t.ano = $1 and t.ref_ref_cod_escola = $2 and c.padrao_ano_escolar = 0 and t.ativo = 1
          ORDER BY tm.ref_cod_turma';

    $turmas = array();
    $turmas = $this->fetchPreparedQuery($sql_turmas, array($ano, $escola));
    $attrs_turmas = array('turma_id');
    $turmas = Portabilis_Array_Utils::filterSet($turmas, $attrs_turmas);

    foreach ($turmas as $key => $turma) {
      $turmas[$key] = array_merge($turmas[$key], $this->getEtapasTurma($ano, $escola, $turma['turma_id']));
    }

    return array("etapas_de_turmas" => $turmas);
  }

  private function getEtapasTurma($ano, $escola, $turma)
  {
    $sql_etapas = 'SELECT tm.data_inicio,
                   tm.data_fim
              FROM pmieducar.turma_modulo tm
              INNER JOIN pmieducar.turma t ON (tm.ref_cod_turma = t.cod_turma)
            WHERE t.ano = $1 and t.ref_ref_cod_escola = $2 and tm.ref_cod_turma = $3
          ORDER BY tm.ref_cod_turma';

    $etapas = array();
    $etapas = $this->fetchPreparedQuery($sql_etapas, array($ano, $escola, $turma));
    $attrs_etapas= array('data_inicio', 'data_fim');
    $etapas = Portabilis_Array_Utils::filterSet($etapas, $attrs_etapas);

    return array("etapas" => $etapas);
  }

  protected function getEscolas(){
    if ($this->canGetEscolas()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $ano = $this->getRequest()->ano;
      $cursoId = $this->getRequest()->curso_id;
      $serieId = $this->getRequest()->serie_id;
      $turmaTurnoId = $this->getRequest()->turma_turno_id;

      $sql = " SELECT DISTINCT cod_escola
                FROM pmieducar.escola e
                INNER JOIN pmieducar.escola_curso ec ON (e.cod_escola = ec.ref_cod_escola)
                INNER JOIN pmieducar.curso c ON (c.cod_curso = ec.ref_cod_curso)
                INNER JOIN pmieducar.escola_serie es ON (es.ref_cod_escola = e.cod_escola)
                INNER JOIN pmieducar.serie s ON (s.cod_serie = es.ref_cod_serie)
                INNER JOIN pmieducar.turma t ON (s.cod_serie = t.ref_ref_cod_serie AND t.ref_ref_cod_escola = e.cod_escola )
                INNER JOIN pmieducar.escola_ano_letivo eal ON(e.cod_escola = eal.ref_cod_escola)
                WHERE t.ano = $1
                AND t.turma_turno_id = $2
                AND c.cod_curso = $3
                AND e.ref_cod_instituicao = $4
                AND s.cod_serie = $5
                AND ec.ativo = 1
                AND c.ativo = 1
                AND e.ativo = 1
                AND es.ativo = 1
                AND s.ativo = 1
                AND t.ativo = 1
                AND eal.ativo = 1
                AND eal.andamento <> 2
                AND eal.ano = $1";
      $escolaIds = $this->fetchPreparedQuery($sql, array($ano, $turmaTurnoId, $cursoId, $instituicaoId, $serieId));

      foreach($escolaIds as $escolaId){
        // $this->messenger->append("Escola: " . $escolaId[0] . " Maximo de alunos no turno: " . $this->_getMaxAlunoTurno($escolaId[0]) . " Quantidade alunos fila: " . $this->_getQtdAlunosFila($escolaId[0]) . " Quantidade matriculas turno: " . $this->_getQtdMatriculaTurno($escolaId[0]));
        if(!$this->existeVagasDisponiveis($escolaId[0])){
          if (($key = array_search($escolaId, $escolaIds)) !== false) {
          unset($escolaIds[$key]);
      }
        }
      }
      if(empty($escolaIds)){
        $this->messenger->append("Desculpe, mas aparentemente não existem mais vagas disponíveis para a seleção informada. Altere a seleção e tente novamente.");
        return array( 'escolas' => 0);
      }
      else{
      $attrs = array('cod_escola');
      return array( 'escolas' => Portabilis_Array_Utils::filterSet($escolaIds, $attrs));
      }
    }
  }

    function existeVagasDisponiveis($escolaId){

    // Caso a capacidade de alunos naquele turno seja menor ou igual ao ao número de alunos matrículados + alunos na reserva de vaga externa deve bloquear
    if ($this->_getMaxAlunoTurno($escolaId) <= ($this->_getQtdAlunosFila($escolaId) + $this->_getQtdMatriculaTurno($escolaId) )){
      // $this->mensagem .= Portabilis_String_Utils::toLatin1("Não existem vagas disponíveis para essa série/turno!") . '<br/>';
      return false;
    }

    return true;
  }

  function _getQtdAlunosFila($escolaId){

    $sql = 'SELECT count(1) as qtd
              FROM pmieducar.matricula
              WHERE ano = $1
              AND ref_ref_cod_escola = $2
              AND ref_cod_curso = $3
              AND ref_ref_cod_serie = $4
              AND turno_pre_matricula = $5
              AND aprovado = 11 ';

    return (int) Portabilis_Utils_Database::selectField($sql, array($this->getRequest()->ano, $escolaId,
                                                  $this->getRequest()->curso_id, $this->getRequest()->serie_id,
                                                  $this->getRequest()->turma_turno_id));
  }

  function _getQtdMatriculaTurno($escolaId){
    $obj_mt = new clsPmieducarMatriculaTurma();

    return count(array_filter(($obj_mt->lista($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
              $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
              $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
              $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL, $int_ativo = 1,
              $int_ref_cod_serie = $this->getRequest()->serie_id, $int_ref_cod_curso = $this->getRequest()->curso_id,
              $int_ref_cod_escola = $escolaId,
              $int_ref_cod_instituicao = $this->getRequest()->instituicao_id, $int_ref_cod_aluno = NULL, $mes = NULL,
              $aprovado = NULL, $mes_menor_que = NULL, $int_sequencial = NULL,
              $int_ano_matricula = $this->getRequest()->ano, $tem_avaliacao = NULL, $bool_get_nome_aluno = FALSE,
              $bool_aprovados_reprovados = NULL, $int_ultima_matricula = NULL,
              $bool_matricula_ativo = true, $bool_escola_andamento = true,
              $mes_matricula_inicial = FALSE, $get_serie_mult = FALSE,
              $int_ref_cod_serie_mult = NULL, $int_semestre = NULL,
              $pegar_ano_em_andamento = FALSE, $parar=NULL, $diario = FALSE,
              $int_turma_turno_id = $this->getRequest()->turma_turno_id, $int_ano_turma = $this->getRequest()->ano))));
  }
  function _getMaxAlunoTurno($escolaId){
    $obj_t = new clsPmieducarTurma();
    $det_t = $obj_t->detalhe();

    $lista_t = $obj_t->lista($int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null,
    $int_ref_ref_cod_serie = $this->getRequest()->serie_id, $int_ref_ref_cod_escola = $escolaId, $int_ref_cod_infra_predio_comodo = null,
    $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null,
    $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = 1, $int_ref_cod_turma_tipo = null,
    $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null,
    $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = $this->getRequest()->curso_id, $int_ref_cod_instituicao = null,
    $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null,
    $bool_verifica_serie_multiseriada = false, $bool_tem_alunos_aguardando_nota = null, $visivel = null, $turma_turno_id = $this->getRequest()->turma_turno_id, $tipo_boletim = null, $ano = $this->getRequest()->ano, $somenteAnoLetivoEmAndamento = FALSE);

    $max_aluno_turmas = 0;

    foreach ($lista_t as $reg) {
      $max_aluno_turmas += $reg['max_aluno'];
    }

    return $max_aluno_turmas;
  }

protected function getInformacaoEscolas(){


  $sql = " SELECT escola.cod_escola as cod_escola,
          juridica.fantasia as nome,
          coalesce(endereco_pessoa.cep, endereco_externo.cep) as cep,
          coalesce(endereco_pessoa.numero, endereco_externo.numero) as numero,
          coalesce(endereco_pessoa.complemento, endereco_externo.complemento) as complemento,
          coalesce(logradouro.nome,endereco_externo.logradouro) as logradouro,
          coalesce(bairro.nome, endereco_externo.bairro) as bairro,
          coalesce(municipio.nome, endereco_externo.cidade) as municipio,
          coalesce(uf.sigla_uf, endereco_externo.sigla_uf) as uf,
          pais.nome as pais,
          pessoa.email as email,
          fone_pessoa.ddd as ddd,
          fone_pessoa.fone as fone,
          pessoa_responsavel.nome as nome_responsavel,
          educacenso_cod_escola.cod_escola_inep as inep
         from pmieducar.escola
        inner join cadastro.juridica on(escola.ref_idpes = juridica.idpes)
         left join cadastro.pessoa on(juridica.idpes = pessoa.idpes)
         left join cadastro.pessoa pessoa_responsavel on(escola.ref_idpes_gestor = pessoa_responsavel.idpes)
         left join cadastro.fone_pessoa on(fone_pessoa.idpes = pessoa.idpes and fone_pessoa.tipo = 1)
         left join cadastro.endereco_pessoa on(escola.ref_idpes = endereco_pessoa.idpes)
         left join cadastro.endereco_externo on(escola.ref_idpes = endereco_externo.idpes)
         left join public.logradouro on(endereco_pessoa.idlog = logradouro.idlog)
         left join public.municipio on(logradouro.idmun = municipio.idmun)
         left join public.uf on(municipio.sigla_uf = uf.sigla_uf)
         left join public.bairro on(endereco_pessoa.idbai = bairro.idbai and municipio.idmun = bairro.idmun)
         left join public.pais on(uf.idpais = pais.idpais)
         left join modules.educacenso_cod_escola on (educacenso_cod_escola.cod_escola = escola.cod_escola)
        where escola.ativo = 1";

  $escolas = $this->fetchPreparedQuery($sql);

  if(empty($escolas)){
    $this->messenger->append("Desculpe, mas não existem escolas cadastradas");
    return array( 'escolas' => 0);
  }
  else{
    foreach($escolas as &$escola){
      $escola['nome'] = Portabilis_String_Utils::toUtf8($escola['nome']);
      $escola['complemento'] = Portabilis_String_Utils::toUtf8($escola['complemento']);
      $escola['logradouro'] = Portabilis_String_Utils::toUtf8($escola['logradouro']);
      $escola['bairro'] = Portabilis_String_Utils::toUtf8($escola['bairro']);
      $escola['municipio'] = Portabilis_String_Utils::toUtf8($escola['municipio']);
      $escola['nome_responsavel'] = Portabilis_String_Utils::toUtf8($escola['nome_responsavel']);
    }
    $attrs = array('cod_escola', 'nome', 'cep', 'numero', 'complemento', 'logradouro', 'bairro', 'municipio', 'uf', 'pais', 'email', 'ddd', 'fone', 'nome_responsavel', 'inep');
    return array( 'escolas' => Portabilis_Array_Utils::filterSet($escolas, $attrs));
  }
}

protected function getEscolasMultipleSearch(){
  $cursoId = $this->getRequest()->curso_id;

  $sql = "SELECT cod_escola as id,
                 COALESCE(juridica.fantasia, nm_escola) as nome
            from pmieducar.escola
            left join cadastro.pessoa on(escola.ref_idpes = pessoa.idpes)
            left join cadastro.juridica on(juridica.idpes = pessoa.idpes)
            left join pmieducar.escola_complemento ON (escola_complemento.ref_cod_escola = escola.cod_escola)
           inner join pmieducar.escola_curso on(escola.cod_escola = escola_curso.ref_cod_escola)
           inner join pmieducar.curso on(escola_curso.ref_cod_curso = curso.cod_curso)
          where escola.ativo = 1
            and curso.ativo = 1
            and escola_curso.ativo = 1";

  if (is_numeric($cursoId)) {
    $sql .= " and curso.cod_curso = $1";
    $sql = $this->fetchPreparedQuery($sql, array($cursoId));
  } else {
    $sql = $this->fetchPreparedQuery($sql);
  }
  $escolas = Portabilis_Array_Utils::setAsIdValue($sql, 'id', 'nome');

  return array('options' => $escolas);
}

protected function getEscolaAnoLetivo(){
  $escolaId    = $this->getRequest()->escola_id;

  if(is_numeric($escolaId)){
    $sql = "SELECT ano FROM pmieducar.escola_ano_letivo WHERE ref_cod_escola = $1 AND andamento = 1 AND ativo = 1;";
    $ano = $this->fetchPreparedQuery($sql, array($escolaId));
  }
  return $ano[0];
}

protected function getDependenciaAdministrativaEscola(){
  $escolaId    = $this->getRequest()->escola_id;

  if(is_numeric($escolaId)){
    $sql = "SELECT dependencia_administrativa
              FROM pmieducar.escola
             WHERE cod_escola = $1;";
    $dependenciaAdministrativa = $this->fetchPreparedQuery($sql, array($escolaId), false, 'first-line');
  }
  return $dependenciaAdministrativa;
}

  protected function getEscolasUsuarios() {
    $ref_cod_usuario = $this->getRequest()->id;


    if (!$ref_cod_usuario) return null;

    $escolasUsuario = new clsPmieducarEscolaUsuario();
    $escolasUsuario = $escolasUsuario->lista($ref_cod_usuario);

    $escolas = array();

    foreach ($escolasUsuario as $escola) {
      $escolas[] = $escola['ref_cod_escola'];
    }

    return array('escolas' => $escolas);
  }

  protected function getEscolasSelecao() {
    $userId = $this->getSession()->id_pessoa;
    $permissao = new clsPermissoes();
    $nivel = $permissao->nivel_acesso($userId);

    if ($nivel == App_Model_NivelTipoUsuario::ESCOLA ||
        $nivel == App_Model_NivelTipoUsuario::BIBLIOTECA) {

      $escolas_usuario = array();
      $escolasUser = App_Model_IedFinder::getEscolasUser($userId);
      foreach ($escolasUser as $e)
      {
        $escolas_usuario['__'.$e["ref_cod_escola"]] = strtoupper($e["nome"]);
      }
      return array('options' => $escolas_usuario);
    }

    $instituicao = $this->getRequest()->instituicao;
    $escolasInstituicao = App_Model_IedFinder::getEscolas($instituicao);

    foreach ($escolasInstituicao as $id => $nome) {
      $escolas['__'.$id] = strtoupper($this->toUtf8($nome));
    }

    return array('options' => $escolas);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'escola'))
      $this->appendResponse($this->get());

    else if ($this->isRequestFor('get', 'escola-search'))
      $this->appendResponse($this->search());

    elseif ($this->isRequestFor('put', 'escola'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('get', 'escolas'))
      $this->appendResponse($this->getEscolas());

    elseif ($this->isRequestFor('get', 'etapas-por-escola'))
      $this->appendResponse($this->getEtapasPorEscola());

    elseif ($this->isRequestFor('get', 'info-escolas'))
      $this->appendResponse($this->getInformacaoEscolas());

    elseif ($this->isRequestFor('get', 'escolas-multiple-search'))
      $this->appendResponse($this->getEscolasMultipleSearch());

    elseif ($this->isRequestFor('get', 'escola-ano-letivo'))
      $this->appendResponse($this->getEscolaAnoLetivo());

    elseif ($this->isRequestFor('get', 'escola-dependencia-administrativa'))
      $this->appendResponse($this->getDependenciaAdministrativaEscola());

    elseif ($this->isRequestFor('get', 'escolas-usuario'))
      $this->appendResponse($this->getEscolasUsuarios());

    elseif ($this->isRequestFor('get', 'escolas-para-selecao'))
      $this->appendResponse($this->getEscolasSelecao());

    else
      $this->notImplementedOperationError();
  }
}
