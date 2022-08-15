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
    public $data_cadastro;
    public $ref_cod_matricula;
    public $hora_inicio;
    public $hora_fim;
    public $atividades;
    public $conteudos;
    public $planejamento_aula_aee_id;

    public function Inicializar () {
        $this->titulo = 'Atendimento AEE - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_conteudo_ministrado_aee_lst.php');       

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesComponenteMinistradoAee($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro['detalhes'] != null) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->conteudos = array_column($registro['conteudos'], 'planejamento_aula_conteudo_aee_id');
                //$this->conteudos = array_column($registro['planejamento_aula_aee']['conteudo'], 'planejamento_aula_conteudo_aee_id');
                if (!$this->copy) {
                    $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                    $retorno = 'Editar';

                    $this->titulo = 'Atendimento AEE - Edição';
                }
            } else {
                $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_conteudo_ministrado_aee_det.php?id=%d', $this->id)
            : 'educar_professores_conteudo_ministrado_aee_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' Atendimento AEE', [
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
        $this->data_cadastro = dataToBrasil($this->data);
        $this->ano = explode('/', $this->data)[2];

        if ($tipoacao == 'Edita' || !$_POST
            && $this->data
            && is_numeric($this->ref_cod_turma)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        if (is_numeric($this->ref_cod_turma)) {
            $this->campoOculto('data_', dataToBanco($this->data));
            $this->campoOculto('ref_cod_turma_', $this->ref_cod_turma);
            $this->campoOculto('ref_cod_componente_curricular_', $this->ref_cod_componente_curricular);
            $this->campoOculto('fase_etapa_', $this->fase_etapa);
        }

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigatorioRegistroDiarioAtividade = $instituicao['obrigatorio_registro_diario_atividade'];
        $obrigatorioConteudo = $instituicao['permitir_planeja_conteudos'];

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('data', ['required' => $obrigatorio, 'disabled' => $desabilitado]);  // Disabled não funciona; ação colocada no javascript.
        $this->campoHora('hora_inicio', 'Hora Início', $this->hora_inicio, ['required' => $obrigatorio]);    // Disabled não funciona; ação colocada no javascript.
        $this->campoHora('hora_fim', 'Hora Fim', $this->hora_fim, ['required' => $obrigatorio]);      // Disabled não funciona; ação colocada no javascript.  
       
        if (empty($this->id)) {
            $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('componenteCurricular', ['required' => !$obrigatorio, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(['matricula']);
        } else {
            $this->campoTextoDisabled('aluno', 'Aluno', $this->aluno);
        }
       
        if (is_numeric($this->id)) {
            $servidor_id = $this->pessoa_logada;

            $obj = new clsModulesComponenteMinistradoAee();
            $id = $obj->lista(
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_turma,
                null,
                null,
                null,
                null,
                $this->fase_etapa,
                $servidor_id,
                Portabilis_Date_Utils::brToPgSQL($this->data)
            )[0]['id'];

            $this->planejamento_aula_aee_id = $id;
        }
       
        $this->campoQuebra();        

        $this->campoMemo('atividades',
            'Registro diário de aula',
            $this->atividades,
            100,
            5,
            $obrigatorioRegistroDiarioAtividade,
            '',
            '',
            false,
            false,
            'onclick',
            false
        );

        if ($obrigatorioConteudo) {
            $this->adicionarConteudosMultiplaEscolha($obrigatorioConteudo);
        }

        $this->campoMemo('observacao', 'Observação', $this->observacao, 100, 5, false);

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {       

        $data_agora = new DateTime('now');
        $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $data_cadastro =  dataToBanco($this->data);

        $turma = $this->ref_cod_turma;
        $sequencia = $this->fase_etapa;
        $obj = new clsPmieducarTurmaModulo();

        $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia);
        if ($data['inicio'] != null && $data['fim'] != null) {
            $data['inicio_periodo_lancamentos'] = explode(',', $data['inicio']);
            $data['fim_periodo_lancamentos'] = explode(',', $data['fim']);

            array_walk($data['inicio_periodo_lancamentos'], function(&$data_inicio, $key) {
                $data_inicio = new \DateTime($data_inicio);
            });

            array_walk($data['fim_periodo_lancamentos'], function(&$data_fim, $key) {
                $data_fim = new \DateTime($data_fim);
            });
        }

        $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
        $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));

        $podeRegistrar = false;
        if (is_array($data['inicio_periodo_lancamentos']) && is_array($data['fim_periodo_lancamentos'])) {
            for ($i=0; $i < count($data['inicio_periodo_lancamentos']); $i++) {
                $data_inicio = $data['inicio_periodo_lancamentos'][$i];
                $data_fim = $data['fim_periodo_lancamentos'][$i];

                $podeRegistrar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

                if ($podeRegistrar) break;
            }
            $podeRegistrar = $podeRegistrar && new DateTime($data_cadastro) >= $data['inicio'] && new DateTime($data_cadastro) <= $data['fim'];
        } else {
            $podeRegistrar = new DateTime($data_cadastro) >= $data['inicio'] && new DateTime($data_cadastro) <= $data['fim'];
            $podeRegistrar = $podeRegistrar && $data_agora >= $data['inicio'] && $data_agora <= $data['fim'];
        }

        if (!$podeRegistrar) {
            $this->mensagem = 'Cadastro não realizado, pois não é mais possível submeter frequência para esta etapa.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_cad.php');
        }
       
            $obj = new clsModulesComponenteMinistradoAee(
                null,
                $this->data,
                $this->hora_inicio,
                $this->hora_fim,
                $this->ref_cod_matricula,
                $this->atividades,
                $this->observacao,
                $this->conteudos             
            );

            $cadastrou = $obj->cadastra();

            if (!$cadastrou) {
                    $this->mensagem = 'Cadastro não realizado.<br>';
                    $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_cad.php');
                } else {

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_lst.php');
        }

        return false;
    }

    public function Editar() {
        // $this->data = $this->data_;
        // $this->ref_cod_turma = $this->ref_cod_turma_;
        // $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;
        // $this->fase_etapa = $this->fase_etapa_;

        // $obj = new clsPmieducarTurma();
        // $serie = $obj->lista($this->ref_cod_turma)[0]['ref_ref_cod_serie'];

        // $obj = new clsModulesFrequencia(
        //     $this->id,
        //     null,
        //     null,
        //     null,
        //     null,
        //     $serie,
        //     null,
        //     $this->ref_cod_componente_curricular,
        //     null,
        //     $this->data,
        //     null,
        //     null,
        //     $this->fase_etapa,
        //     $this->justificativa,
        // );

        // $editou = $obj->edita();

        // $obj = new clsModulesComponenteMinistradoAee();
        // $componenteMinistrado = $obj->lista(
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     $this->id
        // )[0];

        // if ($componenteMinistrado) {
            $obj = new clsModulesComponenteMinistradoAee(               
                $this->id,
                $this->data,
                $this->hora_inicio,
                $this->hora_fim,
                $this->atividades,
                $this->observacao,
                $this->conteudos
            );

            $editou = $obj->edita();

            //die(var_dump( $obj));

            if ($editou) {
                $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
                $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_lst.php');
            }
    
            $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    
            return false;
        //}


        
    }

    public function Excluir () {        

        $obj = new clsModulesComponenteMinistradoAee();
        $componenteMinistrado = $obj->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->id
        )[0];

        if ($componenteMinistrado) { 
            $obj = new clsModulesComponenteMinistradoAee($componenteMinistrado['id']);
            $excluiu = $obj->excluir();

            if ($excluiu) {
                $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_lst.php');
            } else {
                $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
            }
            
            return false;            
        }
       
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/FrequenciaAee.js',
            '/modules/DynamicInput/Assets/Javascripts/TodasTurmas.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    protected function adicionarConteudosMultiplaEscolha() {
        // ESPECIFICAÇÕES
        /*$helperOptions = [
            'objectName' => 'especificacoes',
        ];

        //$todas_especificacoes = $this->getEspecificacoes($this->planejamento_aula_id);

        $options = [
            'label' => 'Especificações',
            'required' => false,
            'options' => [
                'values' => $this->especificacoes,
                'all_values' => /*$todas_especificacoes*//*[]
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);*/

        // CONTEUDOS
        $helperOptions = [
            'objectName' => 'conteudos',
        ];

        $todos_conteudos = $this->getConteudos($this->planejamento_aula_aee_id);

        $options = [
            'label' => 'Objetivo(s) do conhecimento/conteúdo',
            'required' => true,
            'options' => [
                'values' => $this->conteudos,
                'all_values' => $todos_conteudos
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
    }

    private function getConteudos($planejamento_aula_aee_id = null)
    {
        if (is_numeric($planejamento_aula_aee_id)) {
            $rows = [];

           $obj = new clsModulesPlanejamentoAulaConteudoAee();
            $conteudos = $obj->lista2($planejamento_aula_aee_id);

            foreach ($conteudos as $key => $conteudo) {
                $rows[$conteudo['id']] = $conteudo['conteudo'];
            }

            return $rows;
        }


        return [];
    }

    public function Formular () {
        $this->title = 'Atendimento AEE - Cadastro';
        $this->processoAp = '58';
    }
};