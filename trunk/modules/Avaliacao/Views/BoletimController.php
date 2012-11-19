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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'Core/Controller/Page/ViewController.php';
require_once 'App/Model/IedFinder.php';
require_once 'Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'Avaliacao/Model/FaltaAlunoDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'App/Model/MatriculaSituacao.php';

require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarTurma.inc.php';

/**
 * BoletimController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class BoletimController extends Core_Controller_Page_ViewController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo     = 'Avaliação do aluno';
  protected $_processoAp = 642;

  /**
   * @var Avaliacao_Service_Boletim
   */
  protected $_service = NULL;

  /**z
   * @var stdClass
   */
  protected $_situacao = NULL;

  /**
   * Construtor.
   */
  public function __construct()
  {
    // Id do usuário na session
    $usuario = $this->getSession()->id_pessoa;

    $this->_service = new Avaliacao_Service_Boletim(array(
      'matricula' => $this->getRequest()->matricula,
      'usuario'   => $usuario
    ));

    $this->_situacao = $this->_service->getSituacaoAluno();

    // Se o parâmetro for passado, chama método para promover o aluno
    if (!is_null($this->getRequest()->promove)) {
      try {
        $this->_service->promover((bool) $this->getRequest()->promove);

        // Instancia o boletim para carregar service com as alterações efetuadas
        $this->_service = new Avaliacao_Service_Boletim(array(
          'matricula' => $this->getRequest()->matricula,
          'usuario' => $usuario
        ));
      }
      catch (CoreExt_Service_Exception $e) {
        // Ok, situação do aluno pode estar em andamento ou matrícula já foi promovida
      }
    }

    parent::__construct();
  }

  /**
   * Verifica um array de situações de componentes curriculares e retorna TRUE
   * quando ao menos um dos componentes estiver encerrado (aprovado ou reprovado).
   *
   * @param array $componentes
   * @return bool
   */
  protected function _componenteEncerrado(array $componentes)
  {
    foreach ($componentes as $situacao) {
      switch ($situacao->situacao) {
        case App_Model_MatriculaSituacao::APROVADO:
        case App_Model_MatriculaSituacao::APROVADO_APOS_EXAME:
        case App_Model_MatriculaSituacao::REPROVADO:
          return TRUE;
          break;
        default:
          break;
      }
    }

    return FALSE;
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    // Dados da matrícula
    $matricula = $this->_service->getOption('matriculaData');

    // Nome do aluno
    $nome   = $matricula['nome'];

    // Nome da escola
    $escola = new clsPmieducarEscola($matricula['ref_ref_cod_escola']);
    $escola = $escola->detalhe();
    $escola = ucwords(strtolower($escola['nome']));

    // Nome do curso
    $curso = $matricula['curso_nome'];

    // Nome da série
    $serie = $matricula['serie_nome'];

    // Nome da turma
    $turma = $matricula['turma_nome'];

    // Situação da matrícula
    $situacao = App_Model_MatriculaSituacao::getInstance();
    $situacao = $situacao->getValue($matricula['aprovado']);

    // Dados da matrícula
    $this->addDetalhe(array('Aluno', $nome));
    $this->addDetalhe(array('Escola', $escola));
    $this->addDetalhe(array('Curso', $curso));
    $this->addDetalhe(array('Série/Turma', $serie . ' / ' . $turma));
    $this->addDetalhe(array('Situação', $situacao));

    // Booleano para saber se o tipo de nota é nenhum.
    $nenhumaNota = ($this->_service->getRegra()->get('tipoNota') ==
      RegraAvaliacao_Model_Nota_TipoValor::NENHUM);

    // Booleano para saber o tipo de presença em que ocorre apuração
    $porComponente = ($this->_service->getRegra()->get('tipoPresenca') ==
      RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    // Dados da regra de avaliação
    $this->addDetalhe(array('Regra avaliação', $this->_service->getRegra()));
    $this->addDetalhe(array('Apuração de falta', $this->_service->getRegra()->tipoPresenca));
    $this->addDetalhe(array('Parecer descritivo', $this->_service->getRegra()->parecerDescritivo));
    $this->addDetalhe(array('Progressão', $this->_service->getRegra()->tipoProgressao));

    if ($nenhumaNota) {
      $media = 'Não usa nota';
    }
    else {
      $media = $this->_service->getRegra()->media;
    }
    $this->addDetalhe(array('Média', $media));

    // Cria um array com a quantidade de etapas de 1 a n
    $etapas = $this->getEtapas();

    // Atributos para a tabela
    $attributes = array(
      'style' => 'background-color: #A1B3BD; padding: 5px; text-align: center'
    );

    // Atributos para a tabela de notas/faltas
    $zebra = array(
      0 => array('style' => 'background-color: #E4E9ED'),
      1 => array('style' => 'background-color: #FFFFFF')
    );

    // Helper para criar links e urls
    $url = CoreExt_View_Helper_UrlHelper::getInstance();

    // Usa helper de tabela para criar a tabela de notas/faltas
    $table = CoreExt_View_Helper_TableHelper::getInstance();

    // Enum para situação de matrícula
    $situacao = App_Model_MatriculaSituacao::getInstance();

    // Situação do boletim do aluno
    $sit = $this->_situacao;

    // Títulos da tabela
    $labels = array();
    $labels[] = array('data' => 'Disciplinas', 'attributes' => $attributes);

    foreach ($etapas as $etapa) {
      $data = array('data' => sprintf('Etapa %d', $etapa));

      if ($nenhumaNota) {
        $data['colspan'] = 1;
      }
      else {
        $data['colspan'] = $porComponente ? 2 : 1;
      }


      $data['attributes'] = $attributes;
      $labels[] = $data;
    }

    // Flag para auxiliar na composição da tabela em casos onde o parecer
    // descritivo é lançado anualmente e por componente
    $parecerComponenteAnual = FALSE;
    $colspan = 0;

    if ($this->_service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
      if (TRUE == $this->_componenteEncerrado($sit->nota->componentesCurriculares)) {
        $parecerComponenteAnual = TRUE;
        $colspan++;
      }
    }

    // Colspan para tabela com labels e sublabels
    $colspan += $porComponente && $this->alunoPossuiNotaRec() ? 4 : 3;
    if ($nenhumaNota) {
      $colspan--;
    }

    if (! $nenhumaNota) {
      $labels[] = array('data' => $porComponente ? '' : 'Média', 'attributes' => $attributes, 'colspan' => $porComponente ? $colspan : 1);
    }

    // Inclui coluna para % de presença geral.
    if (!$porComponente) {
      if ($this->alunoPossuiNotaRec()) {
        $labels[] = array('data' => 'Exame', 'attributes' => $attributes);
      }

      if ($parecerComponenteAnual) {
        $labels[] = array('data' => 'Parecer', 'attributes' => $attributes);
      }

      $labels[] = array('data' => 'Presença', 'attributes' => $attributes);
      $labels[] = array('data' => 'Situação', 'attributes' => $attributes);
    }

    $table->addHeaderRow($labels);

    // Cria sub-header caso tenha faltas lançadas por componentes
    if ($porComponente) {
      $subLabels = array();
      $subLabels[] = array('attributes' => $attributes);
      for ($i = 0, $loop = count($etapas); $i < $loop; $i++) {
        if (! $nenhumaNota) {
          $subLabels[] = array('data' => 'Nota', 'attributes' => $attributes);
        }
        $subLabels[] = array('data' => 'Falta', 'attributes' => $attributes);
      }

      if (! $nenhumaNota) {
        $subLabels[] = array('data' => 'Média', 'attributes' => $attributes);
      }

      if ($this->alunoPossuiNotaRec()) {
        $subLabels[] = array('data' => 'Exame', 'attributes' => $attributes);
      }

      if ($porComponente) {
        if ($parecerComponenteAnual) {
          $subLabels[] = array('data' => 'Parecer', 'attributes' => $attributes);
        }

        $subLabels[] = array('data' => 'Presença', 'attributes' => $attributes);
        $subLabels[] = array('data' => 'Situação', 'attributes' => $attributes);
      }

      $table->addHeaderRow($subLabels);
    }

    // Atributos usados pelos itens de dados
    $attributes = array('style' => 'padding: 5px; text-align: center');

    // Notas
    $componentes = $this->getComponentesCurriculares();
    $notasComponentes  = $this->getNotasComponentesCurriculares();
    $mediasSituacoes   = $this->_situacao->nota;
    $mediasComponentes = $this->_service->getMediasComponentes();
    $faltasComponentes = $this->_service->getFaltasComponentes();

    // Calcula as porcentagens de presença
    $faltasStats = $this->_service->getSituacaoFaltas();

    // Texto do link
    if ($nenhumaNota) {
      $linkText = 'falta';
      $linkPath = 'falta';
    }
    else {
      $linkText = ($porComponente ? 'nota/falta' : 'nota');
      $linkPath = 'nota';
    }

    // Parâmetros para o link de nota/falta nova
    $newLink = array(
      'text'  => 'Lançar ' . $linkText,
      'path'  => $linkPath,
      'query' => array(
        'matricula' => $matricula['cod_matricula'],
        'componenteCurricular' => 0
      )
    );

    $iteration = 0;
    foreach ($componentes as $id => $componente) {
      $data = array();

      // Nome do componente curricular
      $data[] = array('data' => $componente, 'attributes' => array('style' => 'padding: 5px; text-align: left'));

      $notas         = $notasComponentes[$id];
      $mediaSituacao = $mediasSituacoes->componentesCurriculares[$id];
      $medias        = $mediasComponentes[$id];
      $faltas        = $faltasComponentes[$id];
      $faltaStats    = $faltasStats->componentesCurriculares[$id];
      $parecer       = NULL;

      // Caso os pareceres sejam por componente e anuais, recupera a instância
      if ($parecerComponenteAnual) {
        $parecer = $this->_service->getPareceresComponentes();
        $parecer = $parecer[$id];
      }

      if ($porComponente == TRUE) {
        $new = $url->l('Lançar nota', 'nota',
          array('query' =>
            array('matricula' => $matricula['cod_matricula'], 'componenteCurricular' => $id)
          )
        );
      }

      $newLink['query']['componenteCurricular'] = $id;
      $new = $url->l($newLink['text'], $newLink['path'], array('query' => $newLink['query']));

      $update = array('query' => array(
        'matricula' => $matricula['cod_matricula'],
        'componenteCurricular' => $id,
        'etapa' => 0
      ));

      // Lista as notas do componente por etapa
      for ($i = 0, $loop = count($etapas); $i < $loop; $i++) {
        $nota = $falta = NULL;

        if (isset($notas[$i])) {
          $update['query']['etapa'] = $notas[$i]->etapa;
          $nota = $url->l($notas[$i]->notaArredondada, 'nota', $update);
        }

        if (isset($faltas[$i])) {
          $update['query']['etapa'] = $faltas[$i]->etapa;
          $linkPath = $nenhumaNota ? 'falta' : 'nota';
          $falta = $url->l($faltas[$i]->quantidade, $linkPath, $update);
        }

        /*
         * Exibição muito dinâmica. Em resumo, os casos são:
         *
         * 1. nota & falta componente
         * 2. nota
         * 3. falta componente
         * 4. falta geral
         */
        if ($nenhumaNota) {
          $colspan = 1;
        }
        elseif (! $nenhumaNota && $porComponente && is_null($falta)) {
          $colspan = 2;
        }
        else {
          $colspan = 1;
        }

        // Caso 1.
        if (! $nenhumaNota) {
          if ($nota) {
            // Caso 2: resolvido com colspan.
            $data[] = array('data' => $nota, 'attributes' => $attributes, 'colspan' => $colspan);

            if ($porComponente) {
              $data[] = array('data' => $falta, 'attributes' => $attributes);
            }
          }
          else {
            $data[] = array('data' => $new, 'attributes' => $attributes, 'colspan' => $colspan);
            $new = '';
          }
        }
        // Caso 3.
        elseif ($nenhumaNota && $porComponente) {
          if ($falta) {
            $data[] = array('data' => $falta, 'attributes' => $attributes, 'colspan' => $colspan);
          }
          else {
            $data[] = array('data' => $new, 'attributes' => $attributes, 'colspan' => $colspan);
            $new = '';
          }
        }
        // Caso 4.
        else {
          $data[] = array('data' => '', 'attributes' => $attributes);
        }
      }

      // Média no componente curricular
      if (! $nenhumaNota) {
        $media = $medias[0]->mediaArredondada . ($medias[0]->etapa == 'Rc' ? ' (Rc)' : '');
        $data[] = array('data' => $media, 'attributes' => $attributes);
      }

      // Adiciona uma coluna extra caso aluno esteja em exame em alguma componente curricular ou possua nota de exame
      if ($sit->recuperacao || $this->alunoPossuiNotaRec()) {
        if ($mediaSituacao->situacao == App_Model_MatriculaSituacao::EM_EXAME ||
            $mediaSituacao->situacao == App_Model_MatriculaSituacao::APROVADO_APOS_EXAME ||
            $mediaSituacao->situacao == App_Model_MatriculaSituacao::REPROVADO) {
          $link = $newLink;
          $link['query']['componenteCurricular'] = $id;
          $link['query']['etapa'] = 'Rc';

          if (isset($notas[$i]) && $notas[$i]->etapa == 'Rc') {
            $link['text'] = $notas[$i]->notaArredondada;
          }

          $recuperacaoLink = $url->l($link['text'], $link['path'], array('query' => $link['query']));
          $data[] = array('data' => $recuperacaoLink, 'attributes' => $attributes);
        }
        else {
          $data[] = array('data' => '', 'attributes' => $attributes);
        }
      }

      // Adiciona uma coluna extra caso o parecer seja por componente ao final do ano
      if ($parecerComponenteAnual) {
        $link = array(
          'text'  => '',
          'path'  => 'parecer',
          'query' => array('matricula' => $this->getRequest()->matricula)
        );

        if (0 == count($parecer)) {
          $text = 'Lançar';
        }
        else {
          $text = 'Editar';
        }

        $link['query']['componenteCurricular'] = $id;

        // @todo Constante ou CoreExt_Enum
        $link['query']['etapa'] = 'An';

        $link = $url->l($text, $link['path'], array('query' => $link['query']));

        if (isset($mediaSituacao->situacao) && $mediaSituacao->situacao != App_Model_MatriculaSituacao::EM_ANDAMENTO) {
          $data[] = array('data' => $link, 'attributes' => $attributes);
        }
        else {
          $data[] = array('data' => '', 'attributes' => $attributes);
        }
      }

      // Informações extras como porcentagem de presença e situação do aluno
      if ($porComponente) {
        $data[] = array('data' => sprintf('%.2f%%', $faltaStats->porcentagemPresenca), 'attributes' => $attributes);
      }
      else {
        $data[] = array('data' => '', 'attributes' => $attributes);
      }

      $data[] = array('data' => $situacao->getValue($mediaSituacao->situacao), 'attributes' => $attributes);

      $iteration++;
      $class = $iteration % 2;

      $table->addBodyRow($data, $zebra[$class]);
    }

    $newLink = array(
      'text'  => 'Lançar falta',
      'path'  => 'falta',
      'query' => array('matricula' => $matricula['cod_matricula'])
    );

    // Situação geral das faltas
    $data = array(0 => array('data' => 'Faltas', 'attributes' => array('style' => 'padding: 5px; text-align: left')));
    $faltas = $this->_service->getFaltasGerais();
    $new = $url->l($newLink['text'], $newLink['path'], array('query' => $newLink['query']));

    // Listas faltas (para faltas no geral)
    for ($i = 1, $loop = count($etapas); $i <= $loop; $i++) {
      if (isset($faltas[$i])) {
        $link = $newLink;
        $link['query']['etapa'] = $faltas[$i]->etapa;
        $link = $porComponente ? '' : $url->l($faltas[$i]->quantidade, $link['path'], array('query' => $link['query']));
        $data[] = array('data' => $link, 'attributes' => $attributes);

        if ($porComponente) {
          $data[] = array('data' => '', 'attributes' => $attributes);
        }
      }
      else {
        $new = $porComponente ? '' : $new;
        $data[] = array('data' => $new, 'attributes' => $attributes);
        $new = '';

        if ($porComponente && ! $nenhumaNota) {
          $data[] = array('data' => '', 'attributes' => $attributes);
        }
      }
    }

    if (! $nenhumaNota) {
      $data[] = array();
    }

    if ($this->alunoPossuiNotaRec()) {
      $data[] = array('data' => '', 'attributes' => $attributes);
    }

    if ($parecerComponenteAnual) {
      $data[] = array('data' => '', 'attributes' => $attributes);
    }

    // Porcentagem presença
    $data[] = array('data' => sprintf('%.2f%%', $faltasStats->porcentagemPresenca), 'attributes' => $attributes);
    $data[] = array('data' => $situacao->getValue($sit->falta->situacao), 'attributes' => $attributes);

    $table->addFooterRow($data, $zebra[$class ^ 1]);

    // Adiciona linha com links para lançamento de parecer descritivo geral por etapa
    if ($this->_service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL) {
      $newLink = array(
        'text'  => 'Lançar parecer',
        'path'  => 'parecer',
        'query' => array('matricula' => $matricula['cod_matricula'])
      );

      $data = array(0 => array('data' => 'Pareceres', 'attributes' => array('style' => 'padding: 5px; text-align: left')));
      $pareceres = $this->_service->getPareceresGerais();

      for ($i = 1, $loop = count($etapas); $i <= $loop; $i++) {
        if (isset($pareceres[$i])) {
          $link = $newLink;
          $link['text'] = 'Editar parecer';
          $link['query']['etapa'] = $i;
          $data[] = array('data' => $url->l($link['text'], $link['path'], array('query' => $link['query'])), 'attributes' => $attributes);
        }
        else {
          if ('' == $newLink) {
            $link = '';
          }
          else {
            $link = $url->l($newLink['text'], $newLink['path'], array('query' => $newLink['query']));
          }
          $data[] = array('data' => $link, 'attributes' => $attributes);
          $newLink = '';
        }
      }

      if ($this->alunoPossuiNotaRec()) {
        $data[] = array('data' => '', 'attributes' => $attributes);
      }

      $data[] = array('data' => '', 'attributes' => $attributes);
      $data[] = array('data' => '', 'attributes' => $attributes);

      $table->addFooterRow($data);
    }

    // Adiciona tabela na página
    $this->addDetalhe(array('Disciplinas', '<div id="disciplinas">' . $table . '</div>'));

    // Adiciona link para lançamento de parecer descritivo anual geral
    if (
      FALSE == $sit->andamento &&
      $this->_service->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL
    ) {
      if (0 == count($this->_service->getPareceresGerais())) {
        $label = 'Lançar';
      }
      else {
        $label = 'Editar';
      }

      $link = array(
        'text'  => $label . ' parecer descritivo do aluno',
        'path'  => 'parecer',
        'query' => array('matricula' => $this->getRequest()->matricula)
      );
      $this->addDetalhe(array('Parecer descritivo anual', $url->l($link['text'], $link['path'], array('query' => $link['query']))));
    }

    // Caso o tipo de progressão seja manual, a situação das notas/faltas não
    // esteja mais em "andamento" e a matrícula esteja em andamento, exibe
    // botões de ação
    if (
      $this->_service->getRegra()->get('tipoProgressao') ==
        RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL &&
      FALSE == $sit->andamento && $matricula['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO
    ) {
      $link = array(
        'text' => 'sim',
        'path' => 'boletim',
        'query' => array(
          'matricula' => $this->getRequest()->matricula,
          'promove' => 1
        )
      );

      $sim = '<span class="confirm yes">' .
        $url->l($link['text'], $link['path'], array('query' => $link['query']))
        . '</span>';

      $link['text'] = 'não (retém o aluno)';
      $link['query']['promove'] = 0;

      $nao = '<span class="confirm no">' .
        $url->l($link['text'], $link['path'], array('query' => $link['query']))
        . '</span>';

      $links = '<div style="padding: 5px 0 5px 0">' . $sim . $nao . '</div>';

      $this->addDetalhe(array('Promover aluno?', $links));
    }
  }


  protected function getComponentesCurriculares(){
    if(! isset($this->_componentesCurriculares))
      $this->_componentesCurriculares = $this->_service->getComponentes();

    return $this->_componentesCurriculares;
  }


  protected function getNotasComponentesCurriculares(){
    if(! isset($this->_notasComponentesCurriculares))
      $this->_notasComponentesCurriculares = $this->_service->getNotasComponentes();

    return $this->_notasComponentesCurriculares;
  }


  protected function getEtapas(){
    if(! isset($this->_etapas))
      $this->_etapas = range(1, $this->_service->getOption('etapas'), 1);

    return $this->_etapas;
  }


  /**
  * caso algum componente curricular e alguma etapa possua nota exame lançada, então o aluno possui nota exame
  */
  protected function alunoPossuiNotaRec(){

    $notasComponentesCurriculares = $this->getNotasComponentesCurriculares();

    if (! isset($this->_alunoPossuiNotaRec)){
      foreach($this->getComponentesCurriculares() as $cc){
        $notasCc = $notasComponentesCurriculares[$cc->get('id')];

        foreach ($this->getEtapas() as $etapa){
          foreach($notasCc as $notaCc){
            if($notaCc->etapa == 'Rc'){
              $this->_alunoPossuiNotaRec = true;
              break;
            }
          }

          if (isset($this->_alunoPossuiNotaRec))
            break;
        }

        if (isset($this->_alunoPossuiNotaRec))
          break;
      }
    }

    return $this->_alunoPossuiNotaRec;
  }

}
