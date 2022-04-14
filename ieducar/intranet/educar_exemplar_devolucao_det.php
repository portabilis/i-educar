<?php

use Illuminate\Support\Facades\Session;

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_emprestimo;
    public $ref_usuario_devolucao;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $ref_cod_exemplar;
    public $data_retirada;
    public $data_devolucao;
    public $valor_multa;

    public function Gerar()
    {
        Session::forget('reload');
        Session::save();
        Session::start();

        $this->titulo = 'Exemplar Devolu&ccedil;&atilde;o - Detalhe';

        $this->cod_emprestimo=$_GET['cod_emprestimo'];

        if (!$this->cod_emprestimo) {
            $this->simpleRedirect('educar_exemplar_devolucao_lst.php');
        }

        $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
        $lista = $obj_exemplar_emprestimo->lista($this->cod_emprestimo);
        if (is_array($lista) && count($lista)) {
            $registro = array_shift($lista);

            if (! $registro) {
                $this->simpleRedirect('educar_exemplar_devolucao_lst.php');
            }

            $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
            $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
            $registro['ref_cod_biblioteca'] = $det_ref_cod_biblioteca['nm_biblioteca'];

            $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
            $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $idpes = $det_ref_cod_escola['ref_idpes'];

            $obj_escola = new clsPessoaJuridica($idpes);
            $obj_escola_det = $obj_escola->detalhe();
            $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];

            $obj_ref_cod_exemplar = new clsPmieducarExemplar($registro['ref_cod_exemplar']);
            $det_ref_cod_exemplar = $obj_ref_cod_exemplar->detalhe();

            $acervo = $det_ref_cod_exemplar['ref_cod_acervo'];
            $obj_acervo = new clsPmieducarAcervo($acervo);
            $det_acervo = $obj_acervo->detalhe();
            $titulo_exemplar = $det_acervo['titulo'];

            $obj_cliente = new clsPmieducarCliente($registro['ref_cod_cliente']);
            $det_cliente = $obj_cliente->detalhe();
            $ref_idpes = $det_cliente['ref_idpes'];
            $obj_pessoa = new clsPessoa_($ref_idpes);
            $det_pessoa = $obj_pessoa->detalhe();
            $registro['ref_cod_cliente'] = $det_pessoa['nome'];
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($registro['ref_cod_escola']) {
                $this->addDetalhe([ 'Escola', "{$registro['ref_cod_escola']}"]);
            }
        }
        if ($registro['ref_cod_biblioteca']) {
            $this->addDetalhe([ 'Biblioteca', "{$registro['ref_cod_biblioteca']}"]);
        }
        if ($registro['ref_cod_cliente']) {
            $this->addDetalhe([ 'Cliente', "{$registro['ref_cod_cliente']}"]);
        }
        if ($titulo_exemplar) {
            $this->addDetalhe([ 'Obra', "{$titulo_exemplar}"]);
        }

        $this->addDetalhe([ 'Código exemplar', "{$registro['ref_cod_exemplar']}"]);
        $this->addDetalhe([ 'Tombo', "{$det_ref_cod_exemplar['tombo']}"]);

        if ($registro['data_retirada']) {
            $this->addDetalhe([ 'Data Retirada', dataFromPgToBr($registro['data_retirada'], 'd/m/Y') ]);
        }
        if ($registro['valor_multa']) {
            $this->addDetalhe([ 'Valor Multa', "{$registro['valor_multa']}"]);
        }

        if ($obj_permissoes->permissao_cadastra(628, $this->pessoa_logada, 11)) {
            $this->array_botao = [];
            $this->array_botao_url_script = [];

            $this->array_botao[] = 'Devolução';
            $this->array_botao_url_script[] = "go(\"educar_exemplar_devolucao_cad.php?cod_emprestimo={$registro['cod_emprestimo']}\");";
            $this->array_botao[] = 'Renovação';
            $this->array_botao_url_script[] = "go(\"educar_exemplar_renovacao_cad.php?cod_emprestimo={$registro['cod_emprestimo']}\");";
        }

        $this->url_cancelar = 'educar_exemplar_devolucao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do exemplar para devolução', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Exemplar Devolu&ccedil;&atilde;o';
        $this->processoAp = '628';
    }
};
