<?php

/**
 * i-Educar - Sistema de gestĂŁo escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaĂ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa ĂŠ software livre; vocĂŞ pode redistribuĂ­-lo e/ou modificĂĄ-lo
 * sob os termos da LicenĂ§a PĂşblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versĂŁo 2 da LicenĂ§a, como (a seu critĂŠrio)
 * qualquer versĂŁo posterior.
 *
 * Este programa ĂŠ distribuĂ­Â­do na expectativa de que seja Ăştil, porĂŠm, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implĂ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAĂĂO A UMA FINALIDADE ESPECĂFICA. Consulte a LicenĂ§a PĂşblica Geral
 * do GNU para mais detalhes.
 *
 * VocĂŞ deve ter recebido uma cĂłpia da LicenĂ§a PĂşblica Geral do GNU junto
 * com este programa; se nĂŁo, escreva para a Free Software Foundation, Inc., no
 * endereĂ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de ItajaĂ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponĂ­vel desde a versĂŁo 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsAgenda.inc.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de ItajaĂ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponĂ­vel desde a versĂŁo 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  public function Formular() {
    $this->SetTitulo($this->_instituicao);
    $this->processoAp = 0;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de ItajaĂ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponĂ­vel desde a versĂŁo 1.0.0
 * @version   @@package_version@@
 */
class indice
{
  function addLeadingZero($num)
  {
    if (is_numeric($num)) {
      if ($num < 10) {
        return '0' . $num;
      }

      return $num;
    }
    return 0;
  }

