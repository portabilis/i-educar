<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Services\UrlPresigner;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Validator\NameValidator;
use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use iEducar\Modules\Educacenso\Validator\NisValidator;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Support\View\SelectOptions;
use App\Services\FileService;

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';

require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/modules/clsModulesPessoaTransporte.inc.php';
require_once 'include/modules/clsModulesMotorista.inc.php';
require_once 'image_check.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/Database.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Pessoas Físicas - Cadastro');
        $this->processoAp = 43;
    }
}

class indice extends clsCadastro
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

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(43, $this->pessoa_logada, 7, 'atendidos_lst.php');

        $this->cod_pessoa_fj = $this->getQueryString('cod_pessoa_fj');
        $this->retorno = 'Novo';

        if (is_numeric($this->cod_pessoa_fj)) {
            $this->retorno = 'Editar';
            $objPessoa = new clsPessoaFisica();

            list($this->nm_pessoa, $this->id_federal, $this->data_nasc,
                $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2,
                $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov,
                $this->ddd_telefone_fax, $this->telefone_fax, $this->email,
                $this->tipo_pessoa, $this->sexo, $this->estado_civil,
                $this->pai_id, $this->mae_id, $this->tipo_nacionalidade, $this->pais_origem, $this->naturalidade,
                $this->letra, $this->sus, $this->nis_pis_pasep, $this->ocupacao, $this->empresa, $this->ddd_telefone_empresa,
                $this->telefone_empresa, $this->pessoa_contato, $this->renda_mensal, $this->data_admissao, $this->falecido,
                $this->religiao_id, $this->zona_localizacao_censo, $this->localizacao_diferenciada, $this->nome_social, $this->pais_residencia
            ) =
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
                'pais_residencia'
            );

            $this->loadAddress($this->cod_pessoa_fj);

            $this->id_federal = is_numeric($this->id_federal) ? int2CPF($this->id_federal) : '';
            $this->nis_pis_pasep = int2Nis($this->nis_pis_pasep);
            $this->renda_mensal = number_format($this->renda_mensal, 2, ',', '.');
            // $this->data_nasc = $this->data_nasc ? dataFromPgToBr($this->data_nasc) : '';
            $this->data_admissao = $this->data_admissao ? dataFromPgToBr($this->data_admissao) : '';

            $this->estado_civil_id = $this->estado_civil->ideciv;
            $this->pais_origem_id = $this->pais_origem;
            $this->naturalidade_id = $this->naturalidade;

            $raca = new clsCadastroFisicaRaca($this->cod_pessoa_fj);
            $raca = $raca->detalhe();
            $this->cod_raca = is_array($raca) ? $raca['ref_cod_raca'] : null;
        }

        $this->fexcluir = $obj_permissoes->permissao_excluir(
            43,
            $this->pessoa_logada,
            7
        );

        $this->nome_url_cancelar = 'Cancelar';
        $this->breadcrumb('Pessoa física', ['educar_pessoas_index.php' => 'Pessoas']);

        return $this->retorno;
    }

    public function Gerar()
    {
        $this->form_enctype = ' enctype=\'multipart/form-data\'';
        $camposObrigatorios = !config('legacy.app.remove_obrigatorios_cadastro_pessoa') == 1;
        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
        $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);
        $this->url_cancelar = $this->retorno == 'Editar' ?
        'atendidos_det.php?cod_pessoa=' . $this->cod_pessoa_fj : 'atendidos_lst.php';

        $this->cod_pessoa_fj;
        $objPessoa = new clsPessoaFisica($this->cod_pessoa_fj);
        $db = new clsBanco();

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
            'data_exclusao'
        );

        if (isset($this->cod_pessoa_fj) && !$detalhe['ativo'] == 1 && $this->retorno == 'Editar') {
            $getNomeUsuario = $objPessoa->getNomeUsuario();
            $detalhe['data_exclusao'] = date_format(new DateTime($detalhe['data_exclusao']), 'd/m/Y');
            $this->mensagem = 'Este cadastro foi desativado em <strong>' . $detalhe['data_exclusao'] . '</strong>, pelo usuário <strong>' . $getNomeUsuario . "</strong>. <a href='javascript:ativarPessoa($this->cod_pessoa_fj);'>Reativar cadastro</a>";
        }

        $this->campoCpf('id_federal', 'CPF', $this->id_federal, false);

        $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
        $this->campoTexto('nm_pessoa', 'Nome', $this->nm_pessoa, '50', '255', true);
        $this->campoTexto('nome_social', 'Nome social', $this->nome_social, '50', '255', false);

        $foto = false;
        if (is_numeric($this->cod_pessoa_fj)) {
            $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
            $detalheFoto = $objFoto->detalhe();
            if (count($detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $foto=false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' . (new UrlPresigner())->getPresignedUrl($foto) . '"/>');
            $this->inputsHelper()->checkbox('file_delete', ['label' => 'Excluir a foto']);
            $this->campoArquivo('photo', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 150KB</span>');
        } else {
            $this->campoArquivo('photo', 'Foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 150KB</span>');
        }

        // ao cadastrar pessoa do pai ou mãe apartir do cadastro de outra pessoa,
        // é enviado o tipo de cadastro (pai ou mae).
        $parentType = isset($_REQUEST['parent_type']) ? $_REQUEST['parent_type'] : '';
        // Se a pessoa for pai ou mãe, não tera naturalidade obrigatoria

        $naturalidadeObrigatoria = ($parentType == '' ? true : false);

        // sexo

        $sexo = $this->sexo;

        // sugere sexo quando cadastrando o pai ou mãe

        if (! $sexo && $parentType == 'pai') {
            $sexo = 'M';
        } elseif (! $sexo && $parentType == 'mae') {
            $sexo = 'F';
        }

        $options = [
            'label' => 'Sexo / Estado civil',
            'value' => $sexo,
            'resources' => [
                '' => 'Sexo',
                'M' => 'Masculino',
                'F' => 'Feminino'
            ],
            'inline' => true,
            'required' => $camposObrigatorios
        ];

        $this->inputsHelper()->select('sexo', $options);

        // estado civil

        $this->inputsHelper()->estadoCivil(['label' => '', 'required' => empty($parentType) && $camposObrigatorios]);

        // data nascimento

        $options = [
            'label' => 'Data de nascimento',
            'value' => $this->data_nasc,
            'required' => empty($parentType) && $camposObrigatorios
        ];

        $this->inputsHelper()->date('data_nasc', $options);

        // pai, mãe

        $this->inputPai();
        $this->inputMae();

        // documentos

        $documentos = new clsDocumento();
        $documentos->idpes = $this->cod_pessoa_fj;
        $documentos = $documentos->detalhe();

        // rg

        // o rg é obrigatorio ao cadastrar pai ou mãe, exceto se configurado como opcional.

        $required = (! empty($parentType));

        if ($required && config('legacy.app.rg_pessoa_fisica_pais_opcional')) {
            $required = false;
        }

        $options = [
            'required' => $required,
            'label' => 'RG / Data emissão',
            'placeholder' => 'Documento identidade',
            'value' => $documentos['rg'],
            'max_length' => 25,
            'size' => 27,
            'inline' => true
        ];

        $this->inputsHelper()->integer('rg', $options);

        // data emissão rg

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emissão',
            'value' => $documentos['data_exp_rg'],
            'size' => 19
        ];

        $this->inputsHelper()->date('data_emissao_rg', $options);

        // orgão emissão rg

        $selectOptions = [ null => 'Órgão emissor' ];
        $orgaos = new clsOrgaoEmissorRg();
        $orgaos = $orgaos->lista();

        foreach ($orgaos as $orgao) {
            $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];
        }

        $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

        $options = [
            'required' => false,
            'label' => '',
            'value' => $documentos['idorg_exp_rg'],
            'resources' => $selectOptions,
            'inline' => true
        ];

        $this->inputsHelper()->select('orgao_emissao_rg', $options);

        // uf emissão rg

        $options = [
            'required' => false,
            'label' => '',
            'value' => $documentos['sigla_uf_exp_rg']
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_rg'
        ];

        $this->inputsHelper()->uf($options, $helperOptions);

        // Código NIS (PIS/PASEP)

        $options = [
            'required' => false,
            'label' => 'NIS (PIS/PASEP)',
            'placeholder' => '',
            'value' => $this->nis_pis_pasep,
            'max_length' => 11,
            'size' => 20
        ];

        $this->inputsHelper()->integer('nis_pis_pasep', $options);

        // Carteira do SUS

        $options = [
            'required' => config('legacy.app.fisica.exigir_cartao_sus'),
            'label' => 'Número da carteira do SUS',
            'placeholder' => '',
            'value' => $this->sus,
            'max_length' => 20,
            'size' => 20
        ];

        $this->inputsHelper()->text('sus', $options);

        // tipo de certidao civil

        $selectOptions = [
            null => 'Tipo certidão civil',
            'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
            91 => 'Nascimento (antigo formato)',
            'certidao_casamento_novo_formato' => 'Casamento (novo formato)',
            92 => 'Casamento (antigo formato)'
        ];

        // caso certidao nascimento novo formato tenha sido informado,
        // considera este o tipo da certidão
        if (! empty($documentos['certidao_nascimento'])) {
            $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
        } elseif (! empty($documentos['certidao_casamento'])) {
            $tipoCertidaoCivil = 'certidao_casamento_novo_formato';
        } else {
            $tipoCertidaoCivil = $documentos['tipo_cert_civil'];
        }

        $options = [
            'required' => false,
            'label' => 'Tipo certidão civil',
            'value' => $tipoCertidaoCivil,
            'resources' => $selectOptions,
            'inline' => true
        ];

        $this->inputsHelper()->select('tipo_certidao_civil', $options);

        // termo certidao civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Termo',
            'value' => $documentos['num_termo'],
            'max_length' => 8,
            'inline' => true
        ];

        $this->inputsHelper()->integer('termo_certidao_civil', $options);

        // livro certidao civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Livro',
            'value' => $documentos['num_livro'],
            'max_length' => 8,
            'size' => 15,
            'inline' => true
        ];

        $this->inputsHelper()->text('livro_certidao_civil', $options);

        // folha certidao civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Folha',
            'value' => $documentos['num_folha'],
            'max_length' => 4,
            'inline' => true
        ];

        $this->inputsHelper()->integer('folha_certidao_civil', $options);

        // certidao nascimento (novo padrão)

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Certidão nascimento',
            'value' => $documentos['certidao_nascimento'],
            'max_length' => 32,
            'size' => 32,
            'inline' => true
        ];

        $this->inputsHelper()->integer('certidao_nascimento', $options);

        // certidao casamento (novo padrão)

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Certidão casamento',
            'value' => $documentos['certidao_casamento'],
            'max_length' => 32,
            'size' => 32
        ];

        $this->inputsHelper()->integer('certidao_casamento', $options);

        // uf emissão certidão civil

        $options = [
            'required' => false,
            'label' => 'Estado emissão / Data emissão',
            'label_hint' => 'Informe o estado para poder informar o código do cartório',
            'value' => $documentos['sigla_uf_cert_civil'],
            'inline' => true
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_certidao_civil'
        ];

        $this->inputsHelper()->uf($options, $helperOptions);

        // data emissão certidão civil

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emissão',
            'value' => $documentos['data_emissao_cert_civil'],
            'inline' => true
        ];

        $this->inputsHelper()->date('data_emissao_certidao_civil', $options);

        $options = [
            'label' => '',
            'required' => false
        ];

        // cartório emissão certidão civil
        $options = [
            'required' => false,
            'label' => 'Cartório emissão',
            'value' => $documentos['cartorio_cert_civil'],
            'cols' => 45,
            'max_length' => 200,
        ];

        $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);

        // Passaporte
        $options = [
            'required' => false,
            'label' => 'Passaporte',
            'value' => $documentos['passaporte'],
            'cols' => 45,
            'max_length' => 20
        ];

        $this->inputsHelper()->text('passaporte', $options);

        // carteira de trabalho

        $options = [
            'required' => false,
            'label' => 'Carteira de trabalho / Série',
            'placeholder' => 'Carteira de trabalho',
            'value' => $documentos['num_cart_trabalho'],
            'max_length' => 7,
            'inline' => true
        ];

        $this->inputsHelper()->integer('carteira_trabalho', $options);

        // serie carteira de trabalho

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Série',
            'value' => $documentos['serie_cart_trabalho'],
            'max_length' => 5
        ];

        $this->inputsHelper()->integer('serie_carteira_trabalho', $options);

        // uf emissão carteira de trabalho

        $options = [
            'required' => false,
            'label' => 'Estado emissão / Data emissão',
            'value' => $documentos['sigla_uf_cart_trabalho'],
            'inline' => true
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_carteira_trabalho'
        ];

        $this->inputsHelper()->uf($options, $helperOptions);

        // data emissão carteira de trabalho

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emissão',
            'value' => $documentos['data_emissao_cart_trabalho']
        ];

        $this->inputsHelper()->date('data_emissao_carteira_trabalho', $options);

        // titulo eleitor

        $options = [
            'required' => false,
            'label' => 'Titulo eleitor / Zona / Seção',
            'placeholder' => 'Titulo eleitor',
            'value' => $documentos['num_tit_eleitor'],
            'max_length' => 13,
            'inline' => true
        ];

        $this->inputsHelper()->integer('titulo_eleitor', $options);

        // zona titulo eleitor

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Zona',
            'value' => $documentos['zona_tit_eleitor'],
            'max_length' => 4,
            'inline' => true
        ];

        $this->inputsHelper()->integer('zona_titulo_eleitor', $options);

        // seção titulo eleitor

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Seção',
            'value' => $documentos['secao_tit_eleitor'],
            'max_length' => 4
        ];

        $this->inputsHelper()->integer('secao_titulo_eleitor', $options);

        // Cor/raça.

        $racas = new clsCadastroRaca();
        $racas = $racas->lista(null, null, null, null, null, null, null, true);

        $selectOptionsRaca = [];

        foreach ($racas as $raca) {
            $selectOptionsRaca[$raca['cod_raca']] = $raca['nm_raca'];
        }

        $selectOptionsRaca = Portabilis_Array_Utils::sortByValue($selectOptionsRaca);
        $selectOptionsRaca = array_replace([null => 'Selecione'], $selectOptionsRaca);

        $this->campoLista('cor_raca', 'Raça', $selectOptionsRaca, $this->cod_raca, '', false, '', '', '', $obrigarCamposCenso);

        // nacionalidade

        // tipos
        $tiposNacionalidade = [
            '1' => 'Brasileira',
            '2' => 'Naturalizado brasileiro',
            '3' => 'Estrangeira'
        ];

        $options = [
            'label' => 'Nacionalidade',
            'resources' => $tiposNacionalidade,
            'required' => $obrigarCamposCenso,
            'inline' => true,
            'value' => $this->tipo_nacionalidade
        ];

        $this->inputsHelper()->select('tipo_nacionalidade', $options);

        // pais origem

        $options = [
            'label' => '',
            'placeholder' => 'Informe o nome do pais',
            'required' => true
        ];

        $hiddenInputOptions = [
            'options' => ['value' => $this->pais_origem_id]
        ];

        $helperOptions = [
            'objectName' => 'pais_origem',
            'hiddenInputOptions' => $hiddenInputOptions
        ];

        $this->inputsHelper()->simpleSearchPaisSemBrasil('nome', $options, $helperOptions);

        //Falecido
        $options = ['label' => 'Falecido?', 'required' => false, 'value' => dbBool($this->falecido)];

        $this->inputsHelper()->checkbox('falecido', $options);

        // naturalidade

        $options = ['label' => 'Naturalidade', 'required' => $naturalidadeObrigatoria && $camposObrigatorios];

        $helperOptions = [
            'objectName' => 'naturalidade',
            'hiddenInputOptions' => ['options' => ['value' => $this->naturalidade_id]]
        ];

        $this->inputsHelper()->simpleSearchMunicipio('nome', $options, $helperOptions);

        // Religião
        $this->inputsHelper()->religiao(['required' => false, 'label' => 'Religião']);

        $this->viewAddress();

        $this->inputsHelper()->select('pais_residencia', [
            'label' => 'País de residência',
            'value' => $this->pais_residencia ?: PaisResidencia::BRASIL ,
            'resources' => PaisResidencia::getDescriptiveValues(),
            'required' => true,
        ]);

        $this->inputsHelper()->select('zona_localizacao_censo', [
            'label' => 'Zona de residência',
            'value' => $this->zona_localizacao_censo,
            'resources' => [
                '' => 'Selecione',
                1 => 'Urbana',
                2 => 'Rural'
            ],
            'required' => $obrigarCamposCenso,
        ]);

        $this->inputsHelper()->select('localizacao_diferenciada', [
            'label' => 'Localização diferenciada de residência',
            'value' => $this->localizacao_diferenciada,
            'resources' => SelectOptions::localizacoesDiferenciadasPessoa(),
            'required' => false,
        ]);

        // contato
        $this->campoRotulo('contato', '<b>Contato</b>', '', '', 'Informações de contato da pessoa');
        $this->inputTelefone('1', 'Telefone residencial');
        $this->inputTelefone('2', 'Celular');
        $this->inputTelefone('mov', 'Telefone adicional');
        $this->inputTelefone('fax', 'Fax');
        $this->campoTexto('email', 'E-mail', $this->email, '50', '255', false);

        // renda
        $this->campoRotulo('renda', '<b>Trabalho e renda</b>', '', '', 'Informações de trabalho e renda da pessoa');
        $this->campoTexto('ocupacao', 'Ocupação', $this->ocupacao, '50', '255', false);
        $this->campoMonetario('renda_mensal', 'Renda mensal (R$)', $this->renda_mensal, '9', '10');
        $this->campoData('data_admissao', 'Data de admissão', $this->data_admissao);
        $this->campoTexto('empresa', 'Empresa', $this->empresa, '50', '255', false);
        $this->inputTelefone('empresa', 'Telefone da empresa');
        $this->campoTexto('pessoa_contato', 'Pessoa de contato na empresa', $this->pessoa_contato, '50', '255', false);

        $fileService = new FileService(new UrlPresigner);
        $files = $fileService->getFiles(LegacyIndividual::find($this->cod_pessoa_fj));
        $this->addHtml(view('uploads.upload', ['files' => $files])->render());

        // after change pessoa pai / mae

        if ($parentType) {
            $this->inputsHelper()->hidden('parent_type', ['value' => $parentType]);
        }

        $styles = [
            '/modules/Portabilis/Assets/Stylesheets/Frontend.css',
            '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
            '/modules/Cadastro/Assets/Stylesheets/PessoaFisica.css'
        ];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

        $script = [
            '/modules/Cadastro/Assets/Javascripts/PessoaFisica.js',
            '/modules/Cadastro/Assets/Javascripts/Addresses.js',
            '/modules/Cadastro/Assets/Javascripts/Endereco.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $script);
    }

    public function Novo()
    {
        return $this->createOrUpdate();
    }

    public function Editar()
    {
        return $this->createOrUpdate($this->cod_pessoa_fj);
    }

    public function Excluir()
    {
        $idPes = $this->cod_pessoa_fj;

        $aluno = new clsPmieducarAluno();
        $aluno = $aluno->lista(null, null, null, null, null, $idPes, null, null, null, null, 1);

        if ($aluno) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com aluno.';

            return false;
        }

        $inUse = LegacyIndividual::query()
            ->where('idpes_responsavel', $idPes)
            ->orWhere('idpes_pai', $idPes)
            ->orWhere('idpes_mae', $idPes)
            ->exists();

        if ($inUse) {
            $this->mensagem = 'Não foi possível excluir. A pessoa possuí vínculo(s) com aluno(s) como mãe, pai ou outro responsável.';

            return false;
        }

        $usuario = new clsPmieducarUsuario();
        $usuario = $usuario->lista($idPes, null, null, null, null, null, null, null, null, null, true);
        $funcionario = new clsPortalFuncionario();
        $funcionario->ref_cod_pessoa_fj = $idPes;
        $funcionario = $funcionario->lista(null, null, 1);

        if ($funcionario && $usuario) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com usuário do sistema.';

            return false;
        }

        $servidor = new clsPmieducarServidor();
        $servidor = $servidor->lista($idPes, null, null, null, null, null, null, null, 1);

        if ($servidor) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com servidor.';

            return false;
        }

        $cliente = new clsPmieducarCliente();
        $cliente = $cliente->lista(null, null, null, $idPes, null, null, null, null, null, null, 1);

        if ($cliente) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com cliente.';

            return false;
        }

        $usuarioTransporte = new clsModulesPessoaTransporte();
        $usuarioTransporte = $usuarioTransporte->lista(null, $idPes);

        if ($usuarioTransporte) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com usuário de transporte.';

            return false;
        }

        $motorista = new clsModulesMotorista();
        $motorista = $motorista->lista(null, null, null, null, null, $idPes);

        if ($motorista) {
            $this->mensagem = 'Não foi possível excluir. Esta pessoa possuí vínculo com motorista.';

            return false;
        }

        $pessoaFisica = new clsPessoaFisica($idPes);
        $pessoaFisica->excluir();

        $this->mensagem = 'Exclusão efetuada com sucesso.';

        $this->simpleRedirect('atendidos_lst.php');
    }

    public function afterChangePessoa($id)
    {
        Portabilis_View_Helper_Application::embedJavascript($this, "

        if(window.opener &&  window.opener.afterChangePessoa) {
            var parentType = \$j('#parent_type').val();

            if (parentType)
            window.opener.afterChangePessoa(self, parentType, $id, \$j('#nm_pessoa').val());
            else
            window.opener.afterChangePessoa(self, null, $id, \$j('#nm_pessoa').val());
        }
        else
            document.location = 'atendidos_lst.php';

        ", $afterReady = true);
    }

    protected function loadAlunoByPessoaId($id)
    {
        $aluno = new clsPmieducarAluno();
        $aluno->ref_idpes = $id;

        return $aluno->detalhe();
    }

    protected function inputPai()
    {
        $this->addParentsInput('pai');
    }

    protected function inputMae()
    {
        $this->addParentsInput('mae', 'mãe');
    }

    protected function addParentsInput($parentType, $parentTypeLabel = '')
    {
        if (! $parentTypeLabel) {
            $parentTypeLabel = $parentType;
        }

        if (! isset($this->_aluno)) {
            $this->_aluno = $this->loadAlunoByPessoaId($this->cod_pessoa_fj);
        }

        $parentId = $this->{$parentType . '_id'};

        // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
        //pela antiga interface do cadastro de alunos.

        if (! $parentId && $this->_aluno['nm_' . $parentType]) {
            $nome = Portabilis_String_Utils::toLatin1(
                $this->_aluno['nm_' . $parentType],
                ['transform' => true, 'escape' => false]
            );

            $inputHint = '<br /><b>Dica:</b> Foi informado o nome "' . $nome .
            '" no cadastro de aluno,<br />tente pesquisar esta pessoa ' .
            'pelo CPF ou RG, caso não encontre, cadastre uma nova pessoa.';
        }

        $hiddenInputOptions = ['options' => ['value' => $parentId]];
        $helperOptions = ['objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions];

        $options = [
            'label' => 'Pessoa ' . $parentTypeLabel,
            'size' => 50,
            'required' => false,
            'input_hint' => $inputHint
        ];

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
    }

    protected function validatesCpf($cpf)
    {
        $isValid = true;

        if ($cpf && ! Portabilis_Utils_Validation::validatesCpf($cpf)) {
            $this->erros['id_federal'] = 'CPF inválido.';
            $isValid = false;
        } elseif ($cpf) {
            $fisica = new clsFisica();
            $fisica->cpf = idFederal2int($cpf);
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
        if ($this->obrigarDocumentoPessoa() && !$this->possuiDocumentoObrigatorio()) {
            $this->mensagem = 'É necessário o preenchimento de pelo menos um dos seguintes documentos: CPF, RG ou Certidão civil.';

            return false;
        }

        if (!empty($this->pai_id) && !empty($this->mae_id) && $this->pai_id == $this->mae_id) {
            $this->mensagem = 'Não é possível informar a mesma pessoa para Pai e Mãe';

            return false;
        }

        if (! $this->validatesCpf($this->id_federal)) {
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

        $pessoaId = $this->createOrUpdatePessoa($pessoaIdOrNull);
        $this->savePhoto($pessoaId);
        $this->createOrUpdatePessoaFisica($pessoaId);
        $this->createOrUpdateDocumentos($pessoaId);
        $this->createOrUpdateTelefones($pessoaId);
        $this->saveAddress($pessoaId);
        $this->afterChangePessoa($pessoaId);
        $this->saveFiles($pessoaId);

        return true;
    }

    private function validaNome()
    {
        $validator = new NameValidator($this->nm_pessoa);
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();
            return false;
        }

        return true;
    }

    private function validaLocalizacaoDiferenciada()
    {
        $validator = new DifferentiatedLocationValidator($this->localizacao_diferenciada, $this->zona_localizacao_censo);
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();
            return false;
        }

        return true;
    }

    private function validaDataNascimento()
    {
        $validator = new BirthDateValidator(Portabilis_Date_Utils::brToPgSQL($this->data_nasc));
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();
            return false;
        }

        return true;
    }

    //envia foto e salva caminha no banco
    protected function savePhoto($id)
    {
        if ($this->objPhoto!=null) {
            $caminhoFoto = $this->objPhoto->sendPicture();
            if ($caminhoFoto!='') {
                //new clsCadastroFisicaFoto($id)->exclui();
                $obj = new clsCadastroFisicaFoto($id, $caminhoFoto);
                $detalheFoto = $obj->detalhe();
                if (is_array($detalheFoto) && count($detalheFoto)>0) {
                    $obj->edita();
                } else {
                    $obj->cadastra();
                }

                return true;
            } else {
                echo '<script>alert(\'Foto não salva.\')</script>';

                return false;
            }
        } elseif ($this->file_delete == 'on') {
            $obj = new clsCadastroFisicaFoto($id);
            $obj->excluir();
        }
    }

    // Retorna true caso a foto seja válida
    protected function validatePhoto()
    {
        $this->arquivoFoto = $_FILES['photo'];
        if (!empty($this->arquivoFoto['name'])) {
            $this->arquivoFoto['name'] = mb_strtolower($this->arquivoFoto['name'], 'UTF-8');
            $this->objPhoto = new PictureController($this->arquivoFoto);
            if ($this->objPhoto->validatePicture()) {
                return true;
            } else {
                $this->mensagem = $this->objPhoto->getErrorMessage();

                return false;
            }

            return false;
        } else {
            $this->objPhoto = null;

            return true;
        }
    }

    protected function obrigarDocumentoPessoa()
    {
        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigarDocumentoPessoa = false;

        if ($instituicao && isset($instituicao['obrigar_documento_pessoa'])) {
            $obrigarDocumentoPessoa = dbBool($instituicao['obrigar_documento_pessoa']);
        }

        return $obrigarDocumentoPessoa;
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

        if ($certidaoNascimento && strlen($this->certidao_nascimento) < 32) {
            $this->mensagem = 'O campo referente a certidão de nascimento deve conter exatos 32 dígitos.';

            return false;
        } elseif ($certidaoCasamento && strlen($this->certidao_casamento) < 32) {
            $this->mensagem = 'O campo referente a certidão de casamento deve conter exatos 32 dígitos.';

            return false;
        }

        if (!empty($this->data_nasc) && $certidaoNascimento) {
            $validator = new BirthCertificateValidator($this->certidao_nascimento, Portabilis_Date_Utils::brToPgSQL($this->data_nasc));
            if (!$validator->isValid()) {
                $this->mensagem = $validator->getMessage();
                return false;
            }
        }

        return true;
    }

    protected function validaNisPisPasep()
    {
        if ($this->nis_pis_pasep && strlen($this->nis_pis_pasep) != 11) {
            $this->mensagem = 'O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.';

            return false;
        }

        $validator = new NisValidator($this->nis_pis_pasep ?? '');
        if (!$validator->isValid()) {
            $this->mensagem = $validator->getMessage();
            return false;
        }

        return true;
    }

    protected function validaObrigatoriedadeTelefone()
    {
        $institution = app(LegacyInstitution::class);
        $telefoneObrigatorio = $institution->obrigar_telefone_pessoa;
        $possuiTelefoneInformado = (!empty($this->telefone_1) || !empty($this->telefone_2));

        if ($telefoneObrigatorio && !$possuiTelefoneInformado) {
            return false;
        }

        return true;
    }

    protected function createOrUpdatePessoa($pessoaId = null)
    {
        $pessoa = new clsPessoa_();
        $pessoa->idpes = $pessoaId;
        $pessoa->nome = $this->nm_pessoa;
        $pessoa->email = addslashes($this->email);

        $sql = 'select 1 from cadastro.pessoa WHERE idpes = $1 limit 1';

        if (! $pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $pessoa->tipo = 'F';
            $pessoa->idpes_cad = $this->currentUserId();
            $pessoaId = $pessoa->cadastra();
        } else {
            $pessoa->idpes_rev = $this->currentUserId();
            $pessoa->data_rev = date('Y-m-d H:i:s', time());
            $pessoa->edita();
        }

        return $pessoaId;
    }

    protected function createOrUpdatePessoaFisica($pessoaId)
    {
        $db = new clsBanco();
        $fisica = new clsFisica();
        $fisica->idpes = $pessoaId;
        $fisica->data_nasc = Portabilis_Date_Utils::brToPgSQL($this->data_nasc);
        $fisica->sexo = $this->sexo;
        $fisica->ref_cod_sistema = 'NULL';
        $fisica->cpf = $this->id_federal ? idFederal2int($this->id_federal) : 'NULL';
        $fisica->ideciv = $this->estado_civil_id;
        $fisica->idpes_pai = $this->pai_id ? $this->pai_id : 'NULL';
        $fisica->idpes_mae = $this->mae_id ? $this->mae_id : 'NULL';
        $fisica->nacionalidade = $_REQUEST['tipo_nacionalidade'];
        $fisica->idpais_estrangeiro = $_REQUEST['pais_origem_id'];
        $fisica->idmun_nascimento = $_REQUEST['naturalidade_id'] ?: 'NULL';
        $fisica->sus = $this->sus;
        $fisica->nis_pis_pasep = $this->nis_pis_pasep ? $this->nis_pis_pasep : 'NULL';
        $fisica->ocupacao = $db->escapeString($this->ocupacao);
        $fisica->empresa = $db->escapeString($this->empresa);
        $fisica->ddd_telefone_empresa = $this->ddd_telefone_empresa;
        $fisica->telefone_empresa = $this->telefone_empresa;
        $fisica->pessoa_contato = $db->escapeString($this->pessoa_contato);
        $this->renda_mensal = str_replace('.', '', $this->renda_mensal);
        $this->renda_mensal = str_replace(',', '.', $this->renda_mensal);
        $fisica->renda_mensal = $this->renda_mensal;
        $fisica->data_admissao = $this->data_admissao ? Portabilis_Date_Utils::brToPgSQL($this->data_admissao) : null;
        $fisica->falecido = $this->falecido;
        $fisica->ref_cod_religiao = $this->religiao_id;
        $fisica->zona_localizacao_censo = empty($this->zona_localizacao_censo) ? null : $this->zona_localizacao_censo;
        $fisica->localizacao_diferenciada = $this->localizacao_diferenciada ?: 'null';
        $fisica->nome_social = $this->nome_social;
        $fisica->pais_residencia = $this->pais_residencia;

        $sql = 'select 1 from cadastro.fisica WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $fisica->cadastra();
        } else {
            $fisica->edita();
        }

        $this->createOrUpdateRaca($pessoaId, $this->cor_raca);
    }

    public function createOrUpdateRaca($pessoaId, $corRaca)
    {
        $pessoaId = (int) $pessoaId;
        $corRaca  = (int) $corRaca;

        if ($corRaca == 0) {
            return false;
        } //Quando não tiver cor/raça selecionado não faz update

        $raca = new clsCadastroFisicaRaca($pessoaId, $corRaca);

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
            $_REQUEST['data_emissao_rg']
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
            $_REQUEST['data_emissao_certidao_civil']
        );

        $documentos->sigla_uf_cert_civil = $_REQUEST['uf_emissao_certidao_civil'];
        $documentos->cartorio_cert_civil = addslashes($_REQUEST['cartorio_emissao_certidao_civil']);
        $documentos->passaporte = addslashes($_REQUEST['passaporte']);

        // carteira de trabalho

        $documentos->num_cart_trabalho = $_REQUEST['carteira_trabalho'];
        $documentos->serie_cart_trabalho = $_REQUEST['serie_carteira_trabalho'];

        $documentos->data_emissao_cart_trabalho = Portabilis_Date_Utils::brToPgSQL(
            $_REQUEST['data_emissao_carteira_trabalho']
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

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $documentos->cadastra();
        } else {
            $documentos->edita();
        }
    }

    protected function createOrUpdateTelefones($pessoaId)
    {
        $telefones = [];

        $telefones[] = new clsPessoaTelefone($pessoaId, 1, $this->telefone_1, $this->ddd_telefone_1);
        $telefones[] = new clsPessoaTelefone($pessoaId, 2, $this->telefone_2, $this->ddd_telefone_2);
        $telefones[] = new clsPessoaTelefone($pessoaId, 3, $this->telefone_mov, $this->ddd_telefone_mov);
        $telefones[] = new clsPessoaTelefone($pessoaId, 4, $this->telefone_fax, $this->ddd_telefone_fax);

        foreach ($telefones as $telefone) {
            $telefone->cadastra();
        }
    }

    // inputs usados em Gerar,
    // implementado estes metodos para não duplicar código
    // uma vez que estes campos são usados várias vezes em Gerar.

    protected function inputTelefone($type, $typeLabel = '')
    {
        if (! $typeLabel) {
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
            'inline' => true
        ];

        $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);

        // telefone

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $typeLabel,
            'value' => $this->{"telefone_{$type}"},
            'max_length' => 11
        ];

        $this->inputsHelper()->integer("telefone_{$type}", $options);
    }

    private function saveFiles($idpes)
    {
        $fileService = new FileService(new UrlPresigner);

        if ($this->file_url) {
            $newFiles = json_decode($this->file_url);
            foreach ($newFiles as $file) {
                $fileService->saveFile(
                    $file->url,
                    $file->size,
                    $file->originalName,
                    $file->extension,
                    LegacyIndividual::class,
                    $idpes
                );
            }
        }

        if ($this->file_url_deleted) {
            $deletedFiles = explode(',', $this->file_url_deleted);
            $fileService->deleteFiles($deletedFiles);
        }
    }
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
