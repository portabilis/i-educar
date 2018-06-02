<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesProfessorTurma.inc.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/Database.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor vínculo turma');
    $this->processoAp = 635;
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $id;
  var $ano;
  var $servidor_id;
  var $funcao_exercida;
  var $tipo_vinculo;
  var $permite_lancar_faltas_componente;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $ref_cod_turma;

  function Inicializar()
  {
    $retorno = '';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->servidor_id    = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $this->id = $_GET['id'];


    // URL para redirecionamento
    $backUrl = sprintf(
      'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->servidor_id, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

    if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
      $this->fexcluir = TRUE;
    }

    $retorno = 'Novo';

    if (is_numeric($this->id)) {
      $obj = new clsModulesProfessorTurma($this->id);

      $registro  = $obj->detalhe();

      if ($registro) {
        $this->ref_cod_turma        = $registro['turma_id'];
        $this->funcao_exercida = $registro['funcao_exercida'];
        $this->tipo_vinculo    = $registro['tipo_vinculo'];
        $this->permite_lancar_faltas_componente = $registro['permite_lancar_faltas_componente'];

        $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
        $obj_turma = $obj_turma->detalhe();
        $this->ref_cod_escola = $obj_turma['ref_ref_cod_escola'];

        $this->ref_cod_curso = $obj_turma['ref_cod_curso'];
        $this->ref_cod_serie = $obj_turma['ref_ref_cod_serie'];
        if(!isset($_GET['copia']))
          $retorno     = 'Editar';

        if(isset($_GET['copia'])) $this->ano = date("Y");
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'educar_servidor_vinculo_turma_det.php?id=' . $this->id :
      $backUrl;

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_servidores_index.php"       => "Servidores",
         ""        => "{$nomeMenu} vínculo do servidor à turma"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $retorno;
  }

  function Gerar()
  {

    if($this->id){
      $objProfessorTurma = new clsModulesProfessorTurma($this->id);
      $detProfessorTurma = $objProfessorTurma->detalhe();
      $ano = $detProfessorTurma["ano"];
    }

    if (isset($_GET['copia'])) $ano = NULL;

    $this->campoOculto('id', $this->id);
    $this->campoOculto('servidor_id', $this->servidor_id);
    $this->inputsHelper()->dynamic('ano', array('value' => (is_null($ano) ? date("Y") : $ano)));
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie'));
    $this->inputsHelper()->dynamic(array('turma'), array('required' => !is_null($this->ref_cod_turma)));

    $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
    $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);

    $resources = array( null  => 'Selecione',
                        1     => 'Docente',
                        2     => 'Auxiliar/Assistente educacional',
                        3     => 'Profissional/Monitor de atividade complementar',
                        4     => 'Tradutor Intérprete de LIBRAS',
                        5     => 'Docente titular - Coordenador de tutoria (de módulo ou disciplina) - EAD',
                        6     => 'Docente tutor - Auxiliar (de módulo ou disciplina) - EAD');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Função exercida'), 'resources' => $resources, 'value' => $this->funcao_exercida);
    $this->inputsHelper()->select('funcao_exercida', $options);

        $resources = array( null => 'Nenhum',
                            1    => Portabilis_String_Utils::toLatin1('Concursado/efetivo/estável'),
                            2    => Portabilis_String_Utils::toLatin1('Contrato temporário'),
                            3    => 'Contrato terceirizado',
                            4    => 'Contrato CLT');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Tipo do vínculo'), 'resources' => $resources, 'value' => $this->tipo_vinculo, 'required' => false);
    $this->inputsHelper()->select('tipo_vinculo', $options);
    $this->inputsHelper()->checkbox('permite_lancar_faltas_componente',
                                    array('label' => Portabilis_String_Utils::toLatin1('Professor de área específica?'),
                                          'value' => $this->permite_lancar_faltas_componente,
                                          'help'  =>  Portabilis_String_Utils::toLatin1('Marque esta opção somente se o professor leciona uma disciplina específica na turma selecionada.')));

    $this->inputsHelper()->checkbox('selecionar_todos', array('label' => 'Selecionar/remover todos'));
    $this->inputsHelper()->multipleSearchComponenteCurricular(null, array('label' => 'Componentes lecionados', 'required' => TRUE));

    $scripts = array(
      '/modules/Cadastro/Assets/Javascripts/ServidorVinculoTurma.js'
      );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $backUrl = sprintf(
      'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->servidor_id, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

    if ($this->ref_cod_turma){
      $obj = new clsModulesProfessorTurma(NULL, $this->ano, $this->ref_cod_instituicao, $this->servidor_id, $this->ref_cod_turma, $this->funcao_exercida, $this->tipo_vinculo, $this->permite_lancar_faltas_componente);
      if ($obj->existe2()){
        $this->mensagem .= 'Não é possível cadastrar pois já existe um vínculo com essa turma.<br>';
        return FALSE;
      }else
        $this->gravaComponentes($obj->cadastra());
    }else{

      $obj = new clsPmieducarTurma();
      foreach ($obj->lista(NULL,NULL,NULL,$this->ref_cod_serie,$this->ref_cod_escola,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$this->ano) as $reg) {
        $obj = new clsModulesProfessorTurma(NULL, $this->ano, $this->ref_cod_instituicao, $this->servidor_id, $reg['cod_turma'], $this->funcao_exercida, $this->tipo_vinculo, $this->permite_lancar_faltas_componente);
        $this->gravaComponentes($obj->cadastra());
      }
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
    header('Location: ' . $backUrl);
    die();

  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $backUrl = sprintf(
      'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->servidor_id, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

    $obj = new clsModulesProfessorTurma($this->id, $this->ano, $this->ref_cod_instituicao, $this->servidor_id, $this->ref_cod_turma, $this->funcao_exercida, $this->tipo_vinculo, $this->permite_lancar_faltas_componente);

    if ($obj->existe2()){
      $this->mensagem .= 'Não é possível cadastrar pois já existe um vínculo com essa turma.<br>';
      return FALSE;
    }
    $obj->edita();
    $this->gravaComponentes($this->id);

    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
    header('Location: ' . $backUrl);
    die();

  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $backUrl = sprintf(
      'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->servidor_id, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7, $backUrl);

    $this->excluiComponentes($this->id);
    $obj = new clsModulesProfessorTurma($this->id);
    $obj->excluir();

    $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
    header('Location:' . $backUrl);
    die();

  }

  function gravaComponentes($professor_turma_id){
    $this->excluiComponentes($professor_turma_id);
    foreach ($this->getRequest()->componentecurricular as $componenteCurricularId) {
      if (! empty($componenteCurricularId)) {
        Portabilis_Utils_Database::fetchPreparedQuery('INSERT INTO modules.professor_turma_disciplina VALUES ($1,$2)',array( 'params' =>  array($professor_turma_id, $componenteCurricularId) ));
      }
    }
  }

  function excluiComponentes($professor_turma_id){
    Portabilis_Utils_Database::fetchPreparedQuery('DELETE FROM modules.professor_turma_disciplina WHERE professor_turma_id = $1', array( 'params' => array($professor_turma_id)));
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();

?>
