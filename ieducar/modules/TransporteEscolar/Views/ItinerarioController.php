<?php

class ItinerarioController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
    protected $_titulo = 'Cadastro de Rota';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_processoAp = 578;
    protected $_deleteOption = false;

    protected $_formMap = [

        'id' => [
            'label' => 'Código da rota',
            'help' => '',
        ],
        'descricao' => [
            'label' => 'Descrição',
            'help' => '',
        ]
    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions([
            'edit_success' => '/intranet/transporte_rota_lst.php',
            'delete_sucess' => '/intranet/transporte_rota_lst.php'
        ], $this->_options);
    }

    protected function _initNovo()
    {
        return false;
    }

    protected function _initEditar()
    {
        return false;
    }

    public function Gerar()
    {
        $id = (isset($_GET['id']) ? $_GET['id'] : 0);
        if ($id == 0 || !$this->verificaIdRota($id)) {
            $this->simpleRedirect('/intranet/transporte_rota_lst.php');
        }

        $this->url_cancelar = '/intranet/transporte_rota_det.php?cod_rota=' . $id . '';

        // Código da rota
        $options = [
            'label' => $this->_getLabel('id'),
            'disabled' => true,
            'required' => false,
            'size' => 25
        ];
        $this->inputsHelper()->integer('id', $options);

        // descricao
        $options = [
            'label' => $this->_getLabel('descricao'),
            'disabled' => true,
            'size' => 50,
            'max_length' => 50
        ];
        $this->inputsHelper()->text('descricao', $options);
        $resourceOptionsTable = '
    <table id=\'disciplinas-manual\'>
      <tr>
        <th>Ponto</th>
        <th>Hora</th>
        <th>Tipo</th>
        <th>Veiculo</th>

        <th>Ação</th>
      </tr>
      <tr class=\'ponto\'>
        <td><input class=\'nome obrigatorio disable-on-search change-state-with-parent\'></input></td>
        <td><input class=\'nota\' ></input></td>
        <td>
          <select id=\'disciplinas\' name=\'disciplinas\' class=\'obrigatorio disable-on-search\'>
            <option value=\'\'>Selecione</option>
            <option value=\'I\'>Ida</option>
            <option value=\'V\'>Volta</option>
          </select>
        </td>
        <td>
          <input class=\'nome obrigatorio disable-on-search change-state-with-parent\'></input>
        </td>

        <td>
          <a class=\'remove-disciplina-line\' href=\'#\'>Remover</a>
        </td>
      </tr>
<tr class=\'disciplina\'>
        <td><input class=\'nome obrigatorio disable-on-search change-state-with-parent\'></input></td>
        <td><input class=\'nota\' ></input></td>
        <td>
          <select id=\'disciplinas\' name=\'disciplinas\' class=\'obrigatorio disable-on-search\'>
            <option value=\'\'>Selecione</option>
            <option value=\'I\'>Ida</option>
            <option value=\'V\'>Volta</option>
          </select>
        </td>
        <td>
          <input class=\'nome obrigatorio disable-on-search change-state-with-parent\'></input>
        </td>

        <td>
          <a class=\'remove-disciplina-line\' href=\'#\'>Remover</a>
        </td>
      </tr>
      <tr class=\'actions\'>
        <td colspan=\'4\'>
          <input type=\'button\' class=\'action\' id=\'new-disciplina-line\' name=\'new-line\' value=\'Adicionar ponto\'></input>
        </td>
      </tr>
    </table>';

        $this->appendOutput($resourceOptionsTable);

        //$this->loadResourceAssets($this->getDispatcher());
    }

    public function verificaIdRota($id)
    {
        $obj = new clsModulesRotaTransporteEscolar($id);

        return $obj->existe();
    }
}
