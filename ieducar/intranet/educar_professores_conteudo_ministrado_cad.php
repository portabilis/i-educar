<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;

return new class extends clsCadastro {
    public $bncc;
    public $procedimento_metodologico;
    public $observacao;
    public $frequencia;

    public function Inicializar () {
        $this->titulo = 'Conteúdo Ministrado - Cadastro';

        $retorno = 'Novo';

        // $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_conteudo_ministrado_lst.php');

        // if (is_numeric($this->id)) {
        //     $tmp_obj = new clsModulesFrequencia();
        //     $registro = $tmp_obj->detalhe(
        //         $this->id
        //     );

        //     if ($registro) {
        //         // passa todos os valores obtidos no registro para atributos do objeto
        //         foreach ($registro['detalhes'] as $campo => $val) {
        //             $this->$campo = $val;
        //         }
        //         $this->matriculas = $registro['matriculas'];
        //         $this->ref_cod_serie = $registro['detalhes']['ref_cod_serie']; 

        //         $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
        //         $retorno = 'Editar';

        //         $this->titulo = 'Frequência - Edição';
        //     }
        // }

        $this->nome_url_cancelar = 'Cancelar';
        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_conteudo_ministrado_det.php?id=%d', $this->id)
            : 'educar_professores_conteudo_ministrado_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' Conteúdo ministrado', [
            url('intranet/educar_conteudo_ministrado_index.php') => 'Professores',
        ]);

        return $retorno;
    }

    public function Gerar () {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $this->campoOculto('cod_serie', 9);
        $this->campoOculto('cod_componente_curricular', 5);

        $this->inputsHelper()->dynamic(['frequencia'], ['frequencia' => $this->frequencia]);

        $helperOptions = [
            'objectName' => 'bncc',
        ];

        $todos_bncc = $this->getBNCC(9, 5)['bncc'];
        $this->bncc = array_values(array_intersect($this->bncc, $todos_bncc));

        $options = [
            'label' => 'BNCC',
            'required' => true,
            'size' => 50,
            'options' => [
                'values' => $this->bncc,
                'all_values' => $todos_bncc
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $this->campoMemo('procedimento_metodologico', 'Procedimento metodológico', $this->procedimento_metodologico, 100, 5, true);
        $this->campoMemo('observacao', 'Observação', $this->observacao, 100, 5, false);
    }

    public function Novo() {
        $obj = new clsModulesComponenteMinistrado(
            null,
            $this->frequencia,
            $this->procedimento_metodologico,
            $this->observacao,
            $this->bncc
        );

        $cadastrou = $obj->cadastra();

        if (!$cadastrou) {   
            $this->mensagem = 'Cadastro não realizado.<br>';
            return false;
        } else {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Excluir () {
        
        return false;
    }

    private function getBNCC($cod_serie, $cod_componente_curricular)
    {
        $bncc = [];
        $bncc_temp = [];
        $obj = new clsModulesBNCC();

        if ($bncc_temp = $obj->lista($cod_serie, $cod_componente_curricular)) {
            foreach ($bncc_temp as $bncc_item) {
                $id = $bncc_item['id'];
                $code = $bncc_item['code'];
                $description = $bncc_item['description'];

                $bncc[$id] = $code . ' - ' . $description;
            }
        }

        return ['bncc' => $bncc];
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/BNCC.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function Formular () {
        $this->title = 'Conteúdo ministrado';
        $this->processoAp = '58';
    }
};
