<?php

require_once 'Portabilis/Controller/Page/ListController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Business/Professor.php';

class DiarioController extends Portabilis_Controller_Page_ListController
{
    protected $_titulo = 'Lan&ccedil;amento por turma';
    protected $_processoAp = 642;

    public function Gerar()
    {
        $userId = Portabilis_Utils_User::currentUserId();
        $componenteRequired = $isProfessor = Portabilis_Business_Professor::isProfessor(false, $userId);

        $this->inputsHelper()->input('ano', 'ano');
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie', 'turma', 'etapa']);
        $this->inputsHelper()->dynamic(['componenteCurricularForDiario'], ['required' => $componenteRequired]);
        $this->inputsHelper()->dynamic(['matricula'], ['required' => false ]);

        $navegacaoTab = [
            '1' => 'Horizontal(padr&atilde;o)',
            '2' => 'Vertical',
        ];

        $options = [
            'label' =>'Navega&ccedil;&atilde;o do cursor(tab)',
            'resources' => $navegacaoTab,
            'required' => false,
            'inline' => true,
            'value' => $navegacaoTab[1]
        ];

        $this->inputsHelper()->select('navegacao_tab', $options);

        $this->inputsHelper()->hidden('mostrar_botao_replicar_todos', ['value' => $teste = config('legacy.app.faltas_notas.mostrar_botao_replicar')]);

        $this->loadResourceAssets($this->getDispatcher());
    }

    protected function _preRender()
    {
        parent::_preRender();

        Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
         'educar_index.php'                  => 'Escola',
         ''                                  => 'Lan&ccedil;amento de notas'
    ]);
        $this->enviaLocalizacao($localizacao->montar(), true);
    }
}
?>

