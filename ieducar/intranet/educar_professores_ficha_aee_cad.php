<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;

return new class extends clsCadastro
{
    public $id;
    public $ref_cod_turma;
    public $ref_cod_matricula;
    public $data;
    public $necessidades_aprendizagema;
    public $caracterizacao_pedagogica;

    public function Inicializar()
    {
        $this->titulo = 'Ficha AEE - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];
        $this->copy = $_GET['copy'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_ficha_aee_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesPlanejamentoAulaAee4($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro['detalhes'] != null) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }               

                if (!$this->copy) {
                    $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                    $retorno = 'Editar';

                    $this->titulo = 'Ficha AEE - Edição';
                }
            } else {
                $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_ficha_aee_det.php?id=%d', $this->id)
            : 'educar_professores_ficha_aee_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' Ficha AEE', [
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
            && is_numeric($this->ref_cod_componente_curricular_array)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('data', ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.        
        $this->inputsHelper()->dynamic(['turma', 'matricula']);
        // Montar o inputsHelper->select \/
        // Cria lista de Turmas
        $obj_turma = new clsPmieducarTurma();        
        $lista_turmas = $obj_turma->lista_turmas_aee();
        $turma_resources = ['' => 'Selecione uma Turma'];
        foreach ($lista_turmas as $reg) {
            $turma_resources["{$reg['cod_turma']}"] = "{$reg['nm_turma']} - ({$reg['nome']})";
        }

        // Turmas
        $options = [
            'label' => 'Turma',
            'required' => true,
            'resources' => $turma_resources
        ];
        $this->inputsHelper()->select('ref_cod_turma', $options);

        // Montar o inputsHelper->select \/
        // Cria lista de Alunos
        $obj_aluno = new clsPmieducarMatricula();       
        $lista_alunos = $obj_aluno->lista_matriculas_aee();
        $aluno_resources = ['' => 'Selecione um Aluno'];
        foreach ($lista_alunos as $reg) {
            $aluno_resources["{$reg['cod_matricula']}"] = "{$reg['cod_matricula']} - {$reg['nome']}";
        }

        // Alunos
        $options = [
            'label' => 'Aluno',
            'required' => true,
            'resources' => $aluno_resources
        ];
        $this->inputsHelper()->select('ref_cod_matricula', $options);

        //$this->adicionarBNCCMultiplaEscolha();       

        $this->campoMemo('necessidades_aprendizagem', 'Necessidades de Aprendizagem', $this->necessidade_aprendizagem, 100, 5, !$obrigatorio);
        $this->campoMemo('caracterizacao_pedagogica', 'Caracterização Pedagógica', $this->caracterizacao_pedagogica, 100, 5, !$obrigatorio);

        $this->campoOculto('id', $this->id);
        $this->campoOculto('copy', $this->copy);

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    
        public function Novo() {
            $obj = new clsModulesFichaAee(
                null,
                $this->ref_cod_turma,
                $this->ref_cod_matricula,
                $this->data,
                $this->necessidades_aprendizagem,
                $this->caracterizacao_pedagogica
            );
    
            $cadastrou = $obj->cadastra();
    
            if (!$cadastrou) {
                $this->mensagem = 'Cadastro não realizado.<br>';
                $this->simpleRedirect('educar_professores_ficha_aee_cad.php');
            } else {
                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
            }
    
            $this->mensagem = 'Cadastro não realizado.<br>';
    
            return false;
        }

    public function Editar()
    {
        $obj = new clsModulesPlanejamentoAulaAee(
            $this->id,
            null,
            null,
            null,
            null,
            null,
            $this->ddp,
            $this->bncc,
            $this->conteudos,
            $this->recursos_didaticos,           
            $this->outros
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsModulesPlanejamentoAulaAee($this->id);

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }   

    public function __construct()
    {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $scripts = [           
            '/modules/Cadastro/Assets/Javascripts/PlanejamentoAula.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaExclusao.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaEdicao.js',
            '/modules/Cadastro/Assets/Javascripts/PlanoAulaDuplicacao.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    // public function makeExtra()
    // {
    //     return file_get_contents(__DIR__ . '/scripts/extra/educar-professores-planejamento-de-aula-aee-cad.js');
    // }

    private function adicionarBNCCMultiplaEscolha()
    {
        $this->campoTabelaInicio(
            'objetivos_aprendizagem',
            'Necessidades de aprendizagem',
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
            'required' => true,
            'options' => [
                'values' => $this->bncc_especificacoes,
                'all_values' => $todos_bncc_especificacoes
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('bncc_especificacoes', $options);

        $this->campoTabelaFim();
    }

    

    public function Formular()
    {
        $this->title = 'Plano de aula AEE - Cadastro';
        $this->processoAp = '58';
    }
};