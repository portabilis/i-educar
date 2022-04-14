<?php

return new class extends clsDetalhe {
    public $titulo;

    public function Gerar()
    {
        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

        $this->titulo = 'Empresa transporte escolar - Detalhe';

        $cod_empresa_transporte_escolar = $_GET['cod_empresa'];

        $tmp_obj = new clsModulesEmpresaTransporteEscolar($cod_empresa_transporte_escolar);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('transporte_empresa_lst.php');
        }

        $objPessoaJuridica = new clsPessoaJuridica();
        list($id_federal, $endereco, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $ins_est) = $objPessoaJuridica->queryRapida($registro['ref_idpes'], 'cnpj', 'logradouro', 'cep', 'bairro', 'cidade', 'ddd_1', 'fone_1', 'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'email', 'insc_estadual');

        $this->addDetalhe(['Código da empresa', $cod_empresa_transporte_escolar]);
        $this->addDetalhe(['Nome fantasia', $registro['nome_empresa']]);
        $this->addDetalhe(['Nome do responsável', $registro['nome_responsavel']]);
        $this->addDetalhe(['CNPJ', empty($id_federal) ? '' : int2CNPJ($id_federal)]);
        $this->addDetalhe(['Endereço', $endereco]);
        $this->addDetalhe(['CEP', $cep]);
        $this->addDetalhe(['Bairro', $nm_bairro]);
        $this->addDetalhe(['Cidade', $cidade]);
        if (trim($telefone_1)!='') {
            $this->addDetalhe(['Telefone 1', "({$ddd_telefone_1}) {$telefone_1}"]);
        }
        if (trim($telefone_2)!='') {
            $this->addDetalhe(['Telefone 2', "({$ddd_telefone_2}) {$telefone_2}"]);
        }
        if (trim($telefone_mov)!='') {
            $this->addDetalhe(['Celular', "({$ddd_telefone_mov}) {$telefone_mov}"]);
        }
        if (trim($telefone_fax)!='') {
            $this->addDetalhe(['Fax', "({$ddd_telefone_fax}) {$telefone_fax}"]);
        }

        $this->addDetalhe(['E-mail', $email]);

        if (! $ins_est) {
            $ins_est = 'isento';
        }

        $this->addDetalhe(['Inscrição estadual', $ins_est]);
        $this->addDetalhe(['Observação', $registro['observacao']]);
        $this->url_cancelar = 'transporte_empresa_lst.php';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21235, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = '../module/TransporteEscolar/Empresa';
            $this->url_editar = "../module/TransporteEscolar/Empresa?id={$cod_empresa_transporte_escolar}";
        }

        $this->largura = '100%';

        $this->breadcrumb('Detalhe da empresa de transporte', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Empresas';
        $this->processoAp = 21235;
    }
};
