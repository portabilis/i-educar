<?php

require_once 'Core/Controller/Page/EditController.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/TipoBase.php';
require_once 'ComponenteCurricular/Model/CodigoEducacenso.php';

class EditController extends Core_Controller_Page_EditController
{
    protected $_dataMapper = 'ComponenteCurricular_Model_ComponenteDataMapper';

    protected $_titulo = 'Cadastro de componente curricular';

    protected $_processoAp = 946;

    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;

    protected $_saveOption = true;

    protected $_deleteOption = false;

    protected $_formMap = [
        'instituicao' => [
            'label' => 'Instituição',
            'help' => '',
        ],
        'nome' => [
            'label' => 'Nome',
            'help' => 'Nome por extenso do componente.',
        ],
        'abreviatura' => [
            'label' => 'Nome abreviado',
            'help' => 'Nome abreviado do componente.',
            'entity' => 'abreviatura'
        ],
        'tipo_base' => [
            'label' => 'Base curricular',
            'help' => '',
            'entity' => 'tipo_base'
        ],
        'area_conhecimento' => [
            'label' => 'Área conhecimento',
            'help' => '',
            'entity' => 'area_conhecimento'
        ],
        'codigo_educacenso' => [
            'label' => 'Disciplina Educacenso',
            'help' => '',
            'entity' => 'codigo_educacenso'
        ],
        'ordenamento' => [
            'label' => 'Ordem de apresentação',
            'help' => 'Ordem respeitada no lançamento de notas/faltas.',
            'entity' => 'ordenamento'
        ],
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
        $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => "$nomeMenu componente curricular"
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }

    /**
     * @see clsCadastro::Gerar()
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
            trim($this->getEntity()->nome),
            50,
            500,
            true,
            false,
            false,
            $this->_getHelp('nome')
        );

        // Abreviatura
        $this->campoTexto(
            'abreviatura',
            $this->_getLabel('abreviatura'),
            $this->getEntity()->abreviatura,
            50,
            25,
            true,
            false,
            false,
            $this->_getHelp('abreviatura')
        );

        // Tipo Base
        $tipoBase = ComponenteCurricular_Model_TipoBase::getInstance();

        $this->campoRadio(
            'tipo_base',
            $this->_getLabel('tipo_base'),
            $tipoBase->getEnums(),
            $this->getEntity()->get('tipo_base') ?? ComponenteCurricular_Model_TipoBase::DEFAULT
        );

        // Área de conhecimento
        $areas = $this->getDataMapper()->findAreaConhecimento();
        $areas = CoreExt_Entity::entityFilterAttr($areas, 'id', 'nome');

        $this->campoLista(
            'area_conhecimento',
            $this->_getLabel('area_conhecimento'),
            $areas,
            $this->getEntity()->get('area_conhecimento')
        );

        // Código educacenso
        $codigos = ComponenteCurricular_Model_CodigoEducacenso::getInstance();

        $this->campoLista(
            'codigo_educacenso',
            $this->_getLabel('codigo_educacenso'),
            $codigos->getEnums(),
            $this->getEntity()->get('codigo_educacenso')
        );

        // Ordenamento
        $this-> campoNumero(
            'ordenamento',
            $this->_getLabel('ordenamento'),
            $this->getEntity()->ordenamento==99999 ? null : $this->getEntity()->ordenamento,
            15,
            15,
            false,
            $this->_getHelp('ordenamento')
        );
    }

    /**
     * OVERRIDE
     * Insere um novo registro no banco de dados e redireciona para a página
     * definida pela opção "new_success".
     *
     * @see clsCadastro::Novo()
     */
    public function Novo()
    {
        if ($this->_save()) {
            $this->simpleRedirect('/intranet/educar_componente_curricular_lst.php');
        }

        return false;
    }

    protected function _save()
    {
        $data = [];

        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $this->_formMap)) {
                if ($key == 'ordenamento') {
                    if ((trim($val) == '') || (is_null($val))) {
                        $data[$key] = 99999;
                        continue;
                    }
                }

                $data[$key] = $val;
            }
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
        } catch (Exception $e) {
            $this->mensagem = 'Erro no preenchimento do formulário. ';

            return false;
        }
    }
}
