<?php

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_instituicao;

    public $nm_instituicao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_idtlog;

    public $ref_sigla_uf;

    public $cep;

    public $cidade;

    public $bairro;

    public $logradouro;

    public $numero;

    public $complemento;

    public $nm_responsavel;

    public $ddd_telefone;

    public $telefone;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Instituição - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(['Nome da Instituição']);

        // outros Filtros
        $this->campoTexto(nome: 'nm_instituicao', campo: 'Nome da Instituição', valor: $this->nm_instituicao, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_instituicao = new clsPmieducarInstituicao();
        $obj_instituicao->setOrderby('nm_responsavel ASC');
        $obj_instituicao->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);
        $lista = $obj_instituicao->lista(
            int_cod_instituicao: $this->cod_instituicao,
            str_ref_sigla_uf: $this->ref_sigla_uf,
            int_cep: $this->cep,
            str_cidade: $this->cidade,
            str_bairro: $this->bairro,
            str_logradouro: $this->logradouro,
            int_numero: $this->numero,
            str_complemento: $this->complemento,
            str_nm_responsavel: $this->nm_responsavel,
            int_ddd_telefone: $this->ddd_telefone,
            int_telefone: $this->telefone,
            date_data_cadastro: $this->data_cadastro,
            date_data_exclusao: $this->data_exclusao,
            int_ativo: 1,
            str_nm_instituicao: $this->nm_instituicao
        );

        $total = $obj_instituicao->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"educar_instituicao_det.php?cod_instituicao={$registro['cod_instituicao']}\">{$registro['nm_instituicao']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_instituicao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de instituições', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Instituicao';
        $this->processoAp = '559';
    }
};
