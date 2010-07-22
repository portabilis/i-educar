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
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_matricula;
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
  var $ano;

  var $ref_cod_instituicao;
  var $ref_cod_curso;
  var $ref_cod_escola;

  var $matricula_transferencia;
  var $semestre;
  var $is_padrao;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_matricula = $_GET['cod_matricula'];
    $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

    $obj_aluno = new clsPmieducarAluno($this->ref_cod_aluno);

    if (! $obj_aluno->existe()) {
      header('Location: educar_matricula_lst.php');
      die;
    }

    $url = 'educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno;

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, $url);

    if (is_numeric($this->cod_matricula)) {
      if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
        $this->Excluir();
      }
    }

    $this->url_cancelar = $url;
    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    // primary keys
    $this->campoOculto("cod_matricula", $this->cod_matricula);
    $this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);

    $obj_aluno = new clsPmieducarAluno();
    $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_aluno)) {
      $det_aluno      = array_shift($lst_aluno);
      $this->nm_aluno = $det_aluno['nome_aluno'];
      $this->campoRotulo('nm_aluno', 'Aluno', $this->nm_aluno);
    }

    /*
     * Verifica se existem matrículas para o aluno para apresentar o campo
     * transferência, necessário para o relatório de movimentação mensal.
     */
    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista(NULL, NULL, NULL, NULL, NULL, NULL,
      $this->ref_cod_aluno);

    // Primeira matrícula do sistema exibe campo check
    if (! $lst_matricula) {
      $this->campoCheck('matricula_transferencia',
        'Matrícula de Transferência', '',
        'Caso seja transfência externa por favor marque esta opção.');
    }

    // foreign keys
    $instituicao_obrigatorio  = TRUE;
    $curso_obrigatorio        = TRUE;
    $escola_curso_obrigatorio = TRUE;
    $get_escola               = TRUE;
    $get_curso                = TRUE;
    $get_escola_curso_serie   = TRUE;
    $get_matricula            = TRUE;
    $sem_padrao               = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    if (is_numeric($this->ref_cod_curso)) {
      $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
      $det_curso = $obj_curso->detalhe();

      if (is_numeric($det_curso['ref_cod_tipo_avaliacao'])) {
        $this->campoOculto('apagar_radios', $det_curso['padrao_ano_escolar']);
        $this->campoOculto('is_padrao', $det_curso['padrao_ano_escolar']);
      }
    }

    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

    $this->acao_enviar = 'valida()';
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
      'educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno);

    $obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo();
    $lst_escola_ano_letivo = $obj_escola_ano_letivo->lista($this->ref_cod_escola,
      NULL, NULL, NULL,1, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_escola_ano_letivo)) {
      $det_escola_ano_letivo = array_shift($lst_escola_ano_letivo);
      $this->ano = $det_escola_ano_letivo['ano'];

      $obj_reserva_vaga = new clsPmieducarReservaVaga();
      $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $this->ref_cod_escola,
        $this->ref_ref_cod_serie, NULL, NULL,$this->ref_cod_aluno, NULL, NULL,
        NULL, NULL, 1);

      // Verifica se existe reserva de vaga para o aluno
      if (is_array($lst_reserva_vaga)) {
        $det_reserva_vaga           = array_shift($lst_reserva_vaga);
        $this->ref_cod_reserva_vaga = $det_reserva_vaga['cod_reserva_vaga'];

        $obj_reserva_vaga = new clsPmieducarReservaVaga($this->ref_cod_reserva_vaga,
          NULL, NULL, $this->pessoa_logada, NULL, NULL, NULL, NULL, 0);

        $editou = $obj_reserva_vaga->edita();
        if (! $editou) {
          $this->mensagem = 'Edição não realizada.<br />';
          return FALSE;
        }
      }

      $vagas_restantes = 1;

      if (! $this->ref_cod_reserva_vaga) {
        $obj_turmas = new clsPmieducarTurma();
        $lst_turmas = $obj_turmas->lista(NULL, NULL, NULL, $this->ref_ref_cod_serie,
          $this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, TRUE);

        if (is_array($lst_turmas)) {
          $total_vagas = 0;
          foreach ($lst_turmas as $turmas) {
            $total_vagas += $turmas['max_aluno'];
          }
        }
        else {
          $this->mensagem = 'Série não possui turmas cadastradas.<br />';
          return FALSE;
        }

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista(NULL, NULL, $this->ref_cod_escola,
          $this->ref_ref_cod_serie, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, 1,
          $this->ano, $this->ref_cod_curso, $this->ref_cod_instituicao, 1);

        if (is_array($lst_matricula)) {
          $matriculados = count($lst_matricula);
        }

        $obj_reserva_vaga = new clsPmieducarReservaVaga();
        $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $this->ref_cod_escola,
          $this->ref_ref_cod_serie, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1,
          $this->ref_cod_instituicao, $this->ref_cod_curso);

        if (is_array($lst_reserva_vaga)) {
          $reservados = count($lst_reserva_vaga);
        }

        $vagas_restantes = $total_vagas - ($matriculados + $reservados);
      }

      if ($vagas_restantes <= 0) {
        echo sprintf('
          <script>
            var msg = \'\';
            msg += \'Excedido o número de total de vagas para Matricula!\\n\';
            msg += \'Número total de matriculados: %d\\n\';
            msg += \'Número total de vagas reservadas: %d\\n\';
            msg += \'Número total de vagas: %d\\n\';
            msg += \'Deseja mesmo assim realizar a Matrícula?\';

            if (! confirm(msg)) {
              window.location = \'educar_matricula_lst.php?ref_cod_aluno=%d\';
            }
          </script>',
          $matriculados, $reservados, $total_vagas, $this->ref_cod_aluno
        );
      }

      $obj_matricula_aluno = new clsPmieducarMatricula();
      $lst_matricula_aluno = $obj_matricula_aluno->lista(NULL, NULL, NULL, NULL,
        NULL, NULL, $this->ref_cod_aluno);

      if (! $lst_matricula_aluno) {
        // Primeira matrícula do sistema, consistência (?)
        $this->matricula_transferencia =
          $this->matricula_transferencia == 'on' ? TRUE : FALSE;
      }
      else {
        $this->matricula_transferencia = FALSE;
      }

      if ($this->is_padrao == 1) {
        $this->semestre =  NULL;
      }

      $obj = new clsPmieducarMatricula(NULL, $this->ref_cod_reserva_vaga,
        $this->ref_cod_escola, $this->ref_ref_cod_serie, NULL,
        $this->pessoa_logada, $this->ref_cod_aluno, 3, NULL, NULL, 1, $this->ano,
        1, NULL, NULL, NULL, NULL, $this->ref_cod_curso,
        $this->matricula_transferencia, $this->semestre);

      $cadastrou = $obj->cadastra();
      if ($cadastrou) {
        $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
        $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL,
          $this->ref_cod_aluno, FALSE, NULL, NULL, NULL, TRUE, FALSE);

        // Verifica se existe solicitação de transferência de aluno
        if (is_array($lst_transferencia)) {
          $det_transferencia = array_shift($lst_transferencia);

          $obj_transferencia = new clsPmieducarTransferenciaSolicitacao(
            $det_transferencia['cod_transferencia_solicitacao'], NULL,
            $this->pessoa_logada, NULL, NULL, NULL, NULL, NULL, NULL, 0);

          $editou2 = $obj_transferencia->edita();

          if ($editou2) {
            $obj = new clsPmieducarMatricula($det_transferencia['ref_cod_matricula_saida'],
              NULL, NULL, NULL, $this->pessoa_logada, NULL, NULL, 4, NULL, NULL, 1, NULL, 0);

            $editou3 = $obj->edita();

            if (! $editou3) {
              $this->mensagem = 'Edição não realizada.<br />';
              return FALSE;
            }
          }
          else {
            $this->mensagem = 'Edição não realizada.<br />';
            return FALSE;
          }
        }
        else {
          $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
          $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL,
            $this->ref_cod_aluno, FALSE, NULL, NULL, NULL, FALSE, FALSE);

          // Verifica se existe solicitação de transferência do aluno
          if (is_array($lst_transferencia)) {
            // Verifica cada solicitação de transferência do aluno
            foreach ($lst_transferencia as $transferencia) {
              $obj_matricula = new clsPmieducarMatricula(
                $transferencia['ref_cod_matricula_saida']
              );

              $det_matricula = $obj_matricula->detalhe();

              // Caso a solicitação seja para uma mesma série
              if ($det_matricula['ref_ref_cod_serie'] == $this->ref_ref_cod_serie) {
                $ref_cod_transferencia = $transferencia['cod_transferencia_solicitacao'];
                break;
              }
              // Caso a solicitação seja para a série da sequência
              else {
                $obj_sequencia = new clsPmieducarSequenciaSerie(
                  $det_matricula['ref_ref_cod_serie'], $this->ref_ref_cod_serie,
                  NULL, NULL, NULL, NULL, 1
                );

                if ($obj_sequencia->existe()) {
                  $ref_cod_transferencia = $transferencia['cod_transferencia_solicitacao'];
                  break;
                }
              }

              $ref_cod_transferencia = $transferencia['cod_transferencia_solicitacao'];
            }

            if ($ref_cod_transferencia) {
              $obj_transferencia = new clsPmieducarTransferenciaSolicitacao(
                $ref_cod_transferencia, NULL, $this->pessoa_logada, NULL,
                $cadastrou, NULL, NULL, NULL, NULL, 1, date('Y-m-d')
              );

              $editou2 = $obj_transferencia->edita();

              if ($editou2) {
                $obj_transferencia = new clsPmieducarTransferenciaSolicitacao(
                  $ref_cod_transferencia
                );

                $det_transferencia = $obj_transferencia->detalhe();
                $matricula_saida   = $det_transferencia['ref_cod_matricula_saida'];

                $obj_matricula = new clsPmieducarMatricula($matricula_saida);
                $det_matricula = $obj_matricula->detalhe();

                // Caso a situação da matrícula do aluno esteja em andamento
                if ($det_matricula['aprovado'] == 3) {
                  $obj_matricula = new clsPmieducarMatricula(
                    $cadastrou, NULL, NULL, NULL, $this->pessoa_logada, NULL,
                    NULL, NULL, NULL, NULL, 1, NULL, NULL, $det_matricula['modulo']
                  );

                  $editou_mat = $obj_matricula->edita();

                  if ($editou_mat) {
                    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
                    $lst_matricula_turma = $obj_matricula_turma->lista(
                      $matricula_saida, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1
                    );

                    if (is_array($lst_matricula_turma)) {
                      $det_matricula_turma = array_shift($lst_matricula_turma);

                      $obj_matricula_turma = new clsPmieducarMatriculaTurma(
                        $matricula_saida, $det_matricula_turma['ref_cod_turma'],
                        $this->pessoa_logada, NULL, NULL, NULL, 0, NULL,
                        $det_matricula_turma['sequencial']
                      );

                      $editou_mat_turma = $obj_matricula_turma->edita();

                      if (! $editou_mat_turma) {
                        $this->mensagem = 'Não foi possível editar a Matrícula Turma.<br />';
                        return FALSE;
                      }
                    }
                  }
                }

                $obj = new clsPmieducarMatricula(
                  $matricula_saida, NULL, NULL, NULL,$this->pessoa_logada, NULL,
                  NULL, 4, NULL, NULL, 1, NULL, 0
                );

                $editou3 = $obj->edita();

                if (! $editou3) {
                  $this->mensagem = 'Edição não realizada.<br />';
                  return FALSE;
                }
              }
              else {
                $this->mensagem = 'Edição não realizada.<br />';
                return FALSE;
              }
            }
          }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        header('Location: educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno);
        die();
      }

      $this->mensagem = 'Cadastro não realizado.<br />';
      return FALSE;
    }
    else {
      $this->mensagem = 'Não foi possível encontrar o "Ano Letivo" em andamento da Escola.<br />';
      return FALSE;
    }
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7,
      'educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno);

    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
    $lst_matricula_turma = $obj_matricula_turma->lista(
      $this->cod_matricula, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1
    );

    if (is_array($lst_matricula_turma)) {
      $det_matricula_turma = array_shift($lst_matricula_turma);
      $obj_matricula_turma = new clsPmieducarMatriculaTurma(
        $det_matricula_turma['ref_cod_matricula'],
        $det_matricula_turma['ref_cod_turma'], $this->pessoa_logada, NULL,
        NULL, NULL, 0, NULL, $det_matricula_turma['sequencial']
      );

      $editou = $obj_matricula_turma->edita();

      if (! $editou) {
        $this->mensagem = 'Edição não realizada.<br />';
        return FALSE;
      }
    }

    $obj_matricula = new clsPmieducarMatricula( $this->cod_matricula );
    $det_matricula = $obj_matricula->detalhe();
    $ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

    $obj_sequencia = new clsPmieducarSequenciaSerie();
    $lst_sequencia = $obj_sequencia->lista(
      NULL, $ref_cod_serie, NULL, NULL, NULL, NULL, NULL, NULL, 1
    );

    // Verifica se a série da matrícula cancelada é sequência de alguma outra série
    if (is_array($lst_sequencia)) {
      $det_sequencia    = array_shift($lst_sequencia);
      $ref_serie_origem = $det_sequencia['ref_serie_origem'];

      $obj_matricula = new clsPmieducarMatricula();
      $lst_matricula = $obj_matricula->lista(
        NULL, NULL, NULL, $ref_serie_origem, NULL, NULL,$this->ref_cod_aluno,
        NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0
      );

      // Verifica se o aluno tem matrícula na série encontrada
      if (is_array($lst_matricula)) {
        $det_matricula     = array_shift($lst_matricula);
        $ref_cod_matricula = $det_matricula['cod_matricula'];

        $obj = new clsPmieducarMatricula(
          $ref_cod_matricula, NULL, NULL, NULL, $this->pessoa_logada, NULL, NULL,
          NULL, NULL, NULL, 1, NULL, 1
        );

        $editou1 = $obj->edita();
        if (! $editou1) {
          $this->mensagem = 'Não foi possível editar a "Última Matrícula da Sequência".<br />';
          return FALSE;
        }
      }
    }

    $obj = new clsPmieducarMatricula(
      $this->cod_matricula, NULL, NULL, NULL, $this->pessoa_logada, NULL, NULL,
      NULL, NULL, NULL, 0
    );

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
      header('Location: educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno);
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
<script type="text/javascript">
function getCursoMatricula()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var cod_aluno        = <?php print $_GET['ref_cod_aluno'] ?>;
  var campoCurso       = document.getElementById('ref_cod_curso');

  campoCurso.length          = 1;
  campoCurso.disabled        = true;
  campoCurso.options[0].text = 'Carregando curso';

  var xml_curso_matricula = new ajax(atualizaCursoMatricula);

  var url = 'educar_curso_matricula_xml.php?ins=' + campoInstituicao + '&alu=' + cod_aluno;
  xml_curso_matricula.envia(url);
}

