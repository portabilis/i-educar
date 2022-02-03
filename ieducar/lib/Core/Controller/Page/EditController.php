<?php

abstract class Core_Controller_Page_EditController extends clsCadastro implements Core_Controller_Page_Validatable
{
    /**
     * Array associativo de um elemento de formulário, usado para a definição
     * de labels, nome de campos e definição de qual campo foi invalidado por
     * CoreExt_DataMapper::isValid().
     *
     * @var array
     */
    protected $_formMap = [];

    /**
     * Determina se "Cadastrar" ou "Atualizar" são ações disponíveis na interface.
     *
     * @var bool
     */
    protected $_saveOption = false;

    /**
     * Determina se "Excluir" é uma ação disponível na interface.
     *
     * @var bool
     */
    protected $_deleteOption = false;

    /**
     * Determina o nível de acesso necessário para as ações de Cadastro/Exclusão.
     *
     * @var int
     */
    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;

    /**
     * Determina um caminho para redirecionar o usuário caso seus privilégios de
     * acesso sejam insuficientes.
     *
     * @var string
     */
    protected $_nivelAcessoInsuficiente = null;

    /**
     * @var clsPermissoes
     */
    protected $_clsPermissoes = null;

    /**
     * Chama o construtor da superclasse para atribuir $tipoacao do $_POST.
     */
    public function __construct()
    {
        $this->setDataMapper($this->getDataMapper());

        // Adiciona novos itens de configuração
        $this->_options = $this->_options + [
                'save_action' => $this->_saveOption,
                'delete_action' => $this->_deleteOption,
                'nivel_acesso' => $this->_nivelAcessoOption,
                'nivel_acesso_insuficiente' => $this->_nivelAcessoInsuficiente
            ];

        // Configura botões padrão
        if (0 < $this->getRequest()->id) {
            $this->setOptions([
                'url_cancelar' => [
                    'path' => 'view', 
                    'options' => ['query' => ['id' => $this->getRequest()->id]]
                ]
            ]);
        }

        $this->_preConstruct();
        parent::__construct();
        $this->_postConstruct();
    }

    /**
     * Subclasses podem sobrescrever esse método para executar operações antes
     * da chamada ao construtor de clsCadastro().
     */
    protected function _preConstruct()
    {
    }

    /**
     * Subclasses podem sobrescrever esse método para executar operações após
     * a chamada ao construtor de clsCadastro().
     */
    protected function _postConstruct()
    {
    }

    /**
     * Retorna um label de um item de formulário.
     *
     * @param string $key
     *
     * @return string
     */
    protected function _getLabel($key)
    {
        return $this->_formMap[$key]['label'];
    }

    /**
     * Retorna uma string de ajuda para um item de formulário.
     *
     * @param string $key
     *
     * @return string
     */
    protected function _getHelp($key)
    {
        return $this->_formMap[$key]['help'];
    }

    /**
     * Retorna o atributo de CoreExt_Entity para recuperar o valor de um item
     * de formulário.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function _getEntity($key)
    {
        return $this->_formMap[$key]['entity'];
    }

    /**
     * Retorna um label de um item de formulário através do nome de um atributo de
     * CoreExt_Entity.
     *
     * @param string $key
     *
     * @return string
     */
    protected function _getEntityLabel($key)
    {
        foreach ($this->_formMap as $oKey => $map) {
            if ($key == $map['entity'] || $key == $oKey) {
                return $map['label'];
            }
        }
    }

    /**
     * @see Core_Controller_Page_Validatable::getValidators()
     */
    public function getValidators()
    {
        return [];
    }

    /**
     * Sobrescreve o método Inicializar() de clsCadastro com operações padrões
     * para o caso de uma CoreExt_Entity que use o campo identidade id.
     *
     * Seu comportamento pode ser alterado sobrescrevendo-se os métodos _initNovo
     * e _initEditar.
     *
     * O retorno desse método é usado em RenderHTML() que define qual método de
     * sua API (Novo, Editar, Excluir ou Gerar) será chamado.
     *
     * @return string
     *
     * @see    clsCadastro::RenderHTML()
     * @see    clsCadastro::Inicializar()
     *
     * @throws Core_Controller_Page_Exception
     */
    public function Inicializar()
    {
        if ($this->_initNovo()) {
            return 'Novo';
        }

        if ($this->getOption('save_action')) {
            $this->_hasPermissaoCadastra();
        }

        // Habilita botão de exclusão de registro
        if ($this->getOption('delete_action')) {
            $this->fexcluir = $this->_hasPermissaoExcluir();
        }

        if ($this->_initEditar()) {
            return 'Editar';
        }
    }

    public function Formular()
    {
        if ($this->tipoacao == 'Excluir') {
            return $this->Inicializar();
        }
    }

