<?php

use App\Services\UrlPresigner;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;
use iEducar\Modules\Educacenso\Model\VeiculoTransporteEscolar;
use iEducar\Support\View\SelectOptions;

class AlunoController extends Portabilis_Controller_Page_EditController
{
    use LegacyAddressingFields;

    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Cadastro de aluno';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    protected $_processoAp = 578;

    protected $_deleteOption = true;

    protected $cod_aluno;

    public $objPhoto;

    public $arquivoFoto;

    public $file_delete;

    public $caminho_det;

    public $caminho_lst;

    public $observacao;

    protected $_formMap = [
        'pessoa' => [
            'label' => 'Pessoa',
            'help' => '',
        ],

        'justificativa_falta_documentacao' => [
            'label' => 'Justificativa para a falta de documentação',
            'help' => '',
        ],

        'certidao_nascimento' => [
            'label' => 'Certidão de Nascimento',
            'help' => '',
        ],

        'certidao_casamento' => [
            'label' => 'Certidão de Casamento',
            'help' => '',
        ],

        'pai' => [
            'label' => 'Pai',
            'help' => '',
        ],

        'mae' => [
            'label' => 'Mãe',
            'help' => '',
        ],

        'alfabetizado' => [
            'label' => 'Alfabetizado',
            'help' => '',
        ],

        'emancipado' => [
            'label' => 'Emancipado'
        ],

        'transporte' => [
            'label' => 'Transporte escolar público',
            'help' => '',
        ],

        'id' => [
            'label' => 'Código aluno',
            'help' => '',
        ],

        'aluno_inep_id' => [
            'label' => 'Código INEP',
            'help' => '',
        ],

        'aluno_estado_id' => [
            'label' => 'Código rede estadual',
            'help' => '',
        ],

        'deficiencias' => [
            'label' => 'Deficiências / habilidades especiais',
            'help' => '',
        ],

        'laudo_medico' => [
            'label' => 'Laudo médico',
            'help' => '',
        ],

        'documento' => [
            'label' => 'Documentos',
            'help' => '',
        ],

        'sus' => ['label' => 'Número da Carteira do SUS'],

        'altura' => ['label' => 'Altura/Metro'],

        'peso' => ['label' => 'Peso/Kg'],

        'grupo_sanguineo' => ['label' => 'Grupo sanguíneo'],

        'fator_rh' => ['label' => 'Fator RH'],

        'alergia_medicamento' => ['label' => 'O aluno é alérgico a algum medicamento?'],

        'desc_alergia_medicamento' => ['label' => 'Quais?'],

        'alergia_alimento' => ['label' => 'O aluno é alérgico a algum alimento?'],

        'desc_alergia_alimento' => ['label' => 'Quais?'],

        'doenca_congenita' => ['label' => 'O aluno possui doença congênita?'],

        'desc_doenca_congenita' => ['label' => 'Quais?'],

        'fumante' => ['label' => 'O aluno é fumante?'],

        'doenca_caxumba' => ['label' => 'O aluno já contraiu caxumba?'],

        'doenca_sarampo' => ['label' => 'O aluno já contraiu sarampo?'],

        'doenca_rubeola' => ['label' => 'O aluno já contraiu rubeola?'],

        'doenca_catapora' => ['label' => 'O aluno já contraiu catapora?'],

        'doenca_escarlatina' => ['label' => 'O aluno já contraiu escarlatina?'],

        'doenca_coqueluche' => ['label' => 'O aluno já contraiu coqueluche?'],

        'doenca_outras' => ['label' => 'Outras doenças que o aluno já contraiu'],

        'epiletico' => ['label' => 'O aluno é epilético?'],

        'epiletico_tratamento' => ['label' => 'Está em tratamento?'],

        'hemofilico' => ['label' => 'O aluno é hemofílico?'],

        'hipertenso' => ['label' => 'O aluno tem hipertensão?'],

        'asmatico' => ['label' => 'O aluno é asmático?'],

        'diabetico' => ['label' => 'O aluno é diabético?'],

        'insulina' => ['label' => 'Depende de insulina?'],

        'tratamento_medico' => ['label' => 'O aluno faz algum tratamento médico?'],

        'desc_tratamento_medico' => ['label' => 'Qual?'],

        'medicacao_especifica' => ['label' => 'O aluno está ingerindo medicação específica?'],

        'desc_medicacao_especifica' => ['label' => 'Qual?'],

        'acomp_medico_psicologico' => ['label' => 'O aluno tem acompanhamento médico ou psicológico?'],

        'desc_acomp_medico_psicologico' => ['label' => 'Motivo?'],

        'restricao_atividade_fisica' => ['label' => 'O aluno tem restrição a alguma atividade física?'],

        'desc_restricao_atividade_fisica' => ['label' => 'Qual?'],

        'fratura_trauma' => ['label' => 'O aluno sofreu alguma fratura ou trauma?'],

        'desc_fratura_trauma' => ['label' => 'Qual?'],

        'plano_saude' => ['label' => 'O aluno possui algum plano de saúde?'],

        'desc_plano_saude' => ['label' => 'Qual?'],

        'vacina_covid' => ['label' => 'Aluno Vacinado Covid-19?'],

        'desc_vacina_covid' => ['label' => 'Quantas?'],

        'aceita_hospital_proximo' => ['label' => '<b>Em caso de emergência, autorizo levar meu(minha) filho(a) para o Hospital ou Clínica mais próximos:</b>'],

        'desc_aceita_hospital_proximo' => ['label' => 'Responsável'],

        'responsavel' => ['label' => 'Nome'],

        'responsavel_parentesco' => ['label' => 'Parentesco'],

        'responsavel_parentesco_telefone' => ['label' => 'Telefone'],

        'responsavel_parentesco_celular' => ['label' => 'Celular'],

        'moradia' => ['label' => 'Moradia'],

        'material' => ['label' => 'Material'],

        'casa_outra' => ['label' => 'Outro'],

        'moradia_situacao' => ['label' => 'Situação'],

        'quartos' => ['label' => 'Número de quartos'],

        'sala' => ['label' => 'Número de salas'],

        'copa' => ['label' => 'Número de copas'],

        'banheiro' => ['label' => 'Número de banheiros'],

        'garagem' => ['label' => 'Número de garagens'],

        'empregada_domestica' => ['label' => 'Possui empregada doméstica?'],

        'automovel' => ['label' => 'Possui automóvel?'],

        'motocicleta' => ['label' => 'Possui motocicleta?'],

        'geladeira' => ['label' => 'Possui geladeira?'],

        'fogao' => ['label' => 'Possui fogão?'],

        'maquina_lavar' => ['label' => 'Possui máquina de lavar?'],

        'microondas' => ['label' => 'Possui microondas?'],

        'video_dvd' => ['label' => 'Possui vídeo/DVD?'],

        'televisao' => ['label' => 'Possui televisão?'],

        'telefone' => ['label' => 'Possui telefone?'],

        'recursos_tecnologicos' => ['label' => 'Possui acesso à recursos tecnológicos?'],

        'quant_pessoas' => ['label' => 'Quantidades de pessoas residentes no lar'],

        'renda' => ['label' => 'Renda familiar em R$'],

        'agua_encanada' => ['label' => 'Possui água encanada?'],

        'poco' => ['label' => 'Possui poço?'],

        'energia' => ['label' => 'Possui energia?'],

        'esgoto' => ['label' => 'Possui esgoto?'],

        'fossa' => ['label' => 'Possui fossa?'],

        'lixo' => ['label' => 'Possui lixo?'],

        'recursos_prova_inep' => ['label' => 'Recursos necessários para realização de provas'],

        'recebe_escolarizacao_em_outro_espaco' => ['label' => 'Recebe escolarização em outro espaço (diferente da escola)'],

        'transporte_rota' => [
            'label' => 'Rota',
            'help' => '',
        ],

        'transporte_ponto' => [
            'label' => 'Ponto de embarque',
            'help' => '',
        ],

        'transporte_destino' => [
            'label' => 'Destino (Caso for diferente da rota)',
            'help' => '',
        ],

        'transporte_observacao' => [
            'label' => 'Observações do transporte',
            'help' => '',
        ],

        'observacao_aluno' => [
            'label' => 'Observações do aluno',
            'help' => '',
        ]
    ];

