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
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TipoArredondamentoMedia.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'TabelaArredondamento_Model_TabelaDataMapper';
  protected $_titulo            = 'Cadastro de tabela de arredondamento de notas';
  protected $_processoAp        = 949;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  protected $_formMap = array(
    'instituicao' => array(
      'label' => 'Instituição',
      'help'  => ''
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'Um nome para a tabela. Exemplo: "<em>Tabela genérica de conceitos</em>".'
    ),
    'tipoNota' => array(
      'label'  => 'Tipo de nota',
      'help'   => ''
    ),
    'valor_nome' => array(
      'label'  => 'Rótulo da nota:',
      'help'   => 'Exemplos: A, B, C (conceituais)<br />
                  <b>6,5<b>, <b>7,5<b> (numéricas)'
    ),
    'valor_descricao' => array(
      'label'  => '<span style="padding-left: 10px"></span>Descrição:',
      'help'   => 'Exemplos: Bom, Regular, Em Processo.'
    ),
    'valor_valor_minimo' => array(
      'label'  => '<span style="padding-left: 10px"></span>Valor mínimo:',
      'help'   => 'O valor numérico mínimo da nota.'
    ),
    'valor_valor_maximo' => array(
      'label'  => '<span style="padding-left: 10px"></span>Valor máximo:',
      'help'   => 'O valor numérico máximo da nota.'
    ),
    'acao' => array(
      'label'  => '<span style="padding-left: 10px"></span>Ação:',
      'help'   => 'A ação de arredondamento da nota.'
    ),
    'casa_decimal' => array(
      'label'  => '<span style="padding-left: 10px"></span>Casa decimal:',
      'help'   => 'A casa decimal exata para qual a nota deve ser arredondada.'
    ),
    'casa_decimal_exata' => array(
      'label'  => '<span style="padding-left: 10px"></span>Casa decimal exata:',
      'help'   => 'A casa decimal a ser arredondada.'
    )
  );

  /**
   * Array de instâncias TabelaArredondamento_Model_TabelaValor.
   * @var array
   */
  protected $_valores = array();

  /**
   * Setter.
   * @param array $valores
   * @return Core_Controller_Page_Abstract Provê interface fluída
   */
  protected function _setValores(array $valores = array())
  {
    foreach ($valores as $key => $valor) {
      $this->_valores[$valor->id] = $valor;
    }
    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  protected function _getValores()
  {
    return $this->_valores;
  }

  /**
   * Getter
   * @param int $id
   * @return TabelaArredondamento_Model_TabelaValor
   */
  protected function _getValor($id)
  {
    return isset($this->_valores[$id]) ? $this->_valores[$id] : NULL;
  }

  /**
   * @see Core_Controller_Page_EditController#_preConstruct()
   * @todo Interação com a API está errada. Isso já é feito em _initNovo()
   *   na superclasse. VER.
   */
  protected function _preConstruct()
  {
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
      $this->_setValores($this->getDataMapper()->findTabelaValor($this->getEntity()));
    }
  }

  function _preRender(){

    parent::_preRender();

    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/RegraAvaliacao/Assets/Javascripts/TabelaArredondamento.js');

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""        => "$nomeMenu tabela de arredondamento"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Instituição
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'),
      $instituicoes, $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      40, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Tipo de nota
    $notaTipoValor = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
    $notaTipos = $notaTipoValor->getEnums();
    unset($notaTipos[RegraAvaliacao_Model_Nota_TipoValor::NENHUM]);
    unset($notaTipos[RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL]);
    
    if ($this->getEntity()->id!='')
      $this->campoTexto('tipNota',$this->_getLabel('tipoNota'),$notaTipos[$this->getEntity()->get('tipoNota')],40,40,false,false,false,'','','','',true);
    else
      $this->campoRadio('tipoNota', $this->_getLabel('tipoNota'), $notaTipos,
        $this->getEntity()->get('tipoNota'), '', $this->_getHelp('tipoNota'));

    // Parte condicional
    if (!$this->getEntity()->isNew()) {
      // Quebra
      $this->campoQuebra();
      if (RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL == $this->getEntity()->get('tipoNota')) {
        $this->carregaCamposNotasConceituais();
      }elseif(RegraAvaliacao_Model_Nota_TipoValor::NUMERICA == $this->getEntity()->get('tipoNota')){
        $this->carregaCamposNotasNumericas();
      }

      // Quebra
      $this->campoQuebra();
    }
  }

  private function carregaCamposNotasConceituais(){
      // Cria campos para a postagem de notas
    $valores = $this->getDataMapper()->findTabelaValor($this->getEntity());

    for ($i = 0, $loop = count($valores); $i < $loop; $i++) {
      $valorNota = $valores[$i];
      $this->tabela_arredondamento_valor[$i][] = $valorNota->id;
      $this->tabela_arredondamento_valor[$i][] = $valorNota->nome;
      $this->tabela_arredondamento_valor[$i][] = $valorNota->descricao;
      $this->tabela_arredondamento_valor[$i][] = $valorNota->valorMinimo;
      $this->tabela_arredondamento_valor[$i][] = $valorNota->valorMaximo;
    }

      // Inicio da tabela
      $this->campoTabelaInicio("tabela_arredondamento", "Notas para arredondamento", array("ID","Rótulo da nota", "Descrição", "Valor mínimo", "Valor máximo"), $this->tabela_arredondamento_valor);

      // Id
      $this->campoTexto('valor_id', 'id',
        $valorNota->id, 5, 5, FALSE, FALSE, FALSE);

      // Nome
      $this->campoTexto('valor_nome', 'valor_nome',
        $valorNota->nome, 5, 5, TRUE, FALSE, FALSE, $this->_getHelp('valor_nome'));

      // Descrição (se conceitual)
      $this->campoTexto('valor_descricao', 'valor_descricao',
        $valorNota->descricao, 15, 25, TRUE, FALSE, FALSE,
        $this->_getHelp('valor_descricao'));

      // Valor mínimo
      $this->campoTexto('valor_minimo', 'valor_valor_minimo',
        $valorNota->valorMinimo, 6, 6, TRUE, FALSE, FALSE,
        $this->_getHelp('valor_valor_minimo'));

      // Valor máximo
      $this->campoTexto('valor_maximo', 'valor_valor_maximo',
        $valorNota->valorMaximo, 6, 6, TRUE, FALSE, FALSE,
        $this->_getHelp('valor_valor_maximo'));

      // Fim da tabela
      $this->campoTabelaFim();
  }

  private function carregaCamposNotasNumericas(){
    // Cria campos para a postagem de notas
    $valores = $this->getDataMapper()->findTabelaValor($this->getEntity());

    for ($i = 0; $i <= 9; $i++) {
      $valorNota = $valores[$i];
      $acao = 0;

      switch ($valorNota->acao) {
        case 'Arredondar para o n&uacute;mero inteiro imediatamente inferior':
           $acao = 1;
          break;
        case 'Arredondar para o n&uacute;mero inteiro imediatamente superior':
          $acao = 2;
          break;
        case 'Arredondar para a casa decimal espec&iacute;fica':
          $acao = 3;
          break;
        default:
           $acao = 0;
          break;
      }

      $this->tabela_arredondamento_valor[$i][] = $valorNota->id;
      $this->tabela_arredondamento_valor[$i][] = $i;
      $this->tabela_arredondamento_valor[$i][] = $i;
      $this->tabela_arredondamento_valor[$i][] = $acao;
      $this->tabela_arredondamento_valor[$i][] = $valorNota->casaDecimalExata;

    };

      // Inicio da tabela
      $this->campoTabelaInicio("tabela_arredondamento_numerica", "Notas para arredondamento", array("ID","Nome", "Casa decimal", "Ação", "Casa decimal exata"), $this->tabela_arredondamento_valor);

      // Id
      $this->campoTexto('valor_id', 'id',
        $valorNota->id, 5, 5, FALSE, FALSE, FALSE);


      // Foi feito um campo oculto com a informação a ser gravada pois o framework não grava informações de campos desabilitados
        $this->campoTexto('valor_nome', 'casa_decimal',
        $valorNota->nome, 1, 1, FALSE, FALSE, FALSE, '', '', '', 'onKeyUp', FALSE);

      // Este campo serve apenas para ser exibido ao usuário, ele não grava a informação no banco, pois o framework não grava campos desabilitados
      $this->campoTexto('valor_nome_fake', 'casa_decimal_fake',
        $valorNota->nome, 1, 1, FALSE, FALSE, FALSE, '', '', '', 'onKeyUp', TRUE);

      // Tipo de arredondamento de média (ou ação)
      $tipoArredondamentoMedia = TabelaArredondamento_Model_TipoArredondamentoMedia::getInstance();
      $this->campoLista('valor_acao', 'acao', $tipoArredondamentoMedia->getEnums(),
       $valorNota->acao, '', FALSE, $this->_getHelp('tipoRecuperacaoParalela'), '', FALSE, FALSE);

      // Casa decimal exata para o caso de arredondamento deste tipo
      $this->campoTexto('valor_casa_decimal_exata', 'valor_casa_decimal_exata',
        $valorNota->casaDecimalExata, 1, 1, FALSE, FALSE, FALSE, '', '', '', 'onKeyUp', FALSE);

      // Fim da tabela
      $this->campoTabelaFim();

  }

  protected function _save()
  {
    // Verifica pela existência do field identity
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
      $entity = $this->getEntity();
    }

    // A contagem usa um dos índices do formulário, senão ia contar sempre 4.
    $loop    = count($this->valor_id);

    // Verifica se existe valor acima de 100
    for ($i = 0; $i < $loop; $i++) {
      if (($this->valor_maximo[$i] >= 100) || ($this->valor_minimo[$i] >= 100)){
        $this->mensagem = 'Erro no formulário';
        return FALSE;
      }
    }
    // Se existir, chama _save() do parent
    if (!isset($entity)) {
      return parent::_save();
    }

    //Exclui todos os valores para inserir corretamente
    $entity->deleteAllValues();

    // Processa os dados da requisição, apenas os valores para a tabela de valores.
    // Mescla arrays
    for ($i = 0; $i < $loop; $i++) {
      $valores[] = array('id'           => $this->valor_id[$i],
                         'nome'         => $this->valor_nome[$i],
                         'descricao'    => $this->valor_descricao[$i],
                         'valor_minimo' => $this->valor_minimo[$i],
                         'valor_maximo' => $this->valor_maximo[$i],
                         'valor_acao'   => $this->valor_acao[$i],
                         'valor_casa_decimal_exata' => $this->valor_casa_decimal_exata[$i]);
    }

    // Array de objetos a persistir
    $insert  = array();

    // Cria um array de objetos a persistir
    for ($i = 0; $i < $loop; $i++) {
      $id = $valores[$i]['id'];

      // Não atribui a instância de $entity senão não teria sucesso em verificar
      // se a instância é isNull().
      $data = array(
        // 'id'               => $id,
        'nome'             => $valores[$i]['nome'],
        'descricao'        => $valores[$i]['descricao'],
        'valorMinimo'      => str_replace(",", ".", $valores[$i]['valor_minimo']),
        'valorMaximo'      => str_replace(",", ".", $valores[$i]['valor_maximo']),
        'acao'             => $valores[$i]['valor_acao'],
        'casaDecimalExata' => $valores[$i]['valor_casa_decimal_exata']
      );

      $instance = new TabelaArredondamento_Model_TabelaValor($data);
      if (!$instance->isNull()) {
        $insert['new_' . $i] = $instance;
      }
    }

    // Persiste
    foreach ($insert as $tabelaValor) {
      // Atribui uma tabela de arredondamento a instância de tabela valor
      $tabelaValor->tabelaArredondamento = $entity;

      // Se não tiver nome, passa para o próximo
      if ($tabelaValor->isValid()) {
        $this->getDataMapper()->getTabelaValorDataMapper()->save($tabelaValor);
      }
      else {
        $this->mensagem = 'Erro no formulário';
        return FALSE;
      }
    }

    return TRUE;
  }
}