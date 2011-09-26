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
//  protected $_processoAp = 946;
  protected $_processoAp = 644;
  protected $_formMap  = array();


  //implementado funcoes setRegra e getRegraByName para carregar as regras no inicio do processamento
  //evitando fazer um select novo a cada aluno
  protected function setRegra($service)
  {
    if (! isset($this->_regra))
    {
      $this->_regra = array();
      $this->_regra['id'] = $service->getRegra()->get('id');
      $this->_regra['nome'] = $service->getRegra()->get('nome');
      $this->_regra['tipoNota'] = $service->getRegra()->get('tipoNota');
      $this->_regra['parecerDescritivo'] = $service->getRegra()->get('parecerDescritivo');
      $this->_regra['tipoPresenca'] = $service->getRegra()->get('tipoPresenca');
      //$this->_regra['formulaRecuperacao'] = $service->getRegra()->get('formulaRecuperacao');
      $this->_regra['quantidadeModulos'] = $service->getOption('etapas');
      $this->_regra['showFieldNotaExame'] = $service->getRegra()->get('formulaRecuperacao') == FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO && isset($_GET['etapa']) && $this->_regra['quantidadeModulos'] == $_GET['etapa'];
    }
  }

  protected function getRegraByName($name)
  {
    if (! array_key_exists($name, $this->_regra))
      throw new Exception("A regra de avaliação não possui alguma variavel com o nome '$name', erro ocorrido na função getRegraByName.");

    return $this->_regra[$name];
  }

  protected function setHeaders($service)
  {

    if (! isset($this->_headers))
    {
  
      $this->_headers = array("Matr&iacute;cula", "Aluno", "Situa&ccedil;&atilde;o");

      if ($this->getRegraByName('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM)
      {
        $this->_headers[] ="Nota";

        if ($this->getRegraByName('showFieldNotaExame'))
          $this->_headers[] = "Nota exame";

        #var_dump($service->getRegra());
      }

      $this->_headers[] =   "Falta *";

      if ($this->getRegraByName('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM)
        $this->_headers[] = "Parecer descritivo **";

      $this->_headers[] = "Status altera&ccedil;&atilde;o";
    }
    $this->addCabecalhos($this->_headers);
  }


  protected function setVars()
  {
    $this->ref_cod_aluno = $_GET['ref_cod_aluno'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];
    $this->ref_cod_curso = $_GET['ref_cod_curso'];
    $this->ref_cod_turma = $_GET['ref_cod_turma'];
    $this->ref_ref_cod_serie = $this->ref_cod_serie = $_GET['ref_ref_cod_serie'];
    $this->ano_escolar = $_GET['ano_escolar'];
    $this->ref_cod_componente_curricular = $_GET['ref_cod_componente_curricular'];
    $this->etapa = $_GET['etapa'];

    if ($this->ref_cod_aluno)
    {
      $nome_aluno_filtro = new clsPmieducarAluno();
      $nome_aluno_filtro = $nome_aluno_filtro->lista($int_cod_aluno = $this->ref_cod_aluno);
      $this->nm_aluno = $nome_aluno_filtro[0]['nome_aluno'];
    }
  }


  protected function setSelectionFields()
  {

    #TODO mover para funcao setSelectionFields() ?
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


  protected function getAlunos()
  {

    $alunos = new clsPmieducarMatriculaTurma();
    $alunos->setOrderby('nome');

    #FIXME pega só a ultima matricula ?
    #FIXME revisao todos parametros repassados, bool_escola_andamento passar false ?
    $alunos = $alunos->lista(
      $this->ref_cod_matricula,
      $this->ref_cod_turma,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $this->ref_ref_cod_serie,
      $this->ref_cod_curso,
      $this->ref_cod_escola,
      $this->ref_cod_instituicao,
      $this->ref_cod_aluno,
      NULL,
      NULL,
      NULL,
      NULL,
      $this->ano_escolar,
      NULL,
      TRUE,
      NULL,
      NULL,
      TRUE,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );

    if (! is_array($alunos))
      $alunos = array();

    return $alunos;
  }

  protected function getNota($service, $componenteCurricular, $etapa)
  {
    $nota = urldecode($service->getNotaComponente($componenteCurricular, $etapa)->nota);
    return str_replace(',', '.', $nota);
  }

  protected function getFieldNotaAluno($aluno, $service, $exame = false, $visible = true)
  {

    if ($exame)
      $etapa = 'Rc';
    else
      $etapa = $this->etapa;

    $att = $exame == 'Rc' ? "att='nota_exame'" : "att='nota'";
    $onChangeSelectNota = sprintf("setAtt(%s, matricula=%s, etapa='%s', componente_curricular=%s);",
                      $att, $aluno['ref_cod_matricula'], $etapa, $this->ref_cod_componente_curricular);

    //seleciona os valores de arredondamento apenas no primeiro aluno, pois todos estão matriculados na mesma turma (mesma regra)
    if (! isset($this->valoresArredondamento))    
      $this->valoresArredondamento = $service->getRegra()->tabelaArredondamento->findTabelaValor();

    $valores = array();
    foreach ($this->valoresArredondamento as $valor)
    {

      if ($this->getRegraByName('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA)
        $valores[(string) $valor->nome] = $valor->nome;
      else
        $valores[(string) $valor->valorMaximo] = $valor->nome . ' (' . $valor->descricao .  ')';
    }

    $notaAtual = $this->getNota($service, $this->ref_cod_componente_curricular, $etapa);

    $notas = "<option></optioen>";
    foreach ($valores as $k => $v)
    {
      $k = str_replace(',', '.', urldecode($k));

      if ($notaAtual > -1 && $k == $notaAtual)
        $notas .= "\n<option value='$k' selected='selected'>$v</option>";
      else
        $notas .= "\n<option value='$k'>$v</option>";
    }

    $hidden = $visible ? '' : 'hidden';
    $id = $exame ? "nota_exame-matricula:{$aluno['ref_cod_matricula']}" : "nota-matricula:{$aluno['ref_cod_matricula']}";

    return sprintf('<select id="%s" class="notas %s" onchange="%s">%s</select>', $id, $hidden, $onChangeSelectNota, $notas);
  }

 
  protected function getFieldNomeAluno($aluno)
  {
    return $aluno['ref_cod_aluno'] . ' - ' .  $aluno['nome'];
  }

  protected function getFieldSituacaoAluno($aluno, $service)
  {
    $situacao = App_Model_MatriculaSituacao::getInstance()->getValue(
          $service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->ref_cod_componente_curricular]->situacao);
    
    return sprintf('<span id="situacao-matricula:%s">%s</span>',   $aluno['ref_cod_matricula'],$situacao);
  }


  protected function getFieldFaltaAluno($aluno, $service)
  {
    $onChangeSelectFalta = sprintf("setAtt(att='falta', matricula=%s, etapa=%s, componente_curricular=%s);",
                  $aluno['ref_cod_matricula'], $this->etapa, $this->ref_cod_componente_curricular);

    if ($this->getRegraByName('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
        $_faltaAtual = $service->getFalta($this->etapa, $this->ref_cod_componente_curricular)->quantidade;
    elseif ($this->getRegraByName('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
        $_faltaAtual = $service->getFalta($this->etapa)->quantidade;

    $faltas = "<option></option>";
    foreach (range(0, 100, 1) as $f) {
      if ($_faltaAtual > -1 && $f == $_faltaAtual)
        $faltas .= "\n<option value='$f' selected='selected'>$f</option>";
      else
        $faltas .= "\n<option value='$f'>$f</option>";
    }
    return sprintf('<select id="falta-matricula:%s" class="faltas" onchange="%s">%s</select>',
              $aluno['ref_cod_matricula'], $onChangeSelectFalta, $faltas);
  }


  protected function getFieldParecerDescritivoAluno($aluno, $service)
  {
    if ($this->getRegraByName('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_DESCRITOR or
      $this->getRegraByName('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE or
      $this->getRegraByName('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)
    {
      $etapa_parecer = 'An';
      $onChangeParecer = sprintf("setAtt(att='parecer', matricula=%s, etapa='%s', componente_curricular=%s);",
                       $aluno['ref_cod_matricula'], $etapa_parecer, $this->ref_cod_componente_curricular);
    }
    else
    {
      $etapa_parecer = $this->etapa;
      $onChangeParecer = sprintf("setAtt(att='parecer', matricula=%s, etapa=%s, componente_curricular=%s);",
                       $aluno['ref_cod_matricula'], $etapa_parecer, $this->ref_cod_componente_curricular);
    }

    if ($this->getRegraByName('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE or
      $this->getRegraByName('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE)
    {
      $parecer = $service->getParecerDescritivo($etapa_parecer, $this->ref_cod_componente_curricular);
    }
    else
      $parecer = $service->getParecerDescritivo($etapa_parecer);

    return sprintf('<textarea id="parecer-matricula:%s" class="parecer" onchange="%s" cols="40" rows="10">%s</textarea>',
                  $aluno['ref_cod_matricula'], $onChangeParecer, utf8_decode($parecer));
  }


  protected function addLinhaAluno($aluno, $service)
  {
    $linha_aluno = array(
      $aluno['ref_cod_matricula'], 
      $this->getFieldNomeAluno($aluno), 
      $this->getFieldSituacaoAluno($aluno, $service)
    );

    if ($this->getRegraByName('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM)
    {
      $linha_aluno[] = $this->getFieldNotaAluno($aluno, $service);

      #TODO alterar nome showFieldNotaExame para hasFormulaRecuperacao ?
      if ($this->getRegraByName('showFieldNotaExame'))
      {
        $situacoesExibirExame = array(
          App_Model_MatriculaSituacao::EM_EXAME, 
          App_Model_MatriculaSituacao::APROVADO_APOS_EXAME
        );

        $emExame = in_array($service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->ref_cod_componente_curricular]->situacao, $situacoesExibirExame);

        $possuiNotaExame = $this->getNota($service, $this->ref_cod_componente_curricular, 'Rc') != '';
        $linha_aluno[] = $this->getFieldNotaAluno($aluno, $service, $exame = true, $visible = ($emExame || $possuiNotaExame));
      }
    }

    $linha_aluno[] = $this->getFieldFaltaAluno($aluno, $service);

    if ($this->getRegraByName('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM)
      $linha_aluno[] = $this->getFieldParecerDescritivoAluno($aluno, $service);

    $linha_aluno[] = sprintf('<span id="status_alteracao-matricula:%s"</spam>',   $aluno['ref_cod_matricula']);

    $this->addLinhas($linha_aluno);
  }


  protected function setRodapePagina($service)
  {
    $_tipoParecer = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance()->getValue($this->getRegraByName('parecerDescritivo'));
    if ($_tipoParecer)
      $_tipoParecer = '<br />** ' . $_tipoParecer;

    $_tipoPresenca = RegraAvaliacao_Model_TipoPresenca::getInstance()->getValue($this->getRegraByName('tipoPresenca'));
    $this->rodape = "* $_tipoPresenca $_tipoParecer<br />Regra avalia&ccedil;&atilde;o usada: {$this->getRegraByName('id')} - {$this->getRegraByName('nome')}";
  }


  public function Gerar()
  {

    $this->setVars();
    $this->setSelectionFields();

    if ($this->ref_cod_escola && $this->ref_cod_curso && $this->ref_cod_turma && $this->ref_ref_cod_serie && 
        $this->ano_escolar && $this->ref_cod_componente_curricular && $this->etapa)
    {

      $alunos = $this->getAlunos();

      if (count($alunos))
      {
        #TODO remover ?
        #$ref_cod_serie  = $nm_serie = $ref_cod_escola = $nm_escola = '';

        foreach ($alunos as $aluno)
        {
          $service = new Avaliacao_Service_Boletim(array('matricula' =>   $aluno['ref_cod_matricula'], 'usuario'   => $this->getSession()->id_pessoa));
          $this->setRegra($service);
          $this->addLinhaAluno($aluno, $service);
        }

        $this->setHeaders($service);
        $this->titulo = "Encontrado(s) " . count($alunos) . " aluno(s).";
        $this->setRodapePagina($service);
      }
    }
    else
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
            var __old_event = __bBusca.onclick;
            __bBusca.onclick = function()
            {
              var __not_empty_fields = document.getElementsByClassName('obrigatorio');
              var __all_filled = true;
              for (var i = 0; i < __not_empty_fields.length; i++)
              {
                if (! __not_empty_fields[i].value)
                {
                  var __all_filled = false;
                  break;
                }
              }
              if (! __all_filled)
                alert('Selecione um valor em todos os campos, antes de continuar.');
              else
              {
                __bBusca.disable();
                __bBusca.value = 'Carregando...';
                var form_filtro = document.getElementById('formcadastro');
                var form_resultado = document.getElementById('form_resultado');
                form_resultado.remove();                                
                
                form_filtro.action = '/module/Avaliacao/diario';
                form_filtro.submit();
              }
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

              document.getElementById('ref_cod_instituicao').onchange = function()
              {
                clearSelect(entity = 'ano_escolar', disable = false, text = '', multipleId = false);
                clearSelect(entity = 'curso', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'serie', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'turma', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'etapa', disable = false, text = '', multipleId = false);
                //getDuploEscolaCurso();
                getEscola();
              }

              document.getElementById('ref_cod_escola').onchange = function()
              {
                clearSelect(entity = 'ano_escolar', disable = false, text = '', multipleId = false);

                clearSelect(entity = 'curso', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'serie', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'turma', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
                clearSelect(entity = 'etapa', disable = false, text = '', multipleId = false);
                getEscolaCurso();
              }


            document.getElementById('ref_cod_curso').onchange = function()
            {
              clearSelect(entity = 'ano_escolar', disable = false, text = '', multipleId = false);
              clearSelect(entity = 'serie', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'turma', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'etapa', disable = false, text = '', multipleId = false);
              getAnoEscolar();
            }

            document.getElementById('ano_escolar').onchange = function()
            {
              clearSelect(entity = 'serie', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'turma', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'etapa', disable = false, text = '', multipleId = false);
              getEscolaCursoSerie();
            }

            document.getElementById('ref_ref_cod_serie').onchange = function()
            {
              clearSelect(entity = 'turma', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'etapa', disable = false, text = '', multipleId = false);
              getTurma();
            }

            document.getElementById('ref_cod_turma').onchange = function()
            {
              clearSelect(entity = 'componente_curricular', disable = false, text = '', multipleId = true);
              clearSelect(entity = 'etapa', disable = false, text = '', multipleId = false);
              getComponenteCurricular();
              getEtapa();
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
                if(! attValue.length && ! confirm('Confirma exclusão ' + att.replace('_', ' ') + '?'))
                  alert('Voltar nota...');
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
                  //console.log(vars);
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

