<?php

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'Usuario/Mailers/UsuarioMailer.php';
require_once 'Usuario/Validators/UsuarioValidator.php';

class AlterarSenhaController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Alterar senha';

    protected $_processoAp = 0;

    protected $backwardCompatibility = true;

    protected $_formMap = [
        'matricula' => [
            'label' => 'Matr&iacute;cula',
            'help' => ''
        ],
        'nova_senha' => [
            'label' => 'Nova senha',
            'help' => ''
        ],
        'confirmacao_senha' => [
            'label' => 'Confirma&ccedil;&atilde;o de senha',
            'help' => ''
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
        if (! isset($_POST['password'])) {
            $this->messenger()->append('Para sua seguran&ccedil;a mude sua senha periodicamente, por favor, informe uma nova senha.', 'info');
        }

        $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
        $this->campoSenha('password', $this->_getLabel('nova_senha'), @$_POST['password'], true);
        $this->campoSenha('password_confirmation', $this->_getLabel('confirmacao_senha'), @$_POST['password_confirmation'], true);

        $this->nome_url_sucesso = 'Alterar';
        $this->nome_url_cancelar = 'Deixar para depois';

        if ($GLOBALS['coreExt']['Config']->app->user_accounts->force_password_update != true) {
            $this->url_cancelar      = '/intranet/index.php';
        }
    }

    protected function canSave()
    {
        return UsuarioValidator::validatePassword(
            $this->messenger(),
            $this->getEntity()->senha,
            $_POST['password'],
            $_POST['password_confirmation'],
            md5($_POST['password']),
            $this->getEntity()->matricula
        );
    }

    protected function save()
    {
        $this->getEntity()->setOptions(['senha' => md5($_POST['password']), 'data_troca_senha' => 'now()']);
        $this->getDataMapper()->save($this->getEntity());

        $linkToReset = $_SERVER['HTTP_HOST'] . $this->getRequest()->getBaseurl() . '/' . 'Usuario/AlterarSenha';

        (new UsuarioMailer)->updatedPassword($user = $this->getEntity(), $linkToReset);
    }
}
