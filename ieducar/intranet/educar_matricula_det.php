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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/Utils/CustomLabel.php';
require_once 'Portabilis/String/Utils.php';
require_once 'lib/App/Model/Educacenso.php';

require_once 'App/Model/MatriculaSituacao.php';
require_once 'Portabilis/View/Helper/Application.php';
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
class indice extends clsDetalhe
{
  var $titulo;

  var $ref_cod_matricula;
  var $ref_cod_reserva_vaga;
  var $ref_ref_cod_escola;
  var $ref_ref_cod_serie;
  var $ref_cod_abandono_tipo;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_aluno;
  var $aprovado;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Gerar()
  {

    // carrega estilo para feedback messages, exibindo msgs da api.

    $style = "/modules/Portabilis/Assets/Stylesheets/Frontend.css";
    Portabilis_View_Helper_Application::loadStylesheet($this, $style);

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = "Matrícula - Detalhe";
    $this->addBanner("imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet");

    $this->ref_cod_matricula = $_GET["cod_matricula"];

    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);

    if ($lst_matricula) {
      $registro = array_shift($lst_matricula);
    }

    if (! $registro) {
      header("Location: educar_aluno_det.php?cod_aluno=" . $registro['ref_cod_aluno']);
      die();
    }

    $verificaMatriculaUltimoAno = $obj_matricula->verificaMatriculaUltimoAno($registro['ref_cod_aluno'], $registro['cod_matricula']);

    $existeSaidaEscola = $obj_matricula->existeSaidaEscola($registro['cod_matricula']);

    // Curso
    $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $curso_id = $registro['ref_cod_curso'];
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    // Série
    $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_serie = $obj_serie->detalhe();
    $serie_id = $registro['ref_ref_cod_serie'];
    $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

    // Nome da instituição
    $obj_cod_instituicao = new clsPmieducarInstituicao( $registro['ref_cod_instituicao'] );
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

    // Escola
    $obj_ref_cod_escola = new clsPmieducarEscola( $registro['ref_ref_cod_escola'] );
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $escola_id = $registro['ref_ref_cod_escola'];
    $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

