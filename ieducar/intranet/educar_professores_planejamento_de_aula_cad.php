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
    public $turma_id;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $bnccs;
    public $conteudos;

    public function Inicializar () {
        $this->titulo = 'Planejamento de aula - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_planejamento_de_aula_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesPlanejamentoAulaBNCC($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->data_inicial = $registro['data_inicial'];
                $this->data_final = $registro['data_final']; 
                $this->turma_id = $registro['turma_id'];

                $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                $retorno = 'Editar';

                $this->titulo = 'Planejamento de aula - Edição';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_planejamento_de_aula_det.php?id=%d', $this->id)
            : 'educar_professores_planejamento_de_aula_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' planejamento de aula', [
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
        $this->data_inicial = dataToBrasil($this->data);
        $this->ano = explode('/', $this->data_inicial)[2];

        if ($tipoacao == 'Edita' || !$_POST
            && $this->data_inicial
            && $this->data_final
            && is_numeric($this->turma_id)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('dataInicial', ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('dataFinal', ['required' => $obrigatorio]);      // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        $helperOptions = [
            'objectName' => 'bncc',
        ];

        $todos_bncc = $this->getBNCC($this->frequencia)['bncc'];

        $options = [
            'label' => 'BNCC',
            'required' => false,
            'size' => 50,
            'options' => [
                'values' => $this->bncc,
                'all_values' => $todos_bncc
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
        $this->campoMemo('ddp','DDP',$this->ddp, 100, 5, true);
        $this->campoMemo('atividades','Atividades',$this->atividades, 100, 5, true); 
        $this->campoMemo('conteudos','Conteúdos',$this->conteudos, 100, 5, true);   

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        $data_agora = new DateTime('now');
        $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $obj = new clsModulesPlanejamentoAulaBNCC(
           null,
           $this->turma_id,
           $this->data_inicial,
           $this->data_final,
           $this->ddp, 
           $this->atividades,
           $this->bnccs,
           $this->conteudos,

        );

        $existe = $obj->existe();
        if ($existe){
            $this->mensagem = 'Cadastro não realizado, pois esta frequência já existe.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $cadastrou = $obj->cadastra();

        if (!$cadastrou) {   
            $this->mensagem = 'Cadastro não realizado.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        } else {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar() {

        $this->data_inicial = $this->data_inicial;
        $this->data_final = $this->data_final;
        $this->ddp = $this->ddp;
        $this->atividades = $this->atividades;
        $this->bnccs = $this->bnccs;
        $this->conteudos = $this->conteudos;

        $obj = new clsModulesPlanejamentoAulaBNCC(
            $this->id     
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
 
    private function getBNCC($frequencia = null)
    {
        if (is_numeric($frequencia)) {
            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->lista($frequencia)) {
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
            '/modules/Cadastro/Assets/Javascripts/BNCC.js',
            '/modules/Cadastro/Assets/Javascripts/PlanejamentoAula.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Formular () {
        $this->title = 'Planejamento de aula - Cadastro';
        $this->processoAp = '58';
    }
};
