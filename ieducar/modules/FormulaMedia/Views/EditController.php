<?php

use App\Models\LegacyExamRule;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditController extends Core_Controller_Page_EditController
{
    protected $_dataMapper        = 'FormulaMedia_Model_FormulaDataMapper';
    protected $_titulo            = 'Cadastro de fórmula de cálculo de média';
    protected $_processoAp        = 948;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
    protected $_saveOption        = true;
    protected $_deleteOption      = true;

    protected $_formMap = [
    'instituicao' => [
      'label'  => 'Instituição',
      'help'   => ''
    ],
    'nome' => [
      'label'  => 'Nome',
      'help'   => ''
    ],
    'formulaMedia' => [
      'label'  => 'Fórmula de média final',
      'help'   => 'A fórmula de cálculo.<br />
                   Variáveis disponíveis:<br />
                   &middot; En - Etapa n (de 1 a 10)<br />
                   &middot; Cn - Considera etapa n (de 1 a 10): 1 - Sim, 0 - Não<br />
                   &middot; Et - Total de etapas<br />
                   &middot; Se - Soma das notas das etapas<br />
                   &middot; Rc - Nota da recuperação<br />
                   &middot; RSPN - Recuperação específica n (de 1 a 10)<br />
                   &middot; RSPSN - Soma das etapas ou Recuperação específica (Pega maior) n (de 1 a 10)<br />
                   &middot; RSPMN - Média das etapas ou Média das etapas com Recuperação específica (Pega maior) n (de 1 a 10)<br />
                   Símbolos disponíveis:<br />
                   &middot; (), +, /, *, x<br />
                   &middot; < > ? :
                   A variável "Rc" está disponível apenas<br />
                   quando Tipo de fórmula for "Recuperação".'
    ],
    'tipoFormula' => [
      'label'  => 'Tipo de fórmula',
      'help'   => ''
    ],
    'substituiMenorNotaRc' => [
      'label'  => 'Substitui menor nota por recuperação ',
      'help'   => 'Substitui menor nota (En) por nota de recuperação (Rc) em ordem descrescente.<br/>
                   Somente substitui quando Rc é maior que En.
                   Ex: E1 = 2, E2 = 3, E3 = 2, Rc = 5.
                   Na fórmula será considerado: E1 = 2, E2 = 3, E3 = 5, Rc = 5.'
    ]
  ];

    public function _preRender()
    {
        Portabilis_View_Helper_Application::loadJavascript($this, '/modules/FormulaMedia/Assets/Javascripts/FormulaMedia.js');

        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';

        $this->breadcrumb("$nomeMenu fórmula de média", [
          url('intranet/educar_index.php') => 'Escola',
      ]);
    }

    /**
     * @see clsCadastro#Gerar()
     */
    public function Gerar()
    {
        $this->campoOculto('id', $this->getEntity()->id);

        // Instituição
        $instituicoes = App_Model_IedFinder::getInstituicoes();
        $this->campoLista(
            'instituicao',
            $this->_getLabel('instituicao'),
            $instituicoes,
            $this->getEntity()->instituicao
        );

        // Nome
        $this->campoTexto(
            'nome',
            $this->_getLabel('nome'),
            $this->getEntity()->nome,
            40,
            50,
            true,
            false,
            false,
            $this->_getHelp('nome')
        );

        // Fórmula de média
        $this->campoTexto(
            'formulaMedia',
            $this->_getLabel('formulaMedia'),
            $this->getEntity()->formulaMedia,
            40,
            200,
            true,
            false,
            false,
            $this->_getHelp('formulaMedia')
        );

        // Substitui menor nota
        $this->campoCheck(
            'substituiMenorNotaRc',
            $this->_getLabel('substituiMenorNotaRc'),
            $this->getEntity()->substituiMenorNotaRc,
            '',
            false,
            false,
            false,
            $this->_getHelp('substituiMenorNotaRc')
        );

        // Fórmula de recuperação
        /*$this->campoTexto('formulaRecuperacao', $this->_getLabel('formulaRecuperacao'),
          $this->getEntity()->formulaRecuperacao, 40, 50, TRUE, FALSE, FALSE,
          $this->_getHelp('formulaRecuperacao'));*/

        // Tipo de fórmula
        $tipoFormula = FormulaMedia_Model_TipoFormula::getInstance();
        $this->campoRadio(
            'tipoFormula',
            $this->_getLabel('tipoFormula'),
            $tipoFormula->getEnums(),
            $this->getEntity()->get('tipoFormula')
        );
    }

    private function usedInExamRule()
    {
        $id = $this->getRequest()->id;

        return LegacyExamRule::where('formula_media_id', $id)
            ->orWhere('formula_recuperacao_id', $id)
            ->exists();
    }

    /**
     * Apaga um registro no banco de dados e redireciona para a página indicada
     * pela opção "delete_success".
     *
     * @see clsCadastro::Excluir()
     */
    public function Excluir()
    {
        if ($this->usedInExamRule()) {
            $this->mensagem = 'Não foi possível excluir a fórmula de cálculo de média, pois a mesma possui vínculo com regras de avaliação.';

            return false;
        }

        try {
            parent::Excluir();
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    /**
     * Implementa uma rotina de criação ou atualização de registro padrão para
     * uma instância de CoreExt_Entity que use um campo identidade.
     *
     * @return bool
     *
     * @todo Atualizar todas as Exception de CoreExt_Validate, para poder ter
     *   certeza que o erro ocorrido foi gerado de alguma camada diferente, como
     *   a de conexão com o banco de dados.
     */
    protected function _save()
    {
        $data = [];

        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $this->_formMap)) {
                $data[$key] = $val;
            }
        }

        //fixup for checkbox nota geral
        if (!isset($data['substituiMenorNotaRc'])) {
            $data['substituiMenorNotaRc'] = '0';
        }

        // Verifica pela existência do field identity
        if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
            $entity = $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
        }

        if (isset($entity)) {
            $this->getEntity()->setOptions($data);
        } else {
            $this->setEntity($this->getDataMapper()->createNewEntityInstance($data));
        }

        try {
            $this->getDataMapper()->save($this->getEntity());

            return true;
        } catch (Exception) {
            // TODO: ver @todo do docblock
            $this->mensagem = 'Erro no preenchimento do formulário. ';

            return false;
        }
    }
}
