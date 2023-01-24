<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacyRemedialRule;
use App\Models\LegacySchoolClass;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyDisciplineScoreStudent;
use App\Models\SerieTurma;
use App\Models\NotaExame;
use App\Models\RegraAvaliacaoSerieAno;
use App\Models\RegraAvaliacaoRecuperacao;
use App\Models\RegraAvaliacao;
use App\Process;
use App\Services\ReleasePeriodService;
use App\Services\RemoveHtmlTagsStringService;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DiarioApiController extends ApiCoreController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';
    protected $_processoAp = 642;
    protected $_currentMatriculaId;

    private RemoveHtmlTagsStringService $removeHtmlTagsService;

    public function __construct()
    {
        parent::__construct();
        $this->removeHtmlTagsService = new RemoveHtmlTagsStringService();
    }

    protected function validatesCanChangeDiarioForAno()
    {
        $escola = App_Model_IedFinder::getEscola($this->getRequest()->escola_id);

        $ano = new clsPmieducarEscolaAnoLetivo();
        $ano->ref_cod_escola = $this->getRequest()->escola_id;
        $ano->ano = $this->getRequest()->ano;
        $ano = $ano->detalhe();

        $anoLetivoEncerrado = is_array($ano) && count($ano) > 0 &&
            $ano['ativo'] == 1 && $ano['andamento'] == 2;

        if ($escola['bloquear_lancamento_diario_anos_letivos_encerrados'] == '1' && $anoLetivoEncerrado) {
            $this->messenger->append("O ano letivo '{$this->getRequest()->ano}' está encerrado, esta escola está configurada para não permitir alterar o diário de anos letivos encerrados.");

            return false;
        }

        $objBloqueioAnoLetivo = new clsPmieducarBloqueioAnoLetivo($this->getRequest()->instituicao_id, $this->getRequest()->ano);
        $bloqueioAnoLetivo = $objBloqueioAnoLetivo->detalhe();

        if ($bloqueioAnoLetivo) {
            $dataAtual = strtotime(date('Y-m-d'));
            $data_inicio = strtotime($bloqueioAnoLetivo['data_inicio']);
            $data_fim = strtotime($bloqueioAnoLetivo['data_fim']);

            if ($dataAtual < $data_inicio || $dataAtual > $data_fim) {
                $this->messenger->append('O lançamento de notas nessa instituição está bloqueado nesta data.');

                return false;
            }
        }

        return true;
    }

    protected function validatesRegraAvaliacaoHasNota()
    {
        $isValid = $this->serviceBoletim()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM;

        if (!$isValid) {
            $this->messenger->append('Nota não lançada, pois a regra de avaliação não utiliza nota.');
        }

        return $isValid;
    }

    protected function validatesRegraAvaliacaoHasFormulaRecuperacao()
    {
        $isValid = $this->getRequest()->etapa != 'Rc' ||
        !is_null($this->serviceBoletim()->getRegra()->formulaRecuperacao);

        if (!$isValid) {
            $this->messenger->append('Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.');
        }

        return $isValid;
    }

    protected function validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao()
    {
        $isValid = $this->getRequest()->etapa != 'Rc' ||
            ($this->serviceBoletim()->getRegra()->formulaRecuperacao->get('tipoFormula') ==
            FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO);

        if (!$isValid) {
            $this->messenger->append('Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.');
        }

        return $isValid;
    }

    protected function validatesPreviousNotasHasBeenSet()
    {
        $etapaId = $this->getRequest()->etapa;
        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $serviceBoletim = $this->serviceBoletim();

        try {
            return $serviceBoletim->verificaNotasLancadasNasEtapasAnteriores(
                $etapaId,
                $componenteCurricularId
            );
        } catch (MissingStagesException $exception) {
            $this->messenger->append($exception->getMessage());
            $this->appendResponse('error', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'extra' => $exception->getExtraInfo(),
            ]);
        } catch (Exception $e) {
            $this->messenger->append($e->getMessage());
        }

        return false;
    }

    protected function validatesPreviousFaltasHasBeenSet()
    {
        $etapaId = $this->getRequest()->etapa;
        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $serviceBoletim = $this->serviceBoletim();

        try {
            return $serviceBoletim->verificaFaltasLancadasNasEtapasAnteriores(
                $etapaId,
                $componenteCurricularId
            );
        } catch (Exception $e) {
            $this->messenger->append($e->getMessage());
        }

        return false;
    }

    // post/ delete parecer validations

    protected function validatesEtapaParecer()
    {
        $isValid = false;
        $etapa = $this->getRequest()->etapa;

        $tiposParecerAnual = [RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL];

        $parecerAnual = in_array(
            $this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
            $tiposParecerAnual
        );

        if ($parecerAnual && $etapa != 'An') {
            $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado 'An' e foi recebido '{$etapa}'.");
        } elseif (!$parecerAnual && $etapa == 'An') {
            $this->messenger->append('Valor inválido para o atributo \'etapa\', é esperado um valor diferente de \'An\'.');
        } else {
            $isValid = true;
        }

        return $isValid;
    }

    protected function validatesPresenceOfComponenteCurricularIdIfParecerComponente()
    {
        $tiposParecerComponente = [RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE];

        $parecerPorComponente = in_array(
            $this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
            $tiposParecerComponente
        );

        return (!$parecerPorComponente) || $this->validatesPresenceOf('componente_curricular_id');
    }

    // post parecer validations

    protected function validatesRegraAvaliacaoHasParecer()
    {
        $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
        $isValid = $tpParecer != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM;

        if (!$isValid) {
            $this->messenger->append('Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.');
        }

        return $isValid;
    }

    // delete nota validations

    protected function validatesInexistenceOfNotaExame()
    {
        $isValid = true;

        if ($this->getRequest()->etapa != 'Rc') {
            $notaExame = $this->getNotaAtual($etapa = 'Rc');
            $isValid = empty($notaExame);

            if (!$isValid) {
                $this->messenger->append('Nota da matrícula ' . $this->getRequest()->matricula_id . ' somente pode ser removida, após remover nota do exame.', 'error');
            }
        }

        return $isValid;
    }

    protected function validatesInexistenceNotasInNextEtapas()
    {
        $etapasComNota = [];

        if (is_numeric($this->getRequest()->etapa)) {
            $etapas = $this->serviceBoletim()->getOption('etapas');
            $etapa = $this->getRequest()->etapa + 1;

            for ($etapa; $etapa <= $etapas; $etapa++) {
                $nota = $this->getNotaAtual($etapa);

                if (!empty($nota)) {
                    $etapasComNota[] = $etapa;
                }
            }

            if (!empty($etapasComNota)) {
                $msg = 'Nota somente pode ser removida, após remover as notas lançadas nas etapas posteriores: ' .
                join(', ', $etapasComNota) . '.';
                $this->messenger->append($msg, 'error');
            }
        }

        return empty($etapasComNota);
    }

    // delete falta validations

    protected function validatesInexistenceFaltasInNextEtapas()
    {
        $etapasComFalta = [];

        if (is_numeric($this->getRequest()->etapa)) {
            $etapas = $this->serviceBoletim()->getOption('etapas');
            $etapa = $this->getRequest()->etapa + 1;

            for ($etapa; $etapa <= $etapas; $etapa++) {
                $falta = $this->getFaltaAtual($etapa);

                if (!empty($falta)) {
                    $etapasComFalta[] = $etapa;
                }
            }

            if (!empty($etapasComFalta)) {
                $this->messenger->append('Falta somente pode ser removida, após remover as faltas lançadas nas etapas posteriores: ' . join(', ', $etapasComFalta) . '.', 'error');
            }
        }

        return empty($etapasComFalta);
    }

    protected function validatesPresenceOfMatriculaIdOrComponenteCurricularId()
    {
        if (empty($this->getRequest()->componente_curricular_id) && empty($this->getRequest()->matricula_id)) {
            $this->messenger->append('É necessário receber matricula_id ou componente_curricular_id.', 'error');

            return false;
        }

        return true;
    }

    protected function validatesPeriodoLancamentoFaltasNotas($showMessage = true)
    {
        if ($this->user()->can('modify', Process::POST_OUT_PERIOD)) {
            return true;
        }

        $service = new ReleasePeriodService();
        if ($service->canPostNow(
            $this->getRequest()->escola_id,
            $this->getRequest()->turma_id,
            $this->getRequest()->etapa,
            $this->getRequest()->ano_escolar
        )
        ) {
            return true;
        }

        if ($showMessage) {
            $this->messenger->append('Não é permitido realizar esta alteração fora do período de lançamento de notas/faltas.', 'error');
        }

        return false;
    }

    // responders validations

    protected function canGetMatriculas()
    {
        return $this->validatesPresenceOf(['instituicao_id',
            'escola_id',
            'curso_id',
            'curso_id',
            'serie_id',
            'turma_id',
            'ano',
            'etapa']) &&
        $this->validatesPresenceOfMatriculaIdOrComponenteCurricularId() &&
        $this->validatesCanChangeDiarioForAno();
    }

    protected function canPost()
    {
        return $this->validatesPresenceOf('etapa') &&
        $this->validatesPresenceOf('matricula_id') &&
        $this->canChange() &&
        $this->validatesPeriodoLancamentoFaltasNotas();
    }

    protected function canPostNota()
    {
        return $this->canPost() &&
        $this->validatesPresenceOf('componente_curricular_id') &&
        $this->validatesRegraAvaliacaoHasNota() &&
        $this->validatesRegraAvaliacaoHasFormulaRecuperacao() &&
        $this->validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() &&
        $this->validatesPreviousNotasHasBeenSet();
    }

    protected function canPostNotaGeral()
    {
        return $this->canPost();
    }

    protected function canPostFalta()
    {
        return $this->canPost() &&
        $this->validatesPreviousFaltasHasBeenSet();
    }

    protected function canPostParecer()
    {
        return $this->canPost() &&
        $this->validatesPresenceOf('att_value') &&
        $this->validatesEtapaParecer() &&
        $this->validatesRegraAvaliacaoHasParecer() &&
        $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
    }

    protected function canDelete()
    {
        return $this->validatesPresenceOf('etapa');
    }

    protected function canDeleteNota()
    {
        return $this->canDelete() &&
        $this->validatesPresenceOf('componente_curricular_id') &&
        $this->validatesInexistenceOfNotaExame() &&
        $this->validatesInexistenceNotasInNextEtapas() &&
        $this->validatesPeriodoLancamentoFaltasNotas();
    }

    protected function canDeleteFalta()
    {
        return $this->canDelete() &&
        $this->validatesInexistenceFaltasInNextEtapas() &&
        $this->validatesPeriodoLancamentoFaltasNotas();
    }

    protected function canDeleteParecer()
    {
        return $this->canDelete() &&
        $this->validatesEtapaParecer() &&
        $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
    }

    // responders

    // post
    /**
     * @throws CoreExt_Exception
     */
    protected function postNota()
    {
        if ($this->canPostNota()) {
            $nota = urldecode($this->getRequest()->att_value);
            $notaOriginal = urldecode($this->getRequest()->nota_original);
            $etapa = $this->getRequest()->etapa;

            $nota = $this->serviceBoletim()->calculateStageScore($etapa, $nota, null);

            $array_nota = [
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'nota' => $nota,
                'etapa' => $etapa,
                'notaOriginal' => $nota,
            ];

            if ($_notaAntiga = $this->serviceBoletim()->getNotaComponente($this->getRequest()->componente_curricular_id, $this->getRequest()->etapa)) {
                $array_nota['notaRecuperacaoParalela'] = $_notaAntiga->notaRecuperacaoParalela;
                $array_nota['notaRecuperacaoEspecifica'] = $_notaAntiga->notaRecuperacaoEspecifica;
            }

            $nota = new Avaliacao_Model_NotaComponente($array_nota);
            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->inserirAuditoriaNotas($_notaAntiga, $nota);

            $serie_id = '';
            $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
            foreach($serie as $id) {
                $serie_id = $id->ref_ref_cod_serie;
               
            }
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
            if($tipoNota==1){
                $this->updateMedia();
            }
           
            $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('should_show_recuperacao_especifica', $this->shouldShowRecuperacaoEspecifica());
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('media', round($this->getMediaAtual($this->getRequest()->componente_curricular_id), 3));
       
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

       
                
        

       

    }

    protected function updateMedia(){

        $serie_id = '';
        $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
        foreach($serie as $id) {
            $serie_id = $id->ref_ref_cod_serie;
           
        }
        $substitui_menor_nota = -1;
        $regra_avaliacao_id = null;
        $regra_avaliacao = RegraAvaliacaoSerieAno::where('serie_id', $serie_id)->where('ano_letivo', $this->getRequest()->ano_escolar)->get();
        foreach($regra_avaliacao as $regra) {
            $regra_avaliacao_id  = $regra->regra_avaliacao_id;
            $regra_avaliacao2 = RegraAvaliacaoRecuperacao::where('regra_avaliacao_id', $regra->regra_avaliacao_id)->get();
            foreach($regra_avaliacao2 as $regra2) {
               
                $substitui_menor_nota = $regra2->substitui_menor_nota;
            }
        }
        $tipo_recuperacao_paralela = -1;
        $regra_avaliacoes = RegraAvaliacao::where('id', $regra_avaliacao_id)->get();
        foreach($regra_avaliacoes as $regra_av) {
             $tipo_recuperacao_paralela = $regra_av->tipo_recuperacao_paralela;
        }
       
        if($tipo_recuperacao_paralela==2){
            //recuperacao paralela por etapa substituindo a menor nota
                        
                    if($substitui_menor_nota==1){

                        $nota_alunos = LegacyDisciplineScoreStudent::where('matricula_id', $this->getRequest()->matricula_id)->get();
                        foreach($nota_alunos as $nota_aluno) {
                    
                        $contador =0;
                        $soma_notas =0;
                        $soma_notas_arredondadas =0;
                        $nota_recuperacao =0;
                        $nota_componente_curricular = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', 'not like', 'Rc')->get();
                        foreach($nota_componente_curricular as $list) {
                        
                            $nota1 = 0;
                            $nota2 = 0;
                            
                        
                                $contador++;
                                $nota1 = $list->nota_arredondada;
                            
                            $nota1 = $list->nota_arredondada;
                            $notaRecuperacao = $list->nota_recuperacao_especifica;
                            $etapa_anterior = $list->etapa-1;
                            if(!empty($notaRecuperacao)){
                                $nota_componente_curricular_anterior = LegacyDisciplineScore::whereNotNull('nota')->where('nota_aluno_id', $nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('etapa', $etapa_anterior)->get();
                                foreach($nota_componente_curricular_anterior as $list2) {
                                    $soma_notas = $soma_notas - $list2->nota_arredondada;
                                    $nota2 = $list2->nota_arredondada;
                                }
                                if($nota1<$nota2){
                                    if($notaRecuperacao>$nota1){
                                        $nota1 = $notaRecuperacao;   
                                    }
                                    
                                }elseif($nota2<$nota1){
                                    if($notaRecuperacao>$nota2){
                                        $nota2 = $notaRecuperacao;   
                                    }
                                
                                }elseif($nota2==$nota1){

                                    if($notaRecuperacao>$nota1){
                                        $nota1 = $notaRecuperacao;   
                                    }
                                
                                }
                        }

                        
                            $soma_notas = $soma_notas + ($nota1 + $nota2);
                            $soma_notas_arredondadas = $soma_notas_arredondadas + $list->nota_arredondada;   
                        }
                        $media = $soma_notas / $contador;
                        $nota_exame = 0;
                        $nota_exames = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', '=', 'Rc')->get();
                        foreach($nota_exames as $nota_ex) {
                           
                            $nota_exame = $nota_ex->nota_arredondada;
                        }
                       
                        if(!empty($nota_exame)){
                            $media = ($media + $nota_exame)/2;   
                            
                        }
                        $media = round($media , 2);

                        //verifica a situação da matricula
                        $situacao = 0;
                        $nota_exame_final = $nota_exame;
                        
                        //Se existir exame
                        if(!empty($nota_exame_final)){

                            if($media<5){
                                //reprovado
                                $situacao = 2;
                            }else{
                                //aprovado após exame
                                $situacao = 8; 
                            }   
                           
                        }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media<5){
                              //em exame
                              $situacao = 7; 
                        }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media>=5){
                                //Aprovado
                                $situacao = 1; 
                         }else{
                            //Em andamento
                            $situacao = 3;
                         }



                        if($this->getRequest()->etapa==4 and $media<5){
                            //atualiza a nota que falta no exame final
                              $nota_falta_exame = 10 - $media;
                            
                                  $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $nota_falta_exame);
                                  $this->appendResponse('nota_necessaria_exame', $nota_falta_exame);
                              } else {
                                  $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
                              
                          }
                        $media_arredondada = $soma_notas_arredondadas / $contador;

                        $existe_media = 0;
                        $existe = LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->get();
                        foreach($existe as $sim){
                            $existe_media = 1;    
                        }
                        if($existe_media == 1){
                        LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->update([
                            'media' => $media,
                            'media_arredondada' => $media,
                            'etapa' => $this->getRequest()->etapa,
                            'situacao' => $situacao


                        
                        ]);
                            }else{
                                LegacyDisciplineScoreAverage::create( [
                                    'media' => $media,
                                    'media_arredondada' => $media,
                                    'componente_curricular_id' => $this->getRequest()->componente_curricular_id,
                                    'nota_aluno_id' => $nota_aluno->id,
                                    'situacao' => $situacao,
                                    'etapa' => $this->getRequest()->etapa,
                                    'bloqueada' => false
                                  ]);
                            }
                    }
                    //recuperacao paralela por etapa sem substituir a menor nota
                    }elseif($substitui_menor_nota==0){
                        
                        $nota_alunos = LegacyDisciplineScoreStudent::where('matricula_id', $this->getRequest()->matricula_id)->get();
                        foreach($nota_alunos as $nota_aluno) {
                    
                        $contador_media =0;
                        $contador =0;
                        $soma_notas =0;
                        $soma_notas_avulsas =0;
                        $soma_media = 0;
                        $soma_notas_arredondadas =0;
                        $nota_componente_curricular = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', 'not like', "Rc")->get();
                        foreach($nota_componente_curricular as $list) {
                            $soma_notas = 0;
                            $nota1 = 0;
                            $nota2 = 0;
                        
                            $nota1 = $list->nota_arredondada;
                            $notaRecuperacao = $list->nota_recuperacao_especifica;
                            $etapa_anterior = $list->etapa-1;
                            if(!empty($notaRecuperacao)){
                                $nota_componente_curricular_anterior = LegacyDisciplineScore::whereNotNull('nota')->where('nota_aluno_id', $nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('etapa', $etapa_anterior)->get();
                                foreach($nota_componente_curricular_anterior as $list2) {
                                    $nota2 = $list2->nota_arredondada;
                                }
                                $soma_notas_avulsas = $soma_notas_avulsas - $nota2;
                                $contador = $contador -1;
                                $contador_media ++;
                                $soma_notas = $soma_notas + ($nota1 + $nota2)/2;

                                if($notaRecuperacao >= $soma_notas){
                                    $soma_media = $soma_media + ($soma_notas + $notaRecuperacao)/2;
                                }else{
                                    $soma_media = $soma_media + $soma_notas;
                                }
                                
                            }else{
                            $contador++;
                            $soma_notas_avulsas = $soma_notas_avulsas + $nota1;
                        }
                        
                            
                        }
                        $media = $soma_media / $contador_media;
                        if($soma_notas_avulsas>0){
                            if($contador>0 and is_nan($media)){
                                $media = $soma_notas_avulsas/$contador;
                            }else{
                                $media_notas_avulsas = $soma_notas_avulsas/$contador;
                                $media =  ($media+ $media_notas_avulsas)/2;
                            }

                           
                        }
                        $nota_exame = 0;
                        $nota_exames = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', 'like', "Rc")->get();
                        foreach($nota_exames as $nota_ex) {
                           
                            $nota_exame = $nota_ex->nota_arredondada;
                        }
                        
                        if(!empty($nota_exame)){
                            $media = ($media + $nota_exame)/2;   
                        }
                        $media = round($media , 2);
                            //verifica a situação da matricula
                            $situacao = 0;
                            $nota_exame_final = $nota_exame;
                            
                            //Se existir exame
                            if(!empty($nota_exame_final)){
    
                                if($media<5){
                                    //reprovado
                                    $situacao = 2;
                                }else{
                                    //aprovado após exame
                                    $situacao = 8; 
                                }   
                               
                            }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media<5){
                                  //em exame
                                  $situacao = 7; 
                            }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media>=5){
                                    //Aprovado
                                    $situacao = 1; 
                             }else{
                                //Em andamento
                                $situacao = 3;
                             }
   



                        if($this->getRequest()->etapa==4 and $media<5){
                            //atualiza a nota que falta no exame final
                              $nota_falta_exame = 10 - $media;
                             
                                  $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $nota_falta_exame);
                                  $this->appendResponse('nota_necessaria_exame', $nota_falta_exame);
                              } else {
                                  $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
                              
                          }
                        $existe_media_ = 0;
                        $existe = LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->get();
                        foreach($existe as $sim){
                            $existe_media_ = 1;    
                        }
                        if($existe_media_ == 1){
                        LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->update([
                            'media' => $media,
                            'media_arredondada' => $media,
                            'etapa' => $this->getRequest()->etapa,
                            'situacao'=> $situacao

                        
                        ]);
                            }else{
                                LegacyDisciplineScoreAverage::create( [
                                    'media' => $media,
                                    'media_arredondada' => $media,
                                    'componente_curricular_id' => $this->getRequest()->componente_curricular_id,
                                    'nota_aluno_id' => $nota_aluno->id,
                                    'situacao' => $situacao,
                                    'etapa' => $this->getRequest()->etapa,
                                    'bloqueada' => false
                                  ]);
                            }
                    }

                    }
        
        }elseif($tipo_recuperacao_paralela==1){

            $nota_alunos = LegacyDisciplineScoreStudent::where('matricula_id', $this->getRequest()->matricula_id)->get();
            foreach($nota_alunos as $nota_aluno) {
        
            $contador =0;
            $soma_notas =0;
            $soma_notas_arredondadas =0;
            $nota_componente_curricular = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', 'not like', 'Rc')->get();
            foreach($nota_componente_curricular as $list) {
            
                $nota1 = 0;
                $nota2 = 0;
                $contador++;
                $nota1 = $list->nota_arredondada;
                $notaRecuperacao = $list->nota_recuperacao_especifica;
                $etapa_anterior = $list->etapa-1;
                if(!empty($notaRecuperacao)){
                
                    $nota1 = ($nota1+$notaRecuperacao)/2;
                }

            
                $soma_notas = $soma_notas + $nota1;
                $soma_notas_arredondadas = $soma_notas_arredondadas + $list->nota_arredondada;   
            }
            $media = $soma_notas / $contador;
            $nota_exame = 0;
            $nota_exames = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', '=', 'Rc')->get();
            foreach($nota_exames as $nota_ex) {
                
                $nota_exame = $nota_ex->nota_arredondada;
            }
        
            if(!empty($nota_exame)){
                $media = ($media + $nota_exame)/2;   
            }
            $media = round($media , 2);
            //verifica a situação da matricula
            $situacao = 0;
            $nota_exame_final = $nota_exame;
            
            //Se existir exame
            if(!empty($nota_exame_final)){

                if($media<5){
                    //reprovado
                    $situacao = 2;
                }else{
                    //aprovado após exame
                    $situacao = 8; 
                }   
                
            }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media<5){
                //em exame
                $situacao = 7; 
            }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media>=5){
                    //Aprovado
                    $situacao = 1; 
            }else{
                //Em andamento
                $situacao = 3;
            }




            if($this->getRequest()->etapa==4 and $media<5){
                //atualiza a nota que falta no exame final
                  $nota_falta_exame = 10 - $media;
              
                      $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $nota_falta_exame);
                      $this->appendResponse('nota_necessaria_exame', $nota_falta_exame);
                  } else {
                      $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
                  
              }

            $media_arredondada = $soma_notas_arredondadas / $contador;
            $existe_media_1 = 0;
            $existe = LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->get();
            foreach($existe as $sim){
                $existe_media_1 = 1;    
            }
            if($existe_media_1 == 1){
            LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->update([
                'media' => $media,
                'media_arredondada' => $media,
                'etapa' => $this->getRequest()->etapa, 
                'situacao' => $situacao

            
            ]);
                }else{
                    LegacyDisciplineScoreAverage::create( [
                        'media' => $media,
                        'media_arredondada' => $media,
                        'componente_curricular_id' => $this->getRequest()->componente_curricular_id,
                        'nota_aluno_id' => $nota_aluno->id,
                        'situacao' => $situacao,
                        'etapa' => $this->getRequest()->etapa,
                        'bloqueada' => false
                        ]);
                }
            }


        }else{

                $nota_alunos = LegacyDisciplineScoreStudent::where('matricula_id', $this->getRequest()->matricula_id)->get();
                foreach($nota_alunos as $nota_aluno) {
            
                $contador =0;
                $soma_notas =0;
                $nota_componente_curricular = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', 'not like', 'Rc')->get();
                foreach($nota_componente_curricular as $list) {
                    $contador++;
                    $nota = $list->nota;
                    $soma_notas = $soma_notas + $nota;
                    
                }
                $media = $soma_notas / $contador;
                $nota_exame = 0;
                $nota_exames = LegacyDisciplineScore::where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->where('nota_aluno_id', $nota_aluno->id)->where('etapa', '=', 'Rc')->get();
                foreach($nota_exames as $nota_ex) {
                    
                    $nota_exame = $nota_ex->nota_arredondada;
                }
            
                if(!empty($nota_exame)){
                    $media = ($media + $nota_exame)/2;   
                }
                $media = round($media , 2);
                      //verifica a situação da matricula
                      $situacao = 0;
                      $nota_exame_final = $nota_exame;
                      
                      //Se existir exame
                      if(!empty($nota_exame_final)){

                          if($media<5){
                              //reprovado
                              $situacao = 2;
                          }else{
                              //aprovado após exame
                              $situacao = 8; 
                          }   
                         
                      }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media<5){
                            //em exame
                            $situacao = 7; 
                      }elseif(empty($nota_exame_final) and $this->getRequest()->etapa == 4 and $media>=5){
                              //Aprovado
                              $situacao = 1; 
                       }else{
                          //Em andamento
                          $situacao = 3;
                       }


                if($this->getRequest()->etapa==4 and $media<5){
                  //atualiza a nota que falta no exame final
                    $nota_falta_exame = 10 - $media;
                   
                        $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $nota_falta_exame);
                        $this->appendResponse('nota_necessaria_exame', $nota_falta_exame);
                 }else {
                        $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
                  
                     }

                $existe_media_2 = 0;
                $existe = LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->get();
                foreach($existe as $sim){
                    $existe_media_2 = 1;    
                }
                if($existe_media_2 == 1){
                LegacyDisciplineScoreAverage::where('nota_aluno_id',$nota_aluno->id)->where('componente_curricular_id', $this->getRequest()->componente_curricular_id)->update([
                    'media' => $media,
                    'media_arredondada' => $media,
                    'etapa' => $this->getRequest()->etapa, 
                    'situacao' => $situacao

                
                ]);
                    }else{
                        LegacyDisciplineScoreAverage::create( [
                            'media' => $media,
                            'media_arredondada' => $media,
                            'componente_curricular_id' => $this->getRequest()->componente_curricular_id,
                            'nota_aluno_id' => $nota_aluno->id,
                            'situacao' => $situacao,
                            'etapa' => $this->getRequest()->etapa,
                            'bloqueada' => false
                            ]);
                    }
            }

            }


    }

    protected function postNotaGeral()
    {
        if ($this->canPostNotaGeral()) {
            $notaGeral = urldecode($this->getRequest()->att_value);
            $nota = new Avaliacao_Model_NotaGeral([
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaGeral]);

            $this->serviceBoletim()->updateMediaGeral(0, $this->getRequest()->etapa);
            $this->serviceBoletim()->addNotaGeral($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota geral da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente($this->getRequest()->componente_curricular_id));
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        $serie_id = '';
        $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
        foreach($serie as $id) {
            $serie_id = $id->ref_ref_cod_serie;
           
        }
        $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
        if($tipoNota==1){
            $this->updateMedia();
        }
    }

    protected function postMedia()
    {
        if ($this->canPostMedia()) {
            $mediaLancada = urldecode($this->getRequest()->att_value);
            $componenteCurricular = $this->getRequest()->componente_curricular_id;
            $etapa = $this->getRequest()->etapa;

             
            $this->messenger->append('Média da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
            $this->appendResponse('situacao', $this->getSituacaoComponente($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        } else {
            $this->messenger->append('Usuário não possui permissão para alterar a média do aluno.', 'error');
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $serie_id = '';
        $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
        foreach($serie as $id) {
            $serie_id = $id->ref_ref_cod_serie;
           
        }
        $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
        if($tipoNota==1){
            $this->updateMedia();
        }
    }

    protected function postMediaDesbloqueia()
    {
        if ($this->canPostMedia()) {
            $componenteCurricular = $this->getRequest()->componente_curricular_id;

            if ($this->serviceBoletim()->unlockMediaComponente($componenteCurricular)) {
                $this->messenger->append('Média desbloqueada com sucesso.', 'success');
            } else {
                $this->messenger->append('Ocorreu um erro ao desbloquear a média. Tente novamente.', 'error');
            }
        }
    }

    protected function deleteMedia()
    {
        if ($this->canDeleteMedia()) {
            $media = $this->getMediaAtual();
            if (empty($media) && !is_numeric($media)) {
                $this->messenger->append('Média matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
            } else {
               
                $serie_id = '';
            $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
            foreach($serie as $id) {
                $serie_id = $id->ref_ref_cod_serie;
               
            }
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
            if($tipoNota==1){
                $this->updateMedia();
            }
                $this->messenger->append('Média matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
            }
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    protected function canPostMedia()
    {
        return $this->canPostSituacaoAndNota();
    }

    protected function canDeleteMedia()
    {
        return true;
    }

    /**
     * @throws CoreExt_Exception
     */
    protected function postNotaRecuperacaoParalela()
    {
        if ($this->canPostNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaRecuperacaoParalela = urldecode($this->getRequest()->att_value);
            $etapa = $this->getRequest()->etapa;

            $notaNova = $this->serviceBoletim()->calculateStageScore(
                $etapa,
                $notaOriginal,
                $notaRecuperacaoParalela
            );

            $nota = new Avaliacao_Model_NotaComponente(
                [
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $etapa,
                'nota' => $notaNova,
                'notaRecuperacaoParalela' => $notaRecuperacaoParalela,
                'notaOriginal' => $notaOriginal]
            );

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_nova', ($notaNova > $notaOriginal ? $notaNova : null));
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

       
        $serie_id = '';
            $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
            foreach($serie as $id) {
                $serie_id = $id->ref_ref_cod_serie;
               
            }
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
            if($tipoNota==1){
                $this->updateMedia();
            }
    }

    protected function postNotaRecuperacaoEspecifica()
    {
        if ($this->canPostNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaRecuperacaoParalela = urldecode($this->getRequest()->att_value);

            $nota = new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'etapa' => $this->getRequest()->etapa,
            'nota' => $notaOriginal,
            'notaRecuperacaoEspecifica' => $notaRecuperacaoParalela,
            'notaOriginal' => $notaOriginal
            ]);

        $this->serviceBoletim()->addNota($nota);
        $this->trySaveServiceBoletim();
        $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        $serie_id = '';
            $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
            foreach($serie as $id) {
                $serie_id = $id->ref_ref_cod_serie;
               
            }
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
            if($tipoNota==1){
                $this->updateMedia();
            }
    }
        // Se está sendo lançada nota de recuperação, obviamente o campo deve ser visível
        $this->appendResponse('should_show_recuperacao_especifica', true);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        
        
        
 
 
    
}

    // TODO mover validacao para canPostFalta
    protected function postFalta()
    {
        $canPost = $this->canPostFalta();
        if ($canPost && $this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $canPost = $this->validatesPresenceOf('componente_curricular_id');
        }

        if ($canPost) {
            if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
                $falta = $this->getFaltaComponente();
            } elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
                $falta = $this->getFaltaGeral();
            }

            $this->serviceBoletim()->addFalta($falta);
            $this->trySaveServiceBoletimFaltas();
            $this->messenger->append('Falta matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('should_show_recuperacao_especifica', $this->shouldShowRecuperacaoEspecifica());
    }

    protected function postParecer()
    {
        if ($this->canPostParecer()) {
            $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
            $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo::class;

            if ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE) {
                $parecer = $this->getParecerComponente();
            } else {
                $parecer = $this->getParecerGeral();
            }

            $parecer->parecer = $this->removeHtmlTagsService->execute($parecer->parecer);
            $this->serviceBoletim()->addParecer($parecer);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Parecer descritivo matricula ' . $this->getRequest()->matricula_id . ' alterado com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    // delete

    protected function deleteNota()
    {
        if ($this->canDeleteNota()) {
            $nota = $this->getNotaAtual();
            if (empty($nota) && !is_numeric($nota)) {
                $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
            } else {
                $_notaAntiga = $this->serviceBoletim()->getNotaComponente($this->getRequest()->componente_curricular_id, $this->getRequest()->etapa);
                $this->serviceBoletim()->deleteNota($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
                $this->inserirAuditoriaNotas($_notaAntiga, $nota);
                $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
            }
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));


        $serie_id = '';
        $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
        foreach($serie as $id) {
            $serie_id = $id->ref_ref_cod_serie;
           
        }
        $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
        if($tipoNota==1){
            $this->updateMedia();
        }

    }

    protected function deleteNotaRecuperacaoParalela()
    {
        if ($this->canDeleteNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaAtual = $this->getNotaAtual();
            $nota = new Avaliacao_Model_NotaComponente([
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoEspecifica' => null,
                'notaOriginal' => $notaOriginal]);

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' excluída com sucesso.', 'success');

            $this->appendResponse('situacao', $this->getSituacaoComponente());
            $this->appendResponse('nota_original', $notaOriginal);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $serie_id = '';
            $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
            foreach($serie as $id) {
                $serie_id = $id->ref_ref_cod_serie;
               
            }
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
            if($tipoNota==1){
                $this->updateMedia();
            }
    }

    protected function deleteNotaRecuperacaoEspecifica()
    {
        if ($this->canDeleteNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaAtual = $this->getNotaAtual();
            $nota = new Avaliacao_Model_NotaComponente([
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoParalela' => null,
                'notaOriginal' => $notaOriginal]);

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' excluída com sucesso.', 'success');

            $this->appendResponse('situacao', $this->getSituacaoComponente());
            $this->appendResponse('nota_original', $notaOriginal);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $serie_id = '';
        $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
        foreach($serie as $id) {
            $serie_id = $id->ref_ref_cod_serie;
           
        }
        $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
        if($tipoNota==1){
            $this->updateMedia();
        }
    }

    protected function deleteFalta()
    {
        $canDelete = $this->canDeleteFalta();
        $cnsPresenca = RegraAvaliacao_Model_TipoPresenca::class;
        $tpPresenca = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

        if ($canDelete && $tpPresenca == $cnsPresenca::POR_COMPONENTE) {
            $canDelete = $this->validatesPresenceOf('componente_curricular_id');
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        } else {
            $componenteCurricularId = null;
        }

        if ($canDelete && is_null($this->getFaltaAtual())) {
            $this->messenger->append('Falta matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
        } elseif ($canDelete) {
            $this->serviceBoletim()->deleteFalta($this->getRequest()->etapa, $componenteCurricularId);
            $this->trySaveServiceBoletimFaltas();
            $this->messenger->append('Falta matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    protected function deleteParecer()
    {
        if ($this->canDeleteParecer()) {
            $parecerAtual = $this->getParecerAtual();

            if ((is_null($parecerAtual) || $parecerAtual == '')) {
                $this->messenger->append('Parecer descritivo matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removido.', 'notice');
            } else {
                $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
                $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo::class;

                if ($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE) {
                    $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
                } else {
                    // FIXME #parameters
                    $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa, null);
                }

                $this->trySaveServiceBoletim();
                $this->messenger->append('Parecer descritivo matrícula ' . $this->getRequest()->matricula_id . ' removido com sucesso.', 'success');
            }
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    protected function deleteNotaGeral()
    {
        $this->serviceBoletim()->updateMediaGeral(0, $this->getRequest()->etapa);
        $this->serviceBoletim()->deleteNotaGeral($this->getRequest()->etapa);

        $this->trySaveServiceBoletim();
        $serie_id = '';
        $serie = SerieTurma::where('cod_turma', $this->getRequest()->turma_id)->get();
        foreach($serie as $id) {
            $serie_id = $id->ref_ref_cod_serie;
           
        }
        $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($this->getRequest()->componente_curricular_id, $serie_id);
        if($tipoNota==1){
            $this->updateMedia();
        }

        $this->messenger->append('Nota geral da matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());

    }

    // get

    protected function getRelocationDate()
    {
        /** @var LegacyInstitution $institution */
        $institution = app(LegacyInstitution::class);

        return $institution->relocation_date;
    }

    protected function getMatriculas()
    {
        $regras = $matriculas = [];

        if ($this->canGetMatriculas()) {
            /** @var LegacySchoolClass $schoolClass */
            $schoolClass = LegacySchoolClass::query()
                ->with([
                    'enrollments' => function ($query) {
                        /** @var Builder $query */
                        $query->when($this->getRequest()->matricula_id, function ($query) {
                            $query->where('ref_cod_matricula', $this->getRequest()->matricula_id);
                        });
                        $query->where(function ($query) {
                            $relocationDate = $this->getRelocationDate();

                            /** @var Builder $query */
                            $query->where('ativo', 1);
                            $query->when($relocationDate, function ($query) use ($relocationDate) {
                                /** @var Builder $query */
                                $query->orWhere(function ($query) use ($relocationDate) {
                                    /** @var Builder $query */
                                    $query->where('data_exclusao', '>', $relocationDate);
                                    $query->where(function ($query) {
                                        /** @var Builder $query */
                                        $query->orWhere('transferido', true);
                                        $query->orWhere('remanejado', true);
                                        $query->orWhere('reclassificado', true);
                                        $query->orWhere('abandono', true);
                                        $query->orWhere('falecido', true);
                                    });
                                });
                            });
                        });
                        $query->with([
                            'registration' => function ($query) {
                                $query->with([
                                    'student' => function ($query) {
                                        $query->with([
                                            'person' => function ($query) {
                                                $query->withCount('considerableDeficiencies');
                                            }
                                        ]);
                                    }
                                ]);
                                $query->with('dependencies');
                            }
                        ]);

                        // Pega a última enturmação na turma

                        $query->whereRaw(
                            '
                            sequencial = (
                                SELECT max(sequencial)
                                FROM pmieducar.matricula_turma mt
                                WHERE mt.ref_cod_turma = matricula_turma.ref_cod_turma
                                AND mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                            )
                            '
                        );

                        $query->whereHas('registration', function ($query) {
                            $query->whereHas('student', function ($query) {
                                $query->where('ativo', 1);
                            });
                        });

                        $query->where('ativo', 1);
                    },
                ])
                ->whereKey($this->getRequest()->turma_id)
                ->firstOrFail();

            // Ordena as enturmações pelo sequencial de fechamento e o nome da
            // pessoa conforme comportamento do código anterior.

            $enrollments = $schoolClass->enrollments->sortBy(function ($enrollment) {
                return Str::slug($enrollment->registration->student->person->name);
            })->sortBy(function ($enrollment) {
                return $enrollment->sequencial_fechamento;
            });

            // Pega a regra de avaliação da turma e busca no banco de dados
            // suas tabelas de arredondamento (numérica e conceitual), valores
            // de arredondamento para as duas tabelas e regras de recuperação.

            $evaluationRule = $schoolClass->getEvaluationRule();

            $evaluationRule->load('roundingTable.roundingValues');
            $evaluationRule->load('conceptualRoundingTable.roundingValues');
            $evaluationRule->load('remedialRules');

            // Caso a regra de avaliação possua uma regra diferenciada para
            // alunos com deficiência, também irá buscar no banco de dados por
            // suas tabelas de arredondamento (numérica e conceitual), valores
            // de arredondamento para as duas tabelas e regras de recuperação.

            if ($deficiencyEvaluationRule = $evaluationRule->deficiencyEvaluationRule) {
                $deficiencyEvaluationRule->load('roundingTable.roundingValues');
                $deficiencyEvaluationRule->load('conceptualRoundingTable.roundingValues');
                $deficiencyEvaluationRule->load('remedialRules');
            }

            foreach ($enrollments as $enrollment) {
                /*** @var LegacyRegistration $registration */
                $registration = $enrollment->registration;
                $student = $registration->student;
                $person = $student->person;

                $matricula = [];
                $matriculaId = $enrollment->ref_cod_matricula;
                $turmaId = $enrollment->ref_cod_turma;
                $serieId = $registration->ref_ref_cod_serie;
                $componenteCurricularId = $this->getRequest()->componente_curricular_id;
                $disciplinasDependenciaId = $enrollment->registration->dependencies->pluck('ref_cod_disciplina')->toArray();
                $matriculaDependencia = $enrollment->registration->dependencia;

                if (!empty($componenteCurricularId) && $matriculaDependencia && !in_array($componenteCurricularId, $disciplinasDependenciaId)) {
                    continue;
                }

                // seta id da matricula a ser usado pelo metodo serviceBoletim
                $this->setCurrentMatriculaId($matriculaId);

                if (!($enrollment->remanejado || $enrollment->transferido || $enrollment->abandono || $enrollment->reclassificado || $enrollment->falecido)) {
                    $matricula['componentes_curriculares'] = $this->loadComponentesCurricularesForMatricula($matriculaId, $turmaId, $serieId);
                }

                $matricula['bloquear_troca_de_situacao'] = $registration->isLockedToChangeStatus();
                $matricula['situacao'] = $registration->aprovado;
                $matricula['matricula_id'] = $registration->getKey();
                $matricula['aluno_id'] = $student->getKey();
                $matricula['nome'] = $person->name;

                if ($enrollment->remanejado) {
                    $matricula['situacao_deslocamento'] = 'Remanejado';
                } elseif ($enrollment->transferido) {
                    $matricula['situacao_deslocamento'] = 'Transferido';
                } elseif ($enrollment->abandono) {
                    $matricula['situacao_deslocamento'] = 'Abandono';
                } elseif ($enrollment->reclassificado) {
                    $matricula['situacao_deslocamento'] = 'Reclassificado';
                } elseif ($enrollment->falecido) {
                    $matricula['situacao_deslocamento'] = 'Falecido';
                } else {
                    $matricula['situacao_deslocamento'] = null;
                }

                // Utiliza a regra de avaliação diferenciada quando o aluno
                // possua alguma deficiência que seja considerada e exista uma
                // regra de avaliação diferenciada para a turma.

                $registrationEvaluationRule = $evaluationRule;

                if ($registration->ref_ref_cod_serie != $schoolClass->grade_id) {
                    $registrationEvaluationRule = $registration->getEvaluationRule();
                }

                if ($person->considerable_deficiencies_count && $deficiencyEvaluationRule) {
                    $registrationEvaluationRule = $deficiencyEvaluationRule;
                }

                $matricula['regra'] = $this->getEvaluationRule($registrationEvaluationRule);

                $matricula['regra']['quantidade_etapas'] = $schoolClass->stages->count();

                $regras[$matricula['regra']['id']] = $matricula['regra'];

                $matriculas[] = $matricula;
            }
        }

        if ($matriculas) {
            $this->appendResponse('details', array_values($regras));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);

        return $matriculas;
    }

    // metodos auxiliares responders

    // TODO usar esta funcao onde é verificado se parecer geral
    protected function parecerGeral()
    {
        $tiposParecerGeral = [RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL];

        return in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'), $tiposParecerGeral);
    }

    protected function setCurrentMatriculaId($matriculaId)
    {
        $this->_currentMatriculaId = $matriculaId;
    }

    protected function getCurrentMatriculaId()
    {
        // caso tenha setado _currentMatriculaId, ignora matricula_id recebido nos parametros
        if (!is_null($this->_currentMatriculaId)) {
            $matriculaId = $this->_currentMatriculaId;
        } elseif (!is_null($this->getRequest()->matricula_id)) {
            $matriculaId = $this->getRequest()->matricula_id;
        } else {
            throw new CoreExt_Exception('Não foi possivel recuperar o id da matricula atual.');
        }

        return $matriculaId;
    }
 
    protected function getInstituicao() {
        $instituicao_id = $this->getRequest()->instituicao_id;

        if (is_numeric($instituicao_id) && !empty($instituicao_id)) {
            $obj = new clsPmieducarInstituicao($instituicao_id);
            $instituicao = $obj->detalhe();

            return $instituicao;
        }

        return [];
    }

    /**
     * @param bool $reload
     *
     * @return Avaliacao_Service_Boletim
     *
     * @throws CoreExt_Exception
     */
    protected function serviceBoletim($reload = false)
    {
        $matriculaId = $this->getCurrentMatriculaId();

        if (!isset($this->_boletimServiceInstances)) {
            $this->_boletimServiceInstances = [];
        }

        // set service
        if (!isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
            try {
                $params = [
                    'matricula' => $matriculaId,
                    'usuario' => \Illuminate\Support\Facades\Auth::id(),
                    'componenteCurricularId' => $this->getRequest()->componente_curricular_id,
                    'turmaId' => $this->getRequest()->turma_id,
                ];
                $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
            } catch (Exception $e) {
                $this->messenger->append("Erro ao instanciar serviço boletim para matricula {$matriculaId}: " . $e->getMessage(), 'error', true);
            }
        }

        // validates service
        if (is_null($this->_boletimServiceInstances[$matriculaId])) {
            throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");
        }

        return $this->_boletimServiceInstances[$matriculaId];
    }

    protected function trySaveServiceBoletim()
    {
        try {
            $this->serviceBoletim()->save();
        } catch (CoreExt_Service_Exception $e) {
            // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
            // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
        }
    }

    protected function trySaveServiceBoletimFaltas()
    {
        try {
            $this->serviceBoletim()->saveFaltas(true);
            $this->serviceBoletim()->promover();
        } catch (CoreExt_Service_Exception $e) {
        }
    }

    // metodos auxiliares getFalta

    protected function getQuantidadeFalta()
    {
        $quantidade = (int) $this->getRequest()->att_value;

        if ($quantidade < 0) {
            $quantidade = 0;
        }

        return $quantidade;
    }

    protected function getFaltaGeral()
    {
        return new Avaliacao_Model_FaltaGeral([
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa,
        ]);
    }

    protected function getFaltaComponente()
    {
        return new Avaliacao_Model_FaltaComponente([
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa,
        ]);
    }

    // metodos auxiliares getParecer

    protected function getParecerComponente()
    {
        return new Avaliacao_Model_ParecerDescritivoComponente([
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
            'etapa' => $this->getRequest()->etapa,
        ]);
    }

    protected function getParecerGeral()
    {
        return new Avaliacao_Model_ParecerDescritivoGeral([
            'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
            'etapa' => $this->getRequest()->etapa,
        ]);
    }

    // metodos auxiliares getSituacaoComponente

    protected function getSituacaoComponente($ccId = null)
    {
        if (is_null($ccId)) {
            $ccId = $this->getRequest()->componente_curricular_id;
        }

        if (!$this->serviceBoletim()->exibeSituacao($ccId)) {
            return null;
        }

        $situacao = null;

        $situacoes = $this->getSituacaoComponentes();
        if (isset($situacoes[$ccId])) {
            $situacao = $situacoes[$ccId];
        }

        return $this->safeString($situacao);
    }

    protected function getSituacaoComponentes()
    {
        $situacoes = [];

        try {
            $componentesCurriculares = $this->serviceBoletim()->getSituacaoComponentesCurriculares()->componentesCurriculares;
            foreach ($componentesCurriculares as $componenteCurricularId => $situacaoCc) {
                $situacoes[$componenteCurricularId] = $this->serviceBoletim()->exibeSituacao($componenteCurricularId) ? App_Model_MatriculaSituacao::getInstance()->getValue($situacaoCc->situacao) : null;
            }
        } catch (Exception $e) {
            $matriculaId = $this->getRequest()->matricula_id;
            $this->messenger->append("Erro ao recuperar situação da matrícula '$matriculaId': " .
                $e->getMessage());
        }

        return $situacoes;
    }

    // outros metodos auxiliares

    protected function loadComponentesCurricularesForMatricula($matriculaId, $turmaId, $serieId)
    {
        $componentesCurriculares = [];

        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $etapa = $this->getRequest()->etapa;

        $_componentesCurriculares = App_Model_IedFinder::getComponentesPorMatricula($matriculaId, null, null, $componenteCurricularId, $etapa, $turmaId);

        $turmaId = $this->getRequest()->turma_id;
        $situacoes = $this->getSituacaoComponentes();

        foreach ($_componentesCurriculares as $_componente) {
            $componente = [];
            $componenteId = $_componente->get('id');
            $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($componenteId, $serieId);

            if (clsPmieducarTurma::verificaDisciplinaDispensada($turmaId, $componenteId)) {
                continue;
            }

            $componente['id'] = $componenteId;
            $componente['nome'] = mb_strtoupper($_componente->get('nome'), 'UTF-8');
            $componente['nota_atual'] = $this->getNotaAtual($etapa = null, $componente['id']);
            $componente['nota_exame'] = $this->getNotaExame($componente['id']);
            $componente['falta_atual'] = $this->getFaltaAtual($etapa = null, $componente['id']);
            $componente['parecer_atual'] = $this->getParecerAtual($componente['id']);
            $componente['situacao'] = $this->safeString($situacoes[$componente['id']]);
            $componente['tipo_nota'] = $tipoNota;
            $componente['ultima_etapa'] = App_Model_IedFinder::getUltimaEtapaComponente($turmaId, $componenteId);
            $gravaNotaExame = ($componente['situacao'] == 'Em exame' || $componente['situacao'] == 'Aprovado após exame' || $componente['situacao'] == 'Retido');



            $falta_exame = 0;
            
           $notas_exame = NotaExame::where('ref_cod_matricula',$matriculaId)->where('ref_cod_componente_curricular', $componenteId)->get();
            foreach($notas_exame as $nota_exame){
              $falta_exame =   $nota_exame->nota_exame;
            }
            $falta_exame = round($falta_exame , 2);
            if($falta_exame>0){
                $componente['nota_necessaria_exame'] = $falta_exame;
            }
            else{
                $componente['nota_necessaria_exame'] = null;
            }
            
            $componente['ordenamento'] = $_componente->get('ordenamento');
            $componente['nota_recuperacao_paralela'] = $this->getNotaRecuperacaoParalelaAtual($etapa, $componente['id']);
            $componente['nota_recuperacao_especifica'] = $this->getNotaRecuperacaoEspecificaAtual($etapa, $componente['id']);
            $componente['should_show_recuperacao_especifica'] = $this->shouldShowRecuperacaoEspecifica($etapa, $componente['id']);
            $componente['nota_original'] = $this->getNotaOriginal($etapa, $componente['id']);
            $componente['nota_geral_etapa'] = $this->getNotaGeral($etapa);
            $componente['media'] = $this->getMediaAtual($componente['id']);
            $componente['media_arredondada'] = $this->getMediaArredondadaAtual($componente['id']);
            $componente['media_bloqueada'] = $this->getMediaBloqueada($componente['id']);

           

            //buscando pela área do conhecimento
            $area = $this->getAreaConhecimento($componente['id']);
            $nomeArea = (($area->secao != '') ? $area->secao . ' - ' : '') . $area->nome;
            $componente['ordenamento_ac'] = $area->ordenamento_ac;
            $componente['area_id'] = $area->id;
            $componente['area_nome'] = mb_strtoupper($nomeArea, 'UTF-8');

            //criando chave para ordenamento temporário
            //área de conhecimento + componente curricular

            $componente['ordem_nome_area_conhecimento'] = Str::slug($nomeArea);
            $componente['ordem_componente_curricular'] = Str::slug($_componente->get('nome'));
            $componentesCurriculares[] = $componente;
        }

        $ordenamentoComponentes = [];

        foreach ($componentesCurriculares as $chave => $componente) {
            $ordenamentoComponentes['ordenamento_ac'][$chave] = $componente['ordenamento_ac'];
            $ordenamentoComponentes['ordenamento'][$chave] = $componente['ordenamento'];
            $ordenamentoComponentes['ordem_nome_area_conhecimento'][$chave] = $componente['ordem_nome_area_conhecimento'];
            $ordenamentoComponentes['ordem_componente_curricular'][$chave] = $componente['ordem_componente_curricular'];
        }
        array_multisort(
            $ordenamentoComponentes['ordenamento_ac'],
            SORT_ASC,
            SORT_NUMERIC,
            $ordenamentoComponentes['ordem_nome_area_conhecimento'],
            SORT_ASC,
            $ordenamentoComponentes['ordenamento'],
            SORT_ASC,
            SORT_NUMERIC,
            $ordenamentoComponentes['ordem_componente_curricular'],
            SORT_ASC,
            $componentesCurriculares
        );

        //removendo chave temporária
        $len = count($componentesCurriculares);
        for ($i = 0; $i < $len; $i++) {
            unset($componentesCurriculares[$i]['my_order']);
        }

        return $componentesCurriculares;
    }

    protected function getAreaConhecimento($componenteCurricularId = null)
    {
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possível obter a área de conhecimento pois não foi recebido o id do componente curricular.');
        }

        $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        $where = ['id' => $componenteCurricularId];

        $key = json_encode($where);

        $area = Cache::store('array')->remember("getAreaConhecimento:{$key}", now()->addMinute(), function () use ($mapper, $where) {
            return $mapper->findAll(['area_conhecimento'], $where);
        });

        $areaConhecimento = new stdClass();
        $areaConhecimento->id = $area[0]->area_conhecimento->id;
        $areaConhecimento->nome = $area[0]->area_conhecimento->nome;
        $areaConhecimento->secao = $area[0]->area_conhecimento->secao;
        $areaConhecimento->ordenamento_ac = $area[0]->area_conhecimento->ordenamento_ac;

        return $areaConhecimento;
    }

    protected function createOrUpdateNotaExame($matriculaId, $componenteCurricularId, $notaExame)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId, $notaExame);

        return ($obj->existe() ? $obj->edita() : $obj->cadastra());
    }

    protected function deleteNotaExame($matriculaId, $componenteCurricularId)
    {
        $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId);

        return ($obj->excluir());
    }

    /**
     * @deprecated
     * @see Avaliacao_Service_Boletim::getNotaAtual()
     */
    protected function getNotaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota atual, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->nota);

        return str_replace(',', '.', $nota);
    }

    protected function getNotaGeral($etapa = null)
    {
        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        $nota = urldecode($this->serviceBoletim()->getNotaGeral($etapa)->nota);

        return str_replace(',', '.', $nota);
    }

    protected function getMediaAtual($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a média atual, pois não foi recebido o id do componente curricular.');
        }

        $media = urldecode($this->serviceBoletim()->getMediaComponente($componenteCurricularId)->media);

        $scale = pow(10, 3);

        return floor(floatval($media) * $scale) / $scale;
    }

    protected function getMediaArredondadaAtual($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a média atual, pois não foi recebido o id do componente curricular.');
        }

        $media = urldecode($this->serviceBoletim()->getMediaComponente($componenteCurricularId)->mediaArredondada);

        // $media = round($media,1);

        return str_replace(',', '.', $media);
    }

    protected function getMediaBloqueada($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a média atual, pois não foi recebido o id do componente curricular.');
        }

        $bloqueada = (bool) $this->serviceBoletim()->getMediaComponente($componenteCurricularId)->bloqueada;

        return $bloqueada;
    }

    protected function getNotaRecuperacaoParalelaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota de recuperação paralela atual, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->notaRecuperacaoParalela);
        $nota = str_replace(',', '.', $nota);

        return $nota;
    }

    protected function shouldShowRecuperacaoEspecifica($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota de recuperação específica atual, pois não foi recebido o id do componente curricular.');
        }

        $regra = $this->serviceBoletim()->getRegra();
        $tipoRecuperacaoParalela = $regra->get('tipoRecuperacaoParalela');

        $regraRecuperacao = $regra->getRegraRecuperacaoByEtapa($etapa);

        if ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS
            && $regraRecuperacao && $regraRecuperacao->getLastEtapa() == $etapa) {
            $etapas = $regraRecuperacao->getEtapas();
            $sumNota = 0;
            foreach ($etapas as $key => $_etapa) {
                $sumNota += $this->getNotaOriginal($_etapa, $componenteCurricularId);
            }

            // caso a média das notas da etapa seja menor que média definida na regra e a última nota tenha sido lançada
            // deverá exibir a nota de recuperação
            if ((($sumNota / count($etapas)) < $regraRecuperacao->get('media'))
                && is_numeric($this->getNotaOriginal($etapa, $componenteCurricularId))) {
                return true;
            } else {
                // Caso não exiba, já busca se existe a nota de recuperação e deleta ela
                $notaRecuperacao = $this->serviceBoletim()->getNotaComponente($componenteCurricularId, $regraRecuperacao->getLastEtapa());

                if ($notaRecuperacao) {
                    $nota = new Avaliacao_Model_NotaComponente([
                        'componenteCurricular' => $componenteCurricularId,
                        'nota' => $notaRecuperacao->notaOriginal,
                        'etapa' => $notaRecuperacao->etapa,
                        'notaOriginal' => $notaRecuperacao->notaOriginal,
                        'notaRecuperacaoParalela' => $notaRecuperacao->notaRecuperacaoParalela,
                    ]);

                    $this->serviceBoletim()->addNota($nota);
                    $this->trySaveServiceBoletim();
                }

                return false;
            }
        }

        return false;
    }

    protected function getNotaRecuperacaoEspecificaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota de recuperação específica atual, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->notaRecuperacaoEspecifica);
        $nota = str_replace(',', '.', $nota);

        return $nota;
    }

    protected function getNotaOriginal($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        // validacao
        if (!is_numeric($componenteCurricularId)) {
            throw new Exception('Não foi possivel obter a nota original, pois não foi recebido o id do componente curricular.');
        }

        $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->notaOriginal);
        $nota = str_replace(',', '.', $nota);

        return $nota;
    }

    protected function getNotaExame($componenteCurricularId = null)
    {
        $turmaId = $this->getRequest()->turma_id;
        $regra = $this->serviceBoletim()->getRegra();
        $defineComponentePorEtapa = $regra->get('definirComponentePorEtapa') == 1;
        $ultimaEtapa = $this->getRequest()->etapa == $this->serviceBoletim()->getOption('etapas');
        $ultimaEtapaComponente = App_Model_IedFinder::getUltimaEtapaComponente($turmaId, $componenteCurricularId);

        // somente recupera nota de exame se estiver buscando as matriculas da ultima etapa
        // se existe nota de exame, esta é recuperada mesmo que a regra de avaliação não use mais exame
        if ($ultimaEtapa || ($defineComponentePorEtapa && $ultimaEtapaComponente)) {
            $nota = $this->getNotaAtual($etapa = 'Rc', $componenteCurricularId);
        } else {
            $nota = '';
        }

        return $nota;
    }

    protected function getNotaNecessariaExame($componenteCurricularId = null)
    {
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (!$this->serviceBoletim()->exibeNotaNecessariaExame($componenteCurricularId)) {
            return null;
        }

        $nota = $this->serviceBoletim()->preverNotaRecuperacao($componenteCurricularId);

        return str_replace(',', '.', $nota);
    }

    /**
     * @deprecated
     * @see Avaliacao_Service_Boletim::getFaltaAtual()
     */
    protected function getFaltaAtual($etapa = null, $componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        if (is_null($etapa)) {
            $etapa = $this->getRequest()->etapa;
        }

        if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            if (!is_numeric($componenteCurricularId)) {
                throw new Exception('Não foi possivel obter a falta atual, pois não foi recebido o id do componente curricular.');
            }

            $falta = $this->serviceBoletim()->getFalta($etapa, $componenteCurricularId)->quantidade;
        } elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $falta = $this->serviceBoletim()->getFalta($etapa)->quantidade;
        }

        return $falta;
    }

    protected function getEtapaParecer()
    {
        if ($this->getRequest()->etapa != 'An' && ($this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)) {
            return 'An';
        } else {
            return $this->getRequest()->etapa;
        }
    }

    protected function getParecerAtual($componenteCurricularId = null)
    {
        // defaults
        if (is_null($componenteCurricularId)) {
            $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        }

        $etapaComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
        RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE;

        $anualComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
        RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE;

        if ($etapaComponente or $anualComponente) {
            if (!is_numeric($componenteCurricularId)) {
                throw new Exception('Não foi possivel obter o parecer descritivo atual, pois não foi recebido o id do componente curricular.');
            }

            $parecer = $this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer(), $componenteCurricularId)->parecer;
        } else {
            $parecer = $this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer())->parecer;
        }

        return $this->safeString($parecer, $transform = false);
    }

    protected function getRoundingValues($evaluationRule, $roundingTable)
    {
        $options = [];

        if ($evaluationRule->tipo_nota != RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            $roudingValues = $roundingTable->roundingValues;

            foreach ($roudingValues as $index => $roudingValue) {
                if ($evaluationRule->tipo_nota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
                    $nota = str_replace(',', '.', (string) $roudingValue->nome);
                    $options[$nota] = $nota;
                } else {
                    $options[$index] = [
                        'valor_minimo' => str_replace(',', '.', (string) $roudingValue->valor_minimo),
                        'valor_maximo' => str_replace(',', '.', (string) $roudingValue->valor_maximo),
                        'descricao' => $this->safeString($roudingValue->nome . ' (' . $roudingValue->descricao . ')'),
                    ];
                }
            }
        }

        return $options;
    }

    protected function getNavegacaoTab()
    {
        return $this->getRequest()->navegacao_tab;
    }

    /**
     * Retorna um array com todos os dados necessários para a interface do
     * faltas e notas sobre a regra de avaliação.
     *
     * @param LegacyEvaluationRule $evaluationRule
     *
     * @return array
     */
    protected function getEvaluationRule($evaluationRule)
    {
        $rule = [
            'id' => $evaluationRule->id,
            'nome' => $evaluationRule->nome,
            'nota_maxima_geral' => $evaluationRule->nota_maxima_geral,
            'nota_minima_geral' => $evaluationRule->nota_minima_geral,
            'falta_maxima_geral' => $evaluationRule->falta_maxima_geral,
            'falta_minima_geral' => $evaluationRule->falta_minima_geral,
            'nota_maxima_exame_final' => $evaluationRule->nota_maxima_exame_final,
            'qtd_casas_decimais' => $evaluationRule->qtd_casas_decimais,
            'regra_diferenciada_id' => $evaluationRule->regra_diferenciada_id,
        ];

        $tpPresenca = $evaluationRule->tipo_presenca;

        if ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $rule['tipo_presenca'] = 'geral';
        } elseif ($tpPresenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $rule['tipo_presenca'] = 'por_componente';
        } else {
            $rule['tipo_presenca'] = $tpPresenca;
        }

        $tpNota = $evaluationRule->tipo_nota;

        if ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            $rule['tipo_nota'] = 'nenhum';
        } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
            $rule['tipo_nota'] = 'numerica';
        } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL) {
            $rule['tipo_nota'] = 'conceitual';
        } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
            $rule['tipo_nota'] = 'numericaconceitual';
        } else {
            $rule['tipo_nota'] = $tpNota;
        }

        $tpProgressao = $evaluationRule->tipo_progressao;
        $rule['progressao_manual'] = $tpProgressao == RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL;
        $rule['progressao_manual_ciclo'] = $tpProgressao == RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL_CICLO;
        $rule['progressao_continuada'] = $tpProgressao == RegraAvaliacao_Model_TipoProgressao::CONTINUADA;

        $tpParecer = $evaluationRule->parecer_descritivo;

        if ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM) {
            $rule['tipo_parecer_descritivo'] = 'nenhum';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
            $rule['tipo_parecer_descritivo'] = 'etapa_componente';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL) {
            $rule['tipo_parecer_descritivo'] = 'etapa_geral';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
            $rule['tipo_parecer_descritivo'] = 'anual_componente';
        } elseif ($tpParecer == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL) {
            $rule['tipo_parecer_descritivo'] = 'anual_geral';
        } else {
            $rule['tipo_parecer_descritivo'] = $tpParecer;
        }

        $rule['opcoes_notas'] = $this->getRoundingValues($evaluationRule, $evaluationRule->roundingTable);

        if ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
            $rule['opcoes_notas_conceituais'] = $this->getRoundingValues($evaluationRule, $evaluationRule->conceptualRoundingTable);
        }

        $rule['nomenclatura_exame'] = config('legacy.app.diario.nomenclatura_exame') == 0 ? 'exame' : 'conselho';
        $rule['regra_dependencia'] = config('legacy.app.matricula.dependencia') ? true : false;

        $tipoRecuperacaoParalela = $evaluationRule->tipo_recuperacao_paralela;

        if ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::NAO_USAR) {
            $rule['tipo_recuperacao_paralela'] = 'nao_utiliza';
        } elseif ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA) {
            $rule['tipo_recuperacao_paralela'] = 'por_etapa';
            $rule['media_recuperacao_paralela'] = $evaluationRule->media_recuperacao_paralela;
            $rule['calcula_media_rec_paralela'] = $evaluationRule->calcula_media_rec_paralela;
        } elseif ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS) {
            $rule['tipo_recuperacao_paralela'] = 'etapas_especificas';

            $etapa = $this->getRequest()->etapa;

            /** @var Collection $remedialRules */
            if ($remedialRules = $evaluationRule->remedialRules) {
                /** @var LegacyRemedialRule $remedialRule */
                $remedialRule = $remedialRules->first(function ($remedialRule) use ($etapa) {
                    /** @var LegacyRemedialRule $remedialRule */
                    return in_array($etapa, $remedialRule->getStages());
                });
            }

            if (isset($remedialRule)) {
                $rule['habilita_campo_etapa_especifica'] = $remedialRule->getLastStage() == $etapa;
                $rule['tipo_recuperacao_paralela_nome'] = $remedialRule->descricao;
                $rule['tipo_recuperacao_paralela_nota_maxima'] = $remedialRule->nota_maxima;
            } else {
                $rule['habilita_campo_etapa_especifica'] = false;
                $rule['tipo_recuperacao_paralela_nome'] = '';
                $rule['tipo_recuperacao_paralela_nota_maxima'] = 0;
            }
        }

        if ($evaluationRule->nota_geral_por_etapa == '1') {
            $rule['nota_geral_por_etapa'] = 'SIM';
        } else {
            $rule['nota_geral_por_etapa'] = 'NAO UTILIZA';
        }

        $rule['definir_componente_por_etapa'] = $evaluationRule->definir_componente_etapa == 1;
        $rule['formula_recuperacao_final'] = $evaluationRule->formula_recuperacao_id;
        $rule['desconsiderar_lancamento_frequencia'] = $evaluationRule->desconsiderar_lancamento_frequencia;

        return $rule;
    }

    protected function inserirAuditoriaNotas($notaAntiga, $notaNova)
    {
        if ($this->usaAuditoriaNotas()) {
            $objAuditoria = new clsModulesAuditoriaNota($notaAntiga, $notaNova, $this->getRequest()->turma_id);
            $objAuditoria->cadastra();
        }
    }

    protected function usaAuditoriaNotas()
    {
        return (config('legacy.app.auditoria.notas') == '1');
    }

    public function canChange()
    {
        $user = \Illuminate\Support\Facades\Auth::id();
        $processoAp = $this->_processoAp;
        $obj_permissao = new clsPermissoes();

        return $obj_permissao->permissao_cadastra($processoAp, $user, 7);
    }

    public function postSituacao()
    {
        if (! $this->canPostSituacaoAndNota()) {
            $this->messenger->append('Usuário não possui permissão para alterar a situação da matrícula.', 'error');
        }

        $newStatus = $this->getRequest()->new_status;
        $matriculaId = $this->getRequest()->matricula_id;

        $legacyRegistration = LegacyRegistration::query()->find($matriculaId);
        if ($legacyRegistration instanceof LegacyRegistration && $legacyRegistration->isLockedToChangeStatus() === true) {
            $this->messenger->append('Situação da matrícula ' . $matriculaId . ' não pode ser alterada pois esta bloqueada para mudança de situação');
            return;
        }

        $this->serviceBoletim()->alterarSituacao($newStatus, $matriculaId);
        $this->appendResponse('matricula_id', $matriculaId);
        $this->messenger->append('Situação da matrícula ' . $matriculaId . ' alterada com sucesso.', 'success');
    }

    public function changeRegistrationLockStatus()
    {
        if (! $this->canPostSituacaoAndNota()) {
            $this->messenger->append('Usuário não possui permissão para alterar a situação do bloqueio de status da matrícula.', 'error');
            return;
        }

        $isBlock = $this->getRequest()->bloquear_troca_de_situacao === 'true';
        $matriculaId = $this->getRequest()->matricula_id;

        /** @var LegacyRegistration $legacyRegistration */
        $legacyRegistration = LegacyRegistration::query()->find($matriculaId);
        if ($legacyRegistration === null) {
            return;
        }

        $legacyRegistration->bloquear_troca_de_situacao = $isBlock;
        $legacyRegistration->save();

        $this->appendResponse('matricula_id', $matriculaId);
        $this->appendResponse('bloquear_troca_de_situacao', $isBlock);

        $status = $isBlock ? 'ativado' : 'desativado';
        $this->messenger->append('Bloqueio de troca de situação ' . $status, 'success');
    }

    public function canPostSituacaoAndNota()
    {
        $acesso = new clsPermissoes();

        return $acesso->permissao_cadastra(630, $this->pessoa_logada, 7, null, true);
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'matriculas')) {
            $this->appendResponse('matriculas', $this->getMatriculas());
            $this->appendResponse('instituicao', $this->getInstituicao());
            $this->appendResponse('navegacao_tab', $this->getNavegacaoTab());
            $this->appendResponse('can_change', $this->canChange());
            $this->appendResponse('locked', !$this->validatesPeriodoLancamentoFaltasNotas(false));
        } elseif ($this->isRequestFor('post', 'nota') || $this->isRequestFor('post', 'nota_exame')) {
            $this->postNota();
        } elseif ($this->isRequestFor('post', 'nota_recuperacao_paralela')) {
            $this->postNotaRecuperacaoParalela();
        } elseif ($this->isRequestFor('post', 'nota_recuperacao_especifica')) {
            $this->postNotaRecuperacaoEspecifica();
        } elseif ($this->isRequestFor('post', 'falta')) {
            $this->postFalta();
        } elseif ($this->isRequestFor('post', 'parecer')) {
            $this->postParecer();
        } elseif ($this->isRequestFor('post', 'nota_geral')) {
            $this->postNotaGeral();
        } elseif ($this->isRequestFor('post', 'media')) {
            $this->postMedia();
        } elseif ($this->isRequestFor('post', 'media_desbloqueia')) {
            $this->postMediaDesbloqueia();
        } elseif ($this->isRequestFor('delete', 'media')) {
            $this->deleteMedia();
        } elseif ($this->isRequestFor('post', 'situacao')) {
            $this->postSituacao();
        } elseif ($this->isRequestFor('post', 'bloqueia_troca_de_situacao')) {
            $this->changeRegistrationLockStatus();
        } elseif ($this->isRequestFor('delete', 'nota') || $this->isRequestFor('delete', 'nota_exame')) {
            $this->deleteNota();
        } elseif ($this->isRequestFor('delete', 'nota_recuperacao_paralela')) {
            $this->deleteNotaRecuperacaoParalela();
        } elseif ($this->isRequestFor('delete', 'nota_recuperacao_especifica')) {
            $this->deleteNotaRecuperacaoEspecifica();
        } elseif ($this->isRequestFor('delete', 'falta')) {
            $this->deleteFalta();
        } elseif ($this->isRequestFor('delete', 'parecer')) {
            $this->deleteParecer();
        } elseif ($this->isRequestFor('delete', 'nota_geral')) {
            $this->deleteNotaGeral();
        } else {
            $this->notImplementedOperationError();
        }
    }
}
 