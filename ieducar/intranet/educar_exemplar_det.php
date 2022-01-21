<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_exemplar;
    public $ref_cod_fonte;
    public $ref_cod_motivo_baixa;
    public $ref_cod_acervo;
    public $ref_cod_situacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $permite_emprestimo;
    public $preco;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $data_aquisicao;

    public function Gerar()
    {
        $this->titulo = 'Exemplar - Detalhe';

        $this->cod_exemplar=$_GET['cod_exemplar'];

        $tmp_obj = new clsPmieducarExemplar($this->cod_exemplar);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_exemplar_lst.php');
        }

        $obj_ref_cod_fonte = new clsPmieducarFonte($registro['ref_cod_fonte']);
        $det_ref_cod_fonte = $obj_ref_cod_fonte->detalhe();
        $registro['ref_cod_fonte'] = $det_ref_cod_fonte['nm_fonte'];

        $obj_ref_cod_motivo_baixa = new clsPmieducarMotivoBaixa($registro['ref_cod_motivo_baixa']);
        $det_ref_cod_motivo_baixa = $obj_ref_cod_motivo_baixa->detalhe();
        $registro['ref_cod_motivo_baixa'] = $det_ref_cod_motivo_baixa['nm_motivo_baixa'];

        $obj_ref_cod_acervo = new clsPmieducarAcervo($registro['ref_cod_acervo']);
        $det_ref_cod_acervo = $obj_ref_cod_acervo->detalhe();
        $registro['ref_cod_acervo'] = $det_ref_cod_acervo['titulo'];

        $obj_ref_cod_situacao = new clsPmieducarSituacao($registro['ref_cod_situacao']);
        $det_ref_cod_situacao = $obj_ref_cod_situacao->detalhe();
        $registro['ref_cod_situacao'] = $det_ref_cod_situacao['nm_situacao'];

        $this->addDetalhe(['Código', "{$registro['cod_exemplar']}"]);
        $this->addDetalhe(['Tombo',  "{$registro['tombo']}"]);

        if ($registro['ref_cod_acervo']) {
            $this->addDetalhe([ 'Obra Referéncia', "{$registro['ref_cod_acervo']}"]);
        }
        if ($registro['ref_cod_fonte']) {
            $this->addDetalhe([ 'Fonte', "{$registro['ref_cod_fonte']}"]);
        }
        if ($registro['ref_cod_motivo_baixa']) {
            $this->addDetalhe([ 'Motivo Baixa', "{$registro['ref_cod_motivo_baixa']}"]);
        }
        if ($registro['data_baixa_exemplar']) {
            $this->addDetalhe([ 'Data Baixa', dataFromPgToBr($registro['data_baixa_exemplar'])]);
        }

        if ($registro['ref_cod_situacao']) {
            $this->addDetalhe([ 'Situacão', "{$registro['ref_cod_situacao']}"]);
        }
        if ($registro['permite_emprestimo']) {
            $registro['permite_emprestimo'] = $registro['permite_emprestimo'] == 2 ? 'Sim' :'Não';

            $this->addDetalhe([ 'Permite Empréstimo', "{$registro['permite_emprestimo']}"]);
        }
        if ($registro['preco']) {
            $registro['preco'] = number_format($registro['preco'], 2, ',', '.');
            $this->addDetalhe([ 'Preço', "{$registro['preco']}"]);
        }
        if ($registro['data_aquisicao']) {
            $this->addDetalhe([ 'Data Aquisicão', dataFromPgToBr($registro['data_aquisicao'], 'd/m/Y') ]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(606, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_exemplar_cad.php';
            $this->url_editar = "educar_exemplar_cad.php?cod_exemplar={$registro['cod_exemplar']}";

            if (!$registro['ref_cod_motivo_baixa']) {
                $this->array_botao = ['Baixa'];
                $this->array_botao_url = ["educar_exemplar_baixa.php?cod_exemplar={$registro['cod_exemplar']}"];
            }
        }

        $this->url_cancelar = 'educar_exemplar_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do exemplar', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Exemplar';
        $this->processoAp = '606';
    }
};
