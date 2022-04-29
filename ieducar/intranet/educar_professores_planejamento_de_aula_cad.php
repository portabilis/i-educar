<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;

return new class extends clsCadastro {
    public $id;
    public $ref_cod_turma;
    public $ref_cod_componente_curricular_array;
    public $fase_etapa;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $referencias;
    public $bncc;
    public $conteudo_id;
    public $bncc_especificacoes;
    public $recursos_didaticos;
    public $registro_adaptacao;

    public function Inicializar () {
        $this->titulo = 'Plano de aula - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];
        $this->copy = $_GET['copy'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_planejamento_de_aula_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesPlanejamentoAula($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro['detalhes'] != null) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->bncc = array_column($registro['bnccs'], 'id');

                if (!$this->copy) {
                    $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                    $retorno = 'Editar';

                    $this->titulo = 'Plano de aula - Edição';
                }
            } else {
                $this->simpleRedirect('educar_professores_planejamento_de_aula_lst.php');
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_planejamento_de_aula_det.php?id=%d', $this->id)
            : 'educar_professores_planejamento_de_aula_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' plano de aula', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar () {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $this->data_inicial = dataToBrasil($this->data_inicial);
        $this->data_final = dataToBrasil($this->data_final);

        $this->ano = explode('/', $this->data_inicial)[2];

        if ($tipoacao == 'Edita' || !$_POST
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_numeric($this->ref_cod_turma)
            && is_numeric($this->ref_cod_componente_curricular_array)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('dataInicial', ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('dataFinal', ['required' => $obrigatorio]);      // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado && !$this->copy]);
        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado && !$this->copy]);

        $this->adicionarBNCCMultiplaEscolha();
        $this->adicionarConteudosTabela();

        $this->campoMemo('ddp','Metodologia', $this->ddp, 100, 5, $obrigatorio);
        $this->campoMemo('atividades','Atividades/Avaliações', $this->atividades, 100, 5, !$obrigatorio);
        $this->campoMemo('recursos_didaticos','Recursos didáticos', $this->recursos_didaticos, 100, 5, !$obrigatorio);
        $this->campoMemo('registro_adaptacao','Registro de adaptação', $this->registro_adaptacao, 100, 5, !$obrigatorio);
        $this->campoMemo('referencias','Referências', $this->referencias, 100, 5, !$obrigatorio);

        $this->campoOculto('id', $this->id);
        $this->campoOculto('copy', $this->copy);

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        // educar-professores-planejamento-de-aula-cad.js
    }

    public function Editar() {
        $obj = new clsModulesPlanejamentoAula(
            $this->id,
            null,
            null,
            null,
            null,
            null,
            $this->ddp,
            $this->atividades,
            $this->bncc,
            $this->conteudos,
            $this->referencias
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir () {
        $obj = new clsModulesPlanejamentoAula($this->id);

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }
 
    private function getBNCCTurma($turma = null, $ref_cod_componente_curricular_array = null)
    {
        if (is_numeric($turma)) {
            $obj = new clsPmieducarTurma($turma);
            $resultado = $obj->getGrau();

            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->listaTurma($resultado, $turma, $ref_cod_componente_curricular_array)) {
                foreach ($bncc_temp as $bncc_item) {
                    $id = $bncc_item['id'];
                    $codigo = $bncc_item['codigo'];
                    $habilidade = $bncc_item['habilidade'];

                    $bncc[$id] = $codigo . ' - ' . $habilidade;
                }
            }

            return ['bncc' => $bncc];
        }

        return [];
    }
    
    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/DynamicInput/Assets/Javascripts/TodasTurmas.js',
            '/modules/Cadastro/Assets/Javascripts/PlanejamentoAula.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaExclusao.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaEdicao.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaDuplicacao.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-professores-planejamento-de-aula-cad.js');
    }

    private function adicionarBNCCMultiplaEscolha() {
        $this->campoTabelaInicio(
            'objetivos_aprendizagem',
            'Objetivo(s) de aprendizagem',
            ['Componente curricular', "Habilidade(s)", "Especificação(ões)"],
        );

        // Componente curricular
        $this->campoLista(
            'ref_cod_componente_curricular_array',
            'Componente curricular',
            ['' => 'Selecione o componente curricular'],
            $this->ref_cod_componente_curricular_array,
        );

        // BNCCs
        $todos_bncc = [];
        
        $options = [
            'label' => 'Objetivos de aprendizagem/habilidades (BNCC)',
            'required' => true,
            'options' => [
                'values' => $this->bncc,
                'all_values' => $todos_bncc
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('bncc', $options);

        // BNCCs Especificações
        $todos_bncc_especificacoes = [];
        
        $options = [
            'label' => 'Especificações',
            'required' =>true,
            'options' => [
                'values' => $this->bncc_especificacoes,
                'all_values' => $todos_bncc_especificacoes
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('bncc_especificacoes', $options);

        $this->campoTabelaFim();
    }

    protected function adicionarConteudosTabela()
    {
        $obj = new clsModulesPlanejamentoAulaConteudo();
        $conteudos = $obj->lista($this->id);

        for ($i=0; $i < count($conteudos); $i++) {
            $conteudo = $conteudos[$i];
            $rows[$conteudo['id']][] = $conteudo['conteudo'];
        }

        $this->campoTabelaInicio(
            'conteudos',
            'Objetivo(s) do conhecimento/conteúdo',
            [
                'Conteúdo(s)',
            ],
            $rows
        );

        $this->campoTexto('conteudos', 'Conteúdos', $this->conteudo_id, 100, 2048, true);

        $this->campoTabelaFim();
    }

    public function Formular () {
        $this->title = 'Plano de aula - Cadastro';
        $this->processoAp = '58';
    }
};
