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
    public $data;
    public $ref_cod_turma;
    public $ref_cod_matricula;
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
            $tmp_obj = new clsModulesFichaAee($this->id);
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

        $this->data = dataToBrasil($this->data);

        if (
            $tipoacao == 'Edita' || !$_POST
            && $this->data != ''
            && is_numeric($this->ref_cod_turma)
            && is_numeric($this->ref_cod_matricula)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
        $this->inputsHelper()->dynamic('data', ['required' => $obrigatorio]);

        if (empty($this->id)) {
            $this->campoOculto('ficha_aee_id', $this->id);
            $this->campoOculto('copy', $this->copy);
            $this->inputsHelper()->dynamic(['turma', 'matricula']);
            // Montar o inputsHelper->select \/
            // Cria lista de Turmas
            $obj_turma = new clsPmieducarTurma();
            $lista_turmas = $obj_turma->lista_turmas_aee($this->pessoa_logada);
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
        } else {
            $this->campoTextoDisabled('aluno', 'Aluno', $this->aluno);
            $this->campoTextoDisabled('turma', 'Turma', $this->turma);

            $this->campoOculto('id', $this->id);
            $this->campoOculto('copy', $this->copy);
            $this->campoOculto('data', $this->data);
            $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);
            $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
        }

        $this->campoMemo('caracterizacao_pedagogica', 'Caracterização Pedagógica', $this->caracterizacao_pedagogica, 100, 5, !$obrigatorio);
        $this->campoMemo('necessidades_aprendizagem', 'Necessidades de Aprendizagem', $this->necessidades_aprendizagem, 100, 5, !$obrigatorio);       
    }

    public function __construct()
    {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/FichaAeeExclusao.js',
            '/modules/Cadastro/Assets/Javascripts/FichaAeeEdicao.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-professores-ficha-aee-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Ficha AEE - Cadastro';
        $this->processoAp = '58';
    }
};
