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
    public $ref_cod_componente_curricular;
    public $fase_etapa;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $referencias;
    public $bnccs;
    public $conteudo_id;

    public function Inicializar () {
        $this->titulo = 'Plano de aula - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_planejamento_de_aula_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesPlanejamentoAula($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->bncc = array_column($registro['bnccs'], 'id');

                $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                $retorno = 'Editar';

                $this->titulo = 'Plano de aula - Edição';
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
            && is_numeric($this->ref_cod_componente_curricular)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('dataInicial', ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('dataFinal', ['required' => $obrigatorio]);      // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('componenteCurricular', ['required' => $obrigatorio, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado]);
    
        $this->adicionarBNCCMultiplaEscolha();
        $this->adicionarConteudosTabela();

        $this->campoMemo('ddp','Metodologia', $this->ddp, 100, 5, !$obrigatorio);
        $this->campoMemo('atividades','Atividades/Avaliações', $this->atividades, 100, 5, !$obrigatorio);
        $this->campoMemo('referencias','Referências', $this->referencias, 100, 5, !$obrigatorio);

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        $data_agora = new DateTime('now');
        $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $turma = $this->ref_cod_turma;
        $sequencia = $this->fase_etapa;
        $obj = new clsPmieducarTurmaModulo();

        $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia);
        if ($data['inicio'] != null && $data['fim'] != null) {
            $data['inicio'] = explode(',', $data['inicio']);
            $data['fim'] = explode(',', $data['fim']);

            array_walk($data['inicio'], function(&$data_inicio, $key) {
                $data_inicio = new \DateTime($data_inicio);
            });

            array_walk($data['fim'], function(&$data_fim, $key) {
                $data_fim = new \DateTime($data_fim);
            });
        } else {
            $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
            $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));
        }

        $podeRegistrar = false;
        if (is_array($data['inicio']) && is_array($data['fim'])) {
            for ($i=0; $i < count($data['inicio']); $i++) {
                $data_inicio = $data['inicio'][$i];
                $data_fim = $data['fim'][$i];

                $podeRegistrar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

                if ($podeRegistrar) break;
            }     
        } else {
            $podeRegistrar = $data_agora >= $data['inicio'] && $data_agora <= $data['fim'];
        }

        if (!$podeRegistrar) {
            $this->mensagem = 'Cadastro não realizado, pois não é mais possível submeter plano para esta etapa.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        }

        $obj = new clsModulesPlanejamentoAula(
           null,
           $this->ref_cod_turma,
           $this->ref_cod_componente_curricular,
           $this->fase_etapa,
           dataToBanco($this->data_inicial),
           dataToBanco($this->data_final),
           $this->ddp, 
           $this->atividades,
           $this->bncc,
           $this->conteudos,
           $this->referencias
        );

        $existe = $obj->existe();
        if ($existe){
            $this->mensagem = 'Cadastro não realizado, pois este plano de aula já existe.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
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
 
    private function getBNCCTurma($turma = null, $ref_cod_componente_curricular = null)
    {
        if (is_numeric($turma)) {
            $obj = new clsPmieducarTurma($turma);
            $resultado = $obj->getGrau();

            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->listaTurma($resultado, $turma, $ref_cod_componente_curricular)) {
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
            '/modules/Cadastro/Assets/Javascripts/PlanejamentoAula.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    private function adicionarBNCCMultiplaEscolha($obrigatorio = true) {
        $helperOptions = [
            'objectName' => 'bncc',
        ];

        $todos_bncc = $this->getBNCCTurma($this->ref_cod_turma, $this->ref_cod_componente_curricular)['bncc'];

        $options = [
            'label' => 'Objetivos de aprendizagem/habilidades (BNCC)',
            'required' => $obrigatorio,
            'size' => 50,
            'options' => [
                'values' => $this->bncc,
                'all_values' => $todos_bncc
            ]
        ];

        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
    }

    protected function adicionarConteudosTabela()
    {
        $obj = new clsModulesPlanejamentoAulaConteudo();
        $conteudos = $obj->lista($this->id);

        for ($i=0; $i < count($conteudos); $i++) { 
            $rows[$i][] = $conteudos[$i]['conteudo'];
        }

        $this->campoTabelaInicio(
            'conteudos',
            'Objetivo(s) do conhecimento/conteúdo',
            [
                'Objetivo(s)',
            ],
            $rows
        );

        $this->campoTexto('conteudos','Conteúdos', $this->conteudo_id, 100, 2048, true);   

        $this->campoTabelaFim();
    }

    public function Formular () {
        $this->title = 'Plano de aula - Cadastro';
        $this->processoAp = '58';
    }
};
