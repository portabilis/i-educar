<?php

// TODO migrar para novo padrao

class PromocaoController extends Portabilis_Controller_Page_ListController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
    protected $_titulo     = 'Lançamento por turma';
    protected $_processoAp = 644;
    protected $_formMap    = [];
    private $regras_avaliacao_id;

    public function Gerar()
    {
        $regras = (new RegraAvaliacao_Model_RegraDataMapper())->findAll([], []);
        $regras = CoreExt_Entity::entityFilterAttr($regras, 'id', 'nome');

        $regras = ['' => 'Todas'] + $regras;

        $this->inputsHelper()->dynamic('ano', ['id' => 'ano']);
        $this->inputsHelper()->dynamic('instituicao', ['id' => 'instituicao_id']);
        $this->inputsHelper()->dynamic('escola', ['id' => 'escola', 'required' => false]);
        $this->inputsHelper()->dynamic('curso', ['id' => 'curso', 'required' => false]);
        $this->inputsHelper()->dynamic('serie', ['id' => 'serie', 'required' => false]);
        $this->inputsHelper()->dynamic('turma', ['id' => 'turma', 'required' => false]);
        $this->inputsHelper()->dynamic('situacaoMatricula', ['id' => 'matricula', 'value' => 10, 'required' => false]);
        $this->campoLista('regras_avaliacao_id', 'Regra de avaliação (padrão)', $regras, $this->regras_avaliacao_id, '',false,'','', false,false);

        $this->loadResourceAssets($this->getDispatcher());

        $this->breadcrumb('Atualização de matrículas', [
        url('intranet/educar_configuracoes_index.php') => 'Configurações',
    ]);
    }
}
