<?php
use App\Models\Frequencia;
use App\Models\ComponenteCurricularTurma;
use App\Models\ComponenteCurricularAno;
use App\Models\SerieTurma;
use App\Models\Serie;
use App\Models\Turma;
use App\Models\FrequenciaInformacoes;

return new class extends clsDetalhe {
    public $titulo;
    public $id_freq;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_ref_cod_serie;
    public $ref_ref_cod_escola;
    public $ref_cod_infra_predio_comodo;
    public $nm_turma;
    public $sgl_turma;
    public $max_aluno;
    public $multiseriada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_turma_tipo;
    public $hora_inicial;
    public $hora_final;
    public $hora_inicio_intervalo;
    public $hora_fim_intervalo;
    public $ref_cod_instituicao;
    public $ref_cod_curso;
    public $ref_cod_instituicao_regente;
    public $ref_cod_regente;

    public function Gerar()
    {
        $this->titulo = 'Frequência - Detalhe';
        $this->id_freq = $_GET['id'];

        $obj_permissoes = new clsPermissoes();

        $tmp_obj = new clsModulesFrequencia($this->id_freq);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $obj = new clsPmieducarTurma($registro['detalhes']['ref_cod_turma']);
        $resultado = $obj->getGrau();


        $obj = new clsModulesComponenteMinistrado();
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
            $this->id_freq
        )[0];

        if (isset($componenteMinistrado) && !empty($componenteMinistrado)) {
            $obj = new clsModulesComponenteMinistradoConteudo();
            $componenteMinistrado['conteudos'] = $obj->lista($componenteMinistrado['id']);
        }

        if ($registro['detalhes']['data']) {
            $this->addDetalhe(
                [
                    'Data',
                    dataToBrasil($registro['detalhes']['data'])
                ]
            );
        }

        if ($registro['detalhes']['escola']) {
            $this->addDetalhe(
                [
                    'Escola',
                    $registro['detalhes']['escola']
                ]
            );
        }

        if ($registro['detalhes']['professor']) {
            $this->addDetalhe(
                [
                    'Professor',
                    !empty($registro['detalhes']['cod_professor_registro'])
                        ? $registro['detalhes']['professor_registro']
                        : $registro['detalhes']['professor_turma']
                ]
            );
        }

        if ($registro['detalhes']['turma']) {
            $this->addDetalhe(
                [
                    'Turma',
                    $registro['detalhes']['turma']
                ]
            );
        }

        if ($registro['detalhes']['componente_curricular']) {
            $this->addDetalhe(
                [
                    $resultado == 0 ? 'Componente curricular' : 'Campo de experiência',
                    $registro['detalhes']['componente_curricular']
                ]
            );
        } else {
            $this->addDetalhe(
                [
                    $resultado == 0 ? 'Componente curricular' : 'Campo de experiência',
                    '—'
                ]
            );
        }

        if ($registro['detalhes']['etapa'] && $registro['detalhes']['fase_etapa']) {
            $this->addDetalhe(
                [
                    'Etapa',
                    $registro['detalhes']['fase_etapa'] . "º " . $registro['detalhes']['etapa']
                ]
            );
        }

        if (isset($componenteMinistrado) && $componenteMinistrado['atividades']) {
            $this->addDetalhe(
                [
                    'Registro diário de aula',
                    $componenteMinistrado['atividades']
                ]
            );
        }

        if (isset($componenteMinistrado) && is_array($componenteMinistrado['conteudos'])) {
            $this->montaListaConteudos($componenteMinistrado['conteudos']);
        }

        $ordensAulasArray = [];
        if ($registro['detalhes']['ordens_aulas']) {
            $ordensAulasArray = explode(',', $registro['detalhes']['ordens_aulas']);

            $aulas = '';

            foreach ($ordensAulasArray as $ordemAula) {
                $aulas .= $ordemAula.'º Aula, ';
            }

            $this->addDetalhe(
                [
                    'Ordens das aulas',
                    substr($aulas, 0, -2)
                ]
            );
        }





          //cadastra as informações no banco 
                    
                 
            $turmaId = $registro['detalhes']['ref_cod_turma'];
            $ComponenteId = $registro['detalhes']['ref_cod_componente_curricular'];
          

         
          
          
          
          
          
            $total_dias_letivos_realizados = 0;
            $frequencias = Frequencia::where('ref_cod_turma', $turmaId)->where('data','<=', $registro['detalhes']['data'])->get(); 
            $total_aulas = '';
            foreach($frequencias as $aulas){
                  $total_dias_letivos_realizados++;
                  
              }

          
            $serie_id = 0;
            $serie_turma = SerieTurma::where('cod_turma', $turmaId)->get(); 
            foreach($serie_turma as $serie){

                  $serie_id = $serie->ref_ref_cod_serie;
              
                  
              }
              $etapa_curso_serie = 0;
              $total_dias_letivos_turma = 0;
              $dias_series = Serie::where('cod_serie', $serie_id)->get(); 
              foreach($dias_series as $dia){

                  $total_dias_letivos_turma = $dia->dias_letivos;
                  $etapa_curso_serie = $dia->etapa_curso;
                  
              }
              $restante =  $total_dias_letivos_turma-$total_dias_letivos_realizados;
              
              if($etapa_curso_serie<6){
 

            $this->addDetalhe(
                [
                    'Informações',
                    "DIAS LETIVOS: ".$total_dias_letivos_turma."| DIAS REALIZADOS: ".$total_dias_letivos_realizados."| DIAS A REALIZAR: ".$restante
                ]
            );
                  

              }else{

                      $carga_horaria = 0;
                      $frequencias = Frequencia::where('ref_componente_curricular', $ComponenteId)->where('ref_cod_turma', $turmaId)->where('data','<=', $registro['detalhes']['data'])->get(); 
                      $total_aulas = '';
                      foreach($frequencias as $aulas){
                          $total_aulas .= $aulas->ordens_aulas.",";
                          
                      }
                      $total_aulas = substr($total_aulas, 0, -1);
                      $str_arr = preg_split ("/\,/", $total_aulas);
                      $total = count($str_arr);

                      
          
                      
                      $carga_horaria = 0;
                      $componentes = ComponenteCurricularAno::where('componente_curricular_id', $ComponenteId)->where('ano_escolar_id', $serie_id)->get(); 
                      foreach($componentes as $componente){
                          $carga_horaria = $componente->carga_horaria;
                          $carga_horaria = round($carga_horaria, 3);
                      } 
                      $aula_restante = $carga_horaria-$total;
                      if($carga_horaria ==0){
                          $aula_restante = 0;
                      }
                      
                      $this->addDetalhe(
                        [
                            'Informações',
                            "CH: ".$carga_horaria." | AULAS REALIZADAS: ".$total." | AULAS A REALIZAR: ".$aula_restante
                        ]
                    );

                    

                  
                         
              }








       

        $this->montaListaFrequenciaAlunos(
            $registro['detalhes']['ref_cod_serie'],
            $ordensAulasArray,
            $registro['matriculas']['refs_cod_matricula'],
            $registro['matriculas']['justificativas'],
            $registro['matriculas']['aulas_faltou'],
            $registro['alunos'],
            $registro['detalhes']['ref_cod_componente_curricular'],
            $registro['detalhes']['ref_cod_turma'],
            $registro['detalhes']['fase_etapa']
        );

        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_professores_frequencia_cad.php';

            $data_agora = new DateTime('now');
            $data_agora = new \DateTime($data_agora->format('Y-m-d'));

            $turma = $registro['detalhes']['cod_turma'];
            $sequencia = $registro['detalhes']['fase_etapa'];
            $obj = new clsPmieducarTurmaModulo();


            $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia, $registro['detalhes']['ref_cod_escola']);
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

            $podeEditar = false;
            if (is_array($data['inicio']) && is_array($data['fim'])) {
                for ($i=0; $i < count($data['inicio']); $i++) {
                    $data_inicio = $data['inicio'][$i];
                    $data_fim = $data['fim'][$i];

                    $podeEditar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

                    if ($podeEditar) break;
                }
            } else {
                $podeEditar = $data_agora >= $data['inicio'] && $data_agora <= $data['fim'];
            }

            $podeEditar = $podeEditar &&
                          ((!empty($registro['detalhes']['cod_professor_registro']) && $registro['detalhes']['cod_professor_registro'] == $this->pessoa_logada) ||
                           empty($registro['detalhes']['cod_professor_registro']));


            if ($podeEditar)
                $this->url_editar = 'educar_professores_frequencia_cad.php?id=' . $registro['detalhes']['id'];
        }

        $this->url_cancelar = 'educar_professores_frequencia_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da frequência', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);
    }

    function montaListaFrequenciaAlunos ($ref_cod_serie, $ordensAulasArray, $matriculas, $justificativas, $aulas_faltou, $alunos, $ref_componente_curricular, $ref_turma, $fase_etapa) {
        $obj = new clsPmieducarSerie();
        $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($ref_cod_serie);

        if (is_string($matriculas) && !empty($matriculas)) {
            $matriculas = explode(',', $matriculas);
            $justificativas = explode(',', $justificativas);
            $aulasArray = explode(';', $aulas_faltou);

            for ($i = 0; $i < count($matriculas); $i++) {
                $alunos[$matriculas[$i]]['presenca'] = true;
                $alunos[$matriculas[$i]]['justificativa'] = $justificativas[$i];
                $alunos[$matriculas[$i]]['aulas'] = explode(',', $aulasArray[$i]);
            }
        }


        $this->tabela .= ' </tr><td class="tableDetalheLinhaSeparador" colspan="3"></td><tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%">';
        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">Nome</span></th>';
        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">FA</span></th>';

        if ($tipo_presenca == 1) {
            $this->tabela .= ' <th><span style="display: block; float: left; width: 100px; font-weight: bold">Presença</span></th>';
         }

        if ($tipo_presenca == 2) {
            for ($i = 1; $i <= count($ordensAulasArray); $i++) {
                $this->tabela .= ' <th><span style="display: block; float: left; width: 100px; font-weight: bold">Aula '.$i.'</span></th>';
            }
        }

        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">Justificativa</span></th></tr>';

        $objFrequencia = new clsModulesFrequencia();
        foreach ($alunos as $aluno) {
            $qtdFaltasGravadas = $objFrequencia->getTotalFaltas($aluno['matricula'], $ref_componente_curricular);


             $checked = !$aluno['presenca'] ? "checked='true'" : '';

             $this->tabela .= "  <tr><td class='formlttd'><p>{$aluno['nome']}</p></td>";
             $this->tabela .= "  <td class='formlttd'><p>{$qtdFaltasGravadas}</p></td>";

            if ($tipo_presenca == 1) {
                $this->tabela .= "  <td style='margin: auto'><input type='checkbox' disabled {$checked}></td>";
            }

            if ($tipo_presenca == 2) {
                for ($i = 1; $i <= count($ordensAulasArray); $i++) {
                    $checked = (!in_array($i, $aluno['aulas']) ? "checked='true'" : '');
                    $this->tabela .= "  <td style='margin: auto'><input type='checkbox' disabled {$checked}></td>";
                }
            }

             $this->tabela .= "  <td class='formlttd'><p>{$aluno['justificativa']}</p></td></tr>";
         }
        $this->tabela .= '</table></div></td></tr>';
        $disciplinas  = '</table><table cellspacing="0" cellpadding="0" border="0" width="100%">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $disciplinas .= '</table>';


        $this->addDetalhe(
            [
                'Alunos',
                $disciplinas
            ]
        );
    }

    function montaListaConteudos ($conteudos) {
        for ($i=0; $i < count($conteudos); $i++) {
            $this->tabela2 .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';

            $this->tabela2 .= "  <span style='display: block; float: left'>{$conteudos[$i][planejamento_aula_conteudo][conteudo]}</span>";

            $this->tabela2 .= '  </div>';
            $this->tabela2 .= '  <br style="clear: left" />';
        }

        $conteudo  = '<table cellspacing="0" cellpadding="0" border="0">';
        $conteudo .= sprintf('<tr align="left"><td class="formlttd">%s</td></tr>', $this->tabela2);
        $conteudo .= '</table>';

        $this->addDetalhe(
            [
                'Conteúdos',
                $conteudo
            ]
        );
    }

    protected function serviceBoletim($matricula_id, $componente_curricular_id, $turma_id, $reload = false)
    {
        $matriculaId = $matricula_id;

        if (!isset($this->_boletimServiceInstances)) {
            $this->_boletimServiceInstances = [];
        }

        // set service
        if (!isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
            try {
                $params = [
                    'matricula' => $matriculaId,
                    'usuario' => \Illuminate\Support\Facades\Auth::id(),
                    'turmaId' => $turma_id,
                ];

                if (!empty($componente_curricular_id)) {
                    $params['componenteCurricularId'] = $componente_curricular_id;
                }

                $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
            } catch (Exception $e) {
                throw new CoreExt_Exception($e->getMessage());
            }
        }

        // validates service
        if (is_null($this->_boletimServiceInstances[$matriculaId])) {
            throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");
        }

        return $this->_boletimServiceInstances[$matriculaId];
    }

    public function Formular()
    {
        $this->title = 'Frequência - Detalhe';
        $this->processoAp = 58;
    }
};
