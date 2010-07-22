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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/relatorio.inc.php';

require_once 'App/Model/IedFinder.php';
require_once 'Avaliacao/Service/Boletim.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Espelho de Notas Bimestral');
    $this->processoAp         = 811;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
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

  var $regra = NULL;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_cod_curso;
  var $ref_cod_modulo;

  var $tipo;

  var $ano;

  var $is_padrao;
  var $semestre;

  var $cursos = array();

  var $get_link;

  function renderHTML()
  {
    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $this->ref_cod_modulo = explode('-', $this->ref_cod_modulo);
    $this->ref_cod_modulo = array_pop($this->ref_cod_modulo);

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_turma)) {
      echo '<script>
             alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
             window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
           </script>';

      return TRUE;
    }

    if ($this->ref_cod_escola) {
      $obj_escola      = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola      = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];

      $obj_instituicao      = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
      $det_instituicao      = $obj_instituicao->detalhe();
      $this->nm_instituicao = $det_instituicao['nm_instituicao'];
    }

    $obj_calendario = new clsPmieducarEscolaAnoLetivo();
    $lista_calendario = $obj_calendario->lista($this->ref_cod_escola, $this->ano,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $obj_turma->detalhe();
    $this->nm_turma = $det_turma['nm_turma'];

    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

    $obj_pessoa = new clsPessoa_($det_turma['ref_cod_regente']);
    $det        = $obj_pessoa->detalhe();
    $this->nm_professor = $det['nome'];

    //
    $regraMapper = new RegraAvaliacao_Model_RegraDataMapper();
    $this->regra = $regraMapper->find($det_serie['regra_avaliacao_id']);

    if (!$lista_calendario) {
      echo '<script>
             alert("Escola não possui calendário definido para este ano");
             window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
           </script>';

      return TRUE;
    }

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie, etapa_curso');
    $lista_serie_curso = $obj->lista(NULL, NULL, NULL, $this->ref_cod_curso,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    $this->nm_curso = $det_curso['nm_curso'];

    // Seleciona os alunos da turma
    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
    $obj_matricula_turma->setOrderby('nome_ascii');
    $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
      $this->ref_cod_turma, NULL, NULL, NULL, NULL, NULL, NULL, 1,
      $this->ref_cod_serie, $this->ref_cod_curso, $this->ref_cod_escola,
      $this->ref_cod_instituicao, NULL, NULL, array(1, 2, 3), NULL, NULL,
      $this->ano, NULL, NULL, NULL, NULL, TRUE, NULL, NULL, TRUE, NULL, $this->semestre);

    $componentes = $array_disc = $array_cab = array();
    if ('f' == $this->tipo && $this->regra->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
      $array_disc = $array_cab = array("FALTAS");
    }
    else {
      try {
        $componentes = App_Model_IedFinder::getComponentesTurma(
          $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_turma
        );
      }
      catch (App_Model_Exception $e) {
      }
    }

    if ($lst_matricula_turma) {
      $titulo = sprintf(
        'Espelho de Notas Bimestral %dº Bimestre Ano %d',
        $this->ref_cod_modulo, $this->ano
      );

      $subtitulo = sprintf(
        "%s\n%s\n%s\n%s -  Turma: %s             %s",
        $this->nm_instituicao, $this->nm_escola, $this->nm_curso,
        $this->nm_serie, $this->nm_turma, date('d/m/Y')
      );

      $relatorio = new relatorios(
        $titulo, 210, FALSE, 'Espelho de Notas Bimestral', 'A4', $subtitulo
      );

      $relatorio->setMargem(20, 20, 50, 50);
      $relatorio->exibe_produzido_por = FALSE;

      if (0 == count($array_disc) && 0 < count($componentes)) {
        foreach ($componentes as $componente) {
          $array_disc[$componente->id] = $componente;
          $array_cab[] = str2upper($componente->abreviatura);
        }

        asort($array_disc);
        sort($array_cab);
      }

      $array_cab = array_merge(array('Cód.', 'Nome do Aluno'), $array_cab);

      $divisoes       = array(40, 165);
      $divisoes_texto = array(40, 165);

      $tamanho_divisao = 32 + (10 - count($array_disc)) * 5;

      for ($ct = 0; $ct < 20; $ct++) {
        $divisoes[]       = $tamanho_divisao;
        $divisoes_texto[] = $tamanho_divisao;
      }

      $relatorio->novalinha($array_cab, 0, 16, TRUE, 'arial', $divisoes,
        '#515151', '#D3D3D3', '#FFFFFF', FALSE, TRUE);

      foreach ($lst_matricula_turma as $matricula) {
        $boletim = new Avaliacao_Service_Boletim(array(
          'matricula'            => $matricula['ref_cod_matricula'],
          'RegraDataMapper'      => $regraMapper
        ));

        $tam_fonte = NULL;
        $tam_linha = 16;

        $componentes = $boletim->getComponentes();

        foreach ($array_disc as $cid => $componente) {
          // Presença geral, seleciona apenas a quantidade de faltas da etapa
          if (0 == $cid) {
            $faltas[$cid] = $boletim->getFalta($this->ref_cod_modulo);
            break;
          }

          if (!in_array($cid, array_keys($componentes))) {
            $notas[$cid]  = 'D';
            $faltas[$cid] = 'D';
            continue;
          }

          $notas[$cid]  = $boletim->getNotaComponente($cid, $this->ref_cod_modulo);
          $faltas[$cid] = $boletim->getFalta($this->ref_cod_modulo, $cid);
        }

        // @todo WTF?!
        if (strlen($matricula['nome']) > 24) {
          $matricula['nome'] = explode(' ', $matricula['nome']);

          if (is_array($matricula['nome'])) {
            $nome_aluno = array_shift($matricula['nome']);
          }

          if (is_array($matricula['nome'])) {
            $nome_aluno .= ' ' . array_shift($matricula['nome']);
          }

          if (is_array($matricula['nome'])) {
            $nome_aluno .= ' ' . array_pop($matricula['nome']);
          }

          $matricula['nome'] = $nome_aluno;
        }

        unset($array_val);
        $array_val = array();
        $array_val[] = $matricula['ref_cod_aluno'];
        $array_val[] = $matricula['nome'];

        foreach ($array_disc as $cid => $disc) {
          if ($this->tipo == 'n') {
            $array_val[] = $notas[$cid]->notaArredondada;
            continue;
          }
          else {
            $array_val[] = $faltas[$cid]->quantidade;
            continue;
          }

          $array_val[] = '';
        }

        $relatorio->novalinha($array_val, 0, $tam_linha, FALSE, 'arial',
          $divisoes_texto, '#515151', '#d3d3d3', '#FFFFFF', FALSE, TRUE, NULL, $tam_fonte);
      }

      $this->get_link = $relatorio->fechaPdf();
    }

    echo sprintf('
      <script>
        window.onload=function()
        {
          parent.EscondeDiv("LoadImprimir");
          window.location="download.php?filename=%s"
        }
      </script>', $this->get_link);

    echo sprintf('
      <html>
        <center>
          Se o download não iniciar automaticamente <br>
          <a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
          <span style="font-size: 10px;">
            Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
            Clique na Imagem para Baixar o instalador<br><br>
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>
      </html>', $this->get_link);
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {
    return FALSE;
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