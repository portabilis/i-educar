<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006 Prefeitura Municipal de Itajaí
 * <ctima@itajai.sc.gov.br>
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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matrícula');
    $this->processoAp = 578;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_matricula_dependencia;
  var $ano;
  var $ref_cod_aluno;
  var $ref_cod_matricula;
  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $componente_curricular_id;
  var $aprovado;
  var $etapa;
  var $etapas;


  function Inicializar()
  {
    //$retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
    $this->cod_matricula_dependencia = $_GET['cod_matricula_dependencia'];

    $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);

    if (! $obj_matricula->existe()) {
      header('Location: educar_aluno_lst.php');
      die;
    }
    $obj_permissoes = new clsPermissoes();

    if(is_numeric($this->cod_matricula_dependencia)){
      $obj_matricula = new clsPmieducarMatriculaDependencia($this->cod_matricula_dependencia);
      $det_matricula = $obj_matricula->detalhe();
      $retorno = 'Editar';

      $this->ano = $det_matricula['ano'];
      $this->ref_cod_aluno = $det_matricula['ref_cod_aluno'];
      $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
      $this->ref_cod_escola = $det_matricula['ref_cod_escola'];
      $this->ref_cod_curso = $det_matricula['ref_cod_curso'];
      $this->ref_cod_serie = $det_matricula['ref_cod_serie'];
      $this->componente_curricular_id = $det_matricula['componente_curricular_id'];

      $this->fexcluir = $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7);

      $cursoId = $this->ref_cod_curso;

      $sql             = "select padrao_ano_escolar from pmieducar.curso where cod_curso = $1 and ativo = 1";
      $padraoAnoLetivo = Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => $cursoId,
                                                                                   'return_only' => 'first-field'));

      if ($padraoAnoLetivo == 1) {
        $escolaId = $this->ref_ref_cod_escola;
        $ano      = $this->ano;

        $sql = "select padrao.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.ano_letivo_modulo
                as padrao, pmieducar.modulo where padrao.ref_ano = $1 and padrao.ref_ref_cod_escola = $2
                and padrao.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by padrao.sequencial";

        $this->etapas = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($ano, $escolaId)));
      }

      else {
        $sql = "select turma.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.turma_modulo as turma,
                pmieducar.modulo where turma.ref_cod_turma = $1 and turma.ref_cod_modulo = modulo.cod_modulo
                and modulo.ativo = 1 order by turma.sequencial";

        $this->etapas = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => $this->cod_turma));
      }
    }else{
      $retorno = 'Novo';
      $det_matricula = $obj_matricula->detalhe();

      $this->ano = $det_matricula['ano'];
      $this->ref_cod_aluno = $det_matricula['ref_cod_aluno'];
      $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
      $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
      $this->ref_cod_curso = $det_matricula['ref_cod_curso'];
      $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
    }

    $url = 'educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;


    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, $url);

    $this->url_cancelar = $url;

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "{$nomeMenu} matr&iacute;cula de depend&ecirc;ncia"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = 'Cancelar';

    $escolaId = $this->ref_cod_escola;
    $cursoId = $this->ref_cod_curso;

    $sql = "select padrao.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.ano_letivo_modulo
            as padrao, pmieducar.modulo where padrao.ref_ano = $1 and padrao.ref_ref_cod_escola = $2
            and padrao.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by padrao.sequencial";

    $this->etapas = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($ano, $escolaId)));

    return $retorno;
  }

  function Gerar()
  {
    $editar = is_numeric($this->cod_matricula_dependencia);
    // primary keys
    $this->campoOculto("ref_cod_matricula", $this->ref_cod_matricula);
    $this->campoOculto("year", $this->ano);
    $this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);
    $this->campoOculto("cod_matricula_dependencia", $this->cod_matricula_dependencia);
    $this->campoOculto("h_ref_cod_instituicao", $this->ref_cod_instituicao);
    $this->campoOculto("h_ref_cod_escola", $this->ref_cod_escola);

    $this->inputsHelper()->dynamic(array('instituicao'), array('disabled' => true));
    $this->inputsHelper()->dynamic(array('escola'), array('disabled' => true));
    $this->inputsHelper()->dynamic(array('curso', 'serie'), array('disabled' => $editar));


    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    $componentes = $componenteMapper->findAll();
    $resourcesComponente = array(null => 'Selecione');

    foreach ($componentes as $key => $value) {
      $resourcesComponente[$value->id] = $value->nome;
    }

    $this->inputsHelper()->select('componente_curricular_id', array('label' => 'Disciplina',
          'resources' => $resourcesComponente, 'disabled' => $editar, 'value' => $this->componente_curricular_id));

    if ($this->ref_cod_aluno){
      $obj_aluno = new clsPmieducarAluno();
      $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno);

      if (is_array($lst_aluno)) {
        $det_aluno      = array_shift($lst_aluno);
        $this->nm_aluno = $det_aluno['nome_aluno'];
        $this->campoRotulo('nm_aluno', 'Aluno', $this->nm_aluno);
      }
    }

    if($editar){
      foreach ($this->etapas as $key => $etapa) {
        $cont++;

        $sql = "SELECT nota, falta, parecer
                  FROM pmieducar.matricula_dependencia_etapa
                  WHERE ref_cod_matricula_dependencia = $1 AND etapa = $2 ";

        $valores = Portabilis_Utils_Database::fetchPreparedQuery($sql,
                array( 'params' => array($this->cod_matricula_dependencia, $cont), 'return_only' => 'first-row'));

        $nota    = (double) $valores['nota'];
        $falta   = $valores['falta'];
        $parecer = $valores['parecer'];

        $this->inputsHelper()->text("nota[{$cont}]", array('label' => "Nota {$cont}&ordm; ".strtolower($etapa['nome']),
                                                            'max_length' => '5',
                                                            'size' => '5',
                                                            'required' => false,
                                                            'value' => $nota
                                                           ));

        $this->inputsHelper()->numeric("falta[{$cont}]", array('label' => "Falta {$cont}&ordm; ".strtolower($etapa['nome']),
                                                            'max_length' => '5',
                                                            'size' => '5',
                                                            'required' => false,
                                                            'value' => $falta
                                                           ));


        $this->campoMemo( "parecer[{$cont}]", "Parecer {$cont}&ordm; ".strtolower($etapa['nome']), $parecer, 60, 5, false );
      }
    }
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ano = $this->year;
    $this->ref_cod_instituicao = $this->h_ref_cod_instituicao;
    $this->ref_cod_escola = $this->h_ref_cod_escola;

    $url = 'educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, $url);

    $obj = new clsPmieducarMatriculaDependencia(NULL, $this->ano, $this->ref_cod_aluno, $this->ref_cod_matricula,
        $this->ref_cod_instituicao, $this->ref_cod_escola, $this->ref_cod_curso, $this->ref_cod_serie,
        $this->componente_curricular_id, 3);

    $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
      header('Location: educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br />';
    return FALSE;
  }

  function Editar(){
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $url = 'educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, $url);

    $destroyAllSql = "DELETE FROM pmieducar.matricula_dependencia_etapa WHERE ref_cod_matricula_dependencia = $1";
    Portabilis_Utils_Database::fetchPreparedQuery($destroyAllSql, array( 'params' => array($this->cod_matricula_dependencia)));

    $insertSql = "INSERT INTO pmieducar.matricula_dependencia_etapa (ref_cod_matricula_dependencia, etapa, nota, falta, parecer)
                  VALUES ($1,$2,$3,$4,$5)";

    foreach ($this->nota as $key => $value) {
      $nota = str_replace(',', '.', $this->nota[$key]);
      $falta = $this->falta[$key];
      $parecer = $this->parecer[$key];

      if(is_numeric($nota) || is_numeric($falta) || !empty($parecer)){
        $falta = (int) $falta;
        Portabilis_Utils_Database::fetchPreparedQuery($insertSql,
              array( 'params' => array($this->cod_matricula_dependencia, $key, $nota, $falta, $parecer)));
      }
    }

    header('Location: educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
    die;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7,
      'educar_matricula_dependencia_det.php?cod_matricula_dependencia=' . $this->cod_matricula_dependencia);

    $obj = new clsPmieducarMatriculaDependencia( $this->cod_matricula_dependencia );

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
      header('Location: educar_matricula_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
      die();
    }

    $this->mensagem = 'Exclusão não realizada.<br />';
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
