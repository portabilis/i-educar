<?php

use App\Models\LegacyPerson;

return new class extends clsDetalhe
{
    public function Gerar()
    {
        $this->titulo = 'Detalhe da empresa';

        $cod_empresa = @$_GET['cod_empresa'];

        $pessoa = LegacyPerson::with(['organization', 'phones', 'place.city'])->find($cod_empresa);

        $razao_social = $pessoa?->nome;
        $nm_pessoa = $pessoa?->organization?->fantasia;
        $id_federal = $pessoa?->organization?->cnpj;
        $http = $pessoa?->url;
        $email = $pessoa?->email;
        $ins_est = $pessoa?->organization?->insc_estadual;
        $capital_social = $pessoa?->organization?->capital_social;

        $endereco = $pessoa?->place?->address;
        $cep = $pessoa?->place?->postal_code;
        $nm_bairro = $pessoa?->place?->neighborhood;
        $cidade = $pessoa?->place?->city?->name;

        $ddd_telefone_1 = $telefone_1 = null;
        $ddd_telefone_2 = $telefone_2 = null;
        $ddd_telefone_mov = $telefone_mov = null;
        $ddd_telefone_fax = $telefone_fax = null;

        foreach ($pessoa?->phones ?? [] as $phone) {
            if ($sufixo = $phone->legacy_suffix) {
                ${"ddd_telefone_$sufixo"} = $phone->ddd;
                ${"telefone_$sufixo"} = $phone->fone;
            }
        }

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

        $obj_permissao = new clsPermissoes;

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