  function RenderHTML()
  {
    @session_start();
    $id_pessoa = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($id_pessoa) {
      $endScript = '<script>var x = new Array();' . PHP_EOL;

      $temp = '';

      if (isset($_GET['negado'])) {
        $temp .= "<center><h3>Acesso negado para este usu&aacute;rio.</h3><br>Caso persista nas tentativas sua conta na intranet poder&aacute; ser bloqueada por tempo indeterminado.</center>";
      }

      $pendencia_administrativa = dbBool($GLOBALS['coreExt']['Config']->app->administrative_pending->exist);
      $texto_pendencia = $GLOBALS['coreExt']['Config']->app->administrative_pending->msg;

      if ($pendencia_administrativa)
        echo '
            <script type="text/javascript" src="scripts/jquery/jquery-1.8.3.min.js"></script>
          <link rel="stylesheet" href="scripts/jquery/jquery-ui.min-1.9.2/css/custom/jquery-ui-1.9.2.custom.min.css">
          <script src="scripts/jquery/jquery-ui.min-1.9.2/js/jquery-ui-1.9.2.custom.min.js"></script>
          <div id="dialog" title="Aviso">
            '.$texto_pendencia.'
          </div>
          <script>
          var $j = jQuery.noConflict();

          $j(function() {
            $j( "#dialog" ).dialog({
              width: 600,
              position: { my: "center", at: "top", of: window },
              buttons: [
                {
                  text: "Entendi. Prosseguir utilizando o sistema.",
                  click: function(){
                    $j(this).dialog("close");
                  }
                }
              ]
            });

          });
         </script>';
      $temp .= "<table class='tablelistagem' id='tableLocalizacao'width='100%' border='0'  cellpadding='0' cellspacing='0'>
                  <tr height='10px'>
                    <td class='fundoLocalizacao' colspan='2'>
                      <div id='localizacao'>
                        <a href='/intranet' title='Ir para o início'>
                          <i class='fa fa-home' aria-hidden='true'></i>
                          <span> Início</span>
                        </a>
                        <a class='flechinha' href='#'> / </a>
                        <a href='#' class='pagina_atual'>Calendário</a>
                      </div>
                    </td>
                  </tr>
                </table>";
      $temp .= '<table width="100%" height="400" align="center" border="0" cellspacing="4" cellpadding="0">';
      $temp .= '
        <tr>
          <td class="fundoCalendarioTopo" style="padding:0px;">DOM</td>
          <td class="fundoCalendarioTopo" style="padding:0px;">SEG</td>
          <td class="fundoCalendarioTopo" style="padding:0px;">TER</td>
          <td class="fundoCalendarioTopo" style="padding:0px;">QUA</td>
          <td class="fundoCalendarioTopo" style="padding:0px;">QUI</td>
          <td class="fundoCalendarioTopo" style="padding:0px;">SEX</td>
          <td class="fundoCalendarioTopo" style="padding:0px;">SAB</td>
        </tr>';

      $mes = $_GET['mes'] ?? date('m');
      $ano = $_GET['ano'] ?? date('Y');

      $temp_var = 0;

      if (class_exists("clsProcesso")) {
        // Busca os codigos das pastas ativas
        $obj_pastas = new clsProcesso();
        $lista_pastas = $obj_pastas->lista_cod();

        // Verificas se existem pastas ativas
        if ($lista_pastas) {
          //Buscas os encaminhamentos da pessoa atual
          $obj_encaminha = new clsEncaminha();
          $lista_minhas_pastas = $obj_encaminha->lista_cod_processos(FALSE,
            FALSE, FALSE, FALSE, FALSE, $id_pessoa, FALSE, FALSE, FALSE,
            FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, $lista_pastas);

          //Verificas se existem encaminhamentos
          if ($lista_minhas_pastas) {
            // Busca os Processos dentro das pastas da pessoa Atual
            $obj_processo = new clsTramite();
            $lista_processos_ativos = $obj_processo->lista_cod(FALSE, FALSE,
              FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
              FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
              FALSE, 1, FALSE, FALSE, FALSE, $lista_minhas_pastas);
          }
        }
      }

      // Faz loop da quantidade de dias do MĂÂŞs
      $max_comp_dia = 5;
      for ($i=1; $i <= date('t', mktime(0, 0, 0, $mes, 1, $ano)); $i++) {
        $qtd = 0;

        $dataAtual   = date('Y/m/d', mktime(0, 0, 0, $mes, $i, $ano));
        $dataAmanha  = date('Y/m/d',mktime(0, 0, 0, $mes, $i + 1, $ano));
        $diaDaSemana = date('w', strtotime(substr($dataAtual, 0, 19)));

        $compromisso_geral = '';

        if (class_exists('clsEncaminha')) {
          $objEncaminha = new clsEncaminha();
          $lista_encaminha = $objEncaminha->lista_cod_encaminha(FALSE, FALSE,
            FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
            FALSE,$dataAtual, $dataAmanha);

          if($lista_encaminha) {
            $lista_encaminha = $obj_encaminha->lista(FALSE, FALSE, FALSE, FALSE,
              FALSE, $id_pessoa, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
              $dataAtual, $dataAmanha, FALSE, FALSE, FALSE, $lista_encaminha);
          }

          if($lista_encaminha) {
            foreach ($lista_encaminha as $encaminha) {
              $id = '';

              if ($encaminha['ref_cod_juris_processo'] &&
                $encaminha['ref_versao_processo']) {

                $objProcesso = new clsProcesso($encaminha['ref_cod_juris_processo'],
                  $encaminha['ref_versao_processo']);

                $detalheProcesso = $objProcesso->detalhe();

                if ($detalheProcesso['ativo'] == 1 && !$detalheProcesso['ref_pessoa_finalizadora'] && $qtd < $max_comp_dia ) {
                  if (! $encaminha['visualizado']) {
                    $temp_var++;
                    $endScript .= " x[$temp_var]= $temp_var;";
                    $id = "id='comp_{$temp_var}'";
                  }

                  $qtd++;
                  $compromisso_geral .= "<a href='juris_processo_det.php?cod_processo={$encaminha['ref_cod_juris_processo']}&versao_processo={$encaminha['ref_versao_processo']}' ><span class='textoAgenda' $id>- Pasta nĂÂş {$encaminha['ref_cod_juris_processo']}</span></a><br>";
                }
              }
              else {
                $objTramite = new clsTramite($encaminha['ref_cod_juris_tramite'],$encaminha['ref_versao_tramite']);
                $detalheTramite = $objTramite->detalhe();
                $objProcesso = new clsProcesso($detalheTramite['ref_cod_juris_processo'],$detalheTramite['ref_versao_processo']);
                $detalheProcesso = $objProcesso->detalhe();

                if ($detalheTramite['ativo'] == 1 &&
                  !$detalheProcesso['ref_pessoa_finalizadora'] && $qtd< $max_comp_dia) {
                  if (!$encaminha['visualizado']) {
                    $temp_var++;
                    $endScript .= " x[$temp_var]= $temp_var;";
                    $id = "id='comp_{$temp_var}'";
                  }

                  $qtd++;
                  $compromisso_geral .= "<a href='juris_tramite_det.php?cod_tramite={$encaminha['ref_cod_juris_tramite']}&versao_tramite={$encaminha['ref_versao_tramite']}' ><span class='textoAgenda' $id>- Processo nĂÂş {$encaminha['ref_cod_juris_tramite']}</span></a><br>";
                }
              }
            }
          }

          if ($lista_processos_ativos) {
            $obj_prazo = new clsJurisTramitePrazo();
            $lista_prazos = $obj_prazo->lista( FALSE, FALSE, FALSE, FALSE, FALSE,
              FALSE, FALSE, FALSE,$dataAtual,$dataAmanha, FALSE, FALSE, FALSE,
              FALSE, $lista_processos_ativos);

            if ($lista_prazos) {
              foreach ($lista_prazos as $prazo) {
                if(strlen($prazo['descricao']) > 10) {
                  $descricao = substr($prazo['descricao'], 0, 10) . '...';
                }
                else {
                  $descricao = $prazo['descricao'];
                }

                if ($qtd < $max_comp_dia) {
                  $compromisso_geral .= "<a href='juris_tramite_det.php?cod_tramite={$lista_tramite[0]['cod_juris_tramite']}&versao_tramite={$lista_tramite[0]['versao_tramite']}' ><span class='textoAgenda' $id>- Prazo: {$descricao}</span></a><br>";
                }

                $qtd++;
              }
            }
          }
        }

        $data_array = explode('/', $dataAtual);
        $data_array = "{$data_array[2]}/{$data_array[1]}/{$data_array[0]}";

        $db = new clsBanco();
        $db->Consulta( "SELECT ref_cod_agenda FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$id_pessoa}' AND principal = 1" );

        if ($db->ProximoRegistro()) {
          list($cod_agenda) = $db->Tupla();
          $obj_agenda = new clsAgenda($id_pessoa, FALSE, $cod_agenda);
        }
        else {
          $obj_agenda = new clsAgenda($id_pessoa, $id_pessoa, FALSE);
          $cod_agenda = $obj_agenda->getCodAgenda();
        }

        $nomeAgenda = $obj_agenda->getNome();

        $lista_compromissos = $obj_agenda->listaCompromissosDia($data_array);

        if ($lista_compromissos) {
          foreach ($lista_compromissos as $compromisso) {
            if ($qtd < $max_comp_dia) {
              $disp_comp = $compromisso['descricao'];
              $titulo = $compromisso['titulo'];
              $qtd_tit_copia_desc = 5;

              if ($titulo) {
                $disp_titulo = $titulo;
              }
              else {
                // se nao tiver titulo pega as X primeiras palavras da descricao
                // ( X = $qtd_tit_copia_desc )
                $disp_titulo = implode(' ', array_slice(explode(' ', $disp_comp),
                  0, $qtd_tit_copia_desc));
              }

              $disp_titulo = '- ' . $disp_titulo;

              if (strlen($disp_titulo) > 15) {
                $disp_titulo = substr($disp_titulo, 0, 12) . '...';
              }

              $temp_var++;
              $compromisso_geral .= "<span class='textoAgenda' id='comp_{$temp_var}'>$disp_titulo</span><br>";

              if ($compromisso['importante'] && strtotime($compromisso['data_inicio']) > time()) {
                $endScript .= "x[{$temp_var}]= {$temp_var};";
              }
            }

            $qtd++;
          }
        }

        if ($compromisso_geral && $dataAtual > date('Y/m/d', time()) &&
          date('Y/m/d', strtotime(substr($dataAtual, 0, 19)) - 3 * 86400) >
          date('Y/m/d', time())) {
          $classe = 'fundoCalendarioLonge';
        }
        elseif ($compromisso_geral && $dataAtual > date('Y/m/d', time()) &&
          date('Y/m/d', strtotime(substr($dataAtual, 0, 19)) - 3 * 86400) <=
          date('Y/m/d', time())) {
          $classe = 'fundoCalendarioProximo';
        }
        elseif ($compromisso_geral && $dataAtual == date('Y/m/d',time())) {
          $classe = 'fundoCalendarioUrgente';
        }
        else {
          $classe = 'fundoCalendario';
        }

        if ($qtd == 2) {
          $pulaLinha = '<br>';
        }

        if($qtd == 1) {
          $pulaLinha = '<br><br>';
        }

        $data_temp = strtotime(substr($dataAtual, 0, 19));
        $compromisso_geral = ($compromisso_geral) ? "$compromisso_geral<a class='agenda-ver-todos' href='agenda.php?cod_agenda={$cod_agenda}&time=$data_temp' ><div align='center' class='textoAgenda'><b>Ver Todos</b></div>" : $compromisso_geral;

        if ($i == 1) {
          $ultimoDiaUltimoMes = date('t', mktime(0, 0, 0, $mes - 1, 1, $ano));
          $temp .= '<tr>';
          for($dias = 0; $dias < $diaDaSemana; $dias++) {
            $dia = $ultimoDiaUltimoMes - $diaDaSemana + 1 + $dias;
            $temp .= "<td class='fundoCalendarioMesDiferente' valign='top'><span class='diasMes'>$dia</span></td>";
          }

          $temp .= "<td class='$classe' valign='top'><div class='dia_agenda'> " . $this->addLeadingZero($i) . " </div>$compromisso_geral</td>";
        }
        else {
          if($diaDaSemana == 0) {
            $temp .= "</tr><tr><td class='$classe' valign='top'><div class='dia_agenda'> " . $this->addLeadingZero($i) . " </div>$compromisso_geral</td>";
          }
          else {
            $temp .= "<td class='$classe' valign='top'><div class='dia_agenda'> " . $this->addLeadingZero($i) . " </div>$compromisso_geral</td>";
          }
        }
      }

      $endScript .= "setInterval('pisca();', 1000);";
      $dia = 1;

      for ($i = $diaDaSemana; $i < 6; $i++) {
        $temp .= "<td class='fundoCalendarioMesDiferente' valign='top'><span class='diasMes'>$dia</span></td>";
        $dia++;
      }

      $anterior = $mes - 1;
      $proximo  = $mes + 1;

      $proximo_ano = date('Y', mktime(0, 0, 0, $mes + 2, 0, $ano));
      $ano_anterior = date('Y', mktime(0, 0, 0, $mes, 0, $ano));

      if ($proximo > 12) {
        $proximo = 1;
      }

      if ($anterior < 1) {
        $anterior = 12;
      }

      $endScript .= "
        var a = 0;
        function pisca()
        {
          for (var i = 1; i<x.length; i++) {
            if (typeof document.getElementById('comp_' + x[i]) == 'object') {
              obj = document.getElementById('comp_' + x[i]);
              obj.className = (obj.className == 'textoAgenda') ? 'textoAgendaVermelho': 'textoAgenda';
            }
          }
          a=1;
        }
      </script>";

      $temp .= "</tr>
      <tr>
        <td colspan=\"6\" align=\"center\" class=\"fundoCalendarioTopo\">
          <a class=\"nav_agenda\" href='index.php?mes=$anterior&ano=$ano_anterior'>
            &laquo; Anterior
          </a> &nbsp;&nbsp;
          <a class=\"nav_agenda\" href='index.php?mes=$proximo&ano=$proximo_ano'>
            Próximo &raquo;
          </a>
        </td>
        <td align=center class='fundoCalendarioTopo'>$mes/$ano</td>
      </tr>
      <tr>
        <td colspan=\"7\" class=\"fundoCalendario\" style=\"height:15px; background-color: #fff;\"><h3 style=\"padding-top:2px;margin:0px\">Agenda do(a): <a href=\"agenda.php?cod_agenda={$cod_agenda}\">$nomeAgenda</a></h3></td>
      </tr>
      </table>{$endScript}";
      return $temp;
    }
    else {
      header('Location: logof.php?login=1');
      die();
    }
  }
  function mostraModalPesquisa($municipio, $linkPesquisa){
    return '<script type="text/javascript" src="scripts/jquery/jquery-1.8.3.min.js"></script>
          <link rel="stylesheet" href="scripts/jquery/jquery-ui.min-1.9.2/css/custom/jquery-ui-1.9.2.custom.min.css">
          <script src="scripts/jquery/jquery-ui.min-1.9.2/js/jquery-ui-1.9.2.custom.min.js"></script>
          <div id="dialog" title="Pesquisa de satisfa&ccedil;&atilde;o">
            <p>Caro usu&aacute;rio(a), a Secretaria Mun. de Educa&ccedil;&atilde;o e a Portabilis Tecnologia, convidam voc&ecirc; a responder a uma pesquisa de satisfa&ccedil;&atilde;o referente ao projeto de moderniza&ccedil;&atilde;o da gest&atilde;o escolar com o i-Educar em ' . $municipio . '.</p>
            <br/>
            <p><i>Voc&ecirc; precisar&aacute; de apenas 5 minutos para responder a pesquisa. :)</i></p>
            <br/>
            <p><b> Contamos com voc&ecirc;! :)</b></p>
          </div>
          <script>
          var $j = jQuery.noConflict();

          $j(function() {
            $j( "#dialog" ).dialog({
              width: 600,
              position: { my: "center", at: "top", of: window },
              buttons: [
                {
                  text: "Participar da pesquisa",
                  click: function(){
                    window.open(' . '"' . $linkPesquisa . '"' . ' , "_blank");
                    $j(this).dialog("close");
                  }
                },
                {
                  text: "N\u00e3o, obrigado",
                  click: function(){
                    $j(this).dialog("close");
                  }
                }
              ]
            });
          });
         </script>';
  }
}

// Instancia objeto de pĂĄgina
$pagina = new clsIndex();

// Instancia objeto de conteĂşdo
$miolo = new indice();

// Atribui o conteĂşdo Ă   pĂĄgina
$pagina->addForm($miolo);

// Gera o cĂłdigo HTML
$pagina->MakeAll();
