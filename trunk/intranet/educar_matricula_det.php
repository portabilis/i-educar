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
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_aluno;
  var $aprovado;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Gerar()
  {
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
      header("Location: educar_matricula_lst.php?ref_cod_aluno=" . $registro['ref_cod_aluno']);
      die();
    }

    // Curso
    $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    // Série
    $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_serie = $obj_serie->detalhe();
    $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

    // Nome da instituição
    $obj_cod_instituicao = new clsPmieducarInstituicao( $registro['ref_cod_instituicao'] );
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

    // Nome da escola
    $obj_ref_cod_escola = new clsPmieducarEscola( $registro['ref_ref_cod_escola'] );
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

    // Nome do aluno
    $obj_aluno = new clsPmieducarAluno();
    $lst_aluno = $obj_aluno->lista($registro['ref_cod_aluno'], NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_aluno)) {
      $det_aluno = array_shift($lst_aluno);
      $nm_aluno = $det_aluno['nome_aluno'];
    }

    // Nome da turma
    $obj_mat_turma = new clsPmieducarMatriculaTurma();
    $det_mat_turma = $obj_mat_turma->lista($this->ref_cod_matricula, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1);

    if ($det_mat_turma){
      $det_mat_turma = array_shift($det_mat_turma);
      $obj_turma     = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
      $det_turma     = $obj_turma->detalhe();
      $nm_turma      = $det_turma['nm_turma'];
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

    if ($nm_turma) {
      $this->addDetalhe(array('Turma', $nm_turma));
    }

    if ($registro['ref_cod_reserva_vaga']) {
      $this->addDetalhe(array('Número Reserva Vaga', $registro['ref_cod_reserva_vaga']));
    }

    if ($registro['aprovado']) {
      if ($registro['aprovado'] == 1) {
        $aprovado = 'Aprovado';
      }
      elseif ($registro['aprovado'] == 2) {
        $aprovado = 'Reprovado';
      }
      elseif ($registro['aprovado'] == 3) {
        $aprovado = 'Em Andamento';
      }
      elseif ($registro['aprovado'] == 4) {
        $aprovado = 'Transferido';
      }
      elseif ($registro['aprovado'] == 5) {
        $aprovado = 'Reclassificado';
      }
      elseif ($registro['aprovado'] == 6) {
        $aprovado = 'Abandono';
      }
      elseif ($registro['aprovado'] == 7) {
        $aprovado = 'Em Exame';
      }

      $this->addDetalhe(array('Situação', $aprovado));
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
        $this->array_botao[]            = 'Cancelar Matrícula';
        $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente cancelar esta matrícula?\"))go(\"educar_matricula_cad.php?cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";

        $this->array_botao[]            = 'Ocorrências Disciplinares';
        $this->array_botao_url_script[] = "go(\"educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";

        // Apenas libera a dispensa de disciplina quando o aluno estiver enturmado
        if ($registro['ref_ref_cod_serie'] && isset($nm_turma)) {
          $this->array_botao[]            = 'Dispensa de Componentes Curriculares';
          $this->array_botao_url_script[] = "go(\"educar_dispensa_disciplina_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
        }

        $this->array_botao[]            = 'Enturmar';
        $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";

        $this->array_botao[]            = 'Abandono';
        $this->array_botao_url_script[] = "if(confirm(\"Deseja confirmar o abandono desta matrícula?\"))go(\"educar_matricula_abandono_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

        if ($registro['ref_ref_cod_serie']) {
          $this->array_botao[]            = 'Reclassificar';
          $this->array_botao_url_script[] = "go(\"educar_matricula_reclassificar_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
        }
      }

      if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
        if (is_array($lst_transferencia) && !isset($data_transferencia)) {
          $this->array_botao[]            = 'Cancelar Solicitação Transferência';
          $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true\")";
        }
        else {
          if ($registro['ref_ref_cod_serie']) {
            $this->array_botao[]            = 'Solicitar Transferência';
            $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
          }
        }

        if ($registro['aprovado'] == 3 &&
           (!is_array($lst_transferencia) && !isset($data_transferencia))
        ) {
          if ($registro['formando'] == 0) {
            $this->array_botao[]            = 'Formando';
            $this->array_botao_url_script[] = "if(confirm(\"Deseja marcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=1\")";
          }
          else {
            $this->array_botao[]            = "Desmarcar como Formando";
            $this->array_botao_url_script[] = "if(confirm(\"Deseja desmarcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=0\")";
          }
        }
      }

      if ($registro['aprovado'] == 4 || $det_transferencia) {
        $this->array_botao[]            = 'Imprimir Atestado Frequência';
        $this->array_botao_url_script[] = "showExpansivelImprimir(400, 200,  \"educar_relatorio_atestado_frequencia.php?cod_matricula={$registro['cod_matricula']}\",[], \"Relatório Atestado de Freqüência\")";
      }
    }

    $this->url_cancelar = 'educar_matricula_lst.php?ref_cod_aluno=' . $registro['ref_cod_aluno'];
    $this->largura      = '100%';
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