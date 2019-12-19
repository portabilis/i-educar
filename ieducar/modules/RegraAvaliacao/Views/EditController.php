<?php

use iEducar\Modules\EvaluationRules\Models\ParallelRemedialCalculationType;

require_once 'Core/Controller/Page/EditController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'RegraAvaliacao/Model/RegraRecuperacaoDataMapper.php';

class EditController extends Core_Controller_Page_EditController
{

    protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';
    protected $_titulo = 'Cadastro de regra de avaliação';
    protected $_processoAp = 947;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
    protected $_saveOption = true;
    protected $_deleteOption = false;

    protected $_formMap = [
        'instituicao' => [
            'label' => 'Instituição',
            'help' => '',
        ],
        'nome' => [
            'label' => 'Nome',
            'help' => 'Nome por extenso do componente.',
        ],
        'tipoNota' => [
            'label' => 'Sistema de nota',
            'help' => ''
        ],
        'tipoProgressao' => [
            'label' => 'Progressão',
            'help' => 'Selecione o método de progressão para a regra.'
        ],
        'tabelaArredondamento' => [
            'label' => 'Tabela de arredondamento de nota',
            'help' => ''
        ],
        'tabelaArredondamentoNumerico' => [
            'label' => 'Tabela de arredondamento de nota numérica',
            'help' => ''
        ],
        'tabelaArredondamentoConceitual' => [
            'label' => 'Tabela de arredondamento de nota conceitual',
            'help' => ''
        ],
        'media' => [
            'label' => 'Média final para promoção',
            'help' => 'Informe a média necessária para promoção<br />
                do aluno, aceita até 3 casas decimais. Exemplos: 5,00; 6,725, 6.<br >
                Se o tipo de progressão for <b>"Progressiva"</b>, esse<br />
                valor não será considerado.'
        ],
        'mediaRecuperacao' => [
            'label' => 'Média exame final para promoção',
            'help' => 'Informe a média necessária para promoção<br />
                do aluno, aceita até 3 casas decimais. Exemplos: 5,00; 6,725, 6.<br >
                Desconsidere esse campo caso selecione o tipo de nota "conceitual"'
        ],
        'tipoCalculoRecuperacaoParalela' => [
            'label' => 'Cálculo da média',
            'help' => 'Determina o cálculo que será utilizado para definir a média da etapa.'
        ],
        'formulaMedia' => [
            'label' => 'Fórmula de cálculo da média',
            'help' => '',
        ],
        'formulaRecuperacao' => [
            'label' => 'Fórmula de cálculo da média de recuperação',
            'help' => '',
        ],
        'porcentagemPresenca' => [
            'label' => 'Porcentagem de presença',
            'help' => 'A porcentagem de presença necessária para o aluno ser aprovado.<br />
                Esse valor é desconsiderado caso o campo "Progressão" esteja como<br />
                "Não progressiva automática - Somente média".<br />
                Em porcentagem, exemplo: <b>75</b> ou <b>80,750</b>'
        ],
        'parecerDescritivo' => [
            'label' => 'Parecer descritivo',
            'help' => '',
        ],
        'tipoPresenca' => [
            'label' => 'Apuração de presença',
            'help' => ''
        ],
        'tipoRecuperacaoParalela' => [
            'label' => 'Permitir recuperação paralela',
            'help' => ''
        ],
        'mediaRecuperacaoParalela' => [
            'label' => 'Média da recuperação paralela',
            'help' => ''
        ],
        'regraDiferenciada' => [
            'label' => 'Regra diferenciada',
            'help' => 'Regra para avaliação de alunos com deficiência'
        ],
        'notaMaximaGeral' => [
            'label' => 'Nota máxima geral',
            'help' => 'Informe o valor máximo para notas no geral'
        ],
        'notaMinimaGeral' => [
            'label' => 'Nota mínima geral',
            'help' => 'Informe o valor mínimo para notas no geral'
        ],
        'notaMaximaExameFinal' => [
            'label' => 'Nota máxima exame final',
            'help' => 'Informe o valor máximo para nota do exame final'
        ],
        'qtdCasasDecimais' => [
            'label' => 'Quantidade máxima de casas decimais',
            'help' => 'Informe o número máximo de casas decimais'
        ],
        'qtdDisciplinasDependencia' => [
            'label' => 'Quantidade de disciplinas dependência',
            'help' => 'Preencha a quantidade de disciplinas permitidas para aprovação do aluno com dependência. Preencha com 0 caso não exista.'
        ],
        'qtdMatriculasDependencia' => [
            'label' => 'Quantidade de matriculas de dependência',
            'help' => 'Preencha a quantidade de matrículas de dependência permitidas por aluno. Preencha com 0 caso não exista.'
        ],
        'disciplinasAglutinadas' => [
            'label' => 'Disciplinas aglutinadas',
            'help' => 'Disciplinas aglutinadas terão as médias somadas para calcular a situação. Formato: Código separado por vírgula (Ex: 1,2)'
        ],
        'recuperacaoDescricao' => [
            'label' => 'Descrição do exame:',
            'help' => 'Exemplo: Recuperação semestral I'
        ],
        'recuperacaoEtapasRecuperadas' => [
            'label' => '<span style="padding-left: 10px"></span>Etapas:',
            'help' => 'Separe as etapas com ponto e vírgula. Exemplo: 1;2.'
        ],
        'recuperacaoSubstituiMenorNota' => [
            'label' => '<span style="padding-left: 10px"></span>Substituir menor nota:',
            'help' => 'Caso marcado irá substituir menor nota.'
        ],
        'recuperacaoMedia' => [
            'label' => '<span style="padding-left: 10px"></span>Média:',
            'help' => 'Abaixo de qual média habilitar campo.'
        ],
        'recuperacaoNotaMaxima' => [
            'label' => '<span style="padding-left: 10px"></span>Nota máx:',
            'help' => 'Nota máxima permitida para lançamento.'
        ],
        'recuperacaoExcluir' => [
            'label' => '<span style="padding-left: 10px"></span>Excluir:'
        ],
        'notaGeralPorEtapa' => [
            'label' => 'Utilizar uma nota geral por etapa'
        ],
        'definirComponentePorEtapa' => [
            'label' => 'Permitir definir componentes em etapas específicas'
        ],
        'reprovacaoAutomatica' => [
            'label' => 'Reprovação automática',
            'help' => 'Marcando esse campo o sistema não apresentará campo de nota de exame para os alunos que não poderão alcançar a média necessária'
        ],
        'aprovaMediaDisciplina' => [
            'label' => 'Aprovar alunos pela média das disciplinas',
            'help' => 'Alunos reprovados podem ser aprovados se a média das médias das disciplinas for superior a nota de aprovação de exame final'
        ]
    ];