    protected function _preConstruct()
    {
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';

        $this->breadcrumb("{$nomeMenu} aluno", [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    protected function _initNovo()
    {
        return false;
    }

    protected function _initEditar()
    {
        return false;
    }

    public function Gerar()
    {
        $this->url_cancelar = '/intranet/educar_aluno_lst.php';

        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $configuracoes = $configuracoes->detalhe();

        $labels_botucatu = config('legacy.app.mostrar_aplicacao') == 'botucatu';

        if ($configuracoes['justificativa_falta_documentacao_obrigatorio']) {
            $this->inputsHelper()->hidden('justificativa_falta_documentacao_obrigatorio');
        }

        $cod_aluno = $_GET['id'];

        if ($cod_aluno or $_GET['person']) {
            if ($_GET['person']) {
                $this->cod_pessoa_fj = $_GET['person'];
                $this->inputsHelper()->hidden('person', ['value' => $this->cod_pessoa_fj]);
            } else {
                $db = new clsBanco();
                $this->cod_pessoa_fj = $db->CampoUnico("select ref_idpes from pmieducar.aluno where cod_aluno = '$cod_aluno'");
            }

            $documentos = new clsDocumento();
            $documentos->idpes = $this->cod_pessoa_fj;
            $documentos = $documentos->detalhe();
        }

        $foto = false;

        if (is_numeric($this->cod_pessoa_fj)) {
            $personObject = new clsFisica($this->cod_pessoa_fj);
            $this->observacao = (empty($personObject->detalhe()['observacao']) == false) ? $personObject->detalhe()['observacao'] : '';
            $objFoto = new clsCadastroFisicaFoto($this->cod_pessoa_fj);
            $detalheFoto = $objFoto->detalhe();
            if (count($detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $this->observacao = '';
            $foto = false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' . (new UrlPresigner())->getPresignedUrl($foto)  . '"/>');
            $this->inputsHelper()->checkbox('file_delete', ['label' => 'Excluir a foto']);
            $this->campoArquivo('file', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <h5 class="i">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 2MB</h5>');
        } else {
            $this->campoArquivo('file', 'Foto', $this->arquivoFoto, 40, '<br/> <h5 class="i">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 2MB</h5>');
        }


        $options = ['label' => _cl('aluno.detalhe.codigo_aluno'), 'disabled' => true, 'required' => false, 'size' => 25];
        $this->inputsHelper()->integer('id', $options);


        $options = ['label' => $this->_getLabel('aluno_inep_id'), 'required' => false, 'size' => 25, 'max_length' => 12];

        if (!$configuracoes['mostrar_codigo_inep_aluno']) {
            $this->inputsHelper()->hidden('aluno_inep_id', ['value' => null]);
        } else {
            $this->inputsHelper()->integer('aluno_inep_id', $options);
        }


        $this->campoRA(
            'aluno_estado_id',
            'Código rede estadual do aluno (RA)',
            $this->aluno_estado_id,
            false
        );


        if (config('legacy.app.alunos.mostrar_codigo_sistema')) {
            $options = [
                'label' => config('legacy.app.alunos.codigo_sistema'),
                'required' => false,
                'size' => 25,
                'max_length' => 30
            ];
            $this->inputsHelper()->text('codigo_sistema', $options);
        }


        $options = ['label' => $this->_getLabel('pessoa'), 'size' => 68];
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);


        $options = ['label' => 'Data de nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => ''];
        $this->inputsHelper()->date('data_nascimento', $options);

        $options = [
            'required' => $required,
            'label' => 'RG / Data emissão',
            'placeholder' => 'Documento identidade',
            'value' => $documentos['rg'],
            'max_length' => 25,
            'size' => 27,
            'inline' => true
        ];

        $this->inputsHelper()->text('rg', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emiss\u00e3o',
            'value' => $documentos['data_exp_rg'],
            'size' => 19
        ];

        $this->inputsHelper()->date('data_emissao_rg', $options);

        $selectOptions = [null => 'Órgão emissor'];
        $orgaos        = new clsOrgaoEmissorRg();
        $orgaos        = $orgaos->lista();

        foreach ($orgaos as $orgao) {
            $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];
        }

        $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

        $options = [
            'required'  => false,
            'label'     => '',
            'value'     => $documentos['idorg_exp_rg'],
            'resources' => $selectOptions,
            'inline'    => true
        ];

        $this->inputsHelper()->select('orgao_emissao_rg', $options);

        $options = [
            'required' => false,
            'label'    => '',
            'value'    => $documentos['sigla_uf_exp_rg']
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_rg'
        ];

        $this->inputsHelper()->uf($options, $helperOptions);

        $nisPisPasep = '';

        if (is_numeric($this->cod_pessoa_fj)) {
            $fisica = new clsFisica($this->cod_pessoa_fj);
            $fisica = $fisica->detalhe();
            $valorCpf = is_numeric($fisica['cpf']) ? int2CPF($fisica['cpf']) : '';
            $nisPisPasep = int2Nis($fisica['nis_pis_pasep']);
        }

        $this->campoCpf('id_federal', 'CPF', $valorCpf);

       

        $escolha_certidao = 'Tipo certidão civil';
        $selectOptions = [
            null => $escolha_certidao,
            'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
            91 => 'Nascimento (antigo formato)',
            'certidao_casamento_novo_formato' => 'Casamento (novo formato)',
            92 => 'Casamento (antigo formato)'
        ];

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
            'inline' => true
        ];

        $this->inputsHelper()->select('tipo_certidao_civil', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Termo',
            'value' => $documentos['num_termo'],
            'max_length' => 8,
            'inline' => true
        ];

        $this->inputsHelper()->integer('termo_certidao_civil', $options);

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


        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Folha',
            'value' => $documentos['num_folha'],
            'max_length' => 4,
            'inline' => true
        ];

        $this->inputsHelper()->integer('folha_certidao_civil', $options);

        $placeholderCertidao = 'Certidão nascimento';
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_nascimento'],
            'max_length' => 32,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->integer('certidao_nascimento', $options);

        $placeholderCertidao = 'Certidão casamento';
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_casamento'],
            'max_length' => 32,
            'size' => 50,
        ];

        $this->inputsHelper()->integer('certidao_casamento', $options);

        $options = [
            'required' => false,
            'label' => 'Estado emissão / Data emissão',
            'value' => $documentos['sigla_uf_cert_civil'],
            'inline' => true
        ];

        $helperOptions = [
            'attrName' => 'uf_emissao_certidao_civil'
        ];

        $this->inputsHelper()->uf($options, $helperOptions);

        $placeholderEmissao = 'Data emissão';
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderEmissao,
            'value' => $documentos['data_emissao_cert_civil'],
            'inline' => true
        ];

        $this->inputsHelper()->date('data_emissao_certidao_civil', $options);

        $options = [
            'label' => '',
            'required' => false
        ];

        $labelCartorio = 'Cartório emissão';
        $options = [
            'required' => false,
            'label' => $labelCartorio,
            'value' => $documentos['cartorio_cert_civil'],
            'cols' => 45,
            'max_length' => 200,
        ];

        $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);

        $resources = [
            null => 'Selecione',
            1 => 'O(a) aluno(a) não possui os documentos pessoais solicitados',
            2 => 'A escola não dispõe ou não recebeu os documentos pessoais do(a) aluno(a)'
        ];

        $options = [
            'label' => $this->_getLabel('justificativa_falta_documentacao'),
            'resources' => $resources,
            'required' => false,
            'label_hint' => 'Pelo menos um dos documentos: CPF, NIS, Certidão de Nascimento (novo formato) deve ser informado para não precisar justificar a ausência de documentação',
            'disabled' => true
        ];

        $this->inputsHelper()->select('justificativa_falta_documentacao', $options);

        $labelPassaporte = 'Passaporte';
        $options = [
            'required' => false,
            'label' => $labelPassaporte,
            'value' => $documentos['passaporte'],
            'cols' => 45,
            'max_length' => 20
        ];

        $this->inputsHelper()->text('passaporte', $options);

        $options = [
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',

            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->text('autorizado_um', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        ];

        $this->inputsHelper()->text('parentesco_um', $options);

        $options = [
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->text('autorizado_dois', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        ];

        $this->inputsHelper()->text('parentesco_dois', $options);

        $options = [
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->text('autorizado_tres', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        ];

        $this->inputsHelper()->text('parentesco_tres', $options);

        $options = [
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->text('autorizado_quatro', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        ];

        $this->inputsHelper()->text('parentesco_quatro', $options);

        $options = [
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',

            'max_length' => 150,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->text('autorizado_cinco', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        ];

        $this->inputsHelper()->text('parentesco_cinco', $options);

        $this->inputPai();

        $this->inputMae();


        $label = $this->_getLabel('responsavel');


        $tiposResponsavel = [null => 'Informe uma Pessoa primeiro'];
        $options = [
            'label' => 'Responsável',
            'resources' => $tiposResponsavel,
            'required' => true,
            'inline' => true
        ];

        $this->inputsHelper()->select('tipo_responsavel', $options);

        $helperOptions = ['objectName' => 'responsavel'];
        $options = ['label' => '', 'size' => 50, 'required' => true];

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);


        $tiposTransporte = [
            null => 'Selecione',
            'nenhum' => 'N&atilde;o utiliza',
            'municipal' => 'Municipal',
            'estadual' => 'Estadual'
        ];

        $options = [
            'label' => $this->_getLabel('transporte'),
            'resources' => $tiposTransporte,
            'required' => true
        ];

        $this->inputsHelper()->select('tipo_transporte', $options);

        $veiculos = VeiculoTransporteEscolar::getDescriptiveValues();
        $helperOptions = ['objectName' => 'veiculo_transporte_escolar'];
        $options = [
            'label' => 'Veículo utilizado',
            'required' => true,
            'options' => [
                'all_values' => $veiculos
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderBy(' descricao asc ');
        $lista_rota = $obj_rota->lista();
        $rota_resources = ['' => 'Selecione uma rota'];
        foreach ($lista_rota as $reg) {
            $rota_resources["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']}";
        }

        $options = ['label' => $this->_getLabel('transporte_rota'), 'required' => false, 'resources' => $rota_resources];
        $this->inputsHelper()->select('transporte_rota', $options);


        $options = ['label' => $this->_getLabel('transporte_ponto'), 'required' => false, 'resources' => ['' => 'Selecione uma rota acima']];
        $this->inputsHelper()->select('transporte_ponto', $options);


        $options = ['label' => $this->_getLabel('transporte_destino'), 'required' => false];
        $this->inputsHelper()->simpleSearchPessoaj('transporte_destino', $options);

        $options = ['label' => $this->_getLabel('transporte_observacao'), 'required' => false, 'size' => 50, 'max_length' => 255];
        $this->inputsHelper()->textArea('transporte_observacao', $options);

        $this->inputsHelper()->religiao(['required' => false, 'label' => 'Religião']);

        $helperOptions = ['objectName' => 'beneficios'];
        $options = [
            'label' => 'Benefícios',
            'size' => 250,
            'required' => false,
            'options' => ['value' => null]
        ];

        $this->inputsHelper()->multipleSearchBeneficios('', $options, $helperOptions);
        $options = [
            'required' => false,
            'label' => 'NIS (PIS/PASEP)',
            'placeholder' => '',
            'value' => $nisPisPasep,
            'max_length' => 11,
            'size' => 20
        ];

        $this->inputsHelper()->integer('nis_pis_pasep', $options);

        $helperOptions = ['objectName' => 'deficiencias'];
        $options = [
            'label' => $this->_getLabel('deficiencias'),
            'size' => 50,
            'required' => false,
            'options' => ['value' => null]
        ];

        $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);

        $options = ['label' => $this->_getLabel('alfabetizado'), 'value' => 'checked'];
        $this->inputsHelper()->checkbox('alfabetizado', $options);

        if (config('legacy.app.alunos.nao_apresentar_campo_alfabetizado')) {
            $this->inputsHelper()->hidden('alfabetizado');
        }

        $options = ['label' => $this->_getLabel('emancipado')];
        $this->inputsHelper()->checkbox('emancipado', $options);

        $this->campoArquivo('documento', $this->_getLabel('documento'), $this->documento, 40, '<br/> <span id=\'span-documento\' style=\'font-style: italic; font-size= 10px;\'\'> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 2MB</span>');

        $this->inputsHelper()->hidden('url_documento');

        $this->campoArquivo('laudo_medico', $this->_getLabel('laudo_medico'), $this->laudo_medico, 40, '<br/> <span id=\'span-laudo_medico\' style=\'font-style: italic; font-size= 10px;\'\'> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 2MB</span>');

        $options = ['label' => $this->_getLabel('observacao_aluno'), 'required' => false, 'size' => 50, 'max_length' => 255, 'value' => $this->observacao];
        $this->inputsHelper()->textArea('observacao_aluno', $options);

        $this->inputsHelper()->hidden('url_laudo_medico');

        $laudo = config('legacy.app.alunos.laudo_medico_obrigatorio');

        if ($laudo == 1) {
            $this->inputsHelper()->hidden('url_laudo_medico_obrigatorio');
        }

        $this->campoTabelaInicio('historico_altura_peso', 'Histórico de altura e peso', ['Data', 'Altura (m)', 'Peso (kg)']);

        $this->inputsHelper()->date('data_historico');

        $this->inputsHelper()->numeric('historico_altura');

        $this->inputsHelper()->numeric('historico_peso');

        $this->campoTabelaFim();


        $options = ['label' => $this->_getLabel('altura'), 'size' => 5, 'max_length' => 4, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('altura', $options);


        $options = ['label' => $this->_getLabel('peso'), 'size' => 5, 'max_length' => 6, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('peso', $options);


        $options = ['label' => $this->_getLabel('grupo_sanguineo'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('grupo_sanguineo', $options);

        $options = ['label' => $this->_getLabel('fator_rh'), 'size' => 5, 'max_length' => 1, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('fator_rh', $options);

        $options = ['label' => $this->_getLabel('sus'), 'size' => 20, 'max_length' => 20, 'required' => config('legacy.app.fisica.exigir_cartao_sus'), 'placeholder' => ''];
        $this->inputsHelper()->text('sus', $options);

        $options = ['label' => $this->_getLabel('alergia_medicamento'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('alergia_medicamento', $options);

        $options = ['label' => $this->_getLabel('desc_alergia_medicamento'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_alergia_medicamento', $options);

        $options = ['label' => $this->_getLabel('alergia_alimento'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('alergia_alimento', $options);

        $options = ['label' => $this->_getLabel('desc_alergia_alimento'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_alergia_alimento', $options);

        $options = ['label' => $this->_getLabel('doenca_congenita'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_congenita', $options);

        $options = ['label' => $this->_getLabel('desc_doenca_congenita'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_doenca_congenita', $options);

        $options = ['label' => $this->_getLabel('fumante'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fumante', $options);

        $options = ['label' => $this->_getLabel('doenca_caxumba'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_caxumba', $options);

        $options = ['label' => $this->_getLabel('doenca_sarampo'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_sarampo', $options);

        $options = ['label' => $this->_getLabel('doenca_rubeola'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_rubeola', $options);

        $options = ['label' => $this->_getLabel('doenca_catapora'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_catapora', $options);

        $options = ['label' => $this->_getLabel('doenca_escarlatina'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_escarlatina', $options);

        $options = ['label' => $this->_getLabel('doenca_coqueluche'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_coqueluche', $options);

        $options = ['label' => $this->_getLabel('doenca_outras'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('doenca_outras', $options);

        $options = ['label' => $this->_getLabel('epiletico'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('epiletico', $options);

        $options = ['label' => $this->_getLabel('epiletico_tratamento'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('epiletico_tratamento', $options);

        $options = ['label' => $this->_getLabel('hemofilico'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('hemofilico', $options);

        $options = ['label' => $this->_getLabel('hipertenso'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('hipertenso', $options);

        $options = ['label' => $this->_getLabel('asmatico'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('asmatico', $options);

        $options = ['label' => $this->_getLabel('diabetico'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('diabetico', $options);

        $options = ['label' => $this->_getLabel('insulina'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('insulina', $options);

        $options = ['label' => $this->_getLabel('tratamento_medico'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('tratamento_medico', $options);

        $options = ['label' => $this->_getLabel('desc_tratamento_medico'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_tratamento_medico', $options);

        $options = ['label' => $this->_getLabel('medicacao_especifica'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('medicacao_especifica', $options);

        $options = ['label' => $this->_getLabel('desc_medicacao_especifica'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_medicacao_especifica', $options);

        $options = ['label' => $this->_getLabel('acomp_medico_psicologico'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('acomp_medico_psicologico', $options);

        $options = ['label' => $this->_getLabel('desc_acomp_medico_psicologico'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_acomp_medico_psicologico', $options);

        $options = ['label' => $this->_getLabel('restricao_atividade_fisica'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('restricao_atividade_fisica', $options);

        $options = ['label' => $this->_getLabel('desc_restricao_atividade_fisica'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_restricao_atividade_fisica', $options);

        $options = ['label' => $this->_getLabel('fratura_trauma'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fratura_trauma', $options);

        $options = ['label' => $this->_getLabel('desc_fratura_trauma'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_fratura_trauma', $options);

        $options = ['label' => $this->_getLabel('plano_saude'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('plano_saude', $options);

        $options = ['label' => $this->_getLabel('desc_plano_saude'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_plano_saude', $options);

        $options = ['label' => $this->_getLabel('vacina_covid'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('vacina_covid', $options);

        $options = ['label' => $this->_getLabel('desc_vacina_covid'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('desc_vacina_covid', $options);

        $options = ['label' => $this->_getLabel('aceita_hospital_proximo'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('aceita_hospital_proximo', $options);

        $options = ['label' => $this->_getLabel('desc_aceita_hospital_proximo'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_aceita_hospital_proximo', $options);

        $this->campoRotulo('tit_dados_responsavel', 'Em caso de emergência, caso não seja encontrado pais ou responsáveis, avisar');

        $options = ['label' => $this->_getLabel('responsavel'), 'size' => 50, 'max_length' => 50, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel', $options);

        $options = ['label' => $this->_getLabel('responsavel_parentesco'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel_parentesco', $options);

        $options = ['label' => $this->_getLabel('responsavel_parentesco_telefone'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel_parentesco_telefone', $options);

        $options = ['label' => $this->_getLabel('responsavel_parentesco_celular'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel_parentesco_celular', $options);

        $moradias = [
            null => 'Selecione',
            'A' => 'Apartamento',
            'C' => 'Casa',
            'O' => 'Outro'
        ];

        $options = [
            'label' => $this->_getLabel('moradia'),
            'resources' => $moradias,
            'required' => false,
            'inline' => true
        ];

        $this->inputsHelper()->select('moradia', $options);

        $materiais_moradia = [
            'A' => 'Alvenaria',
            'M' => 'Madeira',
            'I' => 'Mista'
        ];

        $options = [
            'label' => null,
            'resources' => $materiais_moradia,
            'required' => false,
            'inline' => true
        ];

        $this->inputsHelper()->select('material', $options);

        $options = ['label' => null, 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => 'Descreva'];
        $this->inputsHelper()->text('casa_outra', $options);

        $situacoes = [
            null => 'Selecione',
            '1' => 'Alugado',
            '2' => 'Próprio',
            '3' => 'Cedido',
            '4' => 'Financiado',
            '5' => 'Outros'
        ];

        $options = [
            'label' => $this->_getLabel('moradia_situacao'),
            'resources' => $situacoes,
            'required' => false
        ];

        $this->inputsHelper()->select('moradia_situacao', $options);

        $options = ['label' => $this->_getLabel('quartos'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('quartos', $options);

        $options = ['label' => $this->_getLabel('sala'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('sala', $options);

        $options = ['label' => $this->_getLabel('copa'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('copa', $options);

        $options = ['label' => $this->_getLabel('banheiro'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('banheiro', $options);

        $options = ['label' => $this->_getLabel('garagem'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('garagem', $options);

        $options = ['label' => $this->_getLabel('empregada_domestica'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('empregada_domestica', $options);

        $options = ['label' => $this->_getLabel('automovel'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('automovel', $options);

        $options = ['label' => $this->_getLabel('motocicleta'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('motocicleta', $options);

        $options = ['label' => $this->_getLabel('geladeira'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('geladeira', $options);

        $options = ['label' => $this->_getLabel('fogao'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fogao', $options);

        $options = ['label' => $this->_getLabel('maquina_lavar'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('maquina_lavar', $options);

        $options = ['label' => $this->_getLabel('microondas'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('microondas', $options);

        $options = ['label' => $this->_getLabel('video_dvd'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('video_dvd', $options);

        $options = ['label' => $this->_getLabel('televisao'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('televisao', $options);

        $options = ['label' => $this->_getLabel('telefone'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('telefone', $options);

        $obrigarRecursosTecnologicos = (bool)config('legacy.app.alunos.obrigar_recursos_tecnologicos');
        $this->CampoOculto('obrigar_recursos_tecnologicos', (int) $obrigarRecursosTecnologicos);

        $helperOptions = ['objectName'  => 'recursos_tecnologicos'];
        $recursosTecnologicos = [
            'Internet' => 'Acesso à internet (em casa)',
            'Computador' => 'Computador',
            'Smartphone' => 'Smartphone (celular)',
            'WhatsApp' => 'WhatsApp',
            'Nenhum' => 'Nenhum',
        ];

        $options = [
            'label' => $this->_getLabel('recursos_tecnologicos'),
            'size' => 50,
            'required' => $obrigarRecursosTecnologicos,
            'options' => [
                'values' => $this->recursos_tecnologicos,
                'all_values' => $recursosTecnologicos,
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('_', $options, $helperOptions);

        $options = ['label' => $this->_getLabel('quant_pessoas'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('quant_pessoas', $options);

        $options = ['label' => $this->_getLabel('renda'), 'size' => 5, 'max_length' => 10, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('renda', $options);

        $options = ['label' => $this->_getLabel('agua_encanada'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('agua_encanada', $options);

        $options = ['label' => $this->_getLabel('poco'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('poco', $options);

        $options = ['label' => $this->_getLabel('energia'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('energia', $options);

        $options = ['label' => $this->_getLabel('esgoto'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('esgoto', $options);

        $options = ['label' => $this->_getLabel('fossa'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fossa', $options);

        $options = ['label' => $this->_getLabel('lixo'), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('lixo', $options);

        $recursosProvaInep = RecursosRealizacaoProvas::getDescriptiveValues();
        $helperOptions = ['objectName'  => 'recursos_prova_inep'];
        $options = [
            'label' => $this->_getLabel('recursos_prova_inep'),
            'label_hint' => '<a href="#" class="open-dialog-recursos-prova-inep">Regras do preenchimento dos recursos necessários para realização de provas</a>',
            'size' => 50,
            'required' => false,
            'options' => [
                'values' => $this->recursos_prova_inep,
                'all_values' => $recursosProvaInep
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('_', $options, $helperOptions);

        $selectOptions = [
            1 => 'Não recebe escolarização fora da escola',
            2 => 'Em hospital',
            3 => 'Em domicílio',
        ];

        $options = [
            'required' => false,
            'label' => $this->_getLabel('recebe_escolarizacao_em_outro_espaco'),
            'resources' => $selectOptions
        ];

        $this->inputsHelper()->select('recebe_escolarizacao_em_outro_espaco', $options);


        $this->campoTabelaInicio('projetos', 'Projetos', ['Projeto', 'Data inclusão', 'Data desligamento', 'Turno']);

        $this->inputsHelper()->text('projeto_cod_projeto', ['required' => false]);

        $this->inputsHelper()->date('projeto_data_inclusao', ['required' => false]);

        $this->inputsHelper()->date('projeto_data_desligamento', ['required' => false]);

        $this->inputsHelper()->select('projeto_turno', ['required' => false, 'resources' => ['' => 'Selecione', 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral']]);

        $this->campoTabelaFim();

        $this->inputsHelper()->simpleSearchMunicipio('pessoa-aluno', ['required' => false, 'size' => 57], ['objectName' => 'naturalidade_aluno']);

        $enderecamentoObrigatorio = false;
        $desativarCamposDefinidosViaCep = true;

        $this->viewAddress();

        $zonas = App_Model_ZonaLocalizacao::getInstance();
        $zonas = $zonas->getEnums();
        $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

        $options = [
            'label' => '',
            'placeholder' => 'Zona localização',
            'value' => $this->zona_localizacao,
            'disabled' => $desativarCamposDefinidosViaCep,
            'resources' => $zonas,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->select('zona_localizacao', $options);

        $options = [
            'label' => 'País de residência',
            'value' => $this->pais_residencia ?: PaisResidencia::BRASIL,
            'resources' => PaisResidencia::getDescriptiveValues(),
            'required' => true,
        ];

        $this->inputsHelper()->select('pais_residencia', $options);

        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Cadastro/Assets/Javascripts/Endereco.js',
            '/modules/Cadastro/Assets/Javascripts/Addresses.js',
        ]);

        $this->loadResourceAssets($this->getDispatcher());

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigarCamposCenso = false;
        $obrigarDocumentoPessoa = false;
        $obrigarTelefonePessoa = false;

        if ($instituicao && isset($instituicao['obrigar_campos_censo'])) {
            $obrigarCamposCenso = dbBool($instituicao['obrigar_campos_censo']);
        }
        if ($instituicao && isset($instituicao['obrigar_documento_pessoa'])) {
            $obrigarDocumentoPessoa = dbBool($instituicao['obrigar_documento_pessoa']);
        }
        if ($instituicao && isset($instituicao['obrigar_telefone_pessoa'])) {
            $obrigarTelefonePessoa = dbBool($instituicao['obrigar_telefone_pessoa']);
        }
        $this->CampoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);
        $this->CampoOculto('obrigar_documento_pessoa', (int) $obrigarDocumentoPessoa);
        $this->CampoOculto('obrigar_telefone_pessoa', (int) $obrigarTelefonePessoa);

        $racas         = new clsCadastroRaca();
        $racas         = $racas->lista(null, null, null, null, null, null, null, true);

        foreach ($racas as $raca) {
            $selectOptions[$raca['cod_raca']] = $raca['nm_raca'];
        }

        $selectOptions = [null => 'Selecione'] + Portabilis_Array_Utils::sortByValue($selectOptions);

        $this->campoLista('cor_raca', 'Raça', $selectOptions, $this->cod_raca, '', false, '', '', '', $obrigarCamposCenso);

        $zonas = [
            '' => 'Selecione',
            1  => 'Urbana',
            2  => 'Rural',
        ];

        $options = [
            'label'       => 'Zona Localização',
            'value'       => $this->zona_localizacao_censo,
            'resources'   => $zonas,
            'required'    => $obrigarCamposCenso,
        ];

        $this->inputsHelper()->select('zona_localizacao_censo', $options);

        $options = [
            'label' => 'Localização diferenciada',
            'resources' => SelectOptions::localizacoesDiferenciadasPessoa(),
            'required' => false,
        ];

        $this->inputsHelper()->select('localizacao_diferenciada', $options);

        $tiposNacionalidade = [
            '1'  => 'Brasileiro',
            '2'  => 'Naturalizado brasileiro',
            '3'  => 'Estrangeiro'
        ];

        $options = [
            'label'       => 'Nacionalidade',
            'resources'   => $tiposNacionalidade,
            'required'    => $obrigarCamposCenso,
            'inline'      => true,
            'value'       => $this->tipo_nacionalidade
        ];

        $this->inputsHelper()->select('tipo_nacionalidade', $options);

        $options = [
            'label'       => '',
            'placeholder' => 'Informe o nome do pais',
            'required'    => $obrigarCamposCenso
        ];

        $hiddenInputOptions = [
            'options' => [
                'value' => $this->pais_origem_id
            ]
        ];

        $helperOptions = [
            'objectName'         => 'pais_origem',
            'hiddenInputOptions' => $hiddenInputOptions
        ];
        $this->inputsHelper()->simpleSearchPaisSemBrasil('nome', $options, $helperOptions);
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
        if (!$parentTypeLabel) {
            $parentTypeLabel = $parentType;
        }

        $parentId = $this->{$parentType . '_id'};

        $hiddenInputOptions = ['options' => ['value' => $parentId]];
        $helperOptions = ['objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions];

        $options = [
            'label' => "Pessoa {$parentTypeLabel}",
            'size' => 69,
            'required' => false
        ];

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
    }
}
