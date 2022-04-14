<?php

return new class extends clsDetalhe {
    public function Gerar()
    {
        $this->titulo = 'Detalhe da empresa';

        $cod_empresa = @$_GET['cod_empresa'];

        $objPessoaJuridica = new clsPessoaJuridica();
        [$cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $http, $email, $ins_est, $tipo_pessoa, $razao_social, $capital_social, $ins_mun, $idtlog] = $objPessoaJuridica->queryRapida($cod_empresa, 'idpes', 'fantasia', 'cnpj', 'logradouro', 'cep', 'bairro', 'cidade', 'ddd_1', 'fone_1', 'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'url', 'email', 'insc_estadual', 'tipo', 'nome', 'insc_municipal', 'idtlog');
        $endereco = "$idtlog $endereco";
        $db = new clsBanco();

        $this->addDetalhe(['Razão Social', $razao_social]);
        $this->addDetalhe(['Nome Fantasia', $nm_pessoa]);
        $this->addDetalhe(['CNPJ', empty($id_federal) ? '' : int2CNPJ($id_federal)]);
        $this->addDetalhe(['Endereço', $endereco]);
        $this->addDetalhe(['CEP', $cep]);
        $this->addDetalhe(['Bairro', $nm_bairro]);
        $this->addDetalhe(['Cidade', $cidade]);

        $this->addDetalhe(['Telefone 1', $this->preparaTelefone($ddd_telefone_1, $telefone_1)]);
        $this->addDetalhe(['Telefone 2', $this->preparaTelefone($ddd_telefone_2, $telefone_2)]);
        $this->addDetalhe(['Celular', $this->preparaTelefone( $ddd_telefone_mov, $telefone_mov)]);
        $this->addDetalhe(['Fax', $this->preparaTelefone($ddd_telefone_fax, $telefone_fax)]);

        $this->addDetalhe(['Site', $http]);
        $this->addDetalhe(['E-mail', $email]);

        if (! $ins_est) {
            $ins_est = 'isento';
        }
        $this->addDetalhe(['Inscrição Estadual', $ins_est]);
        $this->addDetalhe(['Capital Social', $capital_social]);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(41, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'empresas_cad.php';
            $this->url_editar = "empresas_cad.php?idpes={$cod_empresa}";
        }

        $this->url_cancelar = 'empresas_lst.php';

        $this->largura = '100%';

        $this->breadcrumb('Detalhe da pessoa jurídica', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    private function preparaTelefone($ddd, $telefone)
    {
        return !empty($telefone) ?  "({$ddd}) {$telefone}" : "";
    }

    public function Formular()
    {
        $this->title = 'Empresas';
        $this->processoAp = 41;
    }
};
