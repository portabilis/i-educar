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

require_once 'include/pmieducar/clsPmieducarAluno.inc.php';

require_once 'App/Model/MatriculaSituacao.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

require_once 'Transporte/Model/Responsavel.php';

class AlunoController extends ApiCoreController
{
  protected $_processoAp        = 578;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;


  // validators

  protected function validatesPessoaId() {
    $existenceOptions = array('schema_name' => 'cadastro', 'field_name' => 'idpes');

    return  $this->validatesPresenceOf('pessoa_id') &&
            $this->validatesExistenceOf('fisica', $this->getRequest()->pessoa_id, $existenceOptions);
  }


  protected function validatesReligiaoId() {
    $isValid = true;

    // beneficio is optional
    if (is_numeric($this->getRequest()->religiao_id)) {
      $isValid = $this->validatesPresenceOf('religiao_id') &&
                 $this->validatesExistenceOf('religiao', $this->getRequest()->religiao_id);
    }

    return $isValid;
  }


    protected function validatesBeneficioId() {
    $isValid = true;

    // beneficio is optional
    if (is_numeric($this->getRequest()->beneficio_id)) {
      $isValid = $this->validatesPresenceOf('beneficio_id') &&
                 $this->validatesExistenceOf('aluno_beneficio', $this->getRequest()->beneficio_id);
    }

    return $isValid;
  }


  protected function validatesResponsavelId() {
    $isValid = true;

    if ($this->getRequest()->tipo_responsavel == 'outra_pessoa') {
      $existenceOptions = array('schema_name' => 'cadastro', 'field_name' => 'idpes');

      $isValid = $this->validatesPresenceOf('responsavel_id') &&
                 $this->validatesExistenceOf('fisica', $this->getRequest()->responsavel_id, $existenceOptions);
    }

    return $isValid;
  }


  protected function validatesResponsavelTipo() {
    $expectedValues = array('mae', 'pai', 'outra_pessoa');

    return $this->validatesPresenceOf('tipo_responsavel') &&
           $this->validator->validatesValueInSetOf($this->getRequest()->tipo_responsavel,
                                                   $expectedValues, 'tipo_responsavel');
  }


  protected function validatesResponsavel() {
    return $this->validatesResponsavelTipo() &&
           $this->validatesResponsavelId();
  }


  protected function validatesTransporte() {
    $expectedValues = array('nenhum', 'municipal', 'estadual');

    return $this->validatesPresenceOf('tipo_transporte') &&
           $this->validator->validatesValueInSetOf($this->getRequest()->tipo_transporte,
                                                   $expectedValues, 'tipo_transporte');
  }


  protected function validatesUniquenessOfAlunoByPessoaId() {
    $existenceOptions = array('schema_name' => 'pmieducar', 'field_name' => 'ref_idpes', 'add_msg_on_error' => false);

    if( ! $this->validatesUniquenessOf('aluno', $this->getRequest()->pessoa_id, $existenceOptions)) {
      $this->messenger->append("Já existe um aluno cadastrado para a pessoa {$this->getRequest()->pessoa_id}.");
      return false;
    }

    return true;
  }


  protected function validatesUniquenessOfAlunoInepId() {
    if ($this->getRequest()->aluno_inep_id) {
      $where  = array('alunoInep' => '$1');
      $params = array($this->getRequest()->aluno_inep_id);

      if ($this->getRequest()->id) {
        $where['aluno'] = '!= $2';
        $params[]       = $this->getRequest()->id;
      }

      $dataMapper = $this->getDataMapperFor('educacenso', 'aluno');
      $entity     = $dataMapper->findAllUsingPreparedQuery(array('aluno'), $where, $params, array(), false);

      if (count($entity) && $entity[0]->get('aluno')) {
        $this->messenger->append("Já existe o aluno {$entity[0]->get('aluno')} cadastrado com código inep ".
                                 "{$this->getRequest()->aluno_inep_id}.");

        return false;
      }
    }

    return true;
  }


