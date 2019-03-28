<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'Portabilis/Messenger.php';
require_once 'Portabilis/Mailer.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/Utils/ReCaptcha.php';

class clsControlador
{
  /**
   * @var boolean
   */
  public $logado;

  /**
   * @var string
   */
  public $erroMsg;

  /**
   * Construtor.
   */
  public function __construct()
  {
    if ('logado' == Session::get('itj_controle')) {
      $this->logado = TRUE;
    }
    else {
      $this->logado = FALSE;
    }

    $this->_maximoTentativasFalhas = 7;
    $this->messenger = new Portabilis_Messenger();
  }

  /**
   * Retorna TRUE para usuário logado
   * @return  boolean
   */
  public function Logado()
  {
    return $this->logado;
  }

  // novo metodo login, logica quebrada em metodos menores
  public function Logar($validateUserCredentials) {
    if ($validateUserCredentials) {
      $user = $this->validateUserCredentials($username = @$_POST['login'], $password = md5(@$_POST['senha']));

      if ($this->canStartLoginSession($user)) {
        $this->startLoginSession($user);
        return null;
      }
    }

    $this->renderLoginPage();
  }

  // valida se o usuário e senha informados, existem no banco de dados.
  protected function validateUserCredentials($username, $password) {
    if (! $this->validateHumanAccess()) {
      $msg = "Você errou a senha muitas vezes, por favor, preencha o campo de " .
             "confirmação visual ou <a class='light decorated' href='/module/Usuario/Rede" .
             "finirSenha'>redefina sua senha</a>.";
      $this->messenger->append($msg, "error", false, "error");
    }

    else {
      $user = Portabilis_Utils_User::loadUsingCredentials($username, $password);

      if (is_null($user)) {
        $this->messenger->append("Usuário ou senha incorreta.", "error");
        $this->incrementTentativasLogin();
      }
      else {
        $this->unsetTentativasLogin();

        $permissoes = new clsPermissoes();
        $user['nivel'] = $permissoes->nivel_acesso($user['id']);

        return $user;
      }
    }

    return false;
  }