    // Nome do aluno
    $obj_aluno = new clsPmieducarAluno();
    $lst_aluno = $obj_aluno->lista($registro['ref_cod_aluno'], NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_aluno)) {
      $det_aluno = array_shift($lst_aluno);
      $nm_aluno = $det_aluno['nome_aluno'];
    }

    if ($registro['cod_matricula']) {
      $this->addDetalhe(array('Número Matrícula', $registro['cod_matricula']));
    }

    if ($nm_aluno) {
      $this->addDetalhe(array('Aluno', $nm_aluno));
    }

    if ($registro['ref_cod_instituicao']) {
      $this->addDetalhe(array('Instituição', $registro['ref_cod_instituicao']));
    }

    if ($registro['ref_ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_ref_cod_escola']));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('Série', $registro['ref_ref_cod_serie']));
    }

    // Nome da turma
    $enturmacoes = new clsPmieducarMatriculaTurma();
    $enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1);

    $existeTurma = false;
    $existeTurmaMulti = false;
    $existeTurmaUnificada = false;
    $nomesTurmas = array();
    $datasEnturmacoes = array();
    foreach ($enturmacoes as $enturmacao) {
      $turma         = new clsPmieducarTurma($enturmacao['ref_cod_turma']);
      $turma         = $turma->detalhe();
      $turma_id = $enturmacao['ref_cod_turma'];
      $nomesTurmas[] = $turma['nm_turma'];
      $datasEnturmacoes[] = Portabilis_Date_Utils::pgSQLToBr($enturmacao['data_enturmacao']);
      if (in_array($turma['etapa_educacenso'], App_Model_Educacenso::etapas_multisseriadas())) {
        $existeTurmaMulti = true;
      }
      if (in_array($turma['etapa_educacenso'], App_Model_Educacenso::etapasEnsinoUnificadas())) {
        $existeTurmaUnificada = true;
      }
    }
    $nomesTurmas = implode('<br />', $nomesTurmas);
    $datasEnturmacoes = implode('<br />', $datasEnturmacoes);

    if ($nomesTurmas){
      $this->addDetalhe(array('Turma', $nomesTurmas));
      $this->addDetalhe(array('Data Enturmação', $datasEnturmacoes));
      $existeTurma = true;
    }else {
      $this->addDetalhe(array('Turma', ''));
      $this->addDetalhe(array('Data Enturmação', ''));
    }

    switch ($registro['turno_id']) {
      case 1:
        $nm_turno = 'Matutino';
        break;
      case 2:
        $nm_turno = 'Vespertino';
        break;
      case 3:
        $nm_turno = 'Integral';
        break;
    }

    if ($registro['turno_id']) {
      $this->addDetalhe(array('Turno da matrícula', $nm_turno));
    }

    if ($registro['ref_cod_reserva_vaga']) {
      $this->addDetalhe(array('Número Reserva Vaga', $registro['ref_cod_reserva_vaga']));
    }

    $campoObs = false;

    $situacao = App_Model_MatriculaSituacao::getSituacao($registro['aprovado']);
    $this->addDetalhe(array('Situação', $situacao));

    if($registro[aprovado] == 4){
      $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

      $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL, NULL, $registro['cod_matricula'], NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, $registro['ref_cod_aluno'], FALSE);

      if (is_array($lst_transferencia)) {
        $det_transferencia = array_shift($lst_transferencia);
      }
      if(!$det_transferencia["ref_cod_escola_destino"] == "0") {
        $tmp_obj = new clsPmieducarEscola($det_transferencia["ref_cod_escola_destino"]);
        $tmp_det = $tmp_obj->detalhe();
        $this->addDetalhe(array("Escola destino", $tmp_det["nome"]));
      }else{
        $this->addDetalhe(array("Escola destino", $det_transferencia["escola_destino_externa"]));
        $this->addDetalhe(array("Estado escola destino", $det_transferencia["estado_escola_destino_externa"]));
        $this->addDetalhe(array("Município escola destino", $det_transferencia["municipio_escola_destino_externa"]));
      }
    }

    if ($registro['aprovado'] == App_Model_MatriculaSituacao::FALECIDO) {
      $this->addDetalhe(array('Observação',Portabilis_String_Utils::toLatin1($registro['observacao'])));
    }

    if ($existeSaidaEscola) {
      $this->addDetalhe(array('Saída da escola','Sim'));
      $this->addDetalhe(array('Data de saída da escola',Portabilis_Date_Utils::pgSQLToBr($registro['data_saida_escola'])));
      $this->addDetalhe(array('Observação',Portabilis_String_Utils::toLatin1($registro['observacao'])));
    }

    if ($campoObs){

      $tipoAbandono = new clsPmieducarAbandonoTipo($registro['ref_cod_abandono_tipo']);
      $tipoAbandono = $tipoAbandono->detalhe();

      $observacaoAbandono = Portabilis_String_Utils::toLatin1($registro['observacao']);

      $this->addDetalhe(array('Motivo do Abandono',$tipoAbandono['nome']));
      $this->addDetalhe(array('Observação',$observacaoAbandono));
    }

    $this->addDetalhe(array('Formando', $registro['formando'] == 0 ? 'N&atilde;o' : 'Sim'));

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      // verifica se existe transferencia
      if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
        $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

        $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
          NULL, $registro['cod_matricula'], NULL, NULL, NULL, NULL, NULL, 1,
          NULL, NULL, $registro['ref_cod_aluno'], FALSE);

        // verifica se existe uma solicitacao de transferencia INTERNA
        if (is_array($lst_transferencia)) {
          $det_transferencia = array_shift($lst_transferencia);
        }

        $data_transferencia = $det_transferencia['data_transferencia'];
      }

      if ($registro['aprovado'] == 3 &&
         (!is_array($lst_transferencia) && !isset($data_transferencia))
      ) {

        // Verificar se tem permissao para executar cancelamento de matricula
        if($this->permissao_cancelar()){

          $this->array_botao[]            = 'Cancelar matrícula';
          $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente cancelar esta matrícula?\"))go(\"educar_matricula_cad.php?cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
        }

        $this->array_botao[]            = 'Ocorrências disciplinares';
        $this->array_botao_url_script[] = "go(\"educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";

        // Apenas libera a dispensa de disciplina quando o aluno estiver enturmado
        //
        if ($registro['ref_ref_cod_serie'] && $existeTurma) {
          $this->array_botao[]            = 'Dispensa de componentes curriculares';
          $this->array_botao_url_script[] = "go(\"educar_dispensa_disciplina_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
        }

        $dependencia = $registro['dependencia'] == 't';

        if ($registro['ref_ref_cod_serie'] && $existeTurma && $dependencia) {
          $this->array_botao[]            = 'Disciplinas de depend&ecirc;ncia';
          $this->array_botao_url_script[] = "go(\"educar_disciplina_dependencia_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
        }

        $this->array_botao[]            = _cl('matricula.detalhe.enturmar');
        $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$registro['cod_matricula']}&ano_letivo={$registro['ano']}\")";

        $this->array_botao[]            = 'Turno';
        $this->array_botao_url_script[] = "go(\"educar_matricula_turno_cad.php?cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

        $this->array_botao[]            = 'Abandono';
        $this->array_botao_url_script[] = "go(\"educar_abandono_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

        $this->array_botao[]            = 'Falecido';
        $this->array_botao_url_script[] = "go(\"educar_falecido_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

        if ($registro['ref_ref_cod_serie']) {
          $this->array_botao[]            = 'Reclassificar';
          $this->array_botao_url_script[] = "go(\"educar_matricula_reclassificar_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
        }
      }

      if ($existeTurmaMulti) {
        $this->array_botao[]            = 'Etapa do aluno';
        $this->array_botao_url_script[] = "go(\"educar_matricula_etapa_turma_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
      }

      if ($existeTurmaUnificada) {
        $this->array_botao[] = 'Etapa da turma unificada';
        $this->array_botao_url_script[] = "go(\"educar_matricula_turma_unificada_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
      }
      if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
        if (is_array($lst_transferencia) && isset($data_transferencia)) {
          $this->array_botao[]            = 'Cancelar solicitação transferência';
          $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
        }
        elseif ($registro['ref_ref_cod_serie']) {
            $this->array_botao[]            = _cl('matricula.detalhe.solicitar_transferencia');
            $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
        }

        if ($registro['aprovado'] == 3 &&
           (!is_array($lst_transferencia) && !isset($data_transferencia))
        ) {
          if ($registro['formando'] == 0) {
            $this->array_botao[]            = 'Formando';
            $this->array_botao_url_script[] = "if(confirm(\"Deseja marcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=1\")";
          }
          else {
            $this->array_botao[]            = "Desmarcar como formando";
            $this->array_botao_url_script[] = "if(confirm(\"Deseja desmarcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=0\")";
          }
        }
      }

      $ultimaMatricula = $obj_matricula->getEndMatricula($registro['ref_cod_aluno']);
      if($registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO && $this->canCancelTransferencia($registro['cod_matricula'])) {
        $this->array_botao[] = 'Cancelar transferência';

        # TODO ver se código, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
        $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
      }
      elseif($registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO && $ultimaMatricula == 4) {
        $this->array_botao[] = 'Cancelar transferência';

        # TODO ver se código, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
        $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
      }

      if ($registro['aprovado'] == App_Model_MatriculaSituacao::ABANDONO) {
        $this->array_botao[]            = "Desfazer abandono";
        $this->array_botao_url_script[] = "deleteAbandono({$registro['cod_matricula']})";
      }

      if (!$existeSaidaEscola &&
          $verificaMatriculaUltimoAno &&
          ($registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO ||
           $registro['aprovado'] == App_Model_MatriculaSituacao::REPROVADO ||
           $registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA ||
           $registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO ||
           $registro['aprovado'] == App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS)) {
        $this->array_botao[]            = "Saída da escola";
        $this->array_botao_url_script[] = "go(\"educar_saida_escola_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&escola={$registro['ref_ref_cod_escola']}\");";
      }

      if ($existeSaidaEscola && $verificaMatriculaUltimoAno) {
        $this->array_botao[]            = "Cancelar saída da escola";
        $this->array_botao_url_script[] = "desfazerSaidaEscola({$registro['cod_matricula']})";
      }

      if ($registro['aprovado'] == App_Model_MatriculaSituacao::RECLASSIFICADO){
        $this->array_botao[]            = 'Desfazer reclassificação';
        $this->array_botao_url_script[] = "deleteReclassificacao({$registro['cod_matricula']})";
      }
    }

    $obj_permissoes = new clsPermissoes();
    $nivelUsuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
    $administrador = 1;

    if ($nivelUsuario == $administrador) {
      $this->array_botao[]            = 'Histórico de enturmações';
      $this->array_botao_url_script[] = "go(\"educar_matricula_historico_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
    }

    $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $registro['ref_cod_aluno'];
    $this->largura      = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Matrícula"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    // js
    $scripts = array(
      '/modules/Portabilis/Assets/Javascripts/Utils.js',
      '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
      '/modules/Cadastro/Assets/Javascripts/MatriculaShow.js'
    );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
  }

  // Verificar se pode cancelar matricula
  function permissao_cancelar(){
    @session_start();

    $this->pessoa_logada = $_SESSION['id_pessoa'];
    $acesso = new clsPermissoes();

    session_write_close();

    /**
     * @param Processo
     * @param Usuário logado
     * @param Nível de acesso
     * @param Redirecionar página
     * @param Super Usuário
     * @param Verifica usuário biblioteca
     */
    return $acesso->permissao_excluir(627, $this->pessoa_logada, 7, null, true);
  }

  function canCancelTransferencia($matriculaId) {
    $sql = "SELECT transferencia_solicitacao.cod_transferencia_solicitacao
              FROM pmieducar.transferencia_solicitacao
             WHERE ref_cod_matricula_saida = $matriculaId
               AND ativo = 1";

    $db = new clsBanco();
    return $db->CampoUnico($sql);

  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
