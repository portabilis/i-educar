<?php

class EditController extends Core_Controller_Page_EditController
{
    protected $_dataMapper = 'AreaConhecimento_Model_AreaDataMapper';

    protected $_titulo = 'Cadastro de área de conhecimento';

    protected $_processoAp = 945;

    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;

    protected $_saveOption = true;

    protected $_deleteOption = true;

    protected $_formMap = [
        'instituicao' => [
            'label' => 'Instituição',
            'help' => ''
        ],
        'nome' => [
            'label' => 'Nome',
            'help' => 'O nome da área de conhecimento. Exemplo: "<em>Ciências da natureza</em>".',
            'entity' => 'nome'
        ],
        'secao' => [
            'label' => 'Seção',
            'help' => 'A seção que abrange a área de conhecimento. Exemplo: "<em>Lógico Matemático</em>".',
            'entity' => 'secao'
        ],
        'ordenamento_ac' => [
            'label' => 'Ordem de apresentação',
            'help' => 'Ordem respeitada no lançamento de notas/faltas.',
            'entity' => 'ordenamento_ac'
        ],
        'agrupar_descritores' => [
            'label' => 'Esta área funciona como agrupador de descritores?',
            'help' => '',
            'entity' => 'agrupar_descritores'
        ],
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';

        $this->breadcrumb("$nomeMenu área de conhecimento", [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    /**
     * @see clsCadastro::Gerar()
     */
    public function Gerar()
    {
        $this->campoOculto('id', $this->getEntity()->id);

        $instituicoes = App_Model_IedFinder::getInstituicoes();

        // Instituição
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
            60,
            200,
            true,
            false,
            false,
            $this->_getHelp('nome')
        );

        // Seção
        $this->campoTexto(
            'secao',
            $this->_getLabel('secao'),
            $this->getEntity()->secao,
            50,
            50,
            false,
            false,
            false,
            $this->_getHelp('secao')
        );

        // Ordenamento
        $this->campoTexto(
            'ordenamento_ac',
            $this->_getLabel('ordenamento_ac'),
            $this->getEntity()->ordenamento_ac==99999 ? null : $this->getEntity()->ordenamento_ac,
            10,
            50,
            false,
            false,
            false,
            $this->_getHelp('ordenamento_ac')
        );

        $this->campoCheck(
            'agrupar_descritores',
            $this->_getLabel('agrupar_descritores'),
            $this->getEntity()->agrupar_descritores,
            '',
            false,
            false,
            false,
            $this->_getHelp('agrupar_descritores')
        );
    }

    protected function _save()
    {
        $data = [];

        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $this->_formMap)) {
                if ($key == 'ordenamento_ac') {
                    if ((trim($val) == '') || (is_null($val))) {
                        $data[$key] = 99999;
                        continue;
                    }
                }

                $data[$key] = $val;
            }
        }

        if (!isset($data['agrupar_descritores'])) {
            $data['agrupar_descritores'] = false;
        } else {
            $data['agrupar_descritores'] = true;
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
            $this->mensagem = 'Erro no preenchimento do formulário. ';

            return false;
        }
    }

    public function Excluir()
    {
        if (isset($this->getRequest()->id)) {
            $sql = 'SELECT id FROM modules.componente_curricular WHERE area_conhecimento_id = '. $this->getRequest()->id;
            $db = new clsBanco();
            $db->Consulta($sql);

            if ($db->numLinhas()) {
                $this->mensagem = 'Não é possível excluir esta área de conhecimento, pois a mesma possui vinculo com componentes curriculares.';

                return false;
            }

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
}