    private $_tipoNotaJs =
        'var tipo_nota = new function() {
            this.isNenhum = function(docObj, formId, fieldsName) {
                var regex = new RegExp(fieldsName);
                var form = docObj.getElementById(formId);

                for (var i = 0; i < form.elements.length; i++) {
                    var elementName = form.elements[i].name;

                    if (null !== elementName.match(regex)) {
                        if (form.elements[i].checked == false) {
                            continue;
                        }

                        docObj.getElementById(\'tabelaArredondamento\').disabled = false;
                        docObj.getElementById(\'media\').disabled = false;
                        docObj.getElementById(\'formulaMedia\').disabled = false;
                        docObj.getElementById(\'formulaRecuperacao\').disabled = false;

                        if (form.elements[i].value == 0) {
                            docObj.getElementById(\'tabelaArredondamento\').disabled = true;
                            docObj.getElementById(\'media\').disabled = true;
                            docObj.getElementById(\'formulaMedia\').disabled = true;
                            docObj.getElementById(\'formulaRecuperacao\').disabled = true;
                        }

                        break;
                    }
                }
            };
        };

        var tabela_arredondamento = new function() {
            this.docObj = null;

            this.getTabelasArredondamento = function(docObj, tipoNota) {
                tabela_arredondamento.docObj = docObj;
                var xml = new ajax(tabela_arredondamento.parseResponse);
                xml.envia("/modules/TabelaArredondamento/Views/TabelaTipoNotaAjax.php?tipoNota=" + tipoNota);
            };

            this.parseResponse = function() {
                if (arguments[0] === null) {
                    return;
                }

                docObj = tabela_arredondamento.docObj;

                tabelas = arguments[0].getElementsByTagName(\'tabela\');
                docObj.options.length = 0;

                for (var i = 0; i < tabelas.length; i++) {
                    docObj[docObj.options.length] = new Option(
                        tabelas[i].firstChild.nodeValue, tabelas[i].getAttribute(\'id\'), false, false
                    );
                }

                if (tabelas.length == 0) {
                    docObj.options[0] = new Option(
                        \'O tipo de nota não possui tabela de arredondamento.\', \'\', false, false
                    );
                }
            }
        }';

    /**
     * Array de instâncias RegraAvaliacao_Model_RegraRecuperacao.
     *
     * @var array
     */
    protected $_recuperacoes = [];

    /**
     * Setter.
     *
     * @param array $recuperacoes
     *
     * @return Core_Controller_Page_Abstract Provê interface fluída
     */
    protected function _setRecuperacoes(array $recuperacoes = [])
    {
        foreach ($recuperacoes as $key => $recuperacao) {
            $this->_recuperacoes[$recuperacao->id] = $recuperacao;
        }

        return $this;
    }

    /**
     * Getter.
     *
     * @return array
     */
    protected function _getRecuperacoes()
    {
        return $this->_recuperacoes;
    }

    /**
     * Getter
     *
     * @param int $id
     *
     * @return RegraAvaliacao_Model_RegraRecuperacao
     */
    protected function _getRecuperacao($id)
    {
        return isset($this->_recuperacoes[$id])
            ? $this->_recuperacoes[$id]
            : null;
    }

    protected function _preRender()
    {
        parent::_preRender();

        // Adiciona o código Javascript de controle do formulário.
        $js = sprintf(
            '<script type="text/javascript">
                %s

                window.onload = function() {
                    // Desabilita os campos relacionados caso o tipo de nota seja "nenhum".
                    new tipo_nota.isNenhum(document, \'formcadastro\', \'tipoNota\');

                    // Faz o binding dos eventos isNenhum e getTabelasArredondamento nos
                    // campos radio de tipo de nota.
                    var events = function() {
                        new tipo_nota.isNenhum(document, \'formcadastro\', \'tipoNota\');
                        new tabela_arredondamento.getTabelasArredondamento(
                            document.getElementById(\'tabelaArredondamento\'),
                            this.value
                        );
                    }

                    new ied_forms.bind(document, \'formcadastro\', \'tipoNota\', \'click\', events);
                }
            </script>',
            $this->_tipoNotaJs
        );

        $this->prependOutput($js);

        Portabilis_View_Helper_Application::loadJavascript(
            $this,
            '/modules/RegraAvaliacao/Assets/Javascripts/RegraAvaliacao.js'
        );

        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => "$nomeMenu regra de avalia&ccedil;&atilde;o"
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }

    /**
     * @see clsCadastro::Gerar()
     */
    public function Gerar()
    {
        $this->campoOculto('id', $this->getEntity()->id);

        // Instituição
        $instituicoes = App_Model_IedFinder::getInstituicoes();

        $this->campoLista(
            'instituicao',
            $this->_getLabel('instituicao'),
            $instituicoes,
            $this->getEntity()->instituicao
        );

        // Nome
        $this->campoTexto(
            'nome',
            $this->_getLabel('nome'),
            $this->getEntity()->nome,
            50,
            50,
            true,
            false,
            false,
            $this->_getHelp('nome')
        );

        // Nota tipo valor
        $notaTipoValor = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
            $this->campoRadio(
            'tipoNota',
            $this->_getLabel('tipoNota'),
            $notaTipoValor->getEnums(),
            $this->getEntity()->get('tipoNota'),
            '',
            $this->_getHelp('tipoNota')
        );

        // Tabela de arredondamento
        $tabelaArredondamento = $this->getDataMapper()
            ->findTabelaArredondamento($this->getEntity());

        $tabelaArredondamento = CoreExt_Entity::entityFilterAttr(
            $tabelaArredondamento,
            'id',
            'nome'
        );

        // Tabela de arredondamento numérico
        $tabelaArredondamentoNumerico = $this->getDataMapper()
            ->findTabelaArredondamento(
                $this->getEntity(),
                ['tipo_nota' => 1]
            );

        $tabelaArredondamentoNumerico = CoreExt_Entity::entityFilterAttr(
            $tabelaArredondamentoNumerico,
            'id',
            'nome'
        );

        // Tabela de arredondamento conceitual
        $tabelaArredondamentoConceitual = $this->getDataMapper()
            ->findTabelaArredondamento(
                $this->getEntity(),
                ['tipo_nota' => 2]
            );

        $tabelaArredondamentoConceitual = CoreExt_Entity::entityFilterAttr(
            $tabelaArredondamentoConceitual,
            'id',
            'nome'
        );

        if (empty($tabelaArredondamento)) {
            $tabelaArredondamento = [
                0 => 'O tipo de nota não possui tabela de arredondamento.'
            ];
        }

        $this->campoLista(
            'tabelaArredondamento',
            $this->_getLabel('tabelaArredondamento'),
            $tabelaArredondamento,
            $this->getEntity()->get('tabelaArredondamento'),
            '',
            false,
            $this->_getHelp('tabelaArredondamento'),
            '',
            false,
            false
        );

        $this->campoLista(
            'tabelaArredondamentoNumero',
            $this->_getLabel('tabelaArredondamentoNumerico'),
            $tabelaArredondamentoNumerico,
            $this->getEntity()->get('tabelaArredondamento'),
            '',
            false,
            $this->_getHelp('tabelaArredondamento'),
            '',
            false,
            false
        );

        $this->campoLista(
            'tabelaArredondamentoConceitual',
            $this->_getLabel('tabelaArredondamentoConceitual'),
            $tabelaArredondamentoConceitual,
            $this->getEntity()->get('tabelaArredondamentoConceitual'),
            '',
            false,
            $this->_getHelp('tabelaArredondamento'),
            '',
            false,
            false
        );

        // Tipo progressão
        $tipoProgressao = RegraAvaliacao_Model_TipoProgressao::getInstance();
            $this->campoRadio(
            'tipoProgressao',
            $this->_getLabel('tipoProgressao'),
            $tipoProgressao->getEnums(),
            $this->getEntity()->get('tipoProgressao'),
            '',
            $this->_getHelp('tipoProgressao')
        );

        // Média
        $this->campoTexto(
            'media',
            $this->_getLabel('media'),
            $this->getEntity()->media,
            5,
            50,
            false,
            false,
            false,
            $this->_getHelp('media')
        );

        $this->campoTexto(
            'mediaRecuperacao',
            $this->_getLabel('mediaRecuperacao'),
            $this->getEntity()->mediaRecuperacao,
            5,
            50,
            false,
            false,
            false,
            $this->_getHelp('mediaRecuperacao')
        );

        // Cálculo média
        $formulas = $this->getDataMapper()->findFormulaMediaFinal();
        $formulas = CoreExt_Entity::entityFilterAttr($formulas, 'id', 'nome');

        $this->campoLista(
            'formulaMedia',
            $this->_getLabel('formulaMedia'),
            $formulas,
            $this->getEntity()->get('formulaMedia'),
            '',
            false,
            $this->_getHelp('formulaMedia'),
            '',
            false,
            false
        );

        // Cálculo média recuperação
        $formulas = $this->getDataMapper()->findFormulaMediaRecuperacao();
        $formulasArray = [0 => 'Não usar recuperação'];
        $formulasArray = $formulasArray + CoreExt_Entity::entityFilterAttr($formulas, 'id', 'nome');

        $this->campoLista(
            'formulaRecuperacao',
            $this->_getLabel('formulaRecuperacao'),
            $formulasArray,
            $this->getEntity()->get('formulaRecuperacao'),
            '',
            false,
            $this->_getHelp('formulaRecuperacao'),
            '',
            false,
            false
        );

        // Porcentagem presença
        $this->campoTexto(
            'porcentagemPresenca',
            $this->_getLabel('porcentagemPresenca'),
            $this->getEntity()->porcentagemPresenca,
            5,
            50,
            true,
            false,
            false,
            $this->_getHelp('porcentagemPresenca')
        );

        // Parecer descritivo
        $parecerDescritivo = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance();

        $this->campoRadio(
            'parecerDescritivo',
            $this->_getLabel('parecerDescritivo'),
            $parecerDescritivo->getEnums(),
            $this->getEntity()->get('parecerDescritivo'),
            '',
            $this->_getHelp('parecerDescritivo')
        );

        // Presença
        $tipoPresenca = RegraAvaliacao_Model_TipoPresenca::getInstance();

        $this->campoRadio(
            'tipoPresenca',
            $this->_getLabel('tipoPresenca'),
            $tipoPresenca->getEnums(),
            $this->getEntity()->get('tipoPresenca'),
            '',
            $this->_getHelp('tipoPresenca')
        );

        $this->campoNumero(
            'notaMaximaGeral',
            $this->_getLabel('notaMaximaGeral'),
            $this->getEntity()->notaMaximaGeral,
            3,
            3,
            true,
            $this->_getHelp('notaMaximaGeral')
        );

        $this->campoNumero(
            'notaMinimaGeral',
            $this->_getLabel('notaMinimaGeral'),
            $this->getEntity()->notaMinimaGeral,
            3,
            3,
            true,
            $this->_getHelp('notaMinimaGeral')
        );

        $this->campoNumero(
            'notaMaximaExameFinal',
            $this->_getLabel('notaMaximaExameFinal'),
            $this->getEntity()->notaMaximaExameFinal,
            3,
            3,
            true,
            $this->_getHelp('notaMaximaExameFinal')
        );

        $this->campoNumero(
            'qtdCasasDecimais',
            $this->_getLabel('qtdCasasDecimais'),
            $this->getEntity()->qtdCasasDecimais,
            3,
            3,
            true,
            $this->_getHelp('qtdCasasDecimais')
        );

        $this->campoNumero(
            'qtdDisciplinasDependencia',
            $this->_getLabel('qtdDisciplinasDependencia'),
            $this->getEntity()->qtdDisciplinasDependencia,
            3,
            3,
            true,
            $this->_getHelp('qtdDisciplinasDependencia')
        );

        $this->campoNumero(
            'qtdMatriculasDependencia',
            $this->_getLabel('qtdMatriculasDependencia'),
            $this->getEntity()->qtdMatriculasDependencia,
            3,
            3,
            true,
            $this->_getHelp('qtdMatriculasDependencia')
        );

        $this->campoTexto(
            'disciplinasAglutinadas',
            $this->_getLabel('disciplinasAglutinadas'),
            $this->getEntity()->disciplinasAglutinadas,
            5,
            50,
            false,
            false,
            false,
            $this->_getHelp('disciplinasAglutinadas')
        );

        $this->campoCheck(
            'reprovacaoAutomatica',
            $this->_getLabel('reprovacaoAutomatica'),
            $this->getEntity()->reprovacaoAutomatica,
            '',
            false,
            false,
            false,
            $this->_getHelp('reprovacaoAutomatica')
        );

        // Nota geral por etapa
        $this->campoCheck(
            'notaGeralPorEtapa',
            $this->_getLabel('notaGeralPorEtapa'),
            $this->getEntity()->notaGeralPorEtapa,
            '',
            false,
            false,
            false,
            $this->_getHelp('notaGeralPorEtapa')
        );

        $this->campoCheck(
            'definirComponentePorEtapa',
            $this->_getLabel('definirComponentePorEtapa'),
            $this->getEntity()->definirComponentePorEtapa,
            '',
            false,
            false,
            false,
            $this->_getHelp('definirComponentePorEtapa')
        );

        $this->campoCheck(
            'aprovaMediaDisciplina',
            $this->_getLabel('aprovaMediaDisciplina'),
            $this->getEntity()->aprovaMediaDisciplina,
            '',
            false,
            false,
            false,
            $this->_getHelp('aprovaMediaDisciplina')
        );

        $regras = $this->getDataMapper()->findAll(
            ['id', 'nome'],
            [],
            ['id'=> 'asc'],
            false
        );

        $regras = CoreExt_Entity::entityFilterAttr($regras, 'id', 'nome');
        $regras = array_replace([0 => 'Não utiliza'], $regras);

        $this->campoLista(
            'regraDiferenciada',
            $this->_getLabel('regraDiferenciada'),
            $regras,
            $this->getEntity()->get('regraDiferenciada'),
            '',
            false,
            $this->_getHelp('regraDiferenciada'),
            '',
            false,
            false
        );

        $tipoRecuperacaoParalela = RegraAvaliacao_Model_TipoRecuperacaoParalela::getInstance();

        $this->campoLista(
            'tipoRecuperacaoParalela',
            $this->_getLabel('tipoRecuperacaoParalela'),
            $tipoRecuperacaoParalela->getEnums(),
            $this->getEntity()->get('tipoRecuperacaoParalela'),
            '',
            false,
            $this->_getHelp('tipoRecuperacaoParalela'),
            '',
            false,
            false
        );

        $this->campoTexto(
            'mediaRecuperacaoParalela',
            $this->_getLabel('mediaRecuperacaoParalela'),
            $this->getEntity()->mediaRecuperacaoParalela,
            5,
            50,
            false,
            false,
            false,
            $this->_getHelp('mediaRecuperacaoParalela')
        );

        $this->campoLista(
            'tipoCalculoRecuperacaoParalela',
            $this->_getLabel('tipoCalculoRecuperacaoParalela'),
            ParallelRemedialCalculationType::getDescriptiveValues(),
            $this->getEntity()->get('tipoCalculoRecuperacaoParalela'),
            '',
            false,
            $this->_getHelp('tipoCalculoRecuperacaoParalela'),
            '',
            false,
            false
        );

        // Parte condicional
        if (!$this->getEntity()->isNew()) {
            // Quebra
            $this->campoQuebra();

            // Ajuda
            $help = 'Caso seja necessário adicionar mais etapas, ' .
                'salve o formulário. Automaticamente 3 campos ' .
                'novos ficarão disponíveis.<br /> ' .
                'As etapas devem ser separadas por ponto e vírgula(;). <br /><br />';

            $this->campoRotulo(
                '__help1',
                '<strong>Recuperações específicas</strong><br />',
                $help,
                false,
                '',
                ''
            );

            // Cria campos para a postagem de notas
            $recuperacoes = $this->getDataMapper()->findRegraRecuperacao(
                $this->getEntity()
            );

            for ($i = 0, $loop = count($recuperacoes); $i < ($loop == 0 ? 5 : $loop + 3); $i++) {
                $recuperacao = $recuperacoes[$i];

                $recuperacaoLabel = sprintf('recuperacao[label][%d]', $i);
                $recuperacaoId = sprintf('recuperacao[id][%d]', $i);
                $recuperacaoDescricao = sprintf('recuperacao[descricao][%d]', $i);
                $recuperacaoEtapasRecuperadas = sprintf('recuperacao[etapas_recuperadas][%d]', $i);
                $recuperacaoSubstituiMenorNota = sprintf('recuperacao[substitui_menor_nota][%d]', $i);
                $recuperacaoMedia = sprintf('recuperacao[media][%d]', $i);
                $recuperacaoNotaMaxima = sprintf('recuperacao[nota_maxima][%d]', $i);
                $recuperacaoExcluir = sprintf('recuperacao[excluir][%d]', $i);

                $this->campoRotulo(
                    $recuperacaoLabel,
                    'Recuperação ' . ($i + 1),
                    $this->_getLabel(''),
                    true
                );

                // Id
                $this->campoOculto($recuperacaoId, $recuperacao->id);

                // Nome
                $this->campoTexto(
                    $recuperacaoDescricao,
                    $this->_getLabel('recuperacaoDescricao'),
                    $recuperacao->descricao,
                    10,
                    25,
                    false,
                    false,
                    true,
                    $this->_getHelp('recuperacaoDescricao')
                );

                // Etapas recuperadas
                $this->campoTexto(
                    $recuperacaoEtapasRecuperadas,
                    $this->_getLabel('recuperacaoEtapasRecuperadas'),
                    $recuperacao->etapasRecuperadas,
                    5,
                    25,
                    false,
                    false,
                    true,
                    $this->_getHelp('recuperacaoEtapasRecuperadas')
                );

                // Substituí menor nota
                $this->campoCheck(
                    $recuperacaoSubstituiMenorNota,
                    $this->_getLabel('recuperacaoSubstituiMenorNota'),
                    $recuperacao->substituiMenorNota,
                    '',
                    true,
                    false,
                    false,
                    $this->_getHelp('recuperacaoSubstituiMenorNota')
                );

                // Média
                $this->campoTexto(
                    $recuperacaoMedia,
                    $this->_getLabel('recuperacaoMedia'),
                    $recuperacao->media,
                    4,
                    4,
                    false,
                    false,
                    true,
                    $this->_getHelp('recuperacaoMedia')
                );

                // Nota máxima
                $this->campoTexto(
                    $recuperacaoNotaMaxima,
                    $this->_getLabel('recuperacaoNotaMaxima'),
                    $recuperacao->notaMaxima,
                    4,
                    4,
                    false,
                    false,
                    true,
                    $this->_getHelp('recuperacaoNotaMaxima')
                );

                // Exclusão
                $this->campoCheck(
                    $recuperacaoExcluir,
                    $this->_getLabel('recuperacaoExcluir'),
                    false,
                    '',
                    false,
                    false,
                    false
                );
            }

            // Quebra
            $this->campoQuebra();
        }
    }

    protected function _save()
    {
        $data = [];

        if ($_POST['tipoNota']==3) {
            $_POST['tabelaArredondamento'] = $_POST['tabelaArredondamentoNumero'];
        } else {
            $_POST['tabelaArredondamentoConceitual'] = null;
        }

        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $this->_formMap)) {
                $data[$key] = $val;
            }
        }

        // Verifica pela existência do field identity
        if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
            $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
            $entity = $this->getEntity();
        }

        //fixup for checkbox nota geral
        if (!isset($data['notaGeralPorEtapa'])) {
            $data['notaGeralPorEtapa'] = '0';
        }

        //fixup for checkbox
        if (!isset($data['definirComponentePorEtapa'])) {
            $data['definirComponentePorEtapa'] = '0';
        }

        //fixup for checkbox
        if (!isset($data['reprovacaoAutomatica'])) {
            $data['reprovacaoAutomatica'] = '0';
        }

        //fixup for checkbox
        if (!isset($data['aprovaMediaDisciplina'])) {
            $data['aprovaMediaDisciplina'] = '0';
        }

        //fixup for checkbox
        if (!isset($data['calculaMediaRecParalela'])) {
            $data['calculaMediaRecParalela'] = '0';
        }

        if (isset($entity)) {
            $this->getEntity()->setOptions($data);
        } else {
            $this->setEntity($this->getDataMapper()->createNewEntityInstance($data));
        }

        // Processa os dados da requisição, apenas os valores para a tabela de valores.
        $recuperacoes = $this->getRequest()->recuperacao;

        // A contagem usa um dos índices do formulário, senão ia contar sempre 4.
        $loop = count($recuperacoes['id']);

        // Array de objetos a persistir
        $insert = [];

        // Cria um array de objetos a persistir
        for ($i = 0; $i < $loop; $i++) {
            $id = $recuperacoes['id'][$i];

            // Não atribui a instância de $entity senão não teria sucesso em verificar
            // se a instância é isNull().
            $data = [
                'id' => $id,
                'descricao' => $recuperacoes['descricao'][$i],
                'etapasRecuperadas' => $recuperacoes['etapas_recuperadas'][$i],
                'substituiMenorNota' => $recuperacoes['substitui_menor_nota'][$i],
                'media' => $recuperacoes['media'][$i],
                'notaMaxima' => $recuperacoes['nota_maxima'][$i]
            ];

            // Se a instância já existir, use-a para garantir UPDATE
            if (null != ($instance = $this->_getRecuperacao($id))) {
                $insert[$id] = $instance->setOptions($data);
            } else {
                $instance = new RegraAvaliacao_Model_RegraRecuperacao($data);
                if (!$instance->isNull()) {
                    if ($recuperacoes['excluir'][$i] && is_numeric($id)) {
                        $this->getDataMapper()
                            ->getRegraRecuperacaoDataMapper()
                            ->delete($instance);
                    } else {
                        $insert['new_' . $i] = $instance;
                    }
                }
            }
        }

        // Persiste
        foreach ($insert as $regraRecuperacao) {
            // Atribui uma tabela de arredondamento a instância de tabela valor
            $regraRecuperacao->regraAvaliacao = $entity;

            if ($regraRecuperacao->isValid()) {
                $this->getDataMapper()
                    ->getRegraRecuperacaoDataMapper()
                    ->save($regraRecuperacao);
            } else {
                $this->mensagem .= 'Erro no formulário';

                return false;
            }
        }

        try {
            $entity = $this->getDataMapper()->save($this->getEntity());
        } catch (Exception $e) {
            // TODO: ver @todo do docblock
            $this->mensagem .= 'Erro no preenchimento do formulário. ';

            return false;
        }

        return true;
    }
}