  protected function validatesUniquenessOfAlunoEstadoId() {
    if ($this->getRequest()->aluno_estado_id) {
      $sql    = "select cod_aluno from pmieducar.aluno where aluno_estado_id = $1";
      $params = array($this->getRequest()->aluno_estado_id);

      if($this->getRequest()->id) {
        $sql     .= " and cod_aluno != $2";
        $params[] = $this->getRequest()->id;
      }

      $alunoId = $this->fetchPreparedQuery($sql, $params, true, 'first-field');

      if ($alunoId) {
        $this->messenger->append("Já existe o aluno $alunoId cadastrado com código estado ".
                                 "{$this->getRequest()->aluno_estado_id}.");

        return false;
      }
    }

    return true;
  }

  // validations

  protected function canGetMatriculas() {
    return $this->validatesId('aluno');
  }

  protected function canChange() {
    return $this->validatesPessoaId()     &&
           $this->validatesResponsavel()  &&
           $this->validatesTransporte()   &&
           $this->validatesReligiaoId()   &&
           $this->validatesBeneficioId()  &&
           $this->validatesUniquenessOfAlunoInepId()  &&
           $this->validatesUniquenessOfAlunoEstadoId();
  }

  protected function canPost() {
    return parent::canPost() &&
           $this->validatesUniquenessOfAlunoByPessoaId();
  }


  protected function canGetOcorrenciasDisciplinares() {
    return $this->validatesId('escola') &&
           $this->validatesId('aluno');
  }

  // load resources

  protected function loadNomeAluno($alunoId) {
    $sql  = "select nome from cadastro.pessoa, pmieducar.aluno where idpes = ref_idpes and cod_aluno = $1";
    $nome = $this->fetchPreparedQuery($sql, $alunoId, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }


  protected function loadTransporte($alunoId) {

    $tiposTransporte = array(
      Transporte_Model_Responsavel::NENHUM    => 'nenhum',
      Transporte_Model_Responsavel::MUNICIPAL => 'municipal',
      Transporte_Model_Responsavel::ESTADUAL  =>'estadual'
    );

    $dataMapper = $this->getDataMapperFor('transporte', 'aluno');
    $entity     = $this->tryGetEntityOf($dataMapper, $alunoId);

    // no antigo cadastro de alunos era considerado como não utiliza transporte,
    // quando não existia dados, para o novo cadastro foi adicionado a opcao 0 (nenhum),
    // então por compatibilidade esta API retorna nenhum, quando não foi encontrado dados.
    if (is_null($entity))
      $tipo = $tiposTransporte[Transporte_Model_Responsavel::NENHUM];
    else
      $tipo = $tiposTransporte[$entity->get('responsavel')];

    return $tipo;
  }


  protected function createOrUpdateTransporte($alunoId) {
    $tiposTransporte = array(
      'nenhum'    => Transporte_Model_Responsavel::NENHUM,
      'municipal' => Transporte_Model_Responsavel::MUNICIPAL,
      'estadual'  => Transporte_Model_Responsavel::ESTADUAL
    );

    $data = array(
      'aluno'       => $alunoId,
      'responsavel' => $tiposTransporte[$this->getRequest()->tipo_transporte],
      'user'        => $this->getSession()->id_pessoa,

      // always setting now...
      'created_at'  => 'NOW()',
    );

    $dataMapper = $this->getDataMapperFor('transporte', 'aluno');
    $entity      = $this->getOrCreateEntityOf($dataMapper, $alunoId);
    $entity->setOptions($data);

    return $this->saveEntity($dataMapper, $entity);
  }


  protected function loadAlunoInepId($alunoId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'aluno');
    $entity     = $this->tryGetEntityOf($dataMapper, $alunoId);

    return (is_null($entity) ? null : $entity->get('alunoInep'));
  }


