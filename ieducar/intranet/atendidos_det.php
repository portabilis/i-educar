<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyRace;
use App\Services\FileService;
use App\Services\UrlPresigner;

return new class extends clsDetalhe
{
    public function Gerar()
    {
        $this->titulo = 'Detalhe da Pessoa';

        $cod_pessoa = (int) $this->getQueryString(name: 'cod_pessoa');

        $objPessoa = new clsPessoaFisica(int_idpes: $cod_pessoa);

        $detalhe = $objPessoa->queryRapida(
            $cod_pessoa,
            'idpes',
            'complemento',
            'nome',
            'cpf',
            'data_nasc',
            'logradouro',
            'idtlog',
            'numero',
            'apartamento',
            'cidade',
            'sigla_uf',
            'cep',
            'ddd_1',
            'fone_1',
            'ddd_2',
            'fone_2',
            'ddd_mov',
            'fone_mov',
            'ddd_fax',
            'fone_fax',
            'email',
            'url',
            'tipo',
            'sexo',
            'zona_localizacao',
            'nome_social'
        );

        $objFoto = new clsCadastroFisicaFoto(idpes: $cod_pessoa);
        $caminhoFoto = $objFoto->detalhe();
        if ($caminhoFoto != false) {
            $this->addDetalhe(detalhe: ['Nome', $detalhe['nome'].'
                <p><img height="117" src="' . (new UrlPresigner())->getPresignedUrl(url: $caminhoFoto['caminho']) . '"/></p>']);
        } else {
            $this->addDetalhe(detalhe: ['Nome', $detalhe['nome']]);
        }

        if ($detalhe['nome_social']) {
            $this->addDetalhe(detalhe: ['Nome social e/ou afetivo', $detalhe['nome_social']]);
        }

        $this->addDetalhe(detalhe: ['CPF', int2cpf(int: $detalhe['cpf'])]);

        if ($detalhe['data_nasc']) {
            $this->addDetalhe(detalhe: ['Data de Nascimento', dataFromPgToBr(data_original: $detalhe['data_nasc'])]);
        }

        // Cor/Raça.
        $raca = new clsCadastroFisicaRaca(ref_idpes: $cod_pessoa);
        $raca = $raca->detalhe();
        if (is_array(value: $raca)) {
            $nameRace = LegacyRace::query()
                ->whereKey(id: $raca['ref_cod_raca'])
                ->value(column: 'nm_raca');

            if ($nameRace) {
                $this->addDetalhe(detalhe: ['Raça', $nameRace]);
            }
        }

        if ($detalhe['logradouro']) {
            if ($detalhe['numero']) {
                $end = ' nº ' . $detalhe['numero'];
            }

            $this->addDetalhe(detalhe: ['Endereço', $detalhe['logradouro'] . ' ' . $end]);
        }

        if ($detalhe['complemento']) {
            $this->addDetalhe(detalhe: ['Complemento', $detalhe['complemento']]);
        }

        if ($detalhe['cidade']) {
            $this->addDetalhe(detalhe: ['Cidade', $detalhe['cidade']]);
        }

        if ($detalhe['sigla_uf']) {
            $this->addDetalhe(detalhe: ['Estado', $detalhe['sigla_uf']]);
        }

        $zona = App_Model_ZonaLocalizacao::getInstance();
        if ($detalhe['zona_localizacao']) {
            $this->addDetalhe(detalhe: [
                'Zona Localização', $zona->getValue(key: $detalhe['zona_localizacao']),
            ]);
        }

        if ($detalhe['cep']) {
            $this->addDetalhe(detalhe: ['CEP', int2cep(int: $detalhe['cep'])]);
        }

        if ($detalhe['fone_1']) {
            $this->addDetalhe(
                detalhe: ['Telefone 1', sprintf('(%s) %s', $detalhe['ddd_1'], $detalhe['fone_1'])]
            );
        }

        if ($detalhe['fone_2']) {
            $this->addDetalhe(
                detalhe: ['Telefone 2', sprintf('(%s) %s', $detalhe['ddd_2'], $detalhe['fone_2'])]
            );
        }

        if ($detalhe['fone_mov']) {
            $this->addDetalhe(
                detalhe: ['Celular', sprintf('(%s) %s', $detalhe['ddd_mov'], $detalhe['fone_mov'])]
            );
        }

        if ($detalhe['fone_fax']) {
            $this->addDetalhe(
                detalhe: ['Fax', sprintf('(%s) %s', $detalhe['ddd_fax'], $detalhe['fone_fax'])]
            );
        }

        if ($detalhe['url']) {
            $this->addDetalhe(detalhe: ['Site', $detalhe['url']]);
        }

        if ($detalhe['email']) {
            $this->addDetalhe(detalhe: ['E-mail', $detalhe['email']]);
        }

        if ($detalhe['sexo']) {
            $this->addDetalhe(detalhe: ['Sexo', $detalhe['sexo'] == 'M' ? 'Masculino' : 'Feminino']);
        }

        $fileService = new FileService(urlPresigner: new UrlPresigner);
        $files = $fileService->getFiles(relation: LegacyIndividual::find($cod_pessoa));

        if (is_array(value: $files) && count(value: $files) > 0) {
            $this->addHtml(html: view(view: 'uploads.upload-details', data: ['files' => $files])->render());
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 43, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->url_novo = 'atendidos_cad.php';
            $this->url_editar = 'atendidos_cad.php?cod_pessoa_fj=' . $detalhe['idpes'];
        }

        $this->url_cancelar = 'atendidos_lst.php';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Pessoa física', breadcrumbs: ['educar_pessoas_index.php' => 'Pessoas']);
    }

    public function Formular()
    {
        $this->title = 'Pessoa';
        $this->processoAp = 43;
    }
};
