<?php

use App\Models\LegacyIndividual;
use App\Services\FileService;
use App\Services\UrlPresigner;

return new class extends clsDetalhe {
    public function Gerar()
    {
        $this->titulo = 'Detalhe da Pessoa';

        $cod_pessoa = (int) $this->getQueryString('cod_pessoa');
        $objPessoa = new clsPessoaFisica($cod_pessoa);
        $db = new clsBanco();

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
            'nome_social',
            'ref_cod_profissao'
        );

       
        $objFoto = new clsCadastroFisicaFoto($cod_pessoa);
        $caminhoFoto = $objFoto->detalhe();
        if ($caminhoFoto!=false) {
            $this->addDetalhe(['Nome', $detalhe['nome'].'
                <p><img height="117" src="' . (new UrlPresigner())->getPresignedUrl($caminhoFoto['caminho']) . '"/></p>']);
        } else {
            $this->addDetalhe(['Nome', $detalhe['nome']]);
        }

        if ($detalhe['nome_social']) {
            $this->addDetalhe(['Nome social e/ou afetivo', $detalhe['nome_social']]);
        }

        $this->addDetalhe(['CPF', int2cpf($detalhe['cpf'])]);

        if ($detalhe['data_nasc']) {
            $this->addDetalhe(['Data de Nascimento', dataFromPgToBr($detalhe['data_nasc'])]);
        }

        // Cor/Raça.
        $raca = new clsCadastroFisicaRaca($cod_pessoa);
        $raca = $raca->detalhe();
        if (is_array($raca)) {
            $raca = new clsCadastroRaca($raca['ref_cod_raca']);
            $raca = $raca->detalhe();

            if (is_array($raca)) {
                $this->addDetalhe(['Raça', $raca['nm_raca']]);
            }
        }

        if ($detalhe['logradouro']) {
            if ($detalhe['numero']) {
                $end = ' nº ' . $detalhe['numero'];
            }

            $this->addDetalhe(['Endereço', $detalhe['logradouro'] . ' ' . $end]);
        }

        if ($detalhe['complemento']) {
            $this->addDetalhe(['Complemento', $detalhe['complemento']]);
        }

        if ($detalhe['cidade']) {
            $this->addDetalhe(['Cidade', $detalhe['cidade']]);
        }

        if ($detalhe['sigla_uf']) {
            $this->addDetalhe(['Estado', $detalhe['sigla_uf']]);
        }

        $zona = App_Model_ZonaLocalizacao::getInstance();
        if ($detalhe['zona_localizacao']) {
            $this->addDetalhe([
                'Zona Localização', $zona->getValue($detalhe['zona_localizacao'])
            ]);
        }

        if ($detalhe['cep']) {
            $this->addDetalhe(['CEP', int2cep($detalhe['cep'])]);
        }

        if ($detalhe['fone_1']) {
            $this->addDetalhe(
                ['Telefone 1', sprintf('(%s) %s', $detalhe['ddd_1'], $detalhe['fone_1'])]
            );
        }

        if ($detalhe['fone_2']) {
            $this->addDetalhe(
                ['Telefone 2', sprintf('(%s) %s', $detalhe['ddd_2'], $detalhe['fone_2'])]
            );
        }

        if ($detalhe['fone_mov']) {
            $this->addDetalhe(
                ['Celular', sprintf('(%s) %s', $detalhe['ddd_mov'], $detalhe['fone_mov'])]
            );
        }

        if ($detalhe['fone_fax']) {
            $this->addDetalhe(
                ['Fax', sprintf('(%s) %s', $detalhe['ddd_fax'], $detalhe['fone_fax'])]
            );
        }

        if ($detalhe['url']) {
            $this->addDetalhe(['Site', $detalhe['url']]);
        }

        if ($detalhe['email']) {
            $this->addDetalhe(['E-mail', $detalhe['email']]);
        }

        if ($detalhe['sexo']) {
            $this->addDetalhe(['Sexo', $detalhe['sexo'] == 'M' ? 'Masculino' : 'Feminino']);
        }
      
        if($detalhe['ref_cod_profissao']){ 
            $this->addDetalhe(['Profissão', $detalhe['ref_cod_profissao']]);
        }
        $fileService = new FileService(new UrlPresigner);
        $files = $fileService->getFiles(LegacyIndividual::find($cod_pessoa));

        if (count($files) > 0) {
            $this->addHtml(view('uploads.upload-details', ['files' => $files])->render());
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(43, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'atendidos_cad.php';
            $this->url_editar = 'atendidos_cad.php?cod_pessoa_fj=' . $detalhe['idpes'];
        }

        $this->url_cancelar = 'atendidos_lst.php';

        $this->largura = '100%';

        $this->breadcrumb('Pessoa física', ['educar_pessoas_index.php' => 'Pessoas']);
    }

    public function Formular()
    {
        $this->title = 'Pessoa';
        $this->processoAp = 43;
    }
};
