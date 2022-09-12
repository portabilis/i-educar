<?php

use App\Models\LegacyEducationNetwork;
use App\Models\LegacySchool;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_escola_rede_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_rede;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_escola_rede_ensino=$_GET['cod_escola_rede_ensino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        if (is_numeric($this->cod_escola_rede_ensino)) {
            $registro = LegacyEducationNetwork::findOrFail($this->cod_escola_rede_ensino)?->toArray();

            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(647, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro['cod_escola_rede_ensino']}" : 'educar_escola_rede_ensino_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' rede de ensino', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_escola_rede_ensino', $this->cod_escola_rede_ensino);

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_rede', 'Rede Ensino', $this->nm_rede, 30, 255, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        $network = new LegacyEducationNetwork();
        $network->ref_usuario_cad = $this->pessoa_logada;
        $network->nm_rede = $this->nm_rede;
        $network->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($network->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_escola_rede_ensino_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';
        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        $network = LegacyEducationNetwork::findOrFail($this->cod_escola_rede_ensino);
        $network->ref_usuario_exc = $this->pessoa_logada;
        $network->nm_rede = $this->nm_rede;
        $network->ativo = 1;
        $network->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($network->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escola_rede_ensino_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        $count = LegacySchool::query()
            ->where('ref_cod_escola_rede_ensino', $this->cod_escola_rede_ensino)
            ->count();

        if ($count > 0) {
            $this->mensagem = 'Você não pode excluir essa Rede de Ensino, pois ele possui vínculo com Escola(s).<br>';
            return false;
        }

        $network = LegacyEducationNetwork::findOrFail($this->cod_escola_rede_ensino);
        $network->ref_usuario_exc = $this->pessoa_logada;
        $network->ativo = 0;

        if ($network->save()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escola_rede_ensino_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';
        return false;
    }

    public function Formular()
    {
        $this->title = 'Escola Rede Ensino';
        $this->processoAp = '647';
    }
};
