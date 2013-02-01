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
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'RegraAvaliacao_Model_RegraDataMapper';
  protected $_titulo            = 'Cadastro de regra de avaliação';
  protected $_processoAp        = 947;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Instituição',
      'help'   => '',
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'Nome por extenso do componente.',
    ),
    'tipoNota' => array(
      'label'  => 'Sistema de nota',
      'help'   => ''
    ),
    'tipoProgressao' => array(
      'label'  => 'Progressão',
      'help'   => 'Selecione o método de progressão para a regra.'
    ),
    'tabelaArredondamento' => array(
      'label'  => 'Tabela de arredondamento de nota',
      'help'   => ''
    ),
    'media' => array(
      'label'  => 'Média final para promoção',
      'help'   => 'Informe a média necessária para promoção<br />
                   do aluno, aceita até 3 casas decimais. Exemplos: 5,00; 6,725, 6.<br >
                   Se o tipo de progressão for <b>"Progressiva"</b>, esse<br />
                   valor não será considerado.'
    ),
    'mediaRecuperacao' => array(
      'label'  => 'Média exame final para promoção',
      'help'   => 'Informe a média necessária para promoção<br />
                   do aluno, aceita até 3 casas decimais. Exemplos: 5,00; 6,725, 6.<br >
                   Desconsidere esse campo caso selecione o tipo de nota "conceitual"'
    ),
    'formulaMedia' => array(
      'label'  => 'Fórmula de cálculo da média',
      'help'   => '',
    ),
    'formulaRecuperacao' => array(
      'label'  => 'Fórmula de cálculo da média de recuperação',
      'help'   => '',
    ),
    'porcentagemPresenca' => array(
      'label'  => 'Porcentagem de presença',
      'help'   => 'A porcentagem de presença necessária para o aluno ser aprovado.<br />
                   Esse valor é desconsiderado caso o campo "Progressão" esteja como<br />
                   "Não progressiva automática - Somente média".<br />
                   Em porcentagem, exemplo: <b>75</b> ou <b>80,750</b>'
    ),
    'parecerDescritivo' => array(
      'label'  => 'Parecer descritivo',
      'help'   => '',
    ),
    'tipoPresenca' => array(
      'label'  => 'Apuração de presença',
      'help'   => ''
    )
  );

  private $_tipoNotaJs = '
