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

        $obrigatorio = true;

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic('data', ['required' => $obrigatorio, 'disabled' => $desabilitado]);  // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('componenteCurricular', ['required' => !$obrigatorio, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado]);

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

 
            $conteudo .= '  </tr><td class="tableDetalheLinhaSeparador" colspan="3"></td><tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%"><tr>';
            $conteudo .= '  <th><p>'."Nome".'</p></th>';
            $conteudo .= '  <th><p>'."Presença".'</p></th>';
            $conteudo .= '  <th><p>'."Justificativa".'</p></th></tr>';
            $conteudo .= '  </td></tr></table>';

            foreach ($this->alunos as $key => $aluno) {
                $id = $aluno['matricula'];
                $name = "alunos[" . $id . "]";
                $checked = !$aluno['presenca'] ? "checked='true'" : '';
                $disabled = !$aluno['presenca'] ? "disabled='true'" : '';

                $conteudo .= '  <tr><td class="formlttd">';
                $conteudo .= '  <label>' . $aluno['nome'] . '</label></td>';
                $conteudo .= "  <td><label>
                                    <input
                                        type='checkbox'
                                        onchange='presencaMudou(this)'
                                        id='alunos[]'
                                        name={$name}
                                        {$checked}
                                        autocomplete='off'
                                    >
                                </label></td>";
                $conteudo .= "  <td><input
                                    type='text'
                                    name='justificativa[${id}][]'
                                    style='width: 100%;'
                                    maxlength=${maxCaracteresObservacao}
                                    value='{$aluno['justificativa']}'
                                    {$disabled}
                                    autocomplete='off'
                                />";
                $conteudo .= '  </td>';
                $conteudo .= '  <br style="clear: left" />';
            }

            if ($conteudo) {
                $alunos .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
                $alunos .= '<tr align="left"><td></td></tr>';
                $alunos .= '<tr align="left"><td>' . $conteudo . '</td></tr>';
                $alunos .= '</table>';
            }
        }

        $this->campoRotulo('alunos_lista_', 'Alunos', "<div id='alunos'>$alunos</div>");
        $this->campoQuebra();

        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        $obj = new clsPmieducarTurma();
        $serie = $obj->lista($this->ref_cod_turma)[0]['ref_ref_cod_serie'];

        $obj = new clsPmieducarSerie();
        $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($serie);

        if ($tipo_presenca == null) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não possui uma regra de avaliação configurada.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        if ($tipo_presenca == 1 && $this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não admite frequência por componente curricular.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        if ($tipo_presenca == 2 && !$this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois o componente curricular é obrigatório para esta série.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

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
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $obj = new clsModulesFrequencia(
            null,
            null,
            null,
            null,
            null,
            $serie,
            $this->ref_cod_turma,
            $this->ref_cod_componente_curricular,
            null,
            $data_cadastro,
            null,
            null,
            $this->fase_etapa,
            $this->justificativa,
        );

        $existe = $obj->existe();
        if ($existe){
            $this->mensagem = 'Cadastro não realizado, pois esta frequência já existe.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $cadastrou = $obj->cadastra();

        if (!$cadastrou) {   
            $this->mensagem = 'Cadastro não realizado.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        } else {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar() {
        $this->data = $this->data_;
        $this->ref_cod_turma = $this->ref_cod_turma_;
        $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;
        $this->fase_etapa = $this->fase_etapa_;

        $obj = new clsPmieducarTurma();
        $serie = $obj->lista($this->ref_cod_turma)[0]['ref_ref_cod_serie'];

        $obj = new clsModulesFrequencia(
            $this->id,
            null,
            null,
            null,
            null,
            $serie,
            null,
            $this->ref_cod_componente_curricular,
            null,
            $this->data,
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
        $this->ref_cod_turma = $this->ref_cod_turma_;
        $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;

        $obj = new clsPmieducarTurma();
        $serie = $obj->lista($this->ref_cod_turma)[0]['ref_ref_cod_serie'];

        $obj = new clsModulesFrequencia(
            $this->id,
            null,
            null,
            null,
            null,
            $serie,
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

        $this->tabela .= ' <tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%"><tr class="tableHeader">';
        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">'+'Nome'+'</span></th>';
        $this->tabela .= ' <th><span style="display: block; float: left; width: 100px; font-weight: bold">'+'Presença'+'</span></th>';
        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">'+'Justificativa'+'</span></th>';
        $this->tabela .= ' </tr>';
        $this->tabela .= ' <tr><td class="tableDetalheLinhaSeparador" colspan="3"></td></tr>';

        foreach ($alunos as $aluno) {
            $this->tabela .= '  <td  class="colorFont">';
            $this->tabela .= "  <p>{$aluno['nome']}</p></td>";

            if(!$aluno['presenca']) {
                $this->tabela .= '  <td >
                                        <input type="checkbox" disabled Checked={false}>
                                    </td>';
            } else {
                $this->tabela .= '  <td >
                                        <input type="checkbox" disabled !Checked>
                                    </td>';
                $this->tabela .= "  <td><p>{$aluno['justificativa']}</p></td>";
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
        $this->title = 'Frequência - Cadastro';
        $this->processoAp = '58';
    }
};