function atualizaCursoMatricula(xml_curso_matricula)
{
  var campoCurso = document.getElementById('ref_cod_curso');
  var DOM_array  = xml_curso_matricula.getElementsByTagName('curso');

  if (DOM_array.length) {
    campoCurso.length          = 1;
    campoCurso.options[0].text = 'Selecione um curso';
    campoCurso.disabled        = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoCurso.options[campoCurso.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('cod_curso'),
        false, false
      );
    }
  }
  else {
    campoCurso.options[0].text = 'A instituição não possui nenhum curso';
  }
}

function getSerieMatricula()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoEscola      = document.getElementById('ref_cod_escola').value;
  var campoCurso       = document.getElementById('ref_cod_curso').value;
  var cod_aluno        = <?php print $_GET['ref_cod_aluno'] ?>;
  var campoSerie       = document.getElementById('ref_ref_cod_serie');

  campoSerie.length          = 1;
  campoSerie.disabled        = true;
  campoSerie.options[0].text = 'Carregando série';

  var xml_serie_matricula = new ajax(atualizaSerieMatricula);

  var url = 'educar_serie_matricula_xml.php?ins=' + campoInstituicao + '&cur=' + campoCurso
          + '&esc=' + campoEscola + '&alu=' + cod_aluno;

  xml_serie_matricula.envia(url);
}

