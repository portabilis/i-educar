<?php

require_once 'Core/Controller/Page/EditController.php';
require_once 'ComponenteCurricular/Model/Componente.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';

class AnoController extends Core_Controller_Page_EditController
{
    protected $_dataMapper = 'ComponenteCurricular_Model_AnoEscolarDataMapper';

    protected $_titulo = 'Configuração de ano escolar';

    protected $_processoAp = 946;

    protected $_formMap = [];

    /**
     * Array de instâncias ComponenteCurricular_Model_AnoEscolar.
     *
     * @var array
     */
    protected $_entries = [];

    /**
     * Setter.
     *
     * @param array $entries
     *
     * @return Core_Controller_Page Provê interface fluída
     */
    public function setEntries(array $entries = [])
    {
        foreach ($entries as $entry) {
            $this->_entries[$entry->anoEscolar] = $entry;
        }

        return $this;
    }

    /**
     * Getter.
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->_entries;
    }

    /**
     * Getter.
     *
     * @param int $id
     *
     * @return ComponenteCurricular_Model_AnoEscolar
     */
    public function getEntry($id)
    {
        return $this->_entries[$id];
    }

    /**
     * Verifica se uma instância ComponenteCurricular_Model_AnoEscolar identificada
     * por $id existe.
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasEntry($id)
    {
        if (isset($this->_entries[$id])) {
            return true;
        }

        return false;
    }

    /**
     * Retorna um array associativo de séries com código de curso como chave.
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    protected function _getSeriesAgrupadasPorCurso()
    {
        $series = App_Model_IedFinder::getSeries($this->getEntity()->instituicao);
        $cursos = [];

        foreach ($series as $id => $nome) {
            $serie = App_Model_IedFinder::getSerie($id);
            $codCurso = $serie['ref_cod_curso'];
            $cursos[$codCurso][$id] = $nome;
        }

        return $cursos;
    }

    /**
     * Retorna o nome de um curso.
     *
     * @param int $id
     *
     * @return string
     */
    protected function _getCursoNome($id)
    {
        return App_Model_IedFinder::getCurso($id);
    }

    /**
     * @see Core_Controller_Page_EditController::_preConstruct()
     */
    public function _preConstruct()
    {
        // Popula array de disciplinas selecionadas
        $this->setOptions(['edit_success_params' => ['id' => $this->getRequest()->cid]]);
        $this->setEntries(
            $this->getDataMapper()->findAll([], [
                'componenteCurricular' => $this->getRequest()->cid
            ])
        );

        // Configura ação cancelar
        $this->setOptions([
            'url_cancelar' => [
                'path' => 'view',
                'options' => [
                    'query' => [
                        'id' => $this->getRequest()->cid
                    ]
                ]
            ]
        ]);
    }

    /**
     * @see Core_Controller_Page_EditController::_initNovo()
     */
    protected function _initNovo()
    {
        if (!isset($this->getRequest()->cid)) {
            $this->setEntity($this->getDataMapper()->createNewEntityInstance());

            return true;
        }

        return false;
    }

    /**
     * @see Core_Controller_Page_EditController::_initEditar()
     */
    protected function _initEditar()
    {
        try {
            $this->setEntity(
                $this->getDataMapper()->createNewEntityInstance([
                    'componenteCurricular' => $this->getRequest()->cid
                ])
            );
        } catch (Exception $e) {
            $this->mensagem = $e;

            return false;
        }

        return true;
    }

    protected function _preRender()
    {
        parent::_preRender();

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Carga horária dos anos escolares'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }

    /**
     * @see clsCadastro::Gerar()
     */
    public function Gerar()
    {
        $this->campoOculto('cid', $this->getEntity()->get('componenteCurricular'));

        // Cursos
        $cursos = $this->_getSeriesAgrupadasPorCurso();

        // Cria a matriz de checkboxes
        foreach ($cursos as $key => $curso) {
            $this->campoRotulo($key, $this->_getCursoNome($key), '', false, '', '');

            foreach ($curso as $c => $serie) {
                $this->campoCheck('ano_escolar['.$c.']', '', $this->hasEntry($c), $serie, false);

                $valor = $this->hasEntry($c) ? $this->getEntry($c)->cargaHoraria : null;

                $this->campoTexto(
                    'carga_horaria['.$c.']',
                    'Carga horária',
                    $valor,
                    5,
                    5,
                    false,
                    false,
                    false
                );
            }
            $this->campoQuebra();
        }
    }

    /**
     * @see Core_Controller_Page_EditController::_save()
     */
    protected function _save()
    {
        $data = $insert = $delete = $intersect = [];

        // O id de componente_curricular será igual ao id da request
        if ($cid = $this->getRequest()->cid) {
            $data['componenteCurricular'] = $cid;
        }

        // Cria um array de Entity geradas pela requisição
        foreach ($this->getRequest()->ano_escolar as $key => $val) {
            $data['anoEscolar'] = $key;
            $data['cargaHoraria'] = $this->getRequest()->carga_horaria[$key];
            $insert[$key] = $this->getDataMapper()->createNewEntityInstance($data);
        }

        // Cria um array de chaves da Entity AnoEscolar para remover
        $entries = $this->getEntries();
        $delete = array_diff(array_keys($entries), array_keys($insert));

        // Cria um array de chaves da Entity AnoEscolar para evitar inserir novamente
        $intersect = array_intersect(array_keys($entries), array_keys($insert));

        // Registros a apagar
        foreach ($delete as $id) {
            $this->getDataMapper()->delete($entries[$id]);
        }

        // Registros a inserir
        foreach ($insert as $key => $entity) {
            // Se o registro já existe, passa para o próximo
            if (false !== array_search($key, $intersect)) {
                $entity->markOld();
            }

            try {
                $this->getDataMapper()->save($entity);
            } catch (Exception $e) {
                $this->mensagem = 'Erro no preenchimento do formulário.';

                return false;
            }
        }

        return true;
    }
}
