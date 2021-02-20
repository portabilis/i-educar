<?php

use App\Menu;
use App\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;


class clsBase
{
    public $titulo = 'Prefeitura Municipal';
    public $clsForm = [];
    public $bodyscript = null;
    public $processoAp;
    public $refresh = false;
    public $renderMenu = true;
    public $renderMenuSuspenso = true;
    public $renderBanner = true;
    public $estilos;
    public $scripts;
    public $_instituicao;

    public function __construct()
    {
        $this->_instituicao = config('legacy.app.template.vars.instituicao');
    }

    public function SetTitulo($titulo)
    {
        $this->titulo = html_entity_decode($titulo);
    }

    public function AddForm($form)
    {
        $this->clsForm[] = $form;
    }

    public function addEstilo($estilo_nome)
    {
        $this->estilos[$estilo_nome] = $estilo_nome;
    }

    public function addScript($script_nome)
    {
        $this->scripts[$script_nome] = $script_nome;
    }

    public function verificaPermissao()
    {
        if (Gate::denies('view', $this->processoAp)) {
            throw new HttpResponseException(
                new RedirectResponse('index.php?negado=1&err=1')
            );
        }
    }

    public function MakeBody()
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
        }

        return $corpo;
    }

    public function Formular()
    {
        return false;
    }

    public function CadastraAcesso()
    {
        if (Session::get('marcado') != "private") {
            $ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
            $ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
            $id_pessoa = \Illuminate\Support\Facades\Auth::id();

            $logAcesso = new clsLogAcesso(FALSE, $ip, $ip_de_rede, $id_pessoa);
            $logAcesso->cadastra();

            Session::put('marcado', 'private');
            Session::save();
            Session::start();
        }
    }

    public function MakeAll()
    {
        $this->Formular();
        $this->verificaPermissao();
        $this->CadastraAcesso();

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