    /**
     * Verifica se o usuário possui privilégios de cadastro para o processo.
     *
     * @return bool|void Redireciona caso a opção 'nivel_acesso_insuficiente' seja diferente de NULL.
     *
     * @throws Core_Controller_Page_Exception
     */
    protected function _hasPermissaoCadastra()
    {
        return $this->getClsPermissoes()->permissao_cadastra(
            $this->getBaseProcessoAp(),
            $this->getOption('id_usuario'),
            $this->getOption('nivel_acesso'),
            $this->getOption('nivel_acesso_insuficiente')
        );
    }

    /**
     * Verifica se o usuário possui privilégios de cadastro para o processo.
     *
     * @return bool
     *
     * @throws Core_Controller_Page_Exception
     */
    protected function _hasPermissaoExcluir()
    {
        return $this->getClsPermissoes()->permissao_excluir(
            $this->getBaseProcessoAp(),
            $this->getOption('id_usuario'),
            $this->getOption('nivel_acesso')
        );
    }

    /**
     * Setter.
     *
     * @param clsPermissoes $instance
     *
     * @return Core_Controller_Page_EditController
     */
    public function setClsPermissoes(clsPermissoes $instance)
    {
        $this->_clsPermissoes = $instance;

        return $this;
    }

    /**
     * Getter.
     *
     * @return clsPermissoes
     */
    public function getClsPermissoes()
    {
        if (is_null($this->_clsPermissoes)) {
            $this->setClsPermissoes(new clsPermissoes());
        }

        return $this->_clsPermissoes;
    }

    /**
     * Hook de execução para verificar se CoreExt_Entity é novo. Verifica
     * simplesmente se o campo identidade foi passado na requisição HTTP e, se não
     * for, cria uma instância de CoreExt_Entity vazia.
     *
     * @return bool
     *
     * @throws Core_Controller_Page_Exception
     */
    protected function _initNovo()
    {
        if (!isset($this->getRequest()->id)) {
            $this->setEntity($this->getDataMapper()->createNewEntityInstance());

            return true;
        }

        return false;
    }

    /**
     * Hook de execução para verificar se CoreExt_Entity é existente através do
     * campo identidade passado pela requisição HTTP.
     *
     * @return bool
     */
    protected function _initEditar()
    {
        try {
            $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
        } catch (Exception $e) {
            $this->mensagem = $e;

            return false;
        }

        return true;
    }

    /**
     * Insere um novo registro no banco de dados e redireciona para a página
     * definida pela opção "new_success".
     *
     * @see clsCadastro::Novo()
     */
    public function Novo()
    {
        if ($this->_save()) {
            $params = '';

            if (0 < count($this->getOption('new_success_params')) &&
                is_array($this->getOption('new_success_params'))) {
                $params = '?' . http_build_query($this->getOption('new_success_params'));
            }

            $this->redirect(
                $this->getDispatcher()->getControllerName() . '/' . $this->getOption('new_success') . $params
            );
        }

        return false;
    }

    /**
     * Atualiza um registro no banco de dados e redireciona para a página
     * definida pela opção "edit_success".
     *
     * Possibilita o uso de uma query string padronizada, usando o array
     * armazenado na opção "edit_success_params"
     *
     * @see clsCadastro::Editar()
     */
    public function Editar()
    {
        if ($this->_save()) {
            if (0 < count($this->getOption('edit_success_params')) &&
                is_array($this->getOption('edit_success_params'))) {
                $params = http_build_query($this->getOption('edit_success_params'));
            } else {
                $params = 'id=' . floatval($this->getEntity()->id);
            }

            $this->redirect(
                $this->getDispatcher()->getControllerName() . '/'
                . $this->getOption('edit_success')
                . '?' . $params
            );
        }

        return false;
    }

    /**
     * Apaga um registro no banco de dados e redireciona para a página indicada
     * pela opção "delete_success".
     *
     * @see clsCadastro::Excluir()
     */
    public function Excluir()
    {
        if (isset($this->getRequest()->id)) {
            if ($this->getDataMapper()->delete($this->getRequest()->id)) {
                if (is_array($this->getOption('delete_success_params'))) {
                    $params = http_build_query($this->getOption('delete_success_params'));
                }

                $this->redirect(
                    $this->getDispatcher()->getControllerName() . '/' .
                    $this->getOption('delete_success') .
                    (isset($params) ? '?' . $params : '')
                );
            }
        }

        return false;
    }

    /**
     * Implementa uma rotina de criação ou atualização de registro padrão para
     * uma instância de CoreExt_Entity que use um campo identidade.
     *
     * @return bool
     *
     * @throws Core_Controller_Page_Exception
     */
    protected function _save()
    {
        $data = [];

        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $this->_formMap)) {
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