  public function startLoginSession($user, $redirectTo = '') {
    // unsetting login attempts here, because when the password is recovered the login attempts should be reseted.
    $this->unsetTentativasLogin();

    Session::put([
      'itj_controle' => 'logado',
      'id_pessoa' => $user['id'],
      'pessoa_setor' => $user['ref_cod_setor_new'],
      'tipo_menu' => $user['tipo_menu'],
      'nivel' => $user['nivel'],
    ]);

    Auth::loginUsingId(Session::get('id_pessoa'));

    Portabilis_Utils_User::logAccessFor($user['id'], $this->getClientIP());
    Portabilis_Utils_User::destroyStatusTokenFor($user['id'], 'redefinir_senha');

    $this->logado = true;
    $this->messenger->append("Usuário logado com sucesso.", "success");

    // solicita email para recuperação de senha, caso usuário ainda não tenha informado.
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        throw new HttpResponseException(
            new RedirectResponse('/module/Usuario/AlterarEmail')
        );
    } elseif ($user['expired_password']) {
        throw new HttpResponseException(
            new RedirectResponse('/module/Usuario/AlterarSenha')
        );
    } elseif (!empty($redirectTo)) {
        throw new HttpResponseException(
            new RedirectResponse($redirectTo)
        );
    }
  }

  public function canStartLoginSession($user) {
    if (! $this->messenger->hasMsgWithType("error")) {
      $this->checkForSuspended($user);
      $this->checkForDisabledAccount($user);
      $this->checkForBannedAccount($user);
      $this->checkForExpiredAccount($user);
      $this->checkForMultipleAccess($user);
      // #TODO verificar se conta nunca usada (exibir "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'." ?)
    }

    return ! $this->messenger->hasMsgWithType("error");
  }

  // renderiza o template de login, com as mensagens adicionadas durante validações
  protected function renderLoginPage() {
    $this->destroyLoginSession();

    $configuracoes = new clsPmieducarConfiguracoesGerais();
    $configuracoes = $configuracoes->detalhe();

    $view = View::make('login', [
      'name' => $configuracoes["ieducar_entity_name"],
      'logo' => $this->getLoginLogo($configuracoes),
      'slug' => $GLOBALS['coreExt']['Config']->app->database->dbname,
      'tagmanager' => $GLOBALS['coreExt']['Config']->app->gtm->id,
      'footerLogin' => $configuracoes["ieducar_login_footer"],
      'footer' => $configuracoes["ieducar_external_footer"],
      'social' => $this->getSocialMediaLinks($configuracoes),
      'register' => $configuracoes["url_cadastro_usuario"],
      'error' => $this->messenger->toHtml('p'),
    ]);

    throw new HttpResponseException(
      new Response($view->render())
    );
  }

  protected function destroyLoginSession($addMsg = false) {
    $tentativasLoginFalhas = Session::get('tentativas_login_falhas');

    Session::flush();

    //mantem tentativas_login_falhas, até que senha senha informada corretamente
    Session::put('tentativas_login_falhas', $tentativasLoginFalhas);

    if ($addMsg)
      $this->messenger->append("Usuário deslogado com sucesso.", "success");
  }

  protected function getClientIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
      // pega o (ultimo) IP real caso o host esteja atrás de um proxy
      $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      $ip = trim(array_pop($ip));
    }
    else
      $ip = $_SERVER['REMOTE_ADDR'];

    return $ip;
  }

  protected function validateHumanAccess() {
    $result = false;

    if (! $this->atingiuTentativasLogin())
      $result = true;

    elseif (Portabilis_Utils_ReCaptcha::check($_POST['g-recaptcha-response'])) {
      $this->unsetTentativasLogin();
      $result = true;
    }

    return $result;
  }

  protected function atingiuTentativasLogin() {
    return Session::get('tentativas_login_falhas')
      && is_numeric(Session::get('tentativas_login_falhas'))
      && Session::get('tentativas_login_falhas') >= $this->_maximoTentativasFalhas;
  }

  protected function incrementTentativasLogin() {
    if (! Session::get('tentativas_login_falhas') or ! is_numeric(Session::get('tentativas_login_falhas')))
        Session::put('tentativas_login_falhas', 1);
    else
      Session::put('tentativas_login_falhas', Session::get('tentativas_login_falhas') + 1);
  }

  protected function unsetTentativasLogin() {
    Session::forget('tentativas_login_falhas');
  }

  protected function checkForDisabledAccount($user) {
    if ($user['ativo'] != '1') {
      $this->messenger->append("Sua conta de usuário foi desativada ou expirou, por favor, " .
                              "entre em contato com o responsável pelo sistema do seu município.", "error", false, "error");
    }
  }

  protected function checkForSuspended($user) {
    $configuracoes = new clsPmieducarConfiguracoesGerais();
    $configuracoes = $configuracoes->detalhe();

    $nivel = (int) $user['nivel'];

    if (!$configuracoes['active_on_ieducar'] && $nivel !== 1) {
        $this->messenger->append("Sua conta de usuário não pode acessar o sistema, " .
            "por favor, entre em contato com o responsável pelo sistema do seu município.", "error", false, "error");
    }
  }

  protected function checkForBannedAccount($user) {
    if ($user['proibido'] != '0') {
      $this->messenger->append("Sua conta de usuário não pode mais acessar o sistema, " .
                              "por favor, entre em contato com o responsável pelo sistema do seu município.",
                              "error", false, "error");
    }
  }

  protected function checkForExpiredAccount($user) {
    if($user['expired_account']) {

      if ($user['ativo'] == 1)
        Portabilis_Utils_User::disableAccount($user['id']);

      $this->messenger->append("Sua conta de usuário expirou, por favor, " .
                              "entre em contato com o responsável pelo sistema do seu município.", "error", false, "error");
    }
  }

  protected function checkForMultipleAccess($user) {
    // considera como acesso multiplo, acesso em diferentes IPs em menos de $tempoMultiploAcesso minutos
    $tempoMultiploAcesso = 10;
    $tempoEmEspera       = abs(time() - strtotime($user['data_login'])) / 60;

    $multiploAcesso = $tempoEmEspera <= $tempoMultiploAcesso &&
                      $user['ip_ultimo_acesso'] != $this->getClientIP();

    if ($multiploAcesso and $user['super']) {

      // #TODO mover lógica email, para mailer especifico

      $subject = "Conta do super usuário {$_SERVER['HTTP_HOST']} acessada em mais de um local";

      $message = ("Aparentemente a conta do super usuário {$user['matricula']} foi acessada em " .
                  "outro computador nos últimos $tempoMultiploAcesso " .
                  "minutos, caso não tenha sido você, por favor, altere sua senha.\n\n" .
                  "Endereço IP último acesso: {$user['ip_ultimo_acesso']}\n".
                  "Endereço IP acesso atual: {$this->getClientIP()}");

      $mailer = new Portabilis_Mailer();
      $mailer->sendMail($user['email'], $subject, $message);
    }
    elseif ($multiploAcesso) {
      $minutosEmEspera = round($tempoMultiploAcesso - $tempoEmEspera) + 1;
      $this->messenger->append("Aparentemente sua conta foi acessada em outro computador nos últimos " .
                              "$tempoMultiploAcesso minutos, caso não tenha sido você, " .
                              "por favor, altere sua senha ou tente novamente em $minutosEmEspera minutos",
                              "error", false, "error");
    }
  }

    public function getSocialMediaLinks($configuracoes){
        $socialMedia = "";

        if($configuracoes['facebook_url'] || $configuracoes['linkedin_url'] || $configuracoes['twitter_url']){
            $socialMedia .= "<p> Siga-nos nas redes sociais&nbsp;&nbsp;</p>";
        }

        if($configuracoes['facebook_url']){
            $socialMedia .= '<a target="_blank" href="'.$configuracoes['facebook_url'].'"><img src="/intranet/imagens/icon-social-facebook.png"></a> ';
        }
        if($configuracoes['linkedin_url']){
            $socialMedia .= '<a target="_blank" href="'.$configuracoes['linkedin_url'].'"><img src="/intranet/imagens/icon-social-linkedin.png"></a> ';
        }
        if($configuracoes['twitter_url']){
            $socialMedia .= '<a target="_blank" href="'.$configuracoes['twitter_url'].'"><img src="/intranet/imagens/icon-social-twitter.png"></a> ';
        }

        return $socialMedia;
    }

    public function getLoginLogo($configuracoes)
    {
        if (empty($configuracoes['ieducar_image'])) {
            return "/intranet/imagens/brasao-republica.png";
        }

        return $configuracoes['ieducar_image'];
    }
}
