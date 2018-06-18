<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   Avaliacao
 * @subpackage  Modules
 *
 * @since     Arquivo disponível desde a versão ?
 *
 * @version   $Id$
 */

require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'image_check.php';
require_once 'App/Model/ZonaLocalizacao.php';
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'lib/Portabilis/Utils/CustomLabel.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'Portabilis/String/Utils.php';

class AlunoController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Cadastro de aluno';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    protected $_processoAp = 578;

    protected $_deleteOption = true;

    protected $cod_aluno;

    // Variáveis para controle da foto
    public $objPhoto;

    public $arquivoFoto;

    public $file_delete;

    public $caminho_det;

    public $caminho_lst;

    protected $_formMap = [
        'pessoa' => [
            'label' => 'Pessoa',
            'help' => '',
        ],

        // 'rg' => array(
        //   'label'  => 'Documento de identidade (RG)',
        //   'help'   => '',
        // ),

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

        'responsavel' => [
            'label' => 'Responsável',
            'help' => '',
        ],

        'alfabetizado' => [
            'label' => 'Alfabetizado',
            'help' => '',
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

        /* *******************
           ** Dados médicos **
           ******************* */
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

        'hospital_clinica' => ['label' => 'Nome'],

        'hospital_clinica_endereco' => ['label' => 'Endereço'],

        'hospital_clinica_telefone' => ['label' => 'Telefone'],

        'responsavel' => ['label' => 'Nome'],

        'responsavel_parentesco' => ['label' => 'Parentesco'],

        'responsavel_parentesco_telefone' => ['label' => 'Telefone'],

        'responsavel_parentesco_celular' => ['label' => 'Celular'],

        /************
         * MORADIA
         ************/

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

        'computador' => ['label' => 'Possui computador?'],

        'geladeira' => ['label' => 'Possui geladeira?'],

        'fogao' => ['label' => 'Possui fogão?'],

        'maquina_lavar' => ['label' => 'Possui máquina de lavar?'],

        'microondas' => ['label' => 'Possui microondas?'],

        'video_dvd' => ['label' => 'Possui vídeo/DVD?'],

        'televisao' => ['label' => 'Possui televisão?'],

        'celular' => ['label' => 'Possui celular?'],

        'telefone' => ['label' => 'Possui telefone?'],

        'quant_pessoas' => ['label' => 'Quantidades de pessoas residentes no lar'],

        'renda' => ['label' => 'Renda familiar em R$'],

        'agua_encanada' => ['label' => 'Possui água encanada?'],

        'poco' => ['label' => 'Possui poço?'],

        'energia' => ['label' => 'Possui energia?'],

        'esgoto' => ['label' => 'Possui esgoto?'],

        'fossa' => ['label' => 'Possui fossa?'],

        'lixo' => ['label' => 'Possui lixo?'],

        /************
         * PROVA INEP
         ************/
        'recursos_prova_inep' => ['label' => 'Recursos prova INEP'],

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
            'label' => 'Observações',
            'help' => '',
        ]
    ];

    protected function _preConstruct()
    {
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => "$nomeMenu aluno"
        ]);

        $this->enviaLocalizacao($localizacao->montar());
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

        $labels_botucatu = $GLOBALS['coreExt']['Config']->app->mostrar_aplicacao == 'botucatu';

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
            $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
            $detalheFoto = $objFoto->detalhe();
            if (count($detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $foto = false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' . $foto . '"/>');
            $this->inputsHelper()->checkbox('file_delete', ['label' => 'Excluir a foto']);
            $this->campoArquivo('file', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 150KB</span>');
        } else {
            $this->campoArquivo('file', 'Foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 150KB</span>');
        }

        // código aluno
        $options = ['label' => _cl('aluno.detalhe.codigo_aluno'), 'disabled' => true, 'required' => false, 'size' => 25];
        $this->inputsHelper()->integer('id', $options);

        // código aluno inep
        $options = ['label' => $this->_getLabel('aluno_inep_id'), 'required' => false, 'size' => 25, 'max_length' => 12];

        if (!$configuracoes['mostrar_codigo_inep_aluno']) {
            $this->inputsHelper()->hidden('aluno_inep_id', ['value' => null]);
        } else {
            $this->inputsHelper()->integer('aluno_inep_id', $options);
        }

        // código aluno rede estadual
        $this->campoRA(
            'aluno_estado_id',
            Portabilis_String_Utils::toLatin1('Código rede estadual do aluno (RA)'),
            $this->aluno_estado_id,
            false
        );

        // código aluno sistema
        if ($GLOBALS['coreExt']['Config']->app->alunos->mostrar_codigo_sistema) {
            $options = [
                'label' => Portabilis_String_Utils::toLatin1($GLOBALS['coreExt']['Config']->app->alunos->codigo_sistema),
                'required' => false,
                'size' => 25,
                'max_length' => 30
            ];
            $this->inputsHelper()->text('codigo_sistema', $options);
        }

        // nome
        $options = ['label' => $this->_getLabel('pessoa'), 'size' => 68];
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);

        // data nascimento
        $options = ['label' => 'Data de nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => ''];
        $this->inputsHelper()->date('data_nascimento', $options);

        // rg
        // $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
        // $this->inputsHelper()->integer('rg', $options);

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

        // data emissão rg
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emiss\u00e3o',
            'value' => $documentos['data_exp_rg'],
            'size' => 19
        ];

        $this->inputsHelper()->date('data_emissao_rg', $options);

        $selectOptions = [ null => 'Órgão emissor' ];
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

        // uf emissão rg

        $options = [
          'required' => false,
          'label'    => '',
          'value'    => $documentos['sigla_uf_exp_rg']
        ];

        $helperOptions = [
          'attrName' => 'uf_emissao_rg'
        ];

        $this->inputsHelper()->uf($options, $helperOptions);

        // cpf
        if (is_numeric($this->cod_pessoa_fj)) {
            $fisica = new clsFisica($this->cod_pessoa_fj);
            $fisica = $fisica->detalhe();
            $valorCpf = is_numeric($fisica['cpf']) ? int2CPF($fisica['cpf']) : '';
        }
        $this->campoCpf('id_federal', 'CPF', $valorCpf);

        // justificativa_falta_documentacao
        $resources = [null => 'Selecione',
            1 => Portabilis_String_Utils::toLatin1('Aluno não possui documentação'),
            2 => Portabilis_String_Utils::toLatin1('Escola não possui informação')];

        $options = ['label' => $this->_getLabel('justificativa_falta_documentacao'),
            'resources' => $resources,
            'required' => false,
            'disabled' => true];

        $this->inputsHelper()->select('justificativa_falta_documentacao', $options);

        // tipo de certidao civil
        $escolha_certidao = Portabilis_String_Utils::toLatin1('Tipo certidão civil');
        $selectOptions = [
            null => $escolha_certidao,
            'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
            91 => 'Nascimento (antigo formato)',
            'certidao_casamento_novo_formato' => 'Casamento (novo formato)',
            92 => 'Casamento (antigo formato)'
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
        $placeholderCertidao = Portabilis_String_Utils::toLatin1('Certidão nascimento');
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_nascimento'],
            'max_length' => 32,
            'size' => 50,
            'inline' => true
        ];

        $this->inputsHelper()->text('certidao_nascimento', $options);

        // certidao casamento (novo padrão)
        $placeholderCertidao = Portabilis_String_Utils::toLatin1('Certidão casamento');
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_casamento'],
            'max_length' => 32,
            'size' => 50,
        ];

        $this->inputsHelper()->text('certidao_casamento', $options);

        // uf emissão certidão civil
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

        // data emissão certidão civil
        $placeholderEmissao = Portabilis_String_Utils::toLatin1('Data emissão');
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

        $helperOptions = [
            'objectName' => 'cartorio_cert_civil_inep',
            'hiddenInputOptions' => [
              'options' => ['value' => $documentos['cartorio_cert_civil_inep']]
            ]
          ];

        $this->inputsHelper()->simpleSearchCartorioInep(null, $options, $helperOptions);

        // cartório emissão certidão civil
        $labelCartorio = Portabilis_String_Utils::toLatin1('Cartório emissão');
        $options = [
            'required' => false,
            'label' => $labelCartorio,
            'value' => $documentos['cartorio_cert_civil'],
            'cols' => 45,
            'max_length' => 200,
        ];

        $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);

        // Passaporte
        $labelPassaporte = Portabilis_String_Utils::toLatin1('Passaporte');
        $options = [
            'required' => false,
            'label' => $labelPassaporte,
            'value' => $documentos['passaporte'],
            'cols' => 45,
            'max_length' => 20
        ];

        $this->inputsHelper()->text('passaporte', $options);

        // pai
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

        //dois
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

        //tres
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

        //quatro
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

        //cinco
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

        // mãe
        $this->inputMae();
        /*    // pai
            $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
            $this->inputsHelper()->text('pai', $options);


            // mãe
            $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
            $this->inputsHelper()->text('mae', $options);*/

        // responsável

        // tipo

        $label = Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel'));

        /*$tiposResponsavel = array(null           => $label,
                                  'pai'          => 'Pai',
                                  'mae'          => 'M&atilde;e',
                                  'outra_pessoa' => 'Outra pessoa');*/
        $tiposResponsavel = [null => 'Informe uma Pessoa primeiro'];
        $options = [
            'label' => Portabilis_String_Utils::toLatin1('Responsável'),
            'resources' => $tiposResponsavel,
            'required' => true,
            'inline' => true
        ];

        $this->inputsHelper()->select('tipo_responsavel', $options);

        // nome
        $helperOptions = ['objectName' => 'responsavel'];
        $options = ['label' => '', 'size' => 50, 'required' => true];

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);

        // transporte publico

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

        $veiculos = [null => 'Nenhum',
            1 => Portabilis_String_Utils::toLatin1('Rodoviário - Vans/Kombis'),
            2 => Portabilis_String_Utils::toLatin1('Rodoviário - Microônibus'),
            3 => Portabilis_String_Utils::toLatin1('Rodoviário - Ônibus'),
            4 => Portabilis_String_Utils::toLatin1('Rodoviário - Bicicleta'),
            5 => Portabilis_String_Utils::toLatin1('Rodoviário - Tração animal'),
            6 => Portabilis_String_Utils::toLatin1('Rodoviário - Outro'),
            7 => Portabilis_String_Utils::toLatin1('Aquaviário/Embarcação - Capacidade de até 5 alunos'),
            8 => Portabilis_String_Utils::toLatin1('Aquaviário/Embarcação - Capacidade entre 5 a 15 alunos'),
            9 => Portabilis_String_Utils::toLatin1('Aquaviário/Embarcação - Capacidade entre 15 a 35 alunos'),
            10 => Portabilis_String_Utils::toLatin1('Aquaviário/Embarcação - Capacidade acima de 35 alunos'),
            11 => Portabilis_String_Utils::toLatin1('Ferroviário - Trem/Metrô')];

        $options = [
            'label' => 'Ve&iacute;culo utilizado',
            'resources' => $veiculos,
            'required' => false
        ];

        $this->inputsHelper()->select('veiculo_transporte_escolar', $options);

        if ($this->getClsPermissoes()->permissao_cadastra(21240, $this->getOption('id_usuario'), 7)) {
            // Cria lista de rotas
            $obj_rota = new clsModulesRotaTransporteEscolar();
            $obj_rota->setOrderBy(' descricao asc ');
            $lista_rota = $obj_rota->lista();
            $rota_resources = ['' => 'Selecione uma rota'];
            foreach ($lista_rota as $reg) {
                $rota_resources["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']}";
            }

            // Transporte Rota
            $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('transporte_rota')), 'required' => false, 'resources' => $rota_resources];
            $this->inputsHelper()->select('transporte_rota', $options);

            // Ponto de Embarque
            $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('transporte_ponto')), 'required' => false, 'resources' => ['' => 'Selecione uma rota acima']];
            $this->inputsHelper()->select('transporte_ponto', $options);

            // Transporte Destino
            $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('transporte_destino')), 'required' => false];
            $this->inputsHelper()->simpleSearchPessoaj('transporte_destino', $options);

            // Transporte observacoes
            $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('transporte_observacao')), 'required' => false, 'size' => 50, 'max_length' => 255];
            $this->inputsHelper()->textArea('transporte_observacao', $options);
        }

        // religião
        $this->inputsHelper()->religiao(['required' => false, 'label' => Portabilis_String_Utils::toLatin1('Religião')]);

        // Benefícios
        $helperOptions = ['objectName' => 'beneficios'];
        $options = [
            'label' => Portabilis_String_Utils::toLatin1('Benefícios'),
            'size' => 250,
            'required' => false,
            'options' => ['value' => null]
        ];

        $this->inputsHelper()->multipleSearchBeneficios('', $options, $helperOptions);

        // Deficiências / habilidades especiais
        $helperOptions = ['objectName' => 'deficiencias'];
        $options = [
            'label' => $this->_getLabel('deficiencias'),
            'size' => 50,
            'required' => false,
            'options' => ['value' => null]
        ];

        $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);

        // alfabetizado
        $options = ['label' => $this->_getLabel('alfabetizado'), 'value' => 'checked'];
        $this->inputsHelper()->checkbox('alfabetizado', $options);

        if ($GLOBALS['coreExt']['Config']->app->alunos->nao_apresentar_campo_alfabetizado) {
            $this->inputsHelper()->hidden('alfabetizado');
        }

        $this->campoArquivo('documento', Portabilis_String_Utils::toLatin1($this->_getLabel('documento')), $this->documento, 40, Portabilis_String_Utils::toLatin1('<br/> <span id=\'span-documento\' style=\'font-style: italic; font-size= 10px;\'\'> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 250KB</span>', ['escape' => false]));

        $this->inputsHelper()->hidden('url_documento');

        $this->campoArquivo('laudo_medico', Portabilis_String_Utils::toLatin1($this->_getLabel('laudo_medico')), $this->laudo_medico, 40, Portabilis_String_Utils::toLatin1('<br/> <span id=\'span-laudo_medico\' style=\'font-style: italic; font-size= 10px;\'\'> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 250KB</span>', ['escape' => false]));

        $this->inputsHelper()->hidden('url_laudo_medico');

        if ($GLOBALS['coreExt']['Config']->app->alunos->laudo_medico_obrigatorio == 1) {
            $this->inputsHelper()->hidden('url_laudo_medico_obrigatorio');
        }

        /* *************************************
           ** Dados para a Aba 'Ficha médica' **
           ************************************* */

        // Histórico de altura e peso

        $this->campoTabelaInicio('historico_altura_peso', 'Histórico de altura e peso', ['Data', 'Altura (m)', 'Peso (kg)']);

        $this->inputsHelper()->date('data_historico');

        $this->inputsHelper()->numeric('historico_altura');

        $this->inputsHelper()->numeric('historico_peso');

        $this->campoTabelaFim();

        // Fim histórico de altura e peso

        // altura
        $options = ['label' => $this->_getLabel('altura'), 'size' => 5, 'max_length' => 4, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('altura', $options);

        // peso
        $options = ['label' => $this->_getLabel('peso'), 'size' => 5, 'max_length' => 6, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('peso', $options);

        // grupo_sanguineo
        $options = ['label' => $this->_getLabel('grupo_sanguineo'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('grupo_sanguineo', $options);

        // fator_rh
        $options = ['label' => $this->_getLabel('fator_rh'), 'size' => 5, 'max_length' => 1, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('fator_rh', $options);

        // sus
        $options = ['label' => $this->_getLabel('sus'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('sus', $options);

        // alergia_medicamento
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('alergia_medicamento')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('alergia_medicamento', $options);

        // desc_alergia_medicamento
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_alergia_medicamento')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_alergia_medicamento', $options);

        // alergia_alimento
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('alergia_alimento')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('alergia_alimento', $options);

        // desc_alergia_alimento
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_alergia_alimento')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_alergia_alimento', $options);

        // doenca_congenita
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_congenita')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_congenita', $options);

        // desc_doenca_congenita
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_doenca_congenita')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_doenca_congenita', $options);

        // fumante
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fumante')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fumante', $options);

        // doenca_caxumba
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_caxumba')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_caxumba', $options);

        // doenca_sarampo
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_sarampo')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_sarampo', $options);

        // doenca_rubeola
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_rubeola')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_rubeola', $options);

        // doenca_catapora
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_catapora')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_catapora', $options);

        // doenca_escarlatina
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_escarlatina')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_escarlatina', $options);

        // doenca_coqueluche
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_coqueluche')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('doenca_coqueluche', $options);

        // doenca_outras
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_outras')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('doenca_outras', $options);

        // epiletico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('epiletico')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('epiletico', $options);

        // epiletico_tratamento
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('epiletico_tratamento')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('epiletico_tratamento', $options);

        // hemofilico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hemofilico')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('hemofilico', $options);

        // hipertenso
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hipertenso')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('hipertenso', $options);

        // asmatico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('asmatico')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('asmatico', $options);

        // diabetico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('diabetico')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('diabetico', $options);

        // insulina
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('insulina')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('insulina', $options);

        // tratamento_medico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tratamento_medico')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('tratamento_medico', $options);

        // desc_tratamento_medico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_tratamento_medico')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_tratamento_medico', $options);

        // medicacao_especifica
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('medicacao_especifica')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('medicacao_especifica', $options);

        // desc_medicacao_especifica
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_medicacao_especifica')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_medicacao_especifica', $options);

        // acomp_medico_psicologico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('acomp_medico_psicologico')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('acomp_medico_psicologico', $options);

        // desc_acomp_medico_psicologico
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_acomp_medico_psicologico')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_acomp_medico_psicologico', $options);

        // restricao_atividade_fisica
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('restricao_atividade_fisica')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('restricao_atividade_fisica', $options);

        // desc_restricao_atividade_fisica
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_restricao_atividade_fisica')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_restricao_atividade_fisica', $options);

        // fratura_trauma
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fratura_trauma')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fratura_trauma', $options);

        // desc_fratura_trauma
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_fratura_trauma')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_fratura_trauma', $options);

        // plano_saude
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('plano_saude')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('plano_saude', $options);

        // desc_plano_saude
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_plano_saude')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('desc_plano_saude', $options);

        $this->campoRotulo('tit_dados_hospital', Portabilis_String_Utils::toLatin1('Em caso de emergência, levar para hospital ou clínica'));

        // hospital_clinica
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('hospital_clinica', $options);

        // hospital_clinica_endereco
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_endereco')), 'size' => 50, 'max_length' => 50, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('hospital_clinica_endereco', $options);

        // hospital_clinica_telefone
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_telefone')), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('hospital_clinica_telefone', $options);

        $this->campoRotulo('tit_dados_responsavel', Portabilis_String_Utils::toLatin1('Em caso de emergência, caso não seja encontrado pais ou responsáveis, avisar'));

        // responsavel
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel')), 'size' => 50, 'max_length' => 50, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel', $options);

        // responsavel_parentesco
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco')), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel_parentesco', $options);

        // responsavel_parentesco_telefone
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco_telefone')), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->text('responsavel_parentesco_telefone', $options);

        // responsavel_parentesco_celular
        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco_celular')), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => ''];
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
            '2' => Portabilis_String_Utils::toLatin1('Próprio'),
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

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quartos')), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('quartos', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('sala')), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('sala', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('copa')), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('copa', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('banheiro')), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('banheiro', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('garagem')), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('garagem', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('empregada_domestica')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('empregada_domestica', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('automovel')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('automovel', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('motocicleta')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('motocicleta', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('computador')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('computador', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('geladeira')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('geladeira', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fogao')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fogao', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('maquina_lavar')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('maquina_lavar', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('microondas')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('microondas', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('video_dvd')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('video_dvd', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('televisao')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('televisao', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('telefone')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('telefone', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('celular')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('celular', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quant_pessoas')), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->integer('quant_pessoas', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('renda')), 'size' => 5, 'max_length' => 10, 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->numeric('renda', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('agua_encanada')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('agua_encanada', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('poco')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('poco', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('energia')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('energia', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('esgoto')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('esgoto', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fossa')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('fossa', $options);

        $options = ['label' => Portabilis_String_Utils::toLatin1($this->_getLabel('lixo')), 'required' => false, 'placeholder' => ''];
        $this->inputsHelper()->checkbox('lixo', $options);

        $recursosProvaInep = [
            1 => 'Auxílio ledor',
            2 => 'Auxílio transcrição',
            3 => 'Guia-intérprete',
            4 => 'Intérprete de LIBRAS',
            5 => 'Leitura labial',
            6 => 'Prova ampliada (Fonte 16)',
            7 => 'Prova ampliada (Fonte 20)',
            8 => 'Prova ampliada (Fonte 24)',
            9 => 'Prova em Braille'
        ];
        $helperOptions = ['objectName'  => 'recursos_prova_inep'];
        $options = [
            'label' => 'Recursos prova INEP',
            'size' => 50,
            'required' => false,
            'options' => [
                'values' => $this->recursos_prova_inep,
                'all_values' => $recursosProvaInep]];
        $this->inputsHelper()->multipleSearchCustom('_', $options, $helperOptions);

        $selectOptions = [
            3 => 'Não recebe',
            1 => 'Em hospital',
            2 => 'Em domicílio'
        ];

        $options = [
            'required' => false,
            'label' => $this->_getLabel('recebe_escolarizacao_em_outro_espaco'),
            'resources' => $selectOptions
        ];

        $this->inputsHelper()->select('recebe_escolarizacao_em_outro_espaco', $options);

        // Projetos
        $this->campoTabelaInicio('projetos', 'Projetos', ['Projeto', Portabilis_String_Utils::toLatin1('Data inclusão'), 'Data desligamento', 'Turno']);

        $this->inputsHelper()->text('projeto_cod_projeto', ['required' => false]);

        $this->inputsHelper()->date('projeto_data_inclusao', ['required' => false]);

        $this->inputsHelper()->date('projeto_data_desligamento', ['required' => false]);

        $this->inputsHelper()->select('projeto_turno', ['required' => false, 'resources' => ['' => 'Selecione', 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral']]);

        $this->campoTabelaFim();

        // Fim projetos

        $this->inputsHelper()->simpleSearchMunicipio('pessoa-aluno', ['required' => false, 'size' => 57], ['objectName' => 'naturalidade_aluno']);

        $enderecamentoObrigatorio = false;
        $desativarCamposDefinidosViaCep = true;

        $this->campoCep(
            'cep_',
            'CEP',
            '',
            $enderecamentoObrigatorio,
            '-',
            "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'/intranet/educar_pesquisa_cep_log_bairro2.php?campo1=bairro_bairro&campo2=bairro_id&campo3=cep&campo4=logradouro_logradouro&campo5=logradouro_id&campo6=distrito_id&campo7=distrito_distrito&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
            false
        );

        $options = ['label' => Portabilis_String_Utils::toLatin1('Município'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $helperOptions = [
            'objectName' => 'municipio',
            'hiddenInputOptions' => ['options' => ['value' => $this->municipio_id]]
        ];

        $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

        $options = ['label' => Portabilis_String_Utils::toLatin1('Distrito'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $helperOptions = [
            'objectName' => 'distrito',
            'hiddenInputOptions' => ['options' => ['value' => $this->distrito_id]]
        ];

        $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

        $helperOptions = ['hiddenInputOptions' => ['options' => ['value' => $this->bairro_id]]];

        $options = ['label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localização - <b>Buscar</b>'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

        $options = [
            'label' => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
            'placeholder' => 'Bairro',
            'value' => $this->bairro,
            'max_length' => 40,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->text('bairro', $options);

        // zona localização
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

        $helperOptions = ['hiddenInputOptions' => ['options' => ['value' => $this->logradouro_id]]];

        $options = ['label' => 'Tipo / Logradouro - <b>Buscar</b>', 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

        // tipo logradouro

        $options = [
            'label' => 'Tipo / Logradouro - <b>Cadastrar</b>',
            'value' => $this->idtlog,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        ];

        $helperOptions = [
            'attrName' => 'idtlog'
        ];

        $this->inputsHelper()->tipoLogradouro($options, $helperOptions);

        // logradouro
        $options = [
            'label' => '',
            'placeholder' => 'Logradouro',
            'value' => '',
            'max_length' => 150,
            'disabled' => $desativarCamposDefinidosViaCep,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->text('logradouro', $options);

        // complemento
        $options = [
            'required' => false,
            'value' => '',
            'max_length' => 20
        ];

        $this->inputsHelper()->text('complemento', $options);

        // numero
        $options = [
            'required' => false,
            'label' => 'Número / Letra',
            'placeholder' => Portabilis_String_Utils::toLatin1('Número'),
            'value' => '',
            'max_length' => 6,
            'inline' => true
        ];

        $this->inputsHelper()->integer('numero', $options);

        // letra
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Letra',
            'value' => $this->letra,
            'max_length' => 1,
            'size' => 15
        ];

        $this->inputsHelper()->text('letra', $options);

        // apartamento
        $options = [
            'required' => false,
            'label' => 'Nº apartamento / Bloco / Andar',
            'placeholder' => 'Apartamento',
            'value' => $this->apartamento,
            'max_length' => 6,
            'inline' => true
        ];

        $this->inputsHelper()->integer('apartamento', $options);

        // bloco
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Bloco',
            'value' => $this->bloco,
            'max_length' => 20,
            'size' => 15,
            'inline' => true
        ];

        $this->inputsHelper()->text('bloco', $options);

        // andar
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Andar',
            'value' => $this->andar,
            'max_length' => 2
        ];

        $this->inputsHelper()->integer('andar', $options);

        $script = '/modules/Cadastro/Assets/Javascripts/Endereco.js';

        Portabilis_View_Helper_Application::loadJavascript($this, $script);

        $this->loadResourceAssets($this->getDispatcher());

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigarCamposCenso = false;
        if ($instituicao && isset($instituicao['obrigar_campos_censo'])) {
            $obrigarCamposCenso = dbBool($instituicao['obrigar_campos_censo']);
        }
        $this->CampoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);

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

        // pais origem

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
        $this->inputsHelper()->simpleSearchPais('nome', $options, $helperOptions);
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

        // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
        //pela antiga interface do cadastro de alunos.

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