  protected function createUpdateOrDestroyEducacensoAluno($alunoId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'aluno');

    if (empty($this->getRequest()->aluno_inep_id))
      $result = $this->deleteEntityOf($dataMapper, $alunoId);
    else {
      $data = array(
        'aluno'      => $alunoId,
        'alunoInep'  => $this->getRequest()->aluno_inep_id,

        // campos deprecados?
        'fonte'      => 'fonte',
        'nomeInep'   => '-',

        // always setting now...
        'created_at' => 'NOW()',
      );

      $entity = $this->getOrCreateEntityOf($dataMapper, $alunoId);
      $entity->setOptions($data);

      $result = $this->saveEntity($dataMapper, $entity);
    }

    return $result;
  }


  // #TODO mover updateResponsavel e updateDeficiencias para API pessoa ?

  protected function updateResponsavel() {
    $pessoa                   = new clsFisica();
    $pessoa->idpes            = $this->getRequest()->pessoa_id;
    $pessoa->nome_responsavel = '';

    $_pessoa                  = $pessoa->detalhe();

    if ($this->getRequest()->tipo_responsavel == 'outra_pessoa')
      $pessoa->idpes_responsavel = $this->getRequest()->responsavel_id;

    elseif($this->getRequest()->tipo_responsavel == 'pai' && $_pessoa['idpes_pai'])
      $pessoa->idpes_responsavel = $_pessoa['idpes_pai'];

    elseif($this->getRequest()->tipo_responsavel == 'mae' && $_pessoa['idpes_mae'])
      $pessoa->idpes_responsavel = $_pessoa['idpes_mae'];

    else
      $pessoa->idpes_responsavel = 'NULL';

    return $pessoa->edita();
  }


  protected function updateDeficiencias() {
    $sql = "delete from cadastro.fisica_deficiencia where ref_idpes = $1";
    $this->fetchPreparedQuery($sql, $this->getRequest()->pessoa_id, false);

    foreach ($this->getRequest()->deficiencias as $id) {
      if (! empty($id)) {
        $deficiencia = new clsCadastroFisicaDeficiencia($this->getRequest()->pessoa_id, $id);
        $deficiencia->cadastra();
      }
    }
  }


  protected function createOrUpdateAluno($id = null){
    $tiposResponsavel               = array('pai' => 'p', 'mae' => 'm', 'outra_pessoa' => 'r');

    $aluno                          = new clsPmieducarAluno();
    $aluno->cod_aluno               = $id;
    $aluno->aluno_estado_id         = Portabilis_String_Utils::toLatin1($this->getRequest()->aluno_estado_id);

    // após cadastro não muda mais id pessoa
    if (is_null($id))
      $aluno->ref_idpes             = $this->getRequest()->pessoa_id;

    $aluno->ref_cod_aluno_beneficio = $this->getRequest()->beneficio_id;
    $aluno->ref_cod_religiao        = $this->getRequest()->religiao_id;
    $aluno->analfabeto              = $this->getRequest()->alfabetizado ? 0 : 1;
    $aluno->tipo_responsavel        = $tiposResponsavel[$this->getRequest()->tipo_responsavel];
    $aluno->ref_usuario_exc         = $this->getSession()->id_pessoa;

    return (is_null($id) ? $aluno->cadastra() : $aluno->edita());
  }

  protected function loadTurmaByMatriculaId($matriculaId) {
    $sql           = 'select ref_cod_turma as id, turma.nm_turma as nome from pmieducar.matricula_turma,
                      pmieducar.turma where ref_cod_matricula = $1 and matricula_turma.ativo = 1 and
                      turma.cod_turma = ref_cod_turma limit 1';

    $turma         = Portabilis_Utils_Database::selectRow($sql, $matriculaId);
    $turma['nome'] = $this->toUtf8($turma['nome'], array('transform' => true));

    return $turma;
  }

  protected function loadEscolaNome($id) {
    $escola             = new clsPmieducarEscola();
    $escola->cod_escola = $id;
    $escola             = $escola->detalhe();

    return $this->toUtf8($escola['nome'], array('transform' => true));
  }

  protected function loadCursoNome($id) {
    $curso            = new clsPmieducarCurso();
    $curso->cod_curso = $id;
    $curso            = $curso->detalhe();

    return $this->toUtf8($curso['nm_curso'], array('transform' => true));
  }

  protected function loadSerieNome($id) {
    $serie            = new clsPmieducarSerie();
    $serie->cod_serie = $id;
    $serie            = $serie->detalhe();

    return $this->toUtf8($serie['nm_serie'], array('transform' => true));
  }

  protected function loadTransferenciaDataEntrada($matriculaId) {
    $sql = "select to_char(data_cadastro, 'DD/MM/YYYY') from pmieducar.matricula
               where cod_matricula=$1 and ativo = 1";

    return Portabilis_Utils_Database::selectField($sql, $matriculaId);
  }

  protected function loadTransferenciaDataSaida($matriculaId) {
    $sql = "select to_char(data_exclusao, 'DD/MM/YYYY') from pmieducar.matricula
               where cod_matricula=$1 and ativo = 1 and (aprovado=4 or aprovado=6)";

    return Portabilis_Utils_Database::selectField($sql, $matriculaId);
  }

  protected function possuiTransferenciaEmAberto($matriculaId) {
    $sql = "select count(cod_transferencia_solicitacao) from pmieducar.transferencia_solicitacao where
            ativo = 1 and ref_cod_matricula_saida = $1 and ref_cod_matricula_entrada is null and
            data_transferencia is null";

    return (Portabilis_Utils_Database::selectField($sql, $matriculaId) > 0);
  }

  protected function loadTipoOcorrenciaDisciplinar($id) {
    if (! isset($this->_tiposOcorrenciasDisciplinares))
      $this->_tiposOcorrenciasDisciplinares = array();

    if (! isset($this->_tiposOcorrenciasDisciplinares[$id])) {
      $ocorrencia                                  = new clsPmieducarTipoOcorrenciaDisciplinar;
      $ocorrencia->cod_tipo_ocorrencia_disciplinar = $id;
      $ocorrencia                                  = $ocorrencia->detalhe();

      $this->_tiposOcorrenciasDisciplinares[$id]   = $this->toUtf8(
        $ocorrencia['nm_tipo'],
        array('transform' => true)
      );

    }

    return $this->_tiposOcorrenciasDisciplinares[$id];
  }


  protected function loadOcorrenciasDisciplinares() {
    $ocorrenciasAluno              = array();

    $sql = "select cod_matricula as id from pmieducar.matricula, pmieducar.escola where
            cod_escola = ref_ref_cod_escola and ref_cod_aluno = $1 and ref_ref_cod_escola =
            $2 and matricula.ativo = 1 order by ano desc, id";

    $params     = array($this->getRequest()->aluno_id, $this->getRequest()->escola_id);
    $matriculas = $this->fetchPreparedQuery($sql, $params);

    $_ocorrenciasMatricula  = new clsPmieducarMatriculaOcorrenciaDisciplinar();

    foreach($matriculas as $matricula) {
      $ocorrenciasMatricula = $_ocorrenciasMatricula->lista($matricula['id'],
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            1,
                                                            $visivel_pais = 1);

      if (is_array($ocorrenciasMatricula)) {
        $attrsFilter                   = array('ref_cod_tipo_ocorrencia_disciplinar' => 'tipo',
                                               'data_cadastro'                       => 'data_hora',
                                               'observacao'                          => 'descricao');

        $ocorrenciasMatricula = Portabilis_Array_Utils::filterSet($ocorrenciasMatricula, $attrsFilter);

        foreach($ocorrenciasMatricula as $ocorrenciaMatricula) {
          $ocorrenciaMatricula['tipo']      = $this->loadTipoOcorrenciaDisciplinar($ocorrenciaMatricula['tipo']);
          $ocorrenciaMatricula['data_hora'] = Portabilis_Date_Utils::pgSQLToBr($ocorrenciaMatricula['data_hora']);
          $ocorrenciaMatricula['descricao'] = $this->toUtf8($ocorrenciaMatricula['descricao']);
          $ocorrenciasAluno[]               = $ocorrenciaMatricula;
        }
      }
    }

    return array('ocorrencias_disciplinares' => $ocorrenciasAluno);
  }

  // search options

  protected function searchOptions() {
    $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;

    return array('sqlParams'    => array($escolaId),
                 'selectFields' => array('matricula_id'));
  }

  protected function sqlsForNumericSearch() {
    $sqls = array();

    // caso nao receba id da escola, pesquisa por codigo aluno em todas as escolas,
    // alunos com e sem matricula são selecionados.
    if (! $this->getRequest()->escola_id) {
      $sqls[] = "select distinct aluno.cod_aluno as id, pessoa.nome as name from
                 pmieducar.aluno, cadastro.pessoa where pessoa.idpes = aluno.ref_idpes
                 and aluno.ativo = 1 and aluno.cod_aluno like $1||'%' and $2 = $2
                 order by cod_aluno limit 15";
    }

    // seleciona por (codigo matricula ou codigo aluno) e opcionalmente por codigo escola,
    // apenas alunos com matricula são selecionados.
    $sqls[] = "select * from (select distinct ON (aluno.cod_aluno) aluno.cod_aluno as id,
               matricula.cod_matricula as matricula_id, pessoa.nome as name from pmieducar.matricula,
               pmieducar.aluno, cadastro.pessoa where aluno.cod_aluno = matricula.ref_cod_aluno and
               pessoa.idpes = aluno.ref_idpes and aluno.ativo = matricula.ativo and
               matricula.ativo = 1 and
               (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else 1=1 end) and
               (matricula.cod_matricula like $1||'%' or matricula.ref_cod_aluno like $1||'%') and
               matricula.aprovado in (1, 2, 3, 4, 7, 8, 9) limit 15) as alunos order by id";

    return $sqls;
  }


  protected function sqlsForStringSearch() {
    $sqls = array();

    // caso nao receba id da escola, pesquisa por nome aluno em todas as escolas,
    // alunos com e sem matricula são selecionados.
    if (! $this->getRequest()->escola_id) {
     $sqls[] = "select distinct aluno.cod_aluno as id,
                pessoa.nome as name from pmieducar.aluno, cadastro.pessoa where
                pessoa.idpes = aluno.ref_idpes and aluno.ativo = 1 and
                lower(to_ascii(pessoa.nome)) like lower(to_ascii($1))||'%' and $2 = $2
                order by nome limit 15";
    }

    // seleciona por nome aluno e e opcionalmente  por codigo escola,
    // apenas alunos com matricula são selecionados.
    $sqls[] = "select * from(select distinct ON (aluno.cod_aluno) aluno.cod_aluno as id,
            matricula.cod_matricula as matricula_id, pessoa.nome as name from pmieducar.matricula,
            pmieducar.aluno, cadastro.pessoa where aluno.cod_aluno = matricula.ref_cod_aluno and
            pessoa.idpes = aluno.ref_idpes and aluno.ativo = matricula.ativo and
            matricula.ativo = 1 and (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2
            else 1=1 end) and
            lower(to_ascii(pessoa.nome)) like lower(to_ascii($1))||'%' and matricula.aprovado in
            (1, 2, 3, 4, 7, 8, 9) limit 15) as alunos order by name";

    return $sqls;
  }

  // api

  protected function tipoResponsavel($aluno) {
    $tipos = array('p' => 'pai', 'm' => 'mae', 'r' => 'outra_pessoa');
    $tipo  = $tipos[$aluno['tipo_responsavel']];

    // no antigo cadastro de aluno, caso não fosse encontrado um tipo de responsavel
    // verificava se a pessoa possua responsavel, pai ou mãe, considerando como
    // responsavel um destes, na respectiva ordem, sendo assim esta api mantem
    // compatibilidade com o antigo cadastro.
    if (! $tipo) {
      $pessoa        = new clsFisica();
      $pessoa->idpes = $aluno['pessoa_id'];
      $pessoa        = $pessoa->detalhe();

      if ($pessoa['idpes_responsavel'] || $pessoa['nome_responsavel'])
        $tipo = $tipos['r'];
      elseif ($pessoa['idpes_pai'] || $pessoa['nome_pai'])
        $tipo = $tipos['p'];
      elseif ($pessoa['idpes_mae'] || $pessoa['nome_mae'])
        $tipo = $tipos['m'];
    }

    return $tipo;
  }

  protected function get() {
    if ($this->canGet()) {
      $id               = $this->getRequest()->id;

      $aluno            = new clsPmieducarAluno();
      $aluno->cod_aluno = $id;
      $aluno            = $aluno->detalhe();

      $attrs  = array(
        'cod_aluno'               => 'id',
        'ref_cod_aluno_beneficio' => 'beneficio_id',
        'ref_cod_religiao'        => 'religiao_id',
        'ref_idpes'               => 'pessoa_id',
        'tipo_responsavel'        => 'tipo_responsavel',
        'ref_usuario_exc'         => 'destroyed_by',
        'data_exclusao'           => 'destroyed_at',
        'analfabeto',
        'ativo',
        'aluno_estado_id'
      );

      $aluno = Portabilis_Array_Utils::filter($aluno, $attrs);

      $aluno['nome']             = $this->loadNomeAluno($id);
      $aluno['tipo_transporte']  = $this->loadTransporte($id);
      $aluno['tipo_responsavel'] = $this->tipoResponsavel($aluno);
      $aluno['aluno_inep_id']    = $this->loadAlunoInepId($id);
      $aluno['ativo']            = $aluno['ativo'] == 1;
      $aluno['aluno_estado_id']  = Portabilis_String_Utils::toUtf8($aluno['aluno_estado_id']);

      $aluno['alfabetizado']     = $aluno['analfabeto'] == 0;
      unset($aluno['analfabeto']);

      // destroyed_by username
      $dataMapper            = $this->getDataMapperFor('usuario', 'funcionario');
      $entity                = $this->tryGetEntityOf($dataMapper, $aluno['destroyed_by']);
      $aluno['destroyed_by'] = is_null($entity) ? null : $entity->get('matricula');

      $aluno['destroyed_at'] = Portabilis_Date_Utils::pgSQLToBr($aluno['destroyed_at']);

      return $aluno;
    }
  }

  protected function getMatriculas() {
    if ($this->canGetMatriculas()) {
      $matriculas = new clsPmieducarMatricula();
      $matriculas->setOrderby('ano DESC, ref_ref_cod_serie DESC, cod_matricula DESC, aprovado');

      $matriculas = $matriculas->lista(
        null,
        null,
        null,
        null,
        null,
        null,
        $this->getRequest()->aluno_id,
        null,
        null,
        null,
        null,
        null,
        1
      );

      $attrs = array(
        'cod_matricula'       => 'id',
        'ref_cod_instituicao' => 'instituicao_id',
        'ref_ref_cod_escola'  => 'escola_id',
        'ref_cod_curso'       => 'curso_id',
        'ref_ref_cod_serie'   => 'serie_id',
        'ref_cod_aluno'       => 'aluno_id',
        'nome'                => 'aluno_nome',
        'aprovado'            => 'situacao',
        'ano'
      );

      $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

      foreach ($matriculas as $index => $matricula) {
        $turma = $this->loadTurmaByMatriculaId($matricula['id']);

        $matriculas[$index]['aluno_nome']   = $this->toUtf8($matricula['aluno_nome'], array('transform' => true));
        $matriculas[$index]['turma_id']     = $turma['id'];
        $matriculas[$index]['turma_nome']   = $turma['nome'];
        $matriculas[$index]['escola_nome']  = $this->loadEscolaNome($matricula['escola_id']);
        $matriculas[$index]['curso_nome']   = $this->loadCursoNome($matricula['curso_id']);
        $matriculas[$index]['serie_nome']   = $this->loadSerieNome($matricula['serie_id']);

        $matriculas[$index]['data_entrada'] = $this->loadTransferenciaDataEntrada($matricula['id']);
        $matriculas[$index]['data_saida']   = $this->loadTransferenciaDataSaida($matricula['id']);

        $matriculas[$index]['situacao']     = App_Model_MatriculaSituacao::getInstance()->getValue(
          $matricula['situacao']
        );

        $matriculas[$index]['user_can_access']         = Portabilis_Utils_User::canAccessEscola($matricula['escola_id']);
        $matriculas[$index]['transferencia_em_aberto'] = $this->possuiTransferenciaEmAberto($matricula['id']);
      }

      return array('matriculas' => $matriculas);
    }
  }

  protected function getOcorrenciasDisciplinares() {
    if ($this->canGetOcorrenciasDisciplinares())
      return $this->loadOcorrenciasDisciplinares();
  }

  protected function post() {
    if ($this->canPost()) {
      $id = $this->createOrUpdateAluno();

      if (is_numeric($id)) {
        $this->updateResponsavel();
        $this->createOrUpdateTransporte($id);
        $this->createUpdateOrDestroyEducacensoAluno($id);
        $this->updateDeficiencias();

        $this->messenger->append('Cadastrado realizado com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente o aluno não pode ser cadastrado, por favor, verifique.');
    }

    return array('id' => $id);
  }

  protected function put() {
    $id = $this->getRequest()->id;

    if ($this->canPut() && $this->createOrUpdateAluno($id)) {
      $this->updateResponsavel();
      $this->createOrUpdateTransporte($id);
      $this->createUpdateOrDestroyEducacensoAluno($id);
      $this->updateDeficiencias();

      $this->messenger->append('Cadastro alterado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.',
                               'error', false, 'error');
    return array('id' => $id);
  }


  protected function enable() {
    $id = $this->getRequest()->id;

    if ($this->canEnable()) {
      $aluno                  = new clsPmieducarAluno();
      $aluno->cod_aluno       = $id;
      $aluno->ref_usuario_exc = $this->getSession()->id_pessoa;
      $aluno->ativo           = 1;

      if($aluno->edita())
        $this->messenger->append('Cadastro ativado com sucesso', 'success', false, 'error');
      else
        $this->messenger->append('Aparentemente o cadastro não pode ser ativado, por favor, verifique.',
                                 'error', false, 'error');
    }

    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;

    if ($this->canDelete()) {
      $aluno                  = new clsPmieducarAluno();
      $aluno->cod_aluno       = $id;
      $aluno->ref_usuario_exc = $this->getSession()->id_pessoa;

      if($aluno->excluir())
        $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
      else
        $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                                 'error', false, 'error');
    }

    return array('id' => $id);
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'aluno'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('get', 'aluno-search'))
      $this->appendResponse($this->search());

    elseif ($this->isRequestFor('get', 'matriculas'))
      $this->appendResponse($this->getMatriculas());

    elseif ($this->isRequestFor('get', 'ocorrencias_disciplinares'))
      $this->appendResponse($this->getOcorrenciasDisciplinares());

    // create
    elseif ($this->isRequestFor('post', 'aluno'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'aluno'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('enable', 'aluno'))
      $this->appendResponse($this->enable());

    elseif ($this->isRequestFor('delete', 'aluno'))
      $this->appendResponse($this->delete());

    else
      $this->notImplementedOperationError();
  }
}
