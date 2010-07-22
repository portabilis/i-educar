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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Espelho de Notas Anual');
    $this->processoAp = 807;
    $this->renderMenu = FALSE;
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

  /**
   * @var RegraAvaliacao_Model_Regra
   */
  var $regra = NULL;

  /**
   * @var array
   */
  static $boletim = array();

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_cod_curso;

  var $semestre;
  var $is_padrao;

  var $ano;

  var $cursos = array();

  var $get_link;

  function renderHTML()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_turma)) {
      echo '<script>
              alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
              window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
            </script>';

      return TRUE;
    }

    if ($this->ref_cod_escola){
      $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];

      $obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
      $det_instituicao = $obj_instituicao->detalhe();
      $this->nm_instituicao = $det_instituicao['nm_instituicao'];
    }

    $obj_calendario   = new clsPmieducarEscolaAnoLetivo();
    $lista_calendario = $obj_calendario->lista($this->ref_cod_escola, $this->ano,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $obj_turma->detalhe();
    $this->nm_turma = $det_turma['nm_turma'];

    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

    // Regra da série
    $regraMapper = new RegraAvaliacao_Model_RegraDataMapper();
    $this->regra = $regraMapper->find($det_serie['regra_avaliacao_id']);

    $obj_pessoa = new clsPessoa_($det_turma['ref_cod_regente']);
    $det = $obj_pessoa->detalhe();
    $this->nm_professor = $det['nome'];

    if (!$lista_calendario) {
      echo '<script>
             alert("Escola não possui calendário definido para este ano");
             window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
           </script>';

      return TRUE;
    }

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    $this->nm_curso = $det_curso['nm_curso'];

    $obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao($det_curso['ref_cod_tipo_avaliacao']);
    $det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
    $conceitual = $det_tipo_avaliacao['conceitual'];

    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
    $obj_matricula_turma->setOrderby('nome_ascii');

    $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
      $this->ref_cod_turma, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie,
      $this->ref_cod_curso, $this->ref_cod_escola, $this->ref_cod_instituicao,
      NULL, NULL, array(1,2,3), NULL, NULL, $this->ano, NULL, NULL, NULL, NULL, TRUE);

    // Recupera os componentes curriculares da turma
    $componentes = App_Model_IedFinder::getComponentesTurma(
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_turma
    );

    // Recupera a quantidade de módulos e o nome do módulo da escola/turma
    $modulo = App_Model_IedFinder::getModulo($this->ref_cod_escola,
      $this->ref_cod_curso, $this->ref_cod_turma, $this->ano);

    $nomeModulo = $modulo['nome'][0];
    $modulos    = $modulo['total'];

    if ($lst_matricula_turma) {
      $relatorio = new relatorios('Espelho de Notas Ano - ' . $this->ano, 210,
        FALSE, 'Espelho de Notas', 'A4',
        "{$this->nm_instituicao}\n{$this->nm_escola}\n{$this->nm_curso}\n{$this->nm_serie} -  Turma: $this->nm_turma         " . date('d/m/Y'));

      $relatorio->setMargem(20, 20, 20, 20);
      $relatorio->exibe_produzido_por = FALSE;

      $array_val = array(
        array(40, 'Cód.'),
        array(160, 'Nome do Aluno')
      );

      foreach (range(1, $modulos) as $num) {
        $array_val[] = array(30, $num . $nomeModulo);
      }

      $array_val[] = array(55, 'M.Parcial');
      $array_val[] = array(50, 'Exame');
      $array_val[] = array(50, 'M.Final');
      $array_val[] = array(38, 'Faltas');

      $arrFuncBody = '
        $values = array();
        foreach ($data as $d) {
          $values[] = $d[$index];
        }
        return $values;
      ';

      $arrFunc = create_function('$data, $index', $arrFuncBody);

      foreach ($componentes as $componente) {
        $relatorio->novalinha(array($componente->nome), 0, 16,
          TRUE, 'arial', array(400), '#515151', '#D3D3D3', '#FFFFFF', FALSE, TRUE);

        $relatorio->novalinha($arrFunc($array_val, 1), 0, 16, TRUE, 'arial',
          $arrFunc($array_val, 0), '#515151', '#D3D3D3',
          '#FFFFFF', FALSE, TRUE);

        foreach ($lst_matricula_turma as $matricula) {
          $codMatricula = $matricula['ref_cod_matricula'];

          if (!isset($this->boletim[$codMatricula])) {
            $boletim = new Avaliacao_Service_Boletim(array(
              'matricula'            => $codMatricula,
              'RegraDataMapper'      => $regraMapper
            ));
          }
          else {
            $boletim = $this->boletim[$codMatricula];
          }

          $media_final = $media_parcial = $nota_exame = '';
          $medias = $boletim->getMediasComponentes();
          $notas  = $boletim->getNotasComponentes();

          if ($boletim->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $faltas = array_sum(CoreExt_Entity::entityFilterAttr(
              $boletim->getFaltasGerais(), 'id', 'quantidade'
            ));
          }
          else {
            $faltas = $boletim->getFaltasComponentes();

            if (isset($faltas[$componente->id])) {
              $faltas = array_sum(CoreExt_Entity::entityFilterAttr(
                $faltas[$componente->id], 'id', 'quantidade'
              ));
            }
            else {
              $faltas = '';
            }
          }

          $etapas = range(1, count($notas[$componente->id]));

          // Se tiver mais etapas nas notas lançadas, significa que prestou exame
          if (count($etapas) > $modulos) {
            array_pop($etapas);
          }

          $data = array(
            array(40, $matricula['ref_cod_aluno']),
            array(160, $matricula['nome'])
          );

          foreach ($etapas as $i) {
            $data[] = array(30, $boletim->getNotaComponente($componente->id, $i)->notaArredondada);
            $media_parcial = $medias[$componente->id][0]->mediaArredondada;

            if ($i == $modulos) {
              $media_final   = $media_parcial;
              $media_parcial = '';
              $nota_exame    = $boletim->getNotaComponente($componente->id, 'Rc')->notaArredondada;
            }
          }

          // Adiciona entradas em branco no array de dados
          for ($i = 0, $loop = $modulos - count($etapas); $i < $loop; $i++) {
            $data[] = array(30, '');
          }

          $data[] = array(55, $media_parcial);
          $data[] = array(50, $nota_exame);
          $data[] = array(50, $media_final);
          $data[] = array(38, $faltas);

          $relatorio->novalinha($arrFunc($data, 1), 0, 12, FALSE, 'arial',
            $arrFunc($data, 0), '#515151', '#D3D3D3', '#FFFFFF', FALSE, TRUE);
        }

        $relatorio->quebraPagina();
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