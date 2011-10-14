<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

#TODO remover includes desnecessarios
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'Core/Controller/Page/ListController.php';
require_once 'App/Model/IedFinder.php';
require_once 'Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'Avaliacao/Model/FaltaAlunoDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'App/Model/MatriculaSituacao.php';

require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';

#require_once 'include/portabilis_utils.php';

class DiarioController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Lan&ccedil;amento por turma';
  protected $_processoAp = 644;
  protected $_formMap  = array();


  protected function setSelectionFields()
  {

    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = True;
    $this->add_onchange_events = True;

    $get_escola = $escola_obrigatorio = $listar_escolas_alocacao_professor = TRUE;
    $get_ano_escolar = $ano_escolar_obrigatorio = TRUE;
    $get_curso = $curso_obrigatorio = $listar_somente_cursos_funcao_professor = TRUE;
    $get_escola_curso_serie = $escola_curso_serie_obrigatorio = TRUE;
    $get_turma = $turma_obrigatorio = $listar_turmas_periodo_alocacao_professor = TRUE;
    $get_componente_curricular = $componente_curricular_obrigatorio = $listar_componentes_curriculares_professor = TRUE;
    $get_etapa = $etapa_obrigatorio = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE,
      FALSE, FALSE, '', "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>", '', '', TRUE);

    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
  }

  
  public function Gerar()
  {

    $this->setSelectionFields();

    $this->rodape = "<strong>N&atilde;o est&aacute; sendo listado as op&ccedil;&otilde;es de filtro que voc&ecirc; espera ?</strong> solicite a(o) secret&aacute;rio(a) da escola que verifique a aloca&ccedil;&atilde;o do seu usu&aacute;rio.";

    $this->largura = '100%';
    $a = <<<EOT

        <style type="text/css">
          #formcadastro #nm_aluno, #formcadastro select {
            min-width: 400px;
          }

          .parecer {
            height: 80px;
          }
        </style>

        <script type="text/javascript">

            document.getElementById('botao_busca').value = 'Carregar';

            function pesquisa_aluno()
            {
              pesquisa_valores_popless('/intranet/educar_pesquisa_aluno.php')
            }

            var __bBusca = document.getElementById('botao_busca');
            __bBusca.onclick = function()
            {
              __bBusca.disable();
              __bBusca.value = 'Carregando...';
              var form_filtro = document.getElementById('formcadastro');
              var form_resultado = document.getElementById('form_resultado');
              form_resultado.remove();                                
              
              //form_filtro.action = '/module/Avaliacao/diario';
              //form_filtro.submit();
              alert('getMatriculas');
            }

            __lupa = document.getElementById('ref_cod_aluno_lupa');
            if (__lupa)
            {
              var __a = document.createElement('a');
              __a.innerHTML = 'Limpar filtros';
              __a.href = document.location.href.split('?')[0];
              __bBusca.parentNode.appendChild(__a);

              var __a = document.createElement('a');
              __a.innerHTML = ' Limpar';
              __a.onclick = function() {
                  __fieldIdAluno = document.getElementById('ref_cod_aluno');
                  if (__fieldIdAluno)
                    __fieldIdAluno.value = '';

                  __fieldNomeAluno = document.getElementById('nm_aluno');
                  if (__fieldNomeAluno)
                    __fieldNomeAluno.value = '';
                };
              __a.href = '#';
              __lupa.parentNode.appendChild(__a);
            }
            
              function _fixSelectsFilter(selectId)
              {
                //try
                //{
                  if (selectId)
                    var _ids = [selectId,];
                  else
                    var _ids = ['ref_cod_instituicao', 'ref_cod_escola', 'ref_cod_curso', 'ano_escolar', 'ref_ref_cod_serie', 'ref_cod_turma', 'etapa', 'ref_cod_componente_curricular', 'nm_aluno'];
                    
                  var w = 0;
                  for (var i=0; i< _ids.length; i++)
                  {
                    var s = document.getElementById(_ids[i]);
                    
                    try
                    {
                      if (s.offsetWidth > w)
                        w = s.offsetWidth;
                    }
                    catch(err) {/*ie :(*/ }
     
                    if (s && s.type == 'select-one')
                    {
                      s.size = s.length;
                      if (s.length == 2 && s.selectedIndex == 0)
                      {
                        s.selectedIndex = 1;
                        s.onchange();
                      }
                    }
                    else if (s && s.type == 'hidden' && s.id == 'ref_cod_instituicao')
                      _fixSelectsFilter('ref_cod_escola');
                  }

                  try
                  {
                    for (var i=0; i< _ids.length; i++)
                    {
                      var s = document.getElementById(_ids[i]);
                      
                      if (s.offsetWidth < w)
                        s.style.width = w + "px";
                    }
                  }
                  catch(err) { /*ie :(*/ }

                //}
              }

              document.getElementById('ref_cod_escola').afterchange = function()
              {
                _fixSelectsFilter('ref_cod_escola');
              }

              document.getElementById('ref_cod_escola').afterchange = function()
              {
                _fixSelectsFilter('ref_cod_escola');
              }

              document.getElementById('ref_cod_curso').afterchange = function()
              {
                _fixSelectsFilter('ref_cod_curso');
              }

              document.getElementById('ano_escolar').afterchange = function()
              {
                _fixSelectsFilter('ano_escolar');
              }

              document.getElementById('ref_ref_cod_serie').afterchange = function()
              {
                _fixSelectsFilter('ref_ref_cod_serie');
              }

              document.getElementById('ref_cod_turma').afterchange = function()
              {
                _fixSelectsFilter('ref_cod_turma');
              }

              document.getElementById('etapa').afterchange = function()
              {
                _fixSelectsFilter('etapa');
              }

              document.getElementById('ref_cod_componente_curricular').afterchange = function()
              {
                _fixSelectsFilter('ref_cod_componente_curricular');
              }

              document.getElementById('nm_aluno').onchange = function()
              {
                _fixSelectsFilter();
              }

            _fixSelectsFilter();
            document.getElementById('botao_busca').focus();

        </script>

        <script type="text/javascript" src="/modules/Avaliacao/Static/ajax.js"> </script>
        <script type="text/javascript" src="/modules/Avaliacao/Static/dom_utils.js"> </script>
        <script type="text/javascript">

          var ajaxReq = new AjaxRequest();

          function setAtt(att, matricula, etapa, componente_curricular)
          {
            try
            {
              var attElement = document.getElementById(att + '-matricula:' + matricula);
              var attValue = attElement.value;

              //Trava para evitar erro com o serviço do boletim
              if (att == 'parecer' && ((/^\d+\.\d+$/.test(attValue)) || (/^\d+$/.test(attValue)) || (/^\.\d+$/.test(attValue)) || (/^\d+\.$/.test(attValue))))
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">Informe pelo menos uma letra.</span>';
              else /* if (attValue.length)*/
              {

                if(! attValue.length)
                {
                  if (confirm('Confirma exclusão ' + att.replace('_', ' ') + '?'))
                  {
                    alert('Exluir (e selecionar opção em branco e atualizar situação aluno).');
                  }
                  else
                  {
                    alert('Não exluir, voltar para opção selecionada.');
                  }
                }
                else
                {
                  var _c = ['notas', 'faltas', 'parecer'];
                  for (var i = 0; i < _c.length; i++)
                  {
                    var _e = document.getElementsByClassName(_c[i]);
                    for (var j = 0; j < _e.length; j++)
                      _e[j].disabled = true;
                  }

                  document.getElementById('status_alteracao-matricula:'+matricula).innerHTML = 'Atualizando... <img src="/modules/Avaliacao/Static/images/min-wait.gif"/>';
                  var vars = "att="+att+"&matricula=" + matricula + "&etapa=" + etapa + "&componente_curricular=" + componente_curricular+"&att_value=" + attValue;

                  if (console)
                    console.log(vars);

                  //alert(vars);
                  ajaxReq.send("POST", "/module/Avaliacao/DiarioAjax", handleRequest, "application/x-www-form-urlencoded; charset=UTF-8", vars);
                }
              }
              /*else
              {
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">Selecione um valor v&aacute;lido.</span>';

              }*/
            }
            catch(err)
            {
              try
              {
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">ERRO1: Ocorreu um erro inesperado, por favor tente novamente.</span>';
                window.location.reload();
              }
              catch(err)
              {
                alert('ERRO2: Ocorreram erros inesperados, por favor tente novamente.');
                window.location.reload();
              }
            }
          }

          function handleRequest()
          {
            try
            {
              if (ajaxReq.getReadyState() == 4 && ajaxReq.getStatus() == 200)
              {
                var xmlData = ajaxReq.getResponseXML().getElementsByTagName("status")[0];
                var error = getText(xmlData.getElementsByTagName('error')[0]);

                if (error == '')
                {
                  var matricula = getText(xmlData.getElementsByTagName('matricula')[0]);
                  var att = getText(xmlData.getElementsByTagName('att')[0]);
                  document.getElementById(att + '-matricula:' + matricula).disabled = false;
                  document.getElementById('botao_busca').disabled = false;

                  var _c = ['notas', 'faltas', 'parecer'];
                  for (var i = 0; i < _c.length; i++)
                  {
                    var _e = document.getElementsByClassName(_c[i]);
                    for (var j = 0; j < _e.length; j++)
                      _e[j].disabled = false;
                  }

                  var situacao = getText(xmlData.getElementsByTagName('situacao')[0]);
                  document.getElementById('situacao'  + '-matricula:' + matricula).innerHTML = situacao;

                  var fieldExame = document.getElementById('nota_exame-matricula:' + matricula);
                  if (att == 'nota' && situacao.toLowerCase().trim() == 'em exame' && fieldExame)
                    fieldExame.setAttribute('class', fieldExame.getAttribute('class').replace('hidden', ''));   

                  var s = '<span class="success" style="color: green;">Atualizado</span>';
                  document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = s;
                }
                else
                {
                  alert("Ocorreram erros, por favor  verifique se a ação foi gravada ou tente novamente. Detalhes: " + error);
                  window.location.reload();
                }
              }
            }
            catch(err)
            {
                  alert("Ocorreram erros inesperados, por favor  verifique se a ação foi gravada ou tente novamente. Detalhes: " + err);
              window.location.reload();
            }
         }
</script>
EOT;

  $this->appendOutput(utf8_decode($a));
  }
}
?>