var tipo_nota = new function() {
  this.isNenhum = function(docObj, formId, fieldsName) {
    var regex = new RegExp(fieldsName);
    var form  = docObj.getElementById(formId);

    for (var i = 0; i < form.elements.length; i++) {
      var elementName = form.elements[i].name;
      if (null !== elementName.match(regex)) {
        if (form.elements[i].checked == false) {
          continue;
        }

        docObj.getElementById(\'tabelaArredondamento\').disabled = false;
        docObj.getElementById(\'media\').disabled = false;
        docObj.getElementById(\'formulaMedia\').disabled = false;
        docObj.getElementById(\'formulaRecuperacao\').disabled = false;

        if (form.elements[i].value == 0) {
          docObj.getElementById(\'tabelaArredondamento\').disabled = true;
          docObj.getElementById(\'media\').disabled = true;
          docObj.getElementById(\'formulaMedia\').disabled = true;
          docObj.getElementById(\'formulaRecuperacao\').disabled = true;
        }

        break;
      }
    }
  };
};

var tabela_arredondamento = new function() {
  this.docObj = null;

  this.getTabelasArredondamento = function(docObj, tipoNota) {
    tabela_arredondamento.docObj = docObj;
    var xml = new ajax(tabela_arredondamento.parseResponse);
    xml.envia("/modules/TabelaArredondamento/Views/TabelaTipoNotaAjax.php?tipoNota=" + tipoNota);
  };

  this.parseResponse = function() {
    if (arguments[0] === null) {
      return;
    }

    docObj = tabela_arredondamento.docObj;

    tabelas = arguments[0].getElementsByTagName(\'tabela\');
    docObj.options.length = 0;
    for (var i = 0; i < tabelas.length; i++) {
      docObj[docObj.options.length] = new Option(
        tabelas[i].firstChild.nodeValue, tabelas[i].getAttribute(\'id\'), false, false
      );
    }

    if (tabelas.length == 0) {
      docObj.options[0] = new Option(
        \'O tipo de nota não possui tabela de arredondamento.\', \'\', false, false
      );
    }
  }
}
';

  protected function _preRender()
  {
    parent::_preRender();

    // Adiciona o código Javascript de controle do formulário.
    $js = sprintf('
      <script type="text/javascript">
        %s

        window.onload = function() {
          // Desabilita os campos relacionados caso o tipo de nota seja "nenhum".
          new tipo_nota.isNenhum(document, \'formcadastro\', \'tipoNota\');

          // Faz o binding dos eventos isNenhum e getTabelasArredondamento nos
          // campos radio de tipo de nota.
          var events = function() {
            new tipo_nota.isNenhum(document, \'formcadastro\', \'tipoNota\');
            new tabela_arredondamento.getTabelasArredondamento(
              document.getElementById(\'tabelaArredondamento\'),
              this.value
            );
          }

          new ied_forms.bind(document, \'formcadastro\', \'tipoNota\', \'click\', events);
        }
      </script>',
      $this->_tipoNotaJs
    );

    $this->prependOutput($js);
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Instituição
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'), $instituicoes,
      $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      50, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Nota tipo valor
    $notaTipoValor = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
    $this->campoRadio('tipoNota', $this->_getLabel('tipoNota'), $notaTipoValor->getEnums(),
      $this->getEntity()->get('tipoNota'), '', $this->_getHelp('tipoNota'));

    // Tabela de arredondamento
    $tabelaArredondamento = $this->getDataMapper()->findTabelaArredondamento($this->getEntity());
    $tabelaArredondamento = CoreExt_Entity::entityFilterAttr($tabelaArredondamento, 'id', 'nome');

    if (empty($tabelaArredondamento)) {
      $tabelaArredondamento = array(0 => 'O tipo de nota não possui tabela de arredondamento.');
    }

    $this->campoLista('tabelaArredondamento', $this->_getLabel('tabelaArredondamento'),
      $tabelaArredondamento, $this->getEntity()->get('tabelaArredondamento'), '',
      FALSE, $this->_getHelp('tabelaArredondamento'), '', FALSE, FALSE);

    // Tipo progressão
    $tipoProgressao = RegraAvaliacao_Model_TipoProgressao::getInstance();
    $this->campoRadio('tipoProgressao', $this->_getLabel('tipoProgressao'),
      $tipoProgressao->getEnums(), $this->getEntity()->get('tipoProgressao'), '',
      $this->_getHelp('tipoProgressao'));

    // Média
    $this->campoTexto('media', $this->_getLabel('media'), $this->getEntity()->media,
      5, 50, FALSE, FALSE, FALSE, $this->_getHelp('media'));

    $this->campoTexto('mediaRecuperacao', $this->_getLabel('mediaRecuperacao'), $this->getEntity()->mediaRecuperacao, 5, 50, FALSE, FALSE, FALSE, $this->_getHelp('mediaRecuperacao'));

    // Cálculo média
    $formulas = $this->getDataMapper()->findFormulaMediaFinal();
    $formulas = CoreExt_Entity::entityFilterAttr($formulas, 'id', 'nome');
    $this->campoLista('formulaMedia', $this->_getLabel('formulaMedia'),
      $formulas, $this->getEntity()->get('formulaMedia'), '', FALSE,
      $this->_getHelp('formulaMedia'), '', FALSE, FALSE);

    // Cálculo média recuperação
    $formulas = $this->getDataMapper()->findFormulaMediaRecuperacao();
    $formulasArray = array(0 => 'Não usar recuperação');
    $formulasArray = $formulasArray + CoreExt_Entity::entityFilterAttr($formulas, 'id', 'nome');

    $this->campoLista('formulaRecuperacao', $this->_getLabel('formulaRecuperacao'),
      $formulasArray, $this->getEntity()->get('formulaRecuperacao'), '', FALSE,
      $this->_getHelp('formulaRecuperacao'), '', FALSE, FALSE);

    // Porcentagem presença
    $this->campoTexto('porcentagemPresenca', $this->_getLabel('porcentagemPresenca'),
      $this->getEntity()->porcentagemPresenca, 5, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('porcentagemPresenca'));

    // Parecer descritivo
    $parecerDescritivo = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance();
    $this->campoRadio('parecerDescritivo', $this->_getLabel('parecerDescritivo'),
      $parecerDescritivo->getEnums(), $this->getEntity()->get('parecerDescritivo'), '',
      $this->_getHelp('parecerDescritivo'));

    // Presença
    $tipoPresenca = RegraAvaliacao_Model_TipoPresenca::getInstance();
    $this->campoRadio('tipoPresenca', $this->_getLabel('tipoPresenca'),
      $tipoPresenca->getEnums(), $this->getEntity()->get('tipoPresenca'), '',
      $this->_getHelp('tipoPresenca'));
  }
}
