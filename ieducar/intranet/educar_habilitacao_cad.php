<?php

use App\Models\LegacyQualification;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_habilitacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_habilitacao=$_GET['cod_habilitacao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(573, $this->pessoa_logada, 3, 'educar_habilitacao_lst.php');

        if (is_numeric($this->cod_habilitacao)) {
            $registro = LegacyQualification::find($this->cod_habilitacao)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $this->fexcluir = $obj_permissoes->permissao_excluir(573, $this->pessoa_logada, 3);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_habilitacao_det.php?cod_habilitacao={$registro['cod_habilitacao']}" : 'educar_habilitacao_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' habilitação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_habilitacao', $this->cod_habilitacao);
        // foreign keys

        $get_escola = false;
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');
        // text
        $this->campoTexto('nm_tipo', 'Habilitação', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $habilitacao = new LegacyQualification();
        $habilitacao->ref_usuario_cad = $this->pessoa_logada;
        $habilitacao->nm_tipo = $this->nm_tipo;
        $habilitacao->descricao = $this->descricao;
        $habilitacao->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($habilitacao->save()) {
            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_habilitacao_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';
        return false;
    }

    public function Editar()
    {
        $habilitacao = LegacyQualification::find($this->cod_habilitacao);
        $habilitacao->ref_usuario_exc = $this->pessoa_logada;
        $habilitacao->ativo = 1;
        $habilitacao->nm_tipo = $this->nm_tipo;
        $habilitacao->descricao = $this->descricao;
        $habilitacao->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($habilitacao->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_habilitacao_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        $habilitacao = LegacyQualification::find($this->cod_habilitacao);
        $habilitacao->ref_usuario_exc = $this->pessoa_logada;
        $habilitacao->ativo = 0;

        if ($habilitacao->save()) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_habilitacao_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';
        return false;
    }

    public function Formular()
    {
        $this->titulo = 'i-Educar - Habilitação';
        $this->processoAp = '573';
    }
};
