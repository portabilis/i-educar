<?php

return new class extends clsDetalhe
{
    public function Gerar()
    {
        $this->titulo = 'Detalhe da empresa';

        $cod_empresa = @$_GET['cod_empresa'];

        $objPessoaJuridica = new clsPessoaJuridica();
        [$cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $http, $email, $ins_est, $tipo_pessoa, $razao_social, $capital_social, $ins_mun, $idtlog] = $objPessoaJuridica->queryRapida($cod_empresa, 'idpes', 'fantasia', 'cnpj', 'logradouro', 'cep', 'bairro', 'cidade', 'ddd_1', 'fone_1', 'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'url', 'email', 'insc_estadual', 'tipo', 'nome', 'insc_municipal', 'idtlog');
        $endereco = "$idtlog $endereco";

        $this->addDetalhe(detalhe: ['Razão Social', $razao_social]);
        $this->addDetalhe(detalhe: ['Nome Fantasia', $nm_pessoa]);
        $this->addDetalhe(detalhe: ['CNPJ', empty($id_federal) ? '' : int2CNPJ(int: $id_federal)]);
        $this->addDetalhe(detalhe: ['Endereço', $endereco]);
        $this->addDetalhe(detalhe: ['CEP', $cep]);
        $this->addDetalhe(detalhe: ['Bairro', $nm_bairro]);
        $this->addDetalhe(detalhe: ['Cidade', $cidade]);

        $this->addDetalhe(detalhe: ['Telefone 1', $this->preparaTelefone(ddd: $ddd_telefone_1, telefone: $telefone_1)]);
        $this->addDetalhe(detalhe: ['Telefone 2', $this->preparaTelefone(ddd: $ddd_telefone_2, telefone: $telefone_2)]);
        $this->addDetalhe(detalhe: ['Celular', $this->preparaTelefone(ddd: $ddd_telefone_mov, telefone: $telefone_mov)]);
        $this->addDetalhe(detalhe: ['Fax', $this->preparaTelefone(ddd: $ddd_telefone_fax, telefone: $telefone_fax)]);

        $this->addDetalhe(detalhe: ['Site', $http]);
        $this->addDetalhe(detalhe: ['E-mail', $email]);

        if (!$ins_est) {
            $ins_est = 'isento';
        }
        $this->addDetalhe(detalhe: ['Inscrição Estadual', $ins_est]);
        $this->addDetalhe(detalhe: ['Capital Social', $capital_social]);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 41, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->url_novo = 'empresas_cad.php';
            $this->url_editar = "empresas_cad.php?idpes={$cod_empresa}";
        }

        $this->url_cancelar = 'empresas_lst.php';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da pessoa jurídica', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    private function preparaTelefone($ddd, $telefone)
    {
        return !empty($telefone) ? "({$ddd}) {$telefone}" : '';
    }

    public function Formular()
    {
        $this->title = 'Empresas';
        $this->processoAp = 41;
    }
};
