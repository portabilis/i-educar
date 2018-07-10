<?php

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class AlterarEmailController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Alterar e-mail';

    protected $_processoAp = 0;

    protected $backwardCompatibility = true;

    protected $_formMap    = [
        'matricula' => [
            'label' => 'Matr&iacute;cula',
            'help' => '',
        ],
        'email' => [
            'label' => 'E-mail',
            'help' => 'E-mail utilizado para recuperar sua senha.',
        ],
    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions(['edit_success' => 'intranet/index.php'], $this->_options);
    }

    protected function _initNovo()
    {
        return false;
    }

    protected function _initEditar()
    {
        $this->setEntity($this->getDataMapper()->find($this->getOption('id_usuario')));

        return true;
    }

    public function Gerar()
    {
        $validEmail = filter_var($this->getEntity()->email, FILTER_VALIDATE_EMAIL) == true;

        if (empty($this->getRequest()->email) &&  ! $validEmail) {
            $this->messenger()->append('Por favor informe um e-mail v&aacute;lido, para ser usado caso voc&ecirc; esque&ccedil;a sua senha.');
        }

        $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
        $this->campoTexto('email', $this->_getLabel('email'), $this->getEntity()->email, 50, 50, true, false, false, $this->_getHelp('email'));

        $this->url_cancelar = '/intranet/index.php';

        if (! $validEmail) {
            $this->nome_url_cancelar = 'Deixar para depois';
        }
    }

    public function save()
    {
        $this->getEntity()->setOptions(['email' => $_POST['email']]);
        $this->getDataMapper()->save($this->getEntity());
    }
}
