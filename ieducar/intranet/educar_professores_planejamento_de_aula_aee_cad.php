<?php

use App\Process;
use App\Models\LegacyGrade;
use Illuminate\Support\Arr;
use App\Models\LegacyDiscipline;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use App\Services\CheckPostedDataService;

return new class extends clsCadastro
{
    public $id;
    public $ref_cod_turma;
    public $ref_cod_matricula;
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

    public function Inicializar()
    {
        $this->titulo = 'Plano de aula AEE - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];
        $this->copy = $_GET['copy'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_planejamento_de_aula_aee_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesPlanejamentoAulaAee($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro['detalhes'] != null) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->bncc = array_column($registro['bnccs'], 'id');
                $this->bncc_especificacoes = array_column($registro['especificacoes'], 'id');
                $this->ref_cod_componente_curricular_array = $registro['componentesCurriculas'];
                $this->conteudos_ids = $registro['conteudos'][1];

                if (!$this->copy) {
                    $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                    $retorno = 'Editar';

                    $this->titulo = 'Plano de aula AEE - Edição';
                }
            } else {
                $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_lst2.php');
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_planejamento_de_aula_aee_det.php?id=%d', $this->id)
            : 'educar_professores_planejamento_de_aula_aee_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' plano de aula', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);

        $this->nome_url_cancelar = 'Cancelar';


        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $this->data_inicial = dataToBrasil($this->data_inicial);
        $this->data_final = dataToBrasil($this->data_final);

        $this->ano = explode('/', $this->data_inicial)[2];

        if (
            $tipoacao == 'Edita' || !$_POST
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_numeric($this->ref_cod_turma)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigatorioConteudoAee = $instituicao['permitir_planeja_conteudos_aee'];

        $this->campoOculto('id', $this->id);
        $this->campoOculto('planejamento_aula_aee_id', $this->id);
        $this->campoOculto('obrigatorio_conteudo', $obrigatorioConteudoAee);
        $this->campoOculto('conteudos_ids', $this->conteudos_ids);        
        $this->inputsHelper()->dynamic('dataInicial', ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('dataFinal', ['required' => $obrigatorio]);      // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        if (empty($this->id)) {
            $this->inputsHelper()->dynamic(['matricula']);
        } else {
            $this->campoTextoDisabled('aluno', 'Aluno', $this->aluno);
        }

        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado]);

        $this->adicionarBNCCMultiplaEscolha();

        if ($obrigatorioConteudoAee) {
            $this->adicionarConteudosTabela($obrigatorioConteudoAee);
        }

        $this->campoMemo('ddp', 'Metodologia', $this->ddp, 100, 5, $obrigatorio);
        $this->campoMemo('recursos_didaticos', 'Recursos didáticos', $this->recursos_didaticos, 100, 5, !$obrigatorio);
        $this->campoMemo('outros', 'Outros', $this->outros, 100, 5, !$obrigatorio);

        $this->campoOculto('id', $this->id);
        $this->campoOculto('copy', $this->copy);

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function __construct()
    {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $scripts = [
            '/modules/DynamicInput/Assets/Javascripts/TodasTurmas.js',
            '/modules/Cadastro/Assets/Javascripts/PlanejamentoAulaAee.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaAeeExclusao.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaAeeEdicao.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-professores-planejamento-de-aula-aee-cad.js');
    }

    private function adicionarBNCCMultiplaEscolha()
    {

        $this->campoTabelaInicio(
            'objetivos_aprendizagem',
            'Objetivo(s) de aprendizagem',
            ['Componente curricular', "Habilidade(s)", "Especificação(ões)"]
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
            'required' => true,
            'options' => [
                'values' => $this->bncc_especificacoes,
                'all_values' => $todos_bncc_especificacoes
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('bncc_especificacoes', $options);

        $this->campoTabelaFim();
    }

    protected function adicionarConteudosTabela($obrigatorioConteudo)
    {
        $obj = new clsModulesPlanejamentoAulaConteudoAee();
        $conteudos = $obj->lista($this->id);

        for ($i = 0; $i < count($conteudos); $i++) {
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

        $this->campoTexto('conteudos', 'Conteúdos', $this->conteudo_id, 100, 2048, $obrigatorioConteudo);

        $this->campoTabelaFim();
    }


    public function Formular()
    {
        $this->title = 'Plano de aula AEE - Cadastro';
        $this->processoAp = '58';
    }
};
