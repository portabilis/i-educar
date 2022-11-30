<?php

use App\Models\LegacyRace;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_raca;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_raca;
    public $data_cadastro;
    public $data_exclusao;
    public $raca_educacenso;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_raca=$_GET['cod_raca'];

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7, 'educar_raca_lst.php');

        if (is_numeric($this->cod_raca)) {
            $races = LegacyRace::query()->find($this->cod_raca)?->toArray();
            if ($races) {

                $this->nm_race = $races->nm_race;
                $this->cod_raca = $races->cod_raca;

                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $this->fexcluir = $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7);

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_raca_det.php?cod_raca=$this->cod_raca" : 'educar_raca_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' raça', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_raca', $this->cod_raca);

        $this->campoTexto('nm_raca', 'Raça', $this->nm_raca, 30, 255, true);

        $resources = [  0 => 'Não declarada',
                                1 => 'Branca',
                                2 => 'Preta',
                                3 => 'Parda',
                                4 => 'Amarela',
                                5 => 'Indígena'];

        $options = ['label' => 'Raça educacenso', 'resources' => $resources, 'value' => $this->raca_educacenso];
        $this->inputsHelper()->select('raca_educacenso', $options);
    }

    public function Novo()
    {
        $race = LegacyRace::query()
            ->create(
                 [
                     'idpes_cad' => $this->pessoa_logada,
                     'nm_raca' => $this->nm_raca,
                     'raca_educacenso' => $this->raca_educacenso
                 ]
             )
         ;

        if ($race) {
            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $race = LegacyRace::query()->findOrFail($this->cod_raca);

        $race->ativo = 1;
        $race->nm_raca = $this->nm_raca;
        $race->idpes_cad = $this->pessoa_logada;
        $race->raca_educacenso = $this->raca_educacenso;


        if ($race->save()) {
            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $race = LegacyRace::query()->findOrFail($this->cod_raca);

        $race->ativo = 0;
        $race->idpes_cad = $this->pessoa_logada;

        if ($race->save()) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Raça';
        $this->processoAp = '678';
    }
};
