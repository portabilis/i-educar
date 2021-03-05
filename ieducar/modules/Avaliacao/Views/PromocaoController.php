<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);



// TODO migrar para novo padrao

class PromocaoController extends Portabilis_Controller_Page_ListController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
    protected $_titulo     = 'Lan&ccedil;amento por turma';
    protected $_processoAp = 644;
    protected $_formMap    = [];

    public function Gerar()
    {
        $this->inputsHelper()->dynamic('ano', ['id' => 'ano']);
        $this->inputsHelper()->dynamic('instituicao', ['id' => 'instituicao_id']);
        $this->inputsHelper()->dynamic('escola', ['id' => 'escola', 'required' => false]);
        $this->inputsHelper()->dynamic('curso', ['id' => 'curso', 'required' => false]);
        $this->inputsHelper()->dynamic('serie', ['id' => 'serie', 'required' => false]);
        $this->inputsHelper()->dynamic('turma', ['id' => 'turma', 'required' => false]);
        $this->inputsHelper()->dynamic('situacaoMatricula', ['id' => 'matricula', 'value' => 10, 'required' => false]);

        $this->loadResourceAssets($this->getDispatcher());

        $this->breadcrumb('Atualização de matrículas', [
        url('intranet/educar_configuracoes_index.php') => 'Configurações',
    ]);
    }
}
