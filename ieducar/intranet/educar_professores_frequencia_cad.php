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
    public $data;
    public $ref_cod_turma;
    public $ref_cod_componente_curricular;
    public $fase_etapa;
    public $ref_cod_serie;
    public $justificativa;

    public function Inicializar () {
        $this->titulo = 'Frequência - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_frequencia_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesFrequencia($this->id);
            $registro = $tmp_obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->matriculas = $registro['matriculas'];
                $this->alunos = $registro['alunos'];
                $this->ref_cod_serie = $registro['detalhes']['ref_cod_serie']; 

                $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                $retorno = 'Editar';

                $this->titulo = 'Frequência - Edição';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_frequencia_det.php?id=%d', $this->id)
            : 'educar_professores_frequencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' frequência', [
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
        
        $this->data = dataToBrasil($this->data);
        $this->ano = explode('/', $this->data)[2];

        if ($tipoacao == 'Edita' || !$_POST
            && is_numeric($this->ref_cod_instituicao)
            && is_numeric($this->ref_cod_escola)
            && is_numeric($this->ref_cod_curso)
            && is_numeric($this->ref_cod_serie)
            && is_numeric($this->ref_cod_turma)
        ) {
            $desabilitado = true;
        }

        if (is_numeric($this->ref_cod_instituicao)
            && is_numeric($this->ref_cod_escola)
            && is_numeric($this->ref_cod_curso)
            && is_numeric($this->ref_cod_serie)
        ) {
            $this->campoOculto('ref_cod_instituicao_', $this->ref_cod_instituicao);
            $this->campoOculto('ref_cod_escola_', $this->ref_cod_escola);
            $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
            $this->campoOculto('ref_cod_serie_', $this->ref_cod_serie);
            $this->campoOculto('ref_cod_componente_curricular_', $this->ref_cod_componente_curricular);
        }

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('data', ['label' => 'Data', 'required' => $obrigatorio]);
        // $this->inputsHelper()->dynamic('instituicao', ['required' => !$obrigatorio, 'instituicao' => $this->ref_cod_instituicao, 'disabled' => $desabilitado]);
        // $this->inputsHelper()->dynamic('escola', ['required' => !$obrigatorio, 'escola' => $this->ref_cod_escola, 'disabled' => $desabilitado]);
        // $this->inputsHelper()->dynamic('curso', ['required' => !$obrigatorio, 'curso' => $this->ref_cod_curso, 'disabled' => $desabilitado]);
        // $this->inputsHelper()->dynamic('serie', ['required' => !$obrigatorio, 'serie' => $this->ref_cod_serie, 'disabled' => $desabilitado]);
        // $this->inputsHelper()->dynamic('turma', ['required' => $obrigatorio, 'turma' => $this->ref_cod_turma, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('todas_Turmas', ['required' => $obrigatorio, 'turma' => $this->ref_cod_turma, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(
            'componenteCurricular',
            ['required' => !$obrigatorio, 'componenteCurricular' => $this->ref_cod_componente_curricular, 'disabled' => $desabilitado]
        );
        $this->inputsHelper()->dynamic(
            'faseEtapa',
            ['required' => $obrigatorio, 'fase_etapa' => $this->fase_etapa, 'label' => 'Etapa']
        );

        // Editar
        $maxCaracteresObservacao = 256;
        $alunos = 'Faltam informações obrigatórias.';

        if ($this->data && is_numeric($this->ref_cod_turma) && $this->fase_etapa && $this->alunos) {                  
            $alunos = '';
            $conteudo = '';

            $matriculas = $this->matriculas['refs_cod_matricula'];
            $justificativas = $this->matriculas['justificativas'];

            if (is_string($matriculas) && !empty($matriculas)) {
                $matriculas = explode(',', $matriculas);
                $justificativas = explode(',', $justificativas);
    
                for ($i = 0; $i < count($matriculas); $i++) {
                    $this->alunos[$matriculas[$i]]['presenca'] = true;
                    $this->alunos[$matriculas[$i]]['justificativa'] = $justificativas[$i];
                }
            }

            $conteudo .= '<div style="margin-bottom: 10px; float: left">';
            $conteudo .= '  <span style="display: block; float: left; width: 400px;">Nome</span>';
            $conteudo .= '  <span style="display: block; float: left; width: 180px;">Presença</span>';
            $conteudo .= '  <span style="display: block; float: left; width: 300px;">' . "Justificativa (" . $maxCaracteresObservacao . " caracteres são permitidos)" . '</span>';
            $conteudo .= '</div>';

            foreach ($this->alunos as $key => $aluno) {
                $id = $aluno['matricula'];
                $name = "alunos[" . $id . "]";
                $checked = !$aluno['presenca'] ? "checked='true'" : '';
                $disabled = !$aluno['presenca'] ? "disabled='true'" : '';

                $conteudo .= '  <div style="margin-bottom: 10px; float: left">';
                $conteudo .= '  <label style="display: block; float: left; width: 400px;">' . $aluno['nome'] . '</label>';
                $conteudo .= "  <label style='display: block; float: left; width: 180px;'>
                                    <input
                                        type='checkbox'
                                        onchange='presencaMudou(this)'
                                        id='alunos[]'
                                        name={$name}
                                        {$checked}
                                        autocomplete='off'
                                    >
                                </label>";
                $conteudo .= "  <input
                                    type='text'
                                    name='justificativa[${id}][]'
                                    style='width: 300px;'
                                    maxlength=${maxCaracteresObservacao}
                                    value='{$aluno['justificativa']}'
                                    {$disabled}
                                    autocomplete='off'
                                />";
                $conteudo .= '  </div>';
                $conteudo .= '  <br style="clear: left" />';
            }

            if ($conteudo) {
                $alunos = '<table cellspacing="0" cellpadding="0" border="0">';
                $alunos .= '<tr align="left"><td>' . $conteudo . '</td></tr>';
                $alunos .= '</table>';
            }
        }

        $this->campoRotulo('alunos_lista_', 'Alunos', "<div id='alunos'>$alunos</div>");
        $this->campoQuebra();

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        $obj = new clsPmieducarSerie();
        $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($this->ref_cod_serie);

        if ($tipo_presenca == null) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não possui uma regra de avaliação configurada.<br>';
            return false;
        }

        if ($tipo_presenca == 1 && $this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não admite frequência por componente curricular.<br>';
            return false;
        }

        if ($tipo_presenca == 2 && !$this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois o componente curricular é obrigatório para esta série.<br>';
            return false;
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
            return false;
        }

        $obj = new clsModulesFrequencia(
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_serie,
            $this->ref_cod_turma,
            $this->ref_cod_componente_curricular,
            null,
            dataToBanco($this->data),
            null,
            null,
            $this->fase_etapa,
            $this->justificativa,
        );

        $cadastrou = $obj->cadastra();

        if (!$cadastrou) {   
            $this->mensagem = 'Cadastro não realizado.<br>';
            return false;
        } else {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar() {
        $this->ref_cod_serie = $this->ref_cod_serie_;
        $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;

        $obj = new clsModulesFrequencia(
            $this->id,
            null,
            null,
            null,
            null,
            $this->ref_cod_serie,
            null,
            $this->ref_cod_componente_curricular,
            null,
            dataToBanco($this->data),
            null,
            null,
            $this->fase_etapa,
            $this->justificativa,
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir () {
        $this->ref_cod_serie = $this->ref_cod_serie_;
        $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;

        $obj = new clsModulesFrequencia(
            $this->id,
            null,
            null,
            null,
            null,
            $this->ref_cod_serie,
            null,
            $this->ref_cod_componente_curricular,
            null,
            null,
            null,
            null,
            $this->fase_etapa,
            null
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }
 
    function montaListaFrequenciaAlunos ($matriculas, $justificativas, $alunos) {
        if (is_string($matriculas) && !empty($matriculas)) {
            $matriculas = explode(',', $matriculas);
            $justificativas = explode(',', $justificativas);

            for ($i = 0; $i < count($matriculas); $i++) {
                $alunos[$matriculas[$i]]['presenca'] = true;
                $alunos[$matriculas[$i]]['justificativa'] = $justificativas[$i];
            }
        }

        $this->tabela .= ' <div style="margin-bottom: 10px;">';
        $this->tabela.= '  <span style="display: block; float: left; width: 300px; font-weight: bold">Nome</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 100px; font-weight: bold">Presença</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 300px; font-weight: bold">Justificativa</span>';
        $this->tabela .= ' </div>';
        $this->tabela .= ' <br style="clear: left" />';

        foreach ($alunos as $aluno) {
            $this->tabela .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
            $this->tabela .= "  <span style='display: block; float: left; width: 300px'>{$aluno['nome']}</span>";

            if(!$aluno['presenca']) {
                $this->tabela .= '  <label style="display: block; float: left; width: 100px;">
                                        <input type="checkbox" disabled Checked={false}>
                                    </label>';
            } else {
                $this->tabela .= '  <label style="display: block; float: left; width: 100px;">
                                        <input type="checkbox" disabled !Checked>
                                    </label>';
                $this->tabela .= "  <span style='display: block; float: left; width: 300px'>{$aluno['justificativa']}</span>";
            }

            $this->tabela .= '  </div>';
            $this->tabela .= '  <br style="clear: left" />';
        }

        $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $disciplinas .= '</table>';

        return $disciplinas;
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/Frequencia.js',
            '/modules/DynamicInput/Assets/Javascripts/TodasTurmas.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Formular () {
        $this->title = 'Frequência';
        $this->processoAp = '58';
    }
};
