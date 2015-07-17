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
  var $data_matricula;

  var $ref_cod_instituicao;
  var $ref_cod_curso;
  var $ref_cod_escola;
  var $ref_cod_turma;

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
      header('Location: educar_aluno_lst.php');
      die;
    }

    $url = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;

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
        'Caso seja transferência externa por favor marque esta opção.');
    }

    // inputs

    $anoLetivoHelperOptions = array('situacoes' => array('em_andamento', 'nao_iniciado'));

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie'));
    $this->inputsHelper()->dynamic('turma', array('required' => false, 'option value' => 'Selecione uma turma'));
    $this->inputsHelper()->dynamic('anoLetivo', array('label' => 'Ano destino'), $anoLetivoHelperOptions);
    $this->inputsHelper()->date('data_matricula', array('label' => 'Data da matrícula', 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y') ));
    

    if (is_numeric($this->ref_cod_curso)) {
      $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
      $det_curso = $obj_curso->detalhe();

      if (is_numeric($det_curso['ref_cod_tipo_avaliacao'])) {
        $this->campoOculto('apagar_radios', $det_curso['padrao_ano_escolar']);
        $this->campoOculto('is_padrao', $det_curso['padrao_ano_escolar']);
      }
    }

    $this->acao_enviar = 'formUtils.submit()';
  }

  protected function getCurso($id) {
    $curso = new clsPmieducarCurso($id);
    return $curso->detalhe();
  }

  function Novo()
  {

    $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;
    $this->nome_url_cancelar = 'Cancelar';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
      'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);

    //novas regras matricula aluno
    $this->ano = $_POST['ano'];

    $anoLetivoEmAndamentoEscola = new clsPmieducarEscolaAnoLetivo();
    $anoLetivoEmAndamentoEscola = $anoLetivoEmAndamentoEscola->lista($this->ref_cod_escola,
                                                                     $this->ano,
                                                                     null,
                                                                     null,
                                                                     2, /*adiciona where 0 ou 1*/
                                                                     null,
                                                                     null,
                                                                     null,
                                                                     null,
                                                                     1
                                                                     );

    if(is_array($anoLetivoEmAndamentoEscola)) {
      require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
      $db = new clsBanco();

      $db->Consulta("select ref_ref_cod_serie, ref_cod_curso from pmieducar.matricula where ativo = 1 and ref_ref_cod_escola = $this->ref_cod_escola and ref_cod_curso = $this->ref_cod_curso and ref_cod_aluno = $this->ref_cod_aluno and aprovado not in (1,2,4,5,6,7,8,9)");

      $db->ProximoRegistro();
      $m = $db->Tupla();
      if (is_array($m) && count($m)) {

        $curso = $this->getCurso($this->ref_cod_curso);

        if ($m['ref_ref_cod_serie'] == $this->ref_cod_serie) {
          $this->mensagem .= "Este aluno já está matriculado nesta série e curso, não é possivel matricular um aluno mais de uma vez na mesma série.<br />";

          return false;
        }

        elseif ($curso['multi_seriado'] != 1) {
          $serie = new clsPmieducarSerie($m['ref_ref_cod_serie'], null, null, $m['ref_cod_curso']);
          $serie = $serie->detalhe();

          if (is_array($serie) && count($serie))
            $nomeSerie = $serie['nm_serie'];
          else
            $nomeSerie = '';

          $this->mensagem .= "Este aluno já está matriculado no(a) '$nomeSerie' deste curso e escola. Como este curso não é multi seriado, não é possivel manter mais de uma matricula em andamento para o mesmo curso.<br />";

          return false;
        }
      }

      else
      {
        $db->Consulta("select ref_ref_cod_escola, ref_cod_curso, ref_ref_cod_serie from pmieducar.matricula where ativo = 1 and ref_ref_cod_escola != $this->ref_cod_escola and ref_cod_aluno = $this->ref_cod_aluno and aprovado not in (1,2,4,5,6,7,8,9) and not exists (select 1 from pmieducar.transferencia_solicitacao as ts where ts.ativo = 1 and ts.ref_cod_matricula_saida = matricula.cod_matricula)");

        $db->ProximoRegistro();
        $m = $db->Tupla();
        if (is_array($m) && count($m)){
          if ($m['ref_cod_curso'] == $this->ref_cod_curso || $GLOBALS['coreExt']['Config']->app->matricula->multiplas_matriculas == 0){
            require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
            require_once 'include/pessoa/clsJuridica.inc.php';
            $serie = new clsPmieducarSerie($m['ref_ref_cod_serie'], null, null, $m['ref_cod_curso']);          
            $serie = $serie->detalhe();
            if (is_array($serie) && count($serie))
              $serie = $serie['nm_serie'];
            else
              $serie = '';
            $escola = new clsPmieducarEscola($m['ref_ref_cod_escola']);
            $escola = $escola->detalhe();
            if (is_array($escola) && count($escola))
            {
              $escola = new clsJuridica($escola['ref_idpes']);
              $escola = $escola->detalhe();
              if (is_array($escola) && count($escola))
                $escola = $escola['fantasia'];
              else
                $escola = '';
            }
            else
              $escola = '';
            $curso = new clsPmieducarCurso($m['ref_cod_curso']);
            $curso = $curso->detalhe();
            if (is_array($curso) && count($curso))
              $curso = $curso['nm_curso'];
            else
              $curso = '';
            $this->mensagem .= "Este aluno já está matriculado no(a) '$serie' do curso '$curso' na escola '$escola', para matricular este aluno na sua escola solicite transferência ao secretário(a) da escola citada.<br />";
            return false;
          }
        }
      }

      $obj_reserva_vaga = new clsPmieducarReservaVaga();
      $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $this->ref_cod_escola,
        $this->ref_cod_serie, NULL, NULL,$this->ref_cod_aluno, NULL, NULL,
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
        $lst_turmas = $obj_turmas->lista(NULL, NULL, NULL, $this->ref_cod_serie,
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
          $this->mensagem = 'A série selecionada não possui turmas cadastradas.<br />';
          return FALSE;
        }

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista(NULL, NULL, $this->ref_cod_escola,
          $this->ref_cod_serie, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, 1,
          $this->ano, $this->ref_cod_curso, $this->ref_cod_instituicao, 1);

        if (is_array($lst_matricula)) {
          $matriculados = count($lst_matricula);
        }

        $obj_reserva_vaga = new clsPmieducarReservaVaga();
        $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $this->ref_cod_escola,
          $this->ref_cod_serie, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1,
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
              window.location = \'educar_aluno_det.php?cod_aluno=%d\';
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

      if (! $this->removerFlagUltimaMatricula($this->ref_cod_aluno)) {
        return false;
      }

      $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);
      
      $obj = new clsPmieducarMatricula(NULL, $this->ref_cod_reserva_vaga,
        $this->ref_cod_escola, $this->ref_cod_serie, NULL,
        $this->pessoa_logada, $this->ref_cod_aluno, 3, NULL, NULL, 1, $this->ano,
        1, NULL, NULL, NULL, NULL, $this->ref_cod_curso,
        $this->matricula_transferencia, $this->semestre, $this->data_matricula);

      $cadastrou = $obj->cadastra();
      
      // turma
      $cod_matricula = $cadastrou;
      $this->enturmacaoMatricula($cod_matricula, $this->ref_cod_turma);
      
      if ($cadastrou) {

        $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();


        #Se encontrar solicitações de transferencia externa (com data de transferencia sem codigo de matricula de entrada), inativa estas
        /*$lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL,
          $this->ref_cod_aluno, FALSE, NULL, NULL, NULL, TRUE, FALSE);

        if (is_array($lst_transferencia)) {
          echo 'Encontrou solicitações de transferencia externa (saida) com data de transferencia';
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
        #senão pega as solicitacoes de transferencia internas (sem data de transferencia e sem codigo de matricula de entrada) e
        #seta a data de transferencia e codigo de matricula de entrada, atualiza a situacao da matricula para transferido e inativa a matricula turma
        else {
        */
          $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
          $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL,
            $this->ref_cod_aluno, FALSE, NULL, NULL, NULL, FALSE, FALSE);

          #TODO interna ?
          // Verifica se existe solicitação de transferência (interna) do aluno
          if (is_array($lst_transferencia)) {
            #echo 'Encontrou solicitações de transferencia interna  (saida) com data de transferencia';
            // Verifica cada solicitação de transferência do aluno
            foreach ($lst_transferencia as $transferencia) {
              $obj_matricula = new clsPmieducarMatricula(
                $transferencia['ref_cod_matricula_saida']
              );

              $det_matricula = $obj_matricula->detalhe();

              // Se a matrícula anterior estava em andamento, copia as notas/faltas/pareceres
              if ($det_matricula['aprovado'] == 3){
                $db->Consulta(" SELECT modules.copia_notas_transf({$det_matricula['cod_matricula']},{$cod_matricula})");
              }              

              // Caso a solicitação seja para uma mesma série
              if ($det_matricula['ref_ref_cod_serie'] == $this->ref_cod_serie) {
                $ref_cod_transferencia = $transferencia['cod_transferencia_solicitacao'];
                break;
              }
              // Caso a solicitação seja para a série da sequência
              else {
                $obj_sequencia = new clsPmieducarSequenciaSerie(
                  $det_matricula['ref_ref_cod_serie'], $this->ref_cod_serie,
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

                  if ($obj_matricula->edita() && ! $this->desativaEnturmacoesMatricula($matricula_saida))
                    return false;
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
        //}

        #TODO set in $_SESSION['flash'] 'Aluno matriculado com sucesso'
        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        header('Location: educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
        #die();
        #return true;
      }
      
      $this->mensagem = 'Cadastro não realizado.<br />';
      return FALSE;
    }
    else {
      $this->mensagem = 'O ano (letivo) selecionado não esta em andamento na escola selecionada.<br />';
      return FALSE;
    }
  }


  function desativaEnturmacoesMatricula($matriculaId) {
    $result = true;

    $enturmacoes = new clsPmieducarMatriculaTurma();
    $enturmacoes = $enturmacoes->lista($matriculaId, NULL, NULL, NULL, NULL,
                                       NULL, NULL, NULL, 1);

    if ($enturmacoes) {
      foreach ($enturmacoes as $enturmacao) {
        $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                     $enturmacao['ref_cod_turma'],
                                                     $this->pessoa_logada, null,
                                                     null, null, 0, null,
                                                     $enturmacao['sequencial']);
        if ($result && ! $enturmacao->edita())
          $result = false;
      }
    }

    if(! $result) {
		  $this->mensagem = "N&atilde;o foi poss&iacute;vel desativar as " .
                        "enturma&ccedil;&otilde;es da matr&iacute;cula.";
    }

    return $result;
  }


  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7,
      'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);

    if (! $this->desativaEnturmacoesMatricula($this->cod_matricula))
      return false;

    $obj_matricula = new clsPmieducarMatricula( $this->cod_matricula );
    $det_matricula = $obj_matricula->detalhe();
    $ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

    $obj_sequencia = new clsPmieducarSequenciaSerie();
    $lst_sequencia = $obj_sequencia->lista(
      NULL, $ref_cod_serie, NULL, NULL, NULL, NULL, NULL, NULL, 1
    );

    // Coloca as matrículas anteriores em andamento
    $obj_transferencia_antiga  = new clsPmieducarTransferenciaSolicitacao();
    $lista_transferencia = $obj_transferencia_antiga->lista(null,null,null,null,null,$this->cod_matricula);
    if (is_array($lista_transferencia)){
      foreach ($lista_transferencia as $transf) {
 
        $obj_mat = new clsPmieducarMatricula($transf['ref_cod_matricula_saida']);
        $obj_mat = $obj_mat->detalhe();
          if ($obj_mat['aprovado']==4){
            $obj_mat = new clsPmieducarMatricula($transf['ref_cod_matricula_saida'],null,null
                         ,null,$this->pessoa_logada,null,null,3);
           $obj_mat->edita();
           $obj_transf  = new clsPmieducarTransferenciaSolicitacao($transf['cod_transferencia_solicitacao']);
           $obj_transf->desativaEntradaTransferencia();
         }
      }
    }    

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
      header('Location: educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
      die();
    }

    $this->mensagem = 'Exclusão não realizada.<br />';
    return FALSE;
  }

  protected function removerFlagUltimaMatricula($alunoId) {
    $matriculas = new clsPmieducarMatricula();
    $matriculas = $matriculas->lista(NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_cod_aluno,
                                     NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1);


    foreach ($matriculas as $matricula) {
      $matricula = new clsPmieducarMatricula($matricula['cod_matricula'], NULL, NULL, NULL,
                                             $this->pessoa_logada, NULL, $alunoId, NULL, NULL,
                                             NULL, 1, NULL, 0);
      if (! $matricula->edita()) {
        $this->mensagem = 'Erro ao remover flag ultima matricula das matriculas anteriores.';
        return false;
      }
    }

    return true;
  }
  
   function enturmacaoMatricula($matriculaId, $turmaDestinoId) {

    $enturmacaoExists = new clsPmieducarMatriculaTurma();
    $enturmacaoExists = $enturmacaoExists->lista($matriculaId,
                                                 $turmaDestinoId,
                                                 NULL, 
                                                 NULL,
                                                 NULL, 
                                                 NULL,
                                                 NULL,
                                                 NULL,
                                                 1);

    $enturmacaoExists = is_array($enturmacaoExists) && count($enturmacaoExists) > 0;
    if (! $enturmacaoExists) {
      $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                   $turmaDestinoId,
                                                   $this->pessoa_logada, 
                                                   $this->pessoa_logada, 
                                                   NULL,
                                                   NULL, 
                                                   1);
      $enturmacao->data_enturmacao = $this->data_matricula;
      return $enturmacao->cadastra();
    }
    return false;
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
