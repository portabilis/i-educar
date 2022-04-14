<?php

use App\Models\State;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_acervo_editora;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $nm_editora;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $telefone;
    public $ddd_telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_acervo_editora=$_GET['cod_acervo_editora'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        if (is_numeric($this->cod_acervo_editora)) {
            $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(595, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_acervo_editora_det.php?cod_acervo_editora={$registro['cod_acervo_editora']}" : 'educar_acervo_editora_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' editora', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_acervo_editora', $this->cod_acervo_editora);

        //foreign keys
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca']);

        //text
        $this->campoTexto('nm_editora', 'Editora', $this->nm_editora, 30, 255, true);

        // foreign keys
        if ($this->cod_acervo_editora) {
            $this->cep = int2CEP($this->cep);
        }

        $this->campoCep('cep', 'CEP', $this->cep, false);

        $opcoes = [ '' => 'Selecione' ] + State::getListKeyAbbreviation()->toArray();

        $this->campoLista('ref_sigla_uf', 'Estado', $opcoes, $this->ref_sigla_uf, '', false, '', '', false, false);

        $this->campoTexto('cidade', 'Cidade', $this->cidade, 30, 60, false);
        $this->campoTexto('bairro', 'Bairro', $this->bairro, 30, 60, false);

        $opcoes = [ '' => 'Selecione' ];

        $this->campoLista('ref_idtlog', 'Tipo Logradouro', $opcoes, $this->ref_idtlog, '', false, '', '', false, false);

        $this->campoTexto('logradouro', 'Logradouro', $this->logradouro, 30, 255, false);

        $this->campoNumero('numero', 'N&uacute;mero', $this->numero, 6, 6);

        $this->campoNumero('ddd_telefone', 'DDD Telefone', $this->ddd_telefone, 2, 2, false);
        $this->campoNumero('telefone', 'Telefone', $this->telefone, 10, 15, false);

        // data
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        $this->cep = idFederal2int($this->cep);

        $obj = new clsPmieducarAcervoEditora(null, $this->pessoa_logada, null, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1, $this->ref_cod_biblioteca);
        $this->cod_acervo_editora = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_acervo_editora = $this->cod_acervo_editora;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_editora_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        $this->cep = idFederal2int($this->cep);

        $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora, null, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_acervo_editora_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_acervo_editora_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Editora';
        $this->processoAp = '595';
    }
};
