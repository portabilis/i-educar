<?php

use App\Models\LegacyIndividual;
use App\Models\Religion;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_religiao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_religiao=$_GET['cod_religiao'];

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(579, $this->pessoa_logada, 3, 'educar_religiao_lst.php');
        //**

        if (is_numeric($this->cod_religiao)) {
            $registro = Religion::findOrFail($this->cod_religiao, ['id', 'name']);
            if ($registro) {
                $this->nm_religiao = $registro->name;
                $this->cod_religiao = $registro->id;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(579, $this->pessoa_logada, 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_religiao_det.php?cod_religiao={$registro['id']}" : 'educar_religiao_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' religião', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('cod_religiao', $this->cod_religiao);
        $this->campoTexto('nm_religiao', 'Religião', $this->nm_religiao, 30, 255, true);
    }

    public function Novo()
    {
        $obj = new Religion();
        $obj->name = $this->nm_religiao;

        if ($obj->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';
        return false;
    }

    public function Editar()
    {
        $obj = Religion::findOrFail($this->cod_religiao);
        $obj->name = $this->nm_religiao;

        if ($obj->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        $exists = LegacyIndividual::where('ref_cod_religiao', $this->cod_religiao)
            ->exists();

        if ($exists) {
            $this->mensagem = 'Você não pode excluir essa Religião, pois ela possui Pessoa(s) Física(s) vinculadas.<br>';
            return false;
        }

        $obj = Religion::findOrFail($this->cod_religiao);

        if ($obj->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';
        return false;
    }

    public function Formular()
    {
        $this->title = 'Religiao';
        $this->processoAp = '579';
    }
};
