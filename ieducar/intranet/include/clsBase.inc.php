<?php

use App\Menu;
use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

require_once 'include/clsCronometro.inc.php';
require_once 'clsConfigItajai.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsLogAcesso.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmicontrolesis/geral.inc.php';
require_once 'include/funcoes.inc.php';
require_once 'Portabilis/Utils/Database.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/String/Utils.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

if ($GLOBALS['coreExt']['Config']->app->ambiente_inexistente) {
    throw new HttpResponseException(
        new RedirectResponse('404.html')
    );
}

class clsBase extends clsConfig
{
    var $titulo = 'Prefeitura Cobra Tecnologia';
    var $clsForm = array();
    var $bodyscript = NULL;
    var $processoAp;
    var $refresh = FALSE;
    var $renderMenu = TRUE;
    var $renderMenuSuspenso = TRUE;
    var $renderBanner = TRUE;
    var $estilos;
    var $scripts;
    var $prog_alert;

    function SetTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    function AddForm($form)
    {
        $this->clsForm[] = $form;
    }

    function addEstilo($estilo_nome)
    {
        $this->estilos[$estilo_nome] = $estilo_nome;
    }

    function addScript($script_nome)
    {
        $this->scripts[$script_nome] = $script_nome;
    }

    function verificaPermissao()
    {
        if (Gate::denies('view', $this->processoAp)) {
            throw new HttpResponseException(
                new RedirectResponse('index.php?negado=1&err=1')
            );
        }
    }

    function MakeBody()
    {
        $corpo = '';

        foreach ($this->clsForm as $form) {
            $corpo .= $form->RenderHTML();

            if (method_exists($form, 'getPrependedOutput')) {
                $corpo = $form->getPrependedOutput() . $corpo;
            }

            if (method_exists($form, 'getAppendedOutput')) {
                $corpo = $corpo . $form->getAppendedOutput();
            }

            if (!isset($form->prog_alert)) {
                continue;
            }

            if (is_string($form->prog_alert) && $form->prog_alert) {
                $this->prog_alert .= $form->prog_alert;
            }
        }

        return $corpo;
    }

    function Formular()
    {
        return FALSE;
    }

    function MakeAll()
    {
        $cronometro = new clsCronometro();
        $cronometro->marca('inicio');

        $this->Formular();
        $this->verificaPermissao();

        $saida_geral = '';

        /** @var User $user */
        $user = Auth::user();
        $menu = Menu::user($user);

        $topmenu = Menu::query()
            ->where('process', $this->processoAp)
            ->first();

        if ($topmenu) {
            View::share('mainmenu', $topmenu->root()->getKey());
        }

        View::share('menu', $menu);
        View::share('title', $this->titulo);

        if ($this->renderMenu) {
            $saida_geral .= $this->MakeBody();
        } else {
            foreach ($this->clsForm as $form) {
                $saida_geral .= $form->RenderHTML();
            }
        }

        $view = 'legacy.body';

        if (!$this->renderMenu || !$this->renderMenuSuspenso) {
            $view = 'legacy.blank';
        }

        echo view($view, ['body' => $saida_geral])->render();
    }
}
