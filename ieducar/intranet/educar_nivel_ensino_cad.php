<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_nivel_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_nivel;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_nivel_ensino=$_GET['cod_nivel_ensino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(571, $this->pessoa_logada, 3, 'educar_nivel_ensino_lst.php');

        if (is_numeric($this->cod_nivel_ensino)) {
            $obj = new clsPmieducarNivelEnsino($this->cod_nivel_ensino);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(571, $this->pessoa_logada, 3);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_nivel_ensino_det.php?cod_nivel_ensino={$registro['cod_nivel_ensino']}" : 'educar_nivel_ensino_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' nível de ensino', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_nivel_ensino', $this->cod_nivel_ensino);

        // foreign keys
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_nivel', 'N&iacute;vel Ensino', $this->nm_nivel, 30, 255, true);
        $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj = new clsPmieducarNivelEnsino(null, null, $this->pessoa_logada, $this->nm_nivel, $this->descricao, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_nivel_ensino_lst.php')
            );
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarNivelEnsino($this->cod_nivel_ensino, $this->pessoa_logada, null, $this->nm_nivel, $this->descricao, null, null, 1, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_nivel_ensino_lst.php')
            );
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarNivelEnsino($this->cod_nivel_ensino, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_nivel_ensino_lst.php')
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'N&iacute;vel Ensino';
        $this->processoAp = '571';
    }
};
