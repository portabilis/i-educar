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
    public $bnccs;
    public $conteudo_id;

    public function Inicializar () {
        $this->titulo = 'Planejamento de aula - Cadastro';

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
                $this->bncc = array_column($registro['bnccs'], 'bncc_id');

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
        $this->data_inicial = dataToBrasil($this->data_inicial);
        $this->data_final = dataToBrasil($this->data_final);

        $this->ano = explode('/', $this->data_inicial)[2];

        if ($tipoacao == 'Edita' || !$_POST
            && $this->data_inicial != ''
            && $this->data_final != ''
            && is_numeric($this->ref_cod_turma)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('dataInicial', ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('dataFinal', ['required' => $obrigatorio]);      // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('componenteCurricular', ['required' => !$obrigatorio, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado]);
        
        $this->campoMemo('ddp','Desdobramento didático pedagógico', $this->ddp, 100, 5, $obrigatorio);
        $this->campoMemo('atividades','Atividades', $this->atividades, 100, 5, $obrigatorio);

        $this->adicionarBNCCMultiplaEscolha();
        $this->adicionarConteudosTabela();

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        $obj = new clsPmieducarTurma();
        $serie = $obj->lista($this->ref_cod_turma)[0]['ref_ref_cod_serie'];

        $obj = new clsPmieducarSerie();
        $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($serie);

        if ($tipo_presenca == null) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não possui uma regra de avaliação configurada.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        }

        if ($tipo_presenca == 1 && $this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não admite frequência por componente curricular.<br>';
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        }

        if ($tipo_presenca == 2 && !$this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois o componente curricular é obrigatório para esta série.<br>';
            //$this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        }

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
            $this->mensagem = 'Cadastro não realizado, pois não é mais possível submeter frequência para esta etapa.<br>';
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
           $this->conteudos
        );

        $existe = $obj->existe();
        if ($existe){
            $this->mensagem = 'Cadastro não realizado, pois este planejamento de aula já existe.<br>';
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
        $this->data_inicial = $this->data_inicial;
        $this->data_final = $this->data_final;
        $this->ddp = $this->ddp;
        $this->atividades = $this->atividades;
        $this->bnccs = $this->bnccs;
        $this->conteudos = $this->conteudos;

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
            $this->conteudos
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
            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->listaTurma($turma, $ref_cod_componente_curricular)) {
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
            'label' => 'Objetivos de aprendizagem/habilidades',
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
        // dd($this->id);
        $obj = new clsModulesPlanejamentoAulaConteudo(null, $this->id);
        $conteudos = $obj->detalhe();

        for ($i=0; $i < count($conteudos); $i++) { 
            $rows[$i][] = $conteudos[$i]['conteudo'];
        }

        $this->campoTabelaInicio(
            'conteudos',
            'Conteúdo(s)',
            [
                'Conteúdo',
            ],
            $rows
        );

        $this->campoTexto('conteudos','Conteúdos', $this->conteudo_id, 100, 2048, true);   

        $this->campoTabelaFim();
    }

    public function Formular () {
        $this->title = 'Planejamento de aula - Cadastro';
        $this->processoAp = '58';
    }
};
