<?php

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Facades\Asset;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyRace;
use App\Models\LegacyUser;
use App\Services\FileService;
use App\Services\UrlPresigner;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use iEducar\Modules\Educacenso\Validator\NameValidator;
use iEducar\Modules\Educacenso\Validator\NisValidator;
use iEducar\Support\View\SelectOptions;
use Illuminate\Support\Facades\Session;

return new class extends clsCadastro
{
    use LegacyAddressingFields;

    public $cod_pessoa_fj;

    public $nm_pessoa;

    public $nome_social;

    public $id_federal;

    public $data_nasc;

    public $ddd_telefone_1;

    public $telefone_1;

    public $ddd_telefone_2;

    public $telefone_2;

    public $ddd_telefone_mov;

    public $telefone_mov;

    public $ddd_telefone_fax;

    public $telefone_fax;

    public $email;

    public $tipo_pessoa;

    public $sexo;

    public $busca_pessoa;

    public $retorno;

    public $cor_raca;

    public $sus;

    public $nis_pis_pasep;

    public $ocupacao;

    public $empresa;

    public $ddd_telefone_empresa;

    public $telefone_empresa;

    public $pessoa_contato;

    public $renda_mensal;

    public $data_admissao;

    public $zona_localizacao_censo;

    public $localizacao_diferenciada;

    public $pais_residencia;

    // Variáveis para controle da foto
    public $objPhoto;

    public $arquivoFoto;

    public $file_delete;

    public $caminho_det;

    public $caminho_lst;

    public $observacao;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 43, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'atendidos_lst.php');

        $this->cod_pessoa_fj = $this->getQueryString(name: 'cod_pessoa_fj');
        $this->retorno = 'Novo';

        if (is_numeric(value: $this->cod_pessoa_fj)) {
            $this->retorno = 'Editar';
            $objPessoa = new clsPessoaFisica();

            [$this->nm_pessoa, $this->id_federal, $this->data_nasc,
                $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2,
                $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov,
                $this->ddd_telefone_fax, $this->telefone_fax, $this->email,
                $this->tipo_pessoa, $this->sexo, $this->estado_civil,
                $this->pai_id, $this->mae_id, $this->tipo_nacionalidade, $this->pais_origem, $this->naturalidade,
                $this->letra, $this->sus, $this->nis_pis_pasep, $this->ocupacao, $this->empresa, $this->ddd_telefone_empresa,
                $this->telefone_empresa, $this->pessoa_contato, $this->renda_mensal, $this->data_admissao, $this->falecido,
                $this->religiao_id, $this->zona_localizacao_censo, $this->localizacao_diferenciada, $this->nome_social, $this->pais_residencia,
                $this->observacao
            ] =
            $objPessoa->queryRapida(
                $this->cod_pessoa_fj,
                'nome',
                'cpf',
                'data_nasc',
                'ddd_1',
                'fone_1',
                'ddd_2',
                'fone_2',
                'ddd_mov',
                'fone_mov',
                'ddd_fax',
                'fone_fax',
                'email',
                'tipo',
                'sexo',
                'ideciv',
                'idpes_pai',
                'idpes_mae',
                'nacionalidade',
                'idpais_estrangeiro',
                'idmun_nascimento',
                'letra',
                'sus',
                'nis_pis_pasep',
                'ocupacao',
                'empresa',
                'ddd_telefone_empresa',
                'telefone_empresa',
                'pessoa_contato',
                'renda_mensal',
                'data_admissao',
                'falecido',
                'ref_cod_religiao',
                'zona_localizacao_censo',
                'localizacao_diferenciada',
                'nome_social',
                'pais_residencia',
                'observacao'
            );

            $this->loadAddress(person: $this->cod_pessoa_fj);

            $this->id_federal = is_numeric(value: $this->id_federal) ? int2CPF(int: $this->id_federal) : '';
            $this->nis_pis_pasep = int2Nis(nis: $this->nis_pis_pasep);
            $this->renda_mensal = number_format(num: (float) $this->renda_mensal, decimals: 2, decimal_separator: ',', thousands_separator: '.');
            // $this->data_nasc = $this->data_nasc ? dataFromPgToBr($this->data_nasc) : '';
            $this->data_admissao = $this->data_admissao ? dataFromPgToBr(data_original: $this->data_admissao) : '';

            $this->estado_civil_id = $this->estado_civil->ideciv;
            $this->pais_origem_id = $this->pais_origem;
            $this->naturalidade_id = $this->naturalidade;
        }

        $this->fexcluir = is_numeric(value: $this->cod_pessoa_fj) && $obj_permissoes->permissao_excluir(
            int_processo_ap: 43,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7
        );

        $nomeMenu = $this->retorno === 'Editar' ? $this->retorno : 'Cadastrar';

        $this->nome_url_cancelar = 'Cancelar';
        $this->breadcrumb(currentPage: "{$nomeMenu} pessoa física", breadcrumbs: ['educar_pessoas_index.php' => 'Pessoas']);

        return $this->retorno;
    }

    public function Gerar()
    {
        $this->form_enctype = ' enctype=\'multipart/form-data\'';
        $camposObrigatorios = !config(key: 'legacy.app.remove_obrigatorios_cadastro_pessoa') == 1;
        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
        $this->campoOculto(nome: 'obrigar_campos_censo', valor: (int) $obrigarCamposCenso);
        $this->url_cancelar = $this->retorno == 'Editar' ?
            'atendidos_det.php?cod_pessoa=' . $this->cod_pessoa_fj : 'atendidos_lst.php';

        $objPessoa = new clsPessoaFisica(int_idpes: $this->cod_pessoa_fj);

        $detalhe = $objPessoa->queryRapida(
            $this->cod_pessoa_fj,
            'idpes',
            'nome',
            'cpf',
            'data_nasc',
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
            'ativo',
            'data_exclusao',
            'observacao'
        );

        if (isset($this->cod_pessoa_fj) && !$detalhe['ativo'] == 1 && $this->retorno == 'Editar') {
            $getNomeUsuario = $objPessoa->getNomeUsuario();
            $detalhe['data_exclusao'] = date_format(object: new DateTime(datetime: $detalhe['data_exclusao']), format: 'd/m/Y');
            $this->mensagem = 'Este cadastro foi desativado em <strong>' . $detalhe['data_exclusao'] . '</strong>, pelo usuário <strong>' . $getNomeUsuario . "</strong>. <a href='javascript:ativarPessoa($this->cod_pessoa_fj);'>Reativar cadastro</a>";
        }

        $this->campoCpf(nome: 'id_federal', campo: 'CPF', valor: $this->id_federal);

        $user = Auth::user();

        if ($user->ref_cod_instituicao) {
            $obrigarCpf = LegacyInstitution::query()
                ->find($user->ref_cod_instituicao, ['obrigar_cpf'])?->obrigar_cpf;
        } else {
            $obrigarCpf = LegacyInstitution::query()
                ->first(['obrigar_cpf'])?->obrigar_cpf;
        }

        $this->campoOculto('obrigarCPF', (int) $obrigarCpf);

        $this->campoOculto(nome: 'cod_pessoa_fj', valor: $this->cod_pessoa_fj);
        $this->campoTexto(nome: 'nm_pessoa', campo: 'Nome', valor: $this->nm_pessoa, tamanhovisivel: '50', tamanhomaximo: '255', obrigatorio: true);
        $this->campoTexto(nome: 'nome_social', campo: 'Nome social e/ou afetivo', valor: $this->nome_social, tamanhovisivel: '50', tamanhomaximo: '255');

        $foto = false;
        if (is_numeric(value: $this->cod_pessoa_fj)) {
            $objFoto = new clsCadastroFisicaFoto(idpes: $this->cod_pessoa_fj);
            $detalheFoto = $objFoto->detalhe();
            if (is_array(value: $detalheFoto) && count(value: $detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $foto = false;
        }

        if ($foto) {
            $this->campoRotulo(nome: 'fotoAtual_', campo: 'Foto atual', valor: '<img height="117" src="' . (new UrlPresigner())->getPresignedUrl(url: $foto) . '"/>');
            $this->inputsHelper()->checkbox(attrName: 'file_delete', inputOptions: ['label' => 'Excluir a foto']);
            $this->campoArquivo(nome: 'photo', campo: 'Trocar foto', valor: $this->arquivoFoto, tamanho: 40, descricao: '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 2MB</span>');
        } else {
            $this->campoArquivo(nome: 'photo', campo: 'Foto', valor: $this->arquivoFoto, tamanho: 40, descricao: '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 2MB</span>');
        }

        // ao cadastrar pessoa do pai ou mãe apartir do cadastro de outra pessoa,
        // é enviado o tipo de cadastro (pai ou mae).
        $parentType = isset($_REQUEST['parent_type']) ? $_REQUEST['parent_type'] : '';
        // Se a pessoa for pai ou mãe, não tera naturalidade obrigatoria

        $naturalidadeObrigatoria = ($parentType == '' ? true : false);

        // sexo

        $sexo = $this->sexo;

        // sugere sexo quando cadastrando o pai ou mãe

        if (!$sexo && $parentType == 'pai') {
            $sexo = 'M';
        } elseif (!$sexo && $parentType == 'mae') {
            $sexo = 'F';
        }

        $options = [
            'label' => 'Sexo / Estado civil',
            'value' => $sexo,
            'resources' => [
                '' => 'Sexo',
                'M' => 'Masculino',
                'F' => 'Feminino',
            ],
            'inline' => true,
            'required' => $camposObrigatorios,
        ];

        $this->inputsHelper()->select(attrName: 'sexo', inputOptions: $options);

        // estado civil

        $this->inputsHelper()->estadoCivil(inputOptions: [
            'label' => '',
            'required' => empty($parentType) && $camposObrigatorios,
        ]);

        // data nascimento

        $options = [
            'label' => 'Data de nascimento',
            'value' => $this->data_nasc,
            'required' => empty($parentType) && $camposObrigatorios,
        ];

        $this->inputsHelper()->date(attrName: 'data_nasc', inputOptions: $options);

        // pai, mãe

        $this->inputPai();
        $this->inputMae();

        // documentos

        $documentos = new clsDocumento();
        $documentos->idpes = $this->cod_pessoa_fj;
        $documentos = $documentos->detalhe();

        $options = [
            'required' => false,
            'label' => 'RG / Data emissão',
            'placeholder' => 'Documento identidade',
            'value' => $documentos['rg'],
            'max_length' => 25,
            'size' => 27,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'rg', inputOptions: $options);

        // data emissão rg

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emissão',
            'value' => $documentos['data_exp_rg'],
            'size' => 19,
        ];

        $this->inputsHelper()->date(attrName: 'data_emissao_rg', inputOptions: $options);

        // orgão emissão rg

        $selectOptions = [null => 'Órgão emissor'];
        $orgaos = new clsOrgaoEmissorRg();
        $orgaos = $orgaos->lista();

        foreach ($orgaos as $orgao) {
            $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];
        }

        $selectOptions = Portabilis_Array_Utils::sortByValue(array: $selectOptions);

        $options = [
            'required' => false,
            'label' => '',
            'value' => $documentos['idorg_exp_rg'],
            'resources' => $selectOptions,
            'inline' => true,
        ];

        $this->inputsHelper()->select(attrName: 'orgao_emissao_rg', inputOptions: $options);

        // uf emissão rg

        $options = [
            'required' => false,
            'label' => '',
            'value' => $documentos['sigla_uf_exp_rg'],
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_rg',
        ];

        $this->inputsHelper()->uf(inputOptions: $options, helperOptions: $helperOptions);

        // Código NIS (PIS/PASEP)

        $options = [
            'required' => false,
            'label' => 'NIS (PIS/PASEP)',
            'placeholder' => '',
            'value' => $this->nis_pis_pasep,
            'max_length' => 11,
            'size' => 20,
        ];

        $this->inputsHelper()->integer(attrName: 'nis_pis_pasep', inputOptions: $options);

        // Carteira do SUS

        $options = [
            'required' => config(key: 'legacy.app.fisica.exigir_cartao_sus'),
            'label' => 'Número da carteira do SUS',
            'placeholder' => '',
            'value' => $this->sus,
            'max_length' => 20,
            'size' => 20,
        ];

        $this->inputsHelper()->text(attrNames: 'sus', inputOptions: $options);

        // tipo de certidao civil

        $selectOptions = [
            null => 'Tipo certidão civil',
            'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
            91 => 'Nascimento (antigo formato)',
            'certidao_casamento_novo_formato' => 'Casamento (novo formato)',
            92 => 'Casamento (antigo formato)',
        ];

        // caso certidao nascimento novo formato tenha sido informado,
        // considera este o tipo da certidão
        if (!empty($documentos['certidao_nascimento'])) {
            $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
        } elseif (!empty($documentos['certidao_casamento'])) {
            $tipoCertidaoCivil = 'certidao_casamento_novo_formato';
        } else {
            $tipoCertidaoCivil = $documentos['tipo_cert_civil'];
        }

        $options = [
            'required' => false,
            'label' => 'Tipo certidão civil',
            'value' => $tipoCertidaoCivil,
            'resources' => $selectOptions,
            'inline' => true,
        ];

        $this->inputsHelper()->select(attrName: 'tipo_certidao_civil', inputOptions: $options);

        // termo certidao civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Termo',
            'value' => $documentos['num_termo'],
            'max_length' => 8,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'termo_certidao_civil', inputOptions: $options);

        // livro certidao civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Livro',
            'value' => $documentos['num_livro'],
            'max_length' => 8,
            'size' => 15,
            'inline' => true,
        ];

        $this->inputsHelper()->text(attrNames: 'livro_certidao_civil', inputOptions: $options);

        // folha certidao civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Folha',
            'value' => $documentos['num_folha'],
            'max_length' => 4,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'folha_certidao_civil', inputOptions: $options);

        // certidao nascimento (novo padrão)

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Certidão nascimento',
            'value' => $documentos['certidao_nascimento'],
            'max_length' => 32,
            'size' => 32,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'certidao_nascimento', inputOptions: $options);

        // certidao casamento (novo padrão)

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Certidão casamento',
            'value' => $documentos['certidao_casamento'],
            'max_length' => 32,
            'size' => 32,
        ];

        $this->inputsHelper()->integer(attrName: 'certidao_casamento', inputOptions: $options);

        // uf emissão certidão civil

        $options = [
            'required' => false,
            'label' => 'Estado emissão / Data emissão',
            'label_hint' => 'Informe o estado para poder informar o código do cartório',
            'value' => $documentos['sigla_uf_cert_civil'],
            'inline' => true,
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_certidao_civil',
        ];

        $this->inputsHelper()->uf(inputOptions: $options, helperOptions: $helperOptions);

        // data emissão certidão civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emissão',
            'value' => $documentos['data_emissao_cert_civil'],
            'inline' => true,
        ];

        $this->inputsHelper()->date(attrName: 'data_emissao_certidao_civil', inputOptions: $options);

        // cartório emissão certidão civil
        $options = [
            'required' => false,
            'label' => 'Cartório emissão',
            'value' => $documentos['cartorio_cert_civil'],
            'cols' => 45,
            'max_length' => 200,
        ];

        $this->inputsHelper()->textArea(attrName: 'cartorio_emissao_certidao_civil', inputOptions: $options);

        // Passaporte
        $options = [
            'required' => false,
            'label' => 'Passaporte',
            'value' => $documentos['passaporte'],
            'cols' => 45,
            'max_length' => 20,
        ];

        $this->inputsHelper()->text(attrNames: 'passaporte', inputOptions: $options);

        // carteira de trabalho

        $options = [
            'required' => false,
            'label' => 'Carteira de trabalho / Série',
            'placeholder' => 'Carteira de trabalho',
            'value' => $documentos['num_cart_trabalho'],
            'max_length' => 7,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'carteira_trabalho', inputOptions: $options);

        // serie carteira de trabalho

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Série',
            'value' => $documentos['serie_cart_trabalho'],
            'max_length' => 5,
        ];

        $this->inputsHelper()->integer(attrName: 'serie_carteira_trabalho', inputOptions: $options);

        // uf emissão carteira de trabalho

        $options = [
            'required' => false,
            'label' => 'Estado emissão / Data emissão',
            'value' => $documentos['sigla_uf_cart_trabalho'],
            'inline' => true,
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_carteira_trabalho',
        ];

        $this->inputsHelper()->uf(inputOptions: $options, helperOptions: $helperOptions);

        // data emissão carteira de trabalho

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emissão',
            'value' => $documentos['data_emissao_cart_trabalho'],
        ];

        $this->inputsHelper()->date(attrName: 'data_emissao_carteira_trabalho', inputOptions: $options);

        // titulo eleitor

        $options = [
            'required' => false,
            'label' => 'Titulo eleitor / Zona / Seção',
            'placeholder' => 'Titulo eleitor',
            'value' => $documentos['num_tit_eleitor'],
            'max_length' => 13,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'titulo_eleitor', inputOptions: $options);

        // zona titulo eleitor

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Zona',
            'value' => $documentos['zona_tit_eleitor'],
            'max_length' => 4,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: 'zona_titulo_eleitor', inputOptions: $options);

        // seção titulo eleitor

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Seção',
            'value' => $documentos['secao_tit_eleitor'],
            'max_length' => 4,
        ];

        $this->inputsHelper()->integer(attrName: 'secao_titulo_eleitor', inputOptions: $options);

        // Cor/raça.

        $race = LegacyRace::query()
            ->where(column: 'ativo', operator: true)
            ->orderBy(column: 'nm_raca')
            ->pluck(column: 'nm_raca', key: 'cod_raca')
            ->prepend(value: 'Selecione', key: '')
            ->toArray();

        $raca = new clsCadastroFisicaRaca(ref_idpes: $this->cod_pessoa_fj);
        $raca = $raca->detalhe();
        $this->cod_raca = is_array(value: $raca) ? $raca['ref_cod_raca'] : $this->cor_raca;

        $this->campoLista(nome: 'cor_raca', campo: 'Raça', valor: $race, default: $this->cod_raca, obrigatorio: $obrigarCamposCenso);

        // nacionalidade

        // tipos
        $tiposNacionalidade = [
            '' => 'Selecione',
            '1' => 'Brasileira',
            '2' => 'Naturalizado brasileiro',
            '3' => 'Estrangeira',
        ];

        $options = [
            'label' => 'Nacionalidade',
            'resources' => $tiposNacionalidade,
            'required' => $obrigarCamposCenso,
            'inline' => true,
            'value' => $this->retorno === 'Novo' ? Nacionalidade::BRASILEIRA : $this->tipo_nacionalidade, //Quando for novo registro, preenche com o valor default brasileiro
        ];

        $this->inputsHelper()->select(attrName: 'tipo_nacionalidade', inputOptions: $options);

        // pais origem

        $options = [
            'label' => '',
            'placeholder' => 'Informe o nome do pais',
            'required' => true,
        ];

        $hiddenInputOptions = [
            'options' => ['value' => $this->pais_origem_id],
        ];

        $helperOptions = [
            'objectName' => 'pais_origem',
            'hiddenInputOptions' => $hiddenInputOptions,
        ];

        $this->inputsHelper()->simpleSearchPaisSemBrasil(attrName: 'nome', inputOptions: $options, helperOptions: $helperOptions);

        //Falecido
        $options = [
            'label' => 'Falecido?',
            'required' => false,
            'value' => dbBool(val: $this->falecido),
        ];

        $this->inputsHelper()->checkbox(attrName: 'falecido', inputOptions: $options);

        // naturalidade

        $options = [
            'label' => 'Naturalidade',
            'required' => $naturalidadeObrigatoria && $camposObrigatorios,
        ];

        $helperOptions = [
            'objectName' => 'naturalidade',
            'hiddenInputOptions' => ['options' => ['value' => $this->naturalidade_id]],
        ];

        $this->inputsHelper()->simpleSearchMunicipio(attrName: 'nome', inputOptions: $options, helperOptions: $helperOptions);

        // Religião
        $this->inputsHelper()->religiao(inputOptions: [
            'required' => false,
            'label' => 'Religião',
        ]);

        $this->viewAddress(optionalFields: true, complementMaxLength: 100);

        $this->inputsHelper()->select(attrName: 'pais_residencia', inputOptions: [
            'label' => 'País de residência',
            'value' => $this->pais_residencia ?: PaisResidencia::BRASIL,
            'resources' => PaisResidencia::getDescriptiveValues(),
            'required' => true,
        ]);

        $this->inputsHelper()->select(attrName: 'zona_localizacao_censo', inputOptions: [
            'label' => 'Zona de residência',
            'value' => $this->zona_localizacao_censo,
            'resources' => [
                '' => 'Selecione',
                1 => 'Urbana',
                2 => 'Rural',
            ],
            'required' => $obrigarCamposCenso,
        ]);

        $this->inputsHelper()->select(attrName: 'localizacao_diferenciada', inputOptions: [
            'label' => 'Localização diferenciada de residência',
            'value' => $this->localizacao_diferenciada,
            'resources' => SelectOptions::localizacoesDiferenciadasPessoa(),
            'required' => false,
        ]);

        // contato
        $this->campoRotulo(nome: 'contato', campo: '<b>Contato</b>', valor: '', duplo: '', descricao: 'Informações de contato da pessoa');
        $this->inputTelefone(type: '1', typeLabel: 'Telefone residencial');
        $this->inputTelefone(type: '2', typeLabel: 'Celular');
        $this->inputTelefone(type: 'mov', typeLabel: 'Telefone adicional');
        $this->inputTelefone(type: 'fax', typeLabel: 'Fax');
        $this->campoTexto(nome: 'email', campo: 'E-mail', valor: $this->email, tamanhovisivel: '50', tamanhomaximo: '255');

        // renda
        $this->campoRotulo(nome: 'renda', campo: '<b>Trabalho e renda</b>', valor: '', duplo: '', descricao: 'Informações de trabalho e renda da pessoa');
        $this->campoTexto(nome: 'ocupacao', campo: 'Ocupação', valor: $this->ocupacao, tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoMonetario(nome: 'renda_mensal', campo: 'Renda mensal (R$)', valor: $this->renda_mensal, tamanhovisivel: '9', tamanhomaximo: '10');
        $this->campoData(nome: 'data_admissao', campo: 'Data de admissão', valor: $this->data_admissao);
        $this->campoTexto(nome: 'empresa', campo: 'Empresa', valor: $this->empresa, tamanhovisivel: '50', tamanhomaximo: '255');
        $this->inputTelefone(type: 'empresa', typeLabel: 'Telefone da empresa');
        $this->campoTexto(nome: 'pessoa_contato', campo: 'Pessoa de contato na empresa', valor: $this->pessoa_contato, tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoTexto(nome: 'observacao', campo: 'Observação', valor: $this->observacao, tamanhovisivel: '50', tamanhomaximo: '255');

        $fileService = new FileService(urlPresigner: new UrlPresigner);
        $files = $this->cod_pessoa_fj && is_numeric($this->cod_pessoa_fj) ? $fileService->getFiles(relation: LegacyIndividual::find($this->cod_pessoa_fj)) : [];
        $this->addHtml(html: view(view: 'uploads.upload', data: ['files' => $files])->render());

        // after change pessoa pai / mae

        if ($parentType) {
            $this->inputsHelper()->hidden(attrName: 'parent_type', inputOptions: ['value' => $parentType]);
        }

        $styles = [
            '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend.css',
            '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
            '/vendor/legacy/Cadastro/Assets/Stylesheets/PessoaFisica.css',
            '/vendor/legacy/Cadastro/Assets/Stylesheets/ModalCadastroPais.css',
        ];

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);

        $script = [
            '/vendor/legacy/Cadastro/Assets/Javascripts/PessoaFisica.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/Addresses.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/Endereco.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/ModalCadastroPais.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $script);
    }

    public function Novo()
    {
        return $this->createOrUpdate();
    }

    public function Editar()
    {
        $user = LegacyUser::find($this->cod_pessoa_fj);
        if ($user) {
            UserUpdated::dispatch($user);
        }

        return $this->createOrUpdate(pessoaIdOrNull: $this->cod_pessoa_fj);
    }

    public function Excluir()
    {
        $idPes = $this->cod_pessoa_fj;

        $aluno = new clsPmieducarAluno();
        $aluno = $aluno->lista(int_cod_aluno: null, int_ref_cod_aluno_beneficio: null, int_ref_cod_religiao: null, int_ref_usuario_exc: null, int_ref_usuario_cad: null, int_ref_idpes: $idPes, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: 1);

        if ($aluno) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com aluno.';

            return false;
        }

        $inUse = LegacyIndividual::query()
            ->where(column: 'idpes_responsavel', operator: $idPes)
            ->orWhere(column: 'idpes_pai', operator: $idPes)
            ->orWhere(column: 'idpes_mae', operator: $idPes)
            ->exists();

        if ($inUse) {
            $this->mensagem = 'Não foi possível excluir. A pessoa possuí vínculo(s) com aluno(s) como mãe, pai ou outro responsável.';

            return false;
        }

        $usuario = new clsPmieducarUsuario();
        $usuario = $usuario->lista(int_cod_usuario: $idPes, int_ref_cod_escola: null, int_ref_cod_instituicao: null, int_ref_funcionario_cad: null, int_ref_funcionario_exc: null, int_ref_cod_tipo_usuario: null, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: true);
        $funcionario = new clsPortalFuncionario();
        $funcionario->ref_cod_pessoa_fj = $idPes;
        $funcionario = $funcionario->lista(str_matricula: null, str_senha: null, int_ativo: 1);

        if ($funcionario && $usuario) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com usuário do sistema.';

            return false;
        }

        $servidor = new clsPmieducarServidor();
        $servidor = $servidor->lista(int_cod_servidor: $idPes, int_ref_cod_deficiencia: null, int_ref_idesco: null, int_carga_horaria: null, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: 1);

        if ($servidor) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com servidor.';

            return false;
        }

        $pessoaFisica = new clsPessoaFisica(int_idpes: $idPes);
        $pessoaFisica->excluir();

        $user = LegacyUser::find($idPes);
        if ($user) {
            UserDeleted::dispatch($user);
        }

        $this->mensagem = 'Exclusão efetuada com sucesso.';

        $this->simpleRedirect(url: 'atendidos_lst.php');
    }

    public function afterChangePessoa($id)
    {
        Portabilis_View_Helper_Application::embedJavascript(viewInstance: $this, script: "

        if(window.opener &&  window.opener.afterChangePessoa) {
            var parentType = \$j('#parent_type').val();
            alert('Alteração realizada com sucesso!');
            if (parentType)
            window.opener.afterChangePessoa(self, parentType, $id, \$j('#nm_pessoa').val());
            else
            window.opener.afterChangePessoa(self, null, $id, \$j('#nm_pessoa').val());
        }
        else
            document.location = 'atendidos_lst.php';

        ", afterReady: $afterReady = false);
    }

    protected function loadAlunoByPessoaId($id)
    {
        $aluno = new clsPmieducarAluno();
        $aluno->ref_idpes = $id;

        return $aluno->detalhe();
    }

    protected function inputPai()
    {
        $this->addParentsInput(parentType: 'pai');
    }

    protected function inputMae()
    {
        $this->addParentsInput(parentType: 'mae', parentTypeLabel: 'mãe');
    }

    protected function addParentsInput($parentType, $parentTypeLabel = '')
    {
        if (!$parentTypeLabel) {
            $parentTypeLabel = $parentType;
        }

        if (!isset($this->_aluno)) {
            $this->_aluno = $this->loadAlunoByPessoaId(id: $this->cod_pessoa_fj);
        }

        $parentId = $this->{$parentType . '_id'};

        // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
        //pela antiga interface do cadastro de alunos.

        if (!$parentId && $this->_aluno['nm_' . $parentType]) {
            $nome = $this->_aluno['nm_' . $parentType];

            $inputHint = '<br /><b>Dica:</b> Foi informado o nome "' . $nome .
                '" no cadastro de aluno,<br />tente pesquisar esta pessoa ' .
                'pelo CPF ou RG, caso não encontre, cadastre uma nova pessoa.';
        }

        $hiddenInputOptions = ['options' => ['value' => $parentId]];
        $helperOptions = [
            'objectName' => $parentType,
            'hiddenInputOptions' => $hiddenInputOptions,
        ];

        $options = [
            'label' => 'Pessoa ' . $parentTypeLabel,
            'size' => 50,
            'required' => false,
            'input_hint' => $inputHint,
        ];

        $this->inputsHelper()->simpleSearchPessoa(attrName: 'nome', inputOptions: $options, helperOptions: $helperOptions);
    }

    protected function validatesCpf($cpf)
    {
        $isValid = true;

        if ($cpf && !Portabilis_Utils_Validation::validatesCpf(cpf: $cpf)) {
            $this->erros['id_federal'] = 'CPF inválido.';
            $isValid = false;
        } elseif ($cpf) {
            $fisica = new clsFisica();
            $fisica->cpf = idFederal2int(str: $cpf);
            $fisica = $fisica->detalhe();

            if ($fisica['cpf'] && $this->cod_pessoa_fj != $fisica['idpes']) {
                $link = '<a class=\'decorated\' target=\'__blank\' href=\'/intranet/atendidos_cad.php?cod_pessoa_fj=' .
                    "{$fisica['idpes']}'>{$fisica['idpes']}</a>";

                $this->erros['id_federal'] = "CPF já utilizado pela pessoa $link.";
                $isValid = false;
            }
        }

        return $isValid;
    }

    protected function createOrUpdate($pessoaIdOrNull = null)
    {
        if ($this->tipo_nacionalidade !== 3 && $this->obrigarCPFPessoa() && !$this->id_federal) {
            $this->mensagem = 'É necessário o preenchimento do CPF.';

            return false;
        }

        if ($this->obrigarDocumentoPessoa() && !$this->possuiDocumentoObrigatorio()) {
            $this->mensagem = 'É necessário o preenchimento de pelo menos um dos seguintes documentos: CPF, RG ou Certidão civil.';

            return false;
        }

        if (!empty($this->pai_id) && !empty($this->mae_id) && $this->pai_id == $this->mae_id) {
            $this->mensagem = 'Não é possível informar a mesma pessoa para Pai e Mãe';

            return false;
        }

        if (!$this->validatesCpf(cpf: $this->id_federal)) {
            return false;
        }

        if (!$this->validatePhoto()) {
            return false;
        }

        if (!$this->validaNisPisPasep()) {
            return false;
        }

        if (!empty($this->nm_pessoa) && !$this->validaNome()) {
            return false;
        }

        if (!empty($this->nome_social) && !$this->validaNomeSocial()) {
            return false;
        }

        if (!empty($this->data_nasc) && !$this->validaDataNascimento()) {
            return false;
        }

        if (!$this->validaCertidao()) {
            return false;
        }

        if (!$this->validaLocalizacaoDiferenciada()) {
            return false;
        }

        if (!$this->validaObrigatoriedadeTelefone()) {
            $this->mensagem = 'É necessário informar um Telefone residencial ou Celular.';

            return false;
        }

        if (!$this->validaCaracteresPermitidosComplemento()) {
            $this->mensagem = 'O campo foi preenchido com valor não permitido. O campo Complemento só permite os caracteres: ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 ª º – / . ,';

            return false;
        }

        if (!$this->validaDadosTelefones()) {
            return false;
        }

        $pessoaId = $this->createOrUpdatePessoa(pessoaId: $pessoaIdOrNull);
        $this->savePhoto(id: $pessoaId);
        $this->createOrUpdatePessoaFisica(pessoaId: $pessoaId);
        $this->createOrUpdateDocumentos(pessoaId: $pessoaId);
        $this->createOrUpdateTelefones(pessoaId: $pessoaId);
        $this->saveAddress(person: $pessoaId, optionalFields: true);
        $this->afterChangePessoa(id: $pessoaId);
        $this->saveFiles(idpes: $pessoaId);

        return true;
    }

    protected function validaDadosTelefones()
    {
        return $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_1, valorTelefone: $this->telefone_1, nomeCampo: 'Telefone residencial') &&
            $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_2, valorTelefone: $this->telefone_2, nomeCampo: 'Celular') &&
            $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_mov, valorTelefone: $this->telefone_mov, nomeCampo: 'Telefone adicional') &&
            $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_fax, valorTelefone: $this->telefone_fax, nomeCampo: 'Fax');
    }

    protected function validaDDDTelefone($valorDDD, $valorTelefone, $nomeCampo)
    {
        $msgRequereTelefone = "O campo: {$nomeCampo}, deve ser preenchido quando o DDD estiver preenchido.";
        $msgRequereDDD = "O campo: DDD, deve ser preenchido quando o {$nomeCampo} estiver preenchido.";

        if (!empty($valorDDD) && empty($valorTelefone)) {
            $this->mensagem = $msgRequereTelefone;

            return false;
        }

        if (empty($valorDDD) && !empty($valorTelefone)) {
            $this->mensagem = $msgRequereDDD;

            return false;
        }

        return true;
    }

    private function validaNome()
    {
        $validator = new NameValidator(name: $this->nm_pessoa);
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();

            return false;
        }

        return true;
    }

    private function validaNomeSocial()
    {
        $validator = new NameValidator(name: $this->nome_social);
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();

            return false;
        }

        return true;
    }

    private function validaLocalizacaoDiferenciada()
    {
        $validator = new DifferentiatedLocationValidator(differentiatedLocation: $this->localizacao_diferenciada, locationZone: $this->zona_localizacao_censo);
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();

            return false;
        }

        return true;
    }

    private function validaDataNascimento()
    {
        $validator = new BirthDateValidator(birthDate: Portabilis_Date_Utils::brToPgSQL(date: $this->data_nasc));
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();

            return false;
        }

        return true;
    }

    //envia foto e salva caminha no banco
    protected function savePhoto($id)
    {
        $caminhoFoto = Asset::get('intranet/imagens/user-perfil.png');
        if ($this->objPhoto != null) {
            $caminhoFoto = $this->objPhoto->sendPicture();
            if ($caminhoFoto != '') {
                $obj = new clsCadastroFisicaFoto(idpes: $id, caminho: $caminhoFoto);
                $detalheFoto = $obj->detalhe();
                if (is_array(value: $detalheFoto) && count(value: $detalheFoto) > 0) {
                    $obj->edita();
                } else {
                    $obj->cadastra();
                }
            } else {
                echo '<script>alert(\'Foto não salva.\')</script>';

                return false;
            }
            $caminhoFoto = (new UrlPresigner())->getPresignedUrl(url: $caminhoFoto);
        } elseif ($this->file_delete == 'on') {
            $obj = new clsCadastroFisicaFoto(idpes: $id);
            $obj->excluir();
        }

        $loggedUser = session(key: 'logged_user');

        if ($loggedUser->personId == $id) {
            Session::put('logged_user_picture', $caminhoFoto);
            Session::save();
        }

        return true;
    }

    // Retorna true caso a foto seja válida
    protected function validatePhoto()
    {
        $this->arquivoFoto = $_FILES['photo'];
        if (!empty($this->arquivoFoto['name'])) {
            $this->arquivoFoto['name'] = mb_strtolower(string: $this->arquivoFoto['name'], encoding: 'UTF-8');
            $this->objPhoto = new PictureController(imageFile: $this->arquivoFoto);
            if ($this->objPhoto->validatePicture()) {
                return true;
            } else {
                $this->mensagem = $this->objPhoto->getErrorMessage();

                return false;
            }
        } else {
            $this->objPhoto = null;

            return true;
        }
    }

    protected function obrigarDocumentoPessoa()
    {
        $user = Auth::user();
        if ($user->ref_cod_instituicao) {
            $obrigarDocumentoPessoa = LegacyInstitution::query()
                ->find($user->ref_cod_instituicao, ['obrigar_documento_pessoa'])?->obrigar_documento_pessoa;
        } else {
            $obrigarDocumentoPessoa = LegacyInstitution::query()
                ->first(['obrigar_documento_pessoa'])?->obrigar_documento_pessoa;
        }

        return $obrigarDocumentoPessoa;
    }

    protected function obrigarCPFPessoa()
    {
        $user = Auth::user();

        if ($user->ref_cod_instituicao) {
            $obrigarCpf = LegacyInstitution::query()
                ->find($user->ref_cod_instituicao, ['obrigar_cpf'])?->obrigar_cpf;
        } else {
            $obrigarCpf = LegacyInstitution::query()
                ->first(['obrigar_cpf'])?->obrigar_cpf;
        }

        return $obrigarCpf;
    }

    protected function possuiDocumentoObrigatorio()
    {
        $certidaoCivil = $this->termo_certidao_civil && $this->folha_certidao_civil && $this->livro_certidao_civil;
        $certidaoNascimentoNovoFormato = $this->certidao_nascimento;
        $certidaoCasamentoNovoFormato = $this->certidao_casamento;

        return $this->id_federal ||
            $this->rg ||
            $certidaoCivil ||
            $certidaoCasamentoNovoFormato ||
            $certidaoNascimentoNovoFormato;
    }

    protected function validaCertidao()
    {
        $certidaoNascimento = ($_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato');
        $certidaoCasamento = ($_REQUEST['tipo_certidao_civil'] == 'certidao_casamento_novo_formato');

        if ($certidaoNascimento && strlen(string: $this->certidao_nascimento) < 32) {
            $this->mensagem = 'O campo referente a certidão de nascimento deve conter exatos 32 dígitos.';

            return false;
        } elseif ($certidaoCasamento && strlen(string: $this->certidao_casamento) < 32) {
            $this->mensagem = 'O campo referente a certidão de casamento deve conter exatos 32 dígitos.';

            return false;
        }

        if (!empty($this->data_nasc) && $certidaoNascimento) {
            $validator = new BirthCertificateValidator(birthCertificate: $this->certidao_nascimento, birthDate: Portabilis_Date_Utils::brToPgSQL(date: $this->data_nasc));
            if (!$validator->isValid()) {
                $this->mensagem = $validator->getMessage();

                return false;
            }
        }

        return true;
    }

    protected function validaNisPisPasep()
    {
        if ($this->nis_pis_pasep && strlen(string: $this->nis_pis_pasep) != 11) {
            $this->mensagem = 'O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.';

            return false;
        }

        $validator = new NisValidator(nis: $this->nis_pis_pasep ?? '');
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();

            return false;
        }

        return true;
    }

    protected function validaObrigatoriedadeTelefone()
    {
        $institution = app(abstract: LegacyInstitution::class);
        $telefoneObrigatorio = $institution->obrigar_telefone_pessoa;
        $possuiTelefoneInformado = (!empty($this->telefone_1) || !empty($this->telefone_2));

        if ($telefoneObrigatorio && !$possuiTelefoneInformado) {
            return false;
        }

        return true;
    }

    protected function validaCaracteresPermitidosComplemento()
    {
        if (empty($this->complement)) {
            return true;
        }
        $pattern = '/^[a-zA-Z0-9ªº\/–\ .,-]+$/';

        return preg_match(pattern: $pattern, subject: $this->complement);
    }

    protected function createOrUpdatePessoa($pessoaId = null)
    {
        $pessoa = new clsPessoa_();
        $pessoa->idpes = $pessoaId;
        $pessoa->nome = $this->nm_pessoa;
        $pessoa->email = addslashes(string: $this->email);

        $sql = 'select 1 from cadastro.pessoa WHERE idpes = $1 limit 1';

        if (!$pessoaId || Portabilis_Utils_Database::selectField(sql: $sql, paramsOrOptions: $pessoaId) != 1) {
            $pessoa->tipo = 'F';
            $pessoa->idpes_cad = $this->currentUserId();
            $pessoaId = $pessoa->cadastra();
        } else {
            $pessoa->idpes_rev = $this->currentUserId();
            $pessoa->data_rev = date(format: 'Y-m-d H:i:s', timestamp: time());
            $pessoa->edita();
        }

        return $pessoaId;
    }

    protected function createOrUpdatePessoaFisica($pessoaId)
    {
        $db = new clsBanco();
        $fisica = new clsFisica();
        $fisica->idpes = $pessoaId;
        $fisica->data_nasc = Portabilis_Date_Utils::brToPgSQL(date: $this->data_nasc);
        $fisica->sexo = $this->sexo;
        $fisica->ref_cod_sistema = 'NULL';
        $fisica->cpf = $this->id_federal ? idFederal2int(str: $this->id_federal) : 'NULL';
        $fisica->ideciv = $this->estado_civil_id;
        $fisica->idpes_pai = $this->pai_id ? $this->pai_id : 'NULL';
        $fisica->idpes_mae = $this->mae_id ? $this->mae_id : 'NULL';
        $fisica->nacionalidade = $_REQUEST['tipo_nacionalidade'];
        $fisica->idpais_estrangeiro = $_REQUEST['pais_origem_id'];
        $fisica->idmun_nascimento = $_REQUEST['naturalidade_id'] ?: 'NULL';
        $fisica->sus = trim(string: $this->sus);
        $fisica->nis_pis_pasep = $this->nis_pis_pasep ? $this->nis_pis_pasep : 'NULL';
        $fisica->ocupacao = $db->escapeString(string: $this->ocupacao);
        $fisica->empresa = $db->escapeString(string: $this->empresa);
        $fisica->ddd_telefone_empresa = $this->ddd_telefone_empresa;
        $fisica->telefone_empresa = $this->telefone_empresa;
        $fisica->pessoa_contato = $db->escapeString(string: $this->pessoa_contato);
        $fisica->renda_mensal = str_replace(search: ',', replace: '.', subject: str_replace(search: '.', replace: '', subject: $this->renda_mensal));
        $fisica->data_admissao = $this->data_admissao ? Portabilis_Date_Utils::brToPgSQL(date: $this->data_admissao) : null;
        $fisica->falecido = $this->falecido;
        $fisica->ref_cod_religiao = $this->religiao_id;
        $fisica->zona_localizacao_censo = empty($this->zona_localizacao_censo) ? null : $this->zona_localizacao_censo;
        $fisica->localizacao_diferenciada = $this->localizacao_diferenciada ?: 'null';
        $fisica->nome_social = $this->nome_social;
        $fisica->pais_residencia = $this->pais_residencia;
        $fisica->observacao = str_replace(search: '+', replace: ' ', subject: $this->observacao);

        $sql = 'select 1 from cadastro.fisica WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField(sql: $sql, paramsOrOptions: $pessoaId) != 1) {
            $fisica->cadastra();
        } else {
            $fisica->edita();
        }

        $this->createOrUpdateRaca(pessoaId: $pessoaId, corRaca: $this->cor_raca);
    }

    public function createOrUpdateRaca($pessoaId, $corRaca)
    {
        $pessoaId = (int) $pessoaId;
        $corRaca = (int) $corRaca;

        if ($corRaca == 0) {
            return false;
        } //Quando não tiver cor/raça selecionado não faz update

        $raca = new clsCadastroFisicaRaca(ref_idpes: $pessoaId, ref_cod_raca: $corRaca);

        if ($raca->existe()) {
            return $raca->edita();
        }

        return $raca->cadastra();
    }

    protected function createOrUpdateDocumentos($pessoaId)
    {
        $documentos = new clsDocumento();
        $documentos->idpes = $pessoaId;

        // rg

        $documentos->rg = $_REQUEST['rg'];

        $documentos->data_exp_rg = Portabilis_Date_Utils::brToPgSQL(
            date: $_REQUEST['data_emissao_rg']
        );

        $documentos->idorg_exp_rg = $_REQUEST['orgao_emissao_rg'];
        $documentos->sigla_uf_exp_rg = $_REQUEST['uf_emissao_rg'];

        // certidão civil

        // o tipo certidão novo padrão é apenas para exibição ao usuário,
        // não precisa ser gravado no banco
        //
        // quando selecionado um tipo diferente do novo formato,
        // é removido o valor de certidao_nascimento.
        //
        if ($_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato') {
            $documentos->tipo_cert_civil = null;
            $documentos->certidao_casamento = '';
            $documentos->certidao_nascimento = $_REQUEST['certidao_nascimento'];
        } elseif ($_REQUEST['tipo_certidao_civil'] == 'certidao_casamento_novo_formato') {
            $documentos->tipo_cert_civil = null;
            $documentos->certidao_nascimento = '';
            $documentos->certidao_casamento = $_REQUEST['certidao_casamento'];
        } else {
            $documentos->tipo_cert_civil = $_REQUEST['tipo_certidao_civil'];
            $documentos->certidao_nascimento = '';
            $documentos->certidao_casamento = '';
        }

        $documentos->num_termo = $_REQUEST['termo_certidao_civil'];
        $documentos->num_livro = $_REQUEST['livro_certidao_civil'];
        $documentos->num_folha = $_REQUEST['folha_certidao_civil'];

        $documentos->data_emissao_cert_civil = Portabilis_Date_Utils::brToPgSQL(
            date: $_REQUEST['data_emissao_certidao_civil']
        );

        $documentos->sigla_uf_cert_civil = $_REQUEST['uf_emissao_certidao_civil'];
        $documentos->cartorio_cert_civil = pg_escape_string(connection: $_REQUEST['cartorio_emissao_certidao_civil']);
        $documentos->passaporte = pg_escape_string(connection: $_REQUEST['passaporte']);

        // carteira de trabalho

        $documentos->num_cart_trabalho = $_REQUEST['carteira_trabalho'];
        $documentos->serie_cart_trabalho = $_REQUEST['serie_carteira_trabalho'];

        $documentos->data_emissao_cart_trabalho = Portabilis_Date_Utils::brToPgSQL(
            date: $_REQUEST['data_emissao_carteira_trabalho']
        );

        $documentos->sigla_uf_cart_trabalho = $_REQUEST['uf_emissao_carteira_trabalho'];

        // titulo de eleitor

        $documentos->num_tit_eleitor = $_REQUEST['titulo_eleitor'];
        $documentos->zona_tit_eleitor = $_REQUEST['zona_titulo_eleitor'];
        $documentos->secao_tit_eleitor = $_REQUEST['secao_titulo_eleitor'];

        // Alteração de documentos compativel com a versão anterior do cadastro,
        // onde era possivel criar uma pessoa, não informando os documentos,
        // o que não criaria o registro do documento, sendo assim, ao editar uma pessoa,
        // o registro do documento será criado, caso não exista.

        $sql = 'select 1 from cadastro.documento WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField(sql: $sql, paramsOrOptions: $pessoaId) != 1) {
            $documentos->cadastra();
        } else {
            $documentos->edita();
        }
    }

    protected function createOrUpdateTelefones($pessoaId)
    {
        $telefones = [];

        $telefones[] = new clsPessoaTelefone(int_idpes: $pessoaId, int_tipo: 1, str_fone: $this->telefone_1, str_ddd: $this->ddd_telefone_1);
        $telefones[] = new clsPessoaTelefone(int_idpes: $pessoaId, int_tipo: 2, str_fone: $this->telefone_2, str_ddd: $this->ddd_telefone_2);
        $telefones[] = new clsPessoaTelefone(int_idpes: $pessoaId, int_tipo: 3, str_fone: $this->telefone_mov, str_ddd: $this->ddd_telefone_mov);
        $telefones[] = new clsPessoaTelefone(int_idpes: $pessoaId, int_tipo: 4, str_fone: $this->telefone_fax, str_ddd: $this->ddd_telefone_fax);

        foreach ($telefones as $telefone) {
            $telefone->cadastra();
        }
    }

    // inputs usados em Gerar,
    // implementado estes metodos para não duplicar código
    // uma vez que estes campos são usados várias vezes em Gerar.

    protected function inputTelefone($type, $typeLabel = '')
    {
        if (!$typeLabel) {
            $typeLabel = "Telefone {$type}";
        }

        // ddd

        $options = [
            'required' => false,
            'label' => "(ddd) / {$typeLabel}",
            'placeholder' => 'ddd',
            'value' => $this->{"ddd_telefone_{$type}"},
            'max_length' => 3,
            'size' => 3,
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: "ddd_telefone_{$type}", inputOptions: $options);

        // telefone

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $typeLabel,
            'value' => $this->{"telefone_{$type}"},
            'max_length' => 11,
        ];

        $this->inputsHelper()->integer(attrName: "telefone_{$type}", inputOptions: $options);
    }

    private function saveFiles($idpes)
    {
        $fileService = new FileService(urlPresigner: new UrlPresigner);

        if ($this->file_url) {
            $newFiles = json_decode(json: $this->file_url);
            foreach ($newFiles as $file) {
                $fileService->saveFile(
                    url: $file->url,
                    size: $file->size,
                    originalName: $file->originalName,
                    extension: $file->extension,
                    typeFileRelation: LegacyIndividual::class,
                    relationId: $idpes
                );
            }
        }

        if ($this->file_url_deleted) {
            $deletedFiles = explode(separator: ',', string: $this->file_url_deleted);
            $fileService->deleteFiles(deletedFiles: $deletedFiles);
        }
    }

    public function Formular()
    {
        $this->title = 'Pessoa Física - Cadastro';
        $this->processoAp = 43;
    }
};