function atualizaSerieMatricula(xml_serie_matricula)
{
  var campoSerie = document.getElementById('ref_ref_cod_serie');
  var DOM_array  = xml_serie_matricula.getElementsByTagName('serie');

  if (DOM_array.length) {
    campoSerie.length          = 1;
    campoSerie.options[0].text = 'Selecione uma série';
    campoSerie.disabled        = false;

    var series = new Array();

    for (var i = 0; i < DOM_array.length; i++) {
      if (! series[DOM_array[i].getAttribute('cod_serie') + '_']) {
        campoSerie.options[campoSerie.options.length] = new Option(
          DOM_array[i].firstChild.data, DOM_array[i].getAttribute('cod_serie'),
          false, false
        );

        series[DOM_array[i].getAttribute('cod_serie') + '_'] = true;
      }
    }
  }
  else {
    campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
  }
}

document.getElementById('ref_cod_escola').onchange = function()
{
  if (document.getElementById('ref_cod_escola').value == '') {
    getCursoMatricula();
  }
  else {
    getEscolaCurso();
  }
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getSerieMatricula();
}

function valida()
{
  if (document.getElementById('ref_cod_escola').value) {
    if (!document.getElementById('ref_ref_cod_serie').value) {
      alert('O campo "Série" deve ser preenchido corretamente!');
      document.getElementById('ref_ref_cod_serie').focus();
      return false;
    }
  }

  if (! acao()) {
    return false;
  }

  document.forms[0].submit();
}
</script>