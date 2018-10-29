<?php

use Cocur\Slugify\Slugify;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;

require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Model/NotaGeralDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';

require_once 'include/pmieducar/clsPmieducarTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarBloqueioLancamentoFaltasNotas.inc.php';
require_once 'include/modules/clsModulesAuditoriaNota.inc.php';
require_once 'include/modules/clsModulesNotaExame.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Object/Utils.php';

//todo: Mover pra algum outro lugar
require_once __DIR__ . '/../../../vendor/autoload.php';

class DiarioApiController extends ApiCoreController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';
    protected $_processoAp = 642;

    protected function validatesValueOfAttValueIsInOpcoesNotas()
    {
        return true;
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
            $dataAtual = strtotime(date("Y-m-d"));
            $data_inicio = strtotime($bloqueioAnoLetivo['data_inicio']);
            $data_fim = strtotime($bloqueioAnoLetivo['data_fim']);

            if ($dataAtual < $data_inicio || $dataAtual > $data_fim) {
                $this->messenger->append("O lançamento de notas nessa instituição está bloqueado nesta data.");
                return false;
            }
        }

        return true;
    }

    protected function validatesRegraAvaliacaoHasNota()
    {
        $isValid = $this->serviceBoletim()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM;

        if (!$isValid) {
            $this->messenger->append("Nota não lançada, pois a regra de avaliação não utiliza nota.");
        }

        return $isValid;
    }

    protected function validatesRegraAvaliacaoHasFormulaRecuperacao()
    {
        $isValid = $this->getRequest()->etapa != 'Rc' ||
        !is_null($this->serviceBoletim()->getRegra()->formulaRecuperacao);

        if (!$isValid) {
            $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.");
        }

        return $isValid;
    }

    protected function validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao()
    {
        $isValid = $this->getRequest()->etapa != 'Rc' ||
            ($this->serviceBoletim()->getRegra()->formulaRecuperacao->get('tipoFormula') ==
            FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO);

        if (!$isValid) {
            $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.");
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
                $etapaId, $componenteCurricularId
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
                $etapaId, $componenteCurricularId
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

        $tiposParecerAnual = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL);

        $parecerAnual = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
            $tiposParecerAnual);

        if ($parecerAnual && $etapa != 'An') {
            $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado 'An' e foi recebido '{$etapa}'.");
        } elseif (!$parecerAnual && $etapa == 'An') {
            $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado um valor diferente de 'An'.");
        } else {
            $isValid = true;
        }

        return $isValid;
    }

    protected function validatesPresenceOfComponenteCurricularIdIfParecerComponente()
    {
        $tiposParecerComponente = array(RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);

        $parecerPorComponente = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
            $tiposParecerComponente);

        return (!$parecerPorComponente) || $this->validatesPresenceOf('componente_curricular_id');
    }

    // post parecer validations

    protected function validatesRegraAvaliacaoHasParecer()
    {
        $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
        $isValid = $tpParecer != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM;

        if (!$isValid) {
            $this->messenger->append("Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.");
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
        $etapasComNota = array();

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
                $msg = "Nota somente pode ser removida, após remover as notas lançadas nas etapas posteriores: " .
                join(', ', $etapasComNota) . '.';
                $this->messenger->append($msg, 'error');
            }
        }

        return empty($etapasComNota);
    }

    // delete falta validations

    protected function validatesInexistenceFaltasInNextEtapas()
    {
        $etapasComFalta = array();

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
                $this->messenger->append("Falta somente pode ser removida, após remover as faltas lançadas nas etapas posteriores: " . join(', ', $etapasComFalta) . '.', 'error');
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

    protected function validatesPeriodoLancamentoFaltasNotas()
    {

        $bloqueioLancamentoFaltasNotas = new clsPmieducarBloqueioLancamentoFaltasNotas(null,
            $this->getRequest()->ano_escolar,
            $this->getRequest()->escola_id,
            $this->getRequest()->etapa);

        $bloquearLancamento = $bloqueioLancamentoFaltasNotas->verificaPeriodo();

        $user = $this->getSession()->id_pessoa;
        $processoAp = 999849;
        $obj_permissao = new clsPermissoes();

        $permissaoLancamento = $obj_permissao->permissao_cadastra($processoAp, $user, 7);

        if ($bloquearLancamento || $permissaoLancamento) {
            return true;
        }

        $this->messenger->append('Não é permitido realizar esta alteração fora do período de lançamento de notas/faltas', 'error');
        return false;
    }

    // responders validations

    protected function canGetMatriculas()
    {
        return $this->validatesPresenceOf(array('instituicao_id',
            'escola_id',
            'curso_id',
            'curso_id',
            'serie_id',
            'turma_id',
            'ano',
            'etapa')) &&
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
        $this->validatesIsNumeric('att_value') &&
        $this->validatesValueOfAttValueIsInOpcoesNotas(false) &&
        $this->validatesPresenceOf('componente_curricular_id') &&
        $this->validatesRegraAvaliacaoHasNota() &&
        $this->validatesRegraAvaliacaoHasFormulaRecuperacao() &&
        $this->validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() &&
        $this->validatesPreviousNotasHasBeenSet();
    }

    protected function canPostNotaGeral()
    {
        return $this->canPost() &&
        $this->validatesIsNumeric('att_value');
        // $this->validatesRegraAvaliacaoHasNota() &&
        // $this->validatesRegraAvaliacaoHasFormulaRecuperacao() &&
        // $this->validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() &&
        // $this->validatesPreviousNotasHasBeenSet();
    }

    protected function canPostFalta()
    {
        return $this->canPost() &&
        $this->validatesIsNumeric('att_value') &&
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
        $this->validatesInexistenceNotasInNextEtapas();
    }

    protected function canDeleteFalta()
    {
        return $this->canDelete() &&
        $this->validatesInexistenceFaltasInNextEtapas();
    }

    protected function canDeleteParecer()
    {
        return $this->canDelete() &&
        $this->validatesEtapaParecer() &&
        $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
    }

    // responders

    // post

    protected function postNota()
    {
        if ($this->canPostNota()) {
            $array_nota = array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'nota' => urldecode($this->getRequest()->att_value),
                'etapa' => $this->getRequest()->etapa,
                'notaOriginal' => urldecode($this->getRequest()->nota_original));

            if ($_notaAntiga = $this->serviceBoletim()->getNotaComponente($this->getRequest()->componente_curricular_id, $this->getRequest()->etapa)) {
                $array_nota['notaRecuperacaoParalela'] = $_notaAntiga->notaRecuperacaoParalela;
                $array_nota['notaRecuperacaoEspecifica'] = $_notaAntiga->notaRecuperacaoEspecifica;
            }

            $nota = new Avaliacao_Model_NotaComponente($array_nota);
            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->inserirAuditoriaNotas($_notaAntiga, $nota);
            $this->messenger->append('Nota matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('should_show_recuperacao_especifica', $this->shouldShowRecuperacaoEspecifica());
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        if (!empty($notaNecessariaExame) && in_array($this->getSituacaoComponente(), array('Em exame', 'Aprovado após exame', 'Retido'))) {
            $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
        } else {
            $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
        }

    }

    protected function postNotaGeral()
    {
        if ($this->canPostNotaGeral()) {
            $notaGeral = urldecode($this->getRequest()->att_value);
            $nota = new Avaliacao_Model_NotaGeral(array(
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaGeral));

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
    }

    protected function postMedia()
    {
        if ($this->canPostMedia()) {
            $mediaLancada = urldecode($this->getRequest()->att_value);
            $componenteCurricular = $this->getRequest()->componente_curricular_id;
            $etapa = $this->getRequest()->etapa;

            $this->serviceBoletim()->updateMediaComponente($mediaLancada, $componenteCurricular, $etapa);
            $this->messenger->append('Média da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
            $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
            $this->appendResponse('situacao', $this->getSituacaoComponente($this->getRequest()->componente_curricular_id));
            $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        } else {
            $this->messenger->append('Usuário não possui permissão para alterar a média do aluno.', 'error');
        }
    }

    protected function deleteMedia()
    {
        if ($this->canDeleteMedia()) {

            $media = $this->getMediaAtual();
            if (empty($media) && !is_numeric($media)) {
                $this->messenger->append('Média matrícula ' . $this->getRequest()->matricula_id . ' inexistente ou já removida.', 'notice');
            } else {
                $this->serviceBoletim()->updateMediaComponente(0, $this->getRequest()->componente_curricular_id, $this->getRequest()->etapa);
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

    protected function postNotaRecuperacaoParalela()
    {
        if ($this->canPostNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaRecuperacaoParalela = urldecode($this->getRequest()->att_value);

            $notaNova = (($notaRecuperacaoParalela > $notaOriginal) ? $notaRecuperacaoParalela : $notaOriginal);

            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaNova,
                'notaRecuperacaoParalela' => urldecode($this->getRequest()->att_value),
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));
        $this->appendResponse('nota_nova', ($notaNova > $notaOriginal ? $notaNova : null));
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        if (!empty($notaNecessariaExame) && in_array($this->getSituacaoComponente(), array('Em exame', 'Aprovado após exame', 'Retido'))) {
            $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
        } else {
            $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
        }

    }

    protected function postNotaRecuperacaoEspecifica()
    {
        if ($this->canPostNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaRecuperacaoParalela = urldecode($this->getRequest()->att_value);

            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoEspecifica' => urldecode($this->getRequest()->att_value),
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        }

        // Se está sendo lançada nota de recuperação, obviamente o campo deve ser visível
        $this->appendResponse('should_show_recuperacao_especifica', true);
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
        $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
        $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));

        if (!empty($notaNecessariaExame) && in_array($this->getSituacaoComponente(), array('Em exame', 'Aprovado após exame', 'Retido'))) {
            $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
        } else {
            $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
        }

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
            $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

            if ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE) {
                $parecer = $this->getParecerComponente();
            } else {
                $parecer = $this->getParecerGeral();
            }

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
    }

    protected function deleteNotaRecuperacaoParalela()
    {
        if ($this->canDeleteNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaAtual = $this->getNotaAtual();
            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoEspecifica' => $notaRecuperacaoEspecifica,
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' excluída com sucesso.', 'success');

            $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
            $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
            $this->appendResponse('situacao', $this->getSituacaoComponente());
            $this->appendResponse('nota_original', $notaOriginal);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        }
    }

    protected function deleteNotaRecuperacaoEspecifica()
    {
        if ($this->canDeleteNota()) {
            $notaOriginal = $this->getNotaOriginal();
            $notaAtual = $this->getNotaAtual();
            $nota = new Avaliacao_Model_NotaComponente(array(
                'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                'etapa' => $this->getRequest()->etapa,
                'nota' => $notaOriginal,
                'notaRecuperacaoParalela' => $notaRecuperacaoParalela,
                'notaOriginal' => $notaOriginal));

            $this->serviceBoletim()->addNota($nota);
            $this->trySaveServiceBoletim();
            $this->messenger->append('Nota de recuperação da matrícula ' . $this->getRequest()->matricula_id . ' excluída com sucesso.', 'success');

            $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
            $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
            $this->appendResponse('situacao', $this->getSituacaoComponente());
            $this->appendResponse('nota_original', $notaOriginal);
            $this->appendResponse('media', $this->getMediaAtual($this->getRequest()->componente_curricular_id));
            $this->appendResponse('media_arredondada', $this->getMediaArredondadaAtual($this->getRequest()->componente_curricular_id));
        }
    }

    protected function deleteFalta()
    {
        $canDelete = $this->canDeleteFalta();
        $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
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
                $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

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

        $this->messenger->append('Nota geral da matrícula ' . $this->getRequest()->matricula_id . ' removida com sucesso.', 'success');
        $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
        $this->appendResponse('situacao', $this->getSituacaoComponente());
    }

    // get

    protected function getMatriculas()
    {
        $regras = $matriculas = array();

        if ($this->canGetMatriculas()) {
            $alunos = new clsPmieducarMatriculaTurma();
            $alunos->setOrderby("sequencial_fechamento , translate(pessoa.nome,'" . Portabilis_String_Utils::toLatin1(åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ) . "', '" . Portabilis_String_Utils::toLatin1(aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN) . "')");

            $alunos = $alunos->lista(
                $this->getRequest()->matricula_id,
                $this->getRequest()->turma_id,
                null,
                null,
                null,
                null,
                null,
                null,
                2,
                $this->getRequest()->serie_id,
                $this->getRequest()->curso_id,
                $this->getRequest()->escola_id,
                $this->getRequest()->instituicao_id,
                $this->getRequest()->aluno_id,
                null,
                null,
                null,
                null,
                $this->getRequest()->ano,
                null,
                true,
                null,
                null,
                true,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                true
            );

            if (!is_array($alunos)) {
                $alunos = array();
            }

            foreach ($alunos as $aluno) {
                $matricula = array();
                $matriculaId = $aluno['ref_cod_matricula'];
                $turmaId = $aluno['ref_cod_turma'];
                $serieId = $aluno['ref_ref_cod_serie'];

                // seta id da matricula a ser usado pelo metodo serviceBoletim
                $this->setCurrentMatriculaId($matriculaId);

                if (!(dbBool($aluno['remanejado']) || dbBool($aluno['transferido']) || dbBool($aluno['abandono']) || dbBool($aluno['reclassificado']) || dbBool($aluno['falecido']))) {
                    $matricula['componentes_curriculares'] = $this->loadComponentesCurricularesForMatricula($matriculaId, $turmaId, $serieId);
                }

                $matricula['matricula_id'] = $aluno['ref_cod_matricula'];
                $matricula['aluno_id'] = $aluno['ref_cod_aluno'];
                $matricula['nome'] = $this->safeString($aluno['nome_aluno']);

                if (dbBool($aluno['remanejado'])) {
                    $matricula['situacao_deslocamento'] = 'Remanejado';
                } elseif (dbBool($aluno['transferido'])) {
                    $matricula['situacao_deslocamento'] = 'Transferido';
                } elseif (dbBool($aluno['abandono'])) {
                    $matricula['situacao_deslocamento'] = 'Abandono';
                } elseif (dbBool($aluno['reclassificado'])) {
                    $matricula['situacao_deslocamento'] = 'Reclassificado';
                } elseif (dbBool($aluno['falecido'])) {
                    $matricula['situacao_deslocamento'] = 'Falecido';
                } else {
                    $matricula['situacao_deslocamento'] = null;
                }

                $matricula['regra'] = $this->getRegraAvaliacao();

                $regras[$matricula['regra']['id']] = $matricula['regra'];

                $matriculas[] = $matricula;
            }
        }

        // adiciona regras de avaliacao
        if (!empty($matriculas)) {
            $this->appendResponse('details', array_values($regras));
        }

        $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);

        return $matriculas;
    }

    // metodos auxiliares responders

    // TODO usar esta funcao onde é verificado se parecer geral
    protected function parecerGeral()
    {
        $tiposParecerGeral = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL);

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
            throw new CoreExt_Exception("Não foi possivel recuperar o id da matricula atual.");
        }

        return $matriculaId;
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
            $this->_boletimServiceInstances = array();
        }

        // set service
        if (!isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
            try {
                $params = array(
                    'matricula' => $matriculaId,
                    'usuario' => $this->getSession()->id_pessoa,
                    'componenteCurricularId' => $this->getRequest()->componente_curricular_id,
                );
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
            $this->serviceBoletim()->saveFaltas();
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
        return new Avaliacao_Model_FaltaGeral(array(
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    protected function getFaltaComponente()
    {
        return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    // metodos auxiliares getParecer

    protected function getParecerComponente()
    {
        return new Avaliacao_Model_ParecerDescritivoComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    protected function getParecerGeral()
    {
        return new Avaliacao_Model_ParecerDescritivoGeral(array(
            'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
            'etapa' => $this->getRequest()->etapa,
        ));
    }

    // metodos auxiliares getSituacaoComponente

    protected function getSituacaoComponente($ccId = null)
    {
        if (is_null($ccId)) {
            $ccId = $this->getRequest()->componente_curricular_id;
        }

        $situacao = null;

        $situacoes = $this->getSituacaoComponentes();
        if(isset($situacoes[$ccId])){
            $situacao = $situacoes[$ccId];
        }

        return $this->safeString($situacao);
    }

    protected function getSituacaoComponentes()
    {
        $situacoes = array();

        try {
            $componentesCurriculares = $this->serviceBoletim()->getSituacaoComponentesCurriculares()->componentesCurriculares;
            foreach($componentesCurriculares as $componenteCurricularId => $situacaoCc){
                $situacoes[$componenteCurricularId] = App_Model_MatriculaSituacao::getInstance()->getValue($situacaoCc->situacao);
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
        $componentesCurriculares = array();

        $componenteCurricularId = $this->getRequest()->componente_curricular_id;
        $etapa = $this->getRequest()->etapa;

        $_componentesCurriculares = App_Model_IedFinder::getComponentesPorMatricula($matriculaId, null, null, $componenteCurricularId, $etapa, $turmaId);

        $turmaId = $this->getRequest()->turma_id;
        $situacoes = $this->getSituacaoComponentes();

        $slugify = new Slugify();

        foreach ($_componentesCurriculares as $_componente) {
            $componente = array();
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

            $componente['nota_necessaria_exame'] = ($gravaNotaExame ? $this->getNotaNecessariaExame($componente['id']) : null);
            $componente['ordenamento'] = $_componente->get('ordenamento');
            $componente['nota_recuperacao_paralela'] = $this->getNotaRecuperacaoParalelaAtual($etapa, $componente['id']);
            $componente['nota_recuperacao_especifica'] = $this->getNotaRecuperacaoEspecificaAtual($etapa, $componente['id']);
            $componente['should_show_recuperacao_especifica'] = $this->shouldShowRecuperacaoEspecifica($etapa, $componente['id']);
            $componente['nota_original'] = $this->getNotaOriginal($etapa, $componente['id']);
            $componente['nota_geral_etapa'] = $this->getNotaGeral($etapa);
            $componente['media'] = $this->getMediaAtual($componente['id']);
            $componente['media_arredondada'] = $this->getMediaArredondadaAtual($componente['id']);

            if (!empty($componente['nota_necessaria_exame'])) {
                $this->createOrUpdateNotaExame($matriculaId, $componente['id'], $componente['nota_necessaria_exame']);
            } else {
                $this->deleteNotaExame($matriculaId, $componente['id']);
            }

            //buscando pela área do conhecimento
            $area = $this->getAreaConhecimento($componente['id']);
            $nomeArea = (($area->secao != '') ? $area->secao . ' - ' : '') . $area->nome;
            $componente['ordenamento_ac'] = $area->ordenamento_ac;
            $componente['area_id'] = $area->id;
            $componente['area_nome'] = mb_strtoupper($nomeArea, 'UTF-8');

            //criando chave para ordenamento temporário
            //área de conhecimento + componente curricular

            $componente['ordem_nome_area_conhecimento'] = $slugify->slugify($nomeArea);
            $componente['ordem_componente_curricular'] = $slugify->slugify($_componente->get('nome'));
            $componentesCurriculares[] = $componente;
        }

        $ordenamentoComponentes = array();

        foreach ($componentesCurriculares as $chave => $componente) {
            $ordenamentoComponentes['ordenamento_ac'][$chave] = $componente['ordenamento_ac'];
            $ordenamentoComponentes['ordenamento'][$chave] = $componente['ordenamento'];
            $ordenamentoComponentes['ordem_nome_area_conhecimento'][$chave] = $componente['ordem_nome_area_conhecimento'];
            $ordenamentoComponentes['ordem_componente_curricular'][$chave] = $componente['ordem_componente_curricular'];
        }
        array_multisort($ordenamentoComponentes['ordenamento_ac'], SORT_ASC, SORT_NUMERIC,
            $ordenamentoComponentes['ordem_nome_area_conhecimento'], SORT_ASC,
            $ordenamentoComponentes['ordenamento'], SORT_ASC, SORT_NUMERIC,
            $ordenamentoComponentes['ordem_componente_curricular'], SORT_ASC,
            $componentesCurriculares);

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

        require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
        $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        $where = array('id' => $componenteCurricularId);

        $area = $mapper->findAll(array('area_conhecimento'), $where);

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
     *
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

        // $media = round($media,1);

        return str_replace(',', '.', $media);
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
                    $nota = new Avaliacao_Model_NotaComponente(array(
                        'componenteCurricular' => $componenteCurricularId,
                        'nota' => $notaRecuperacao->notaOriginal,
                        'etapa' => $notaRecuperacao->etapa,
                        'notaOriginal' => $notaRecuperacao->notaOriginal,
                        'notaRecuperacaoParalela' => $notaRecuperacao->notaRecuperacaoParalela,
                    ));

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

        $nota = urldecode($this->serviceBoletim()->preverNotaRecuperacao($componenteCurricularId));

        return str_replace(',', '.', $nota);
    }

    /**
     * @deprecated
     *
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

    protected function getOpcoesFaltas()
    {
        $opcoes = array();

        foreach (range(0, 100, 1) as $f) {
            $opcoes[$f] = $f;
        }

        return $opcoes;
    }

    protected function canGetOpcoesNotas()
    {
        return true;
    }

    protected function getOpcoesNotas()
    {
        $opcoes = array();

        if ($this->canGetOpcoesNotas()) {
            $tpNota = $this->serviceBoletim()->getRegra()->get('tipoNota');
            $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;

            if ($tpNota != $cnsNota::NENHUM) {
                $tabela = $this->serviceBoletim()->getRegra()->tabelaArredondamento->findTabelaValor();

                foreach ($tabela as $index => $item) {
                    if ($tpNota == $cnsNota::NUMERICA) {
                        $nota = str_replace(',', '.', (string) $item->nome);
                        $opcoes[$nota] = $nota;
                    } else {
                        // $nota                   = str_replace(',', '.', (string) $item->valorMaximo);
                        $opcoes[$index] = array('valor_minimo' => str_replace(',', '.', (string) $item->valorMinimo),
                            'valor_maximo' => str_replace(',', '.', (string) $item->valorMaximo),
                            'descricao' => $this->safeString($item->nome . ' (' . $item->descricao . ')'));

                    }
                }
            }
        }

        return $opcoes;
    }

    //Usado apenas quando na regra o sistema de nota é "Numérica e conceitual"
    protected function getOpcoesNotasConceituais()
    {
        $opcoes = array();

        if ($this->canGetOpcoesNotas()) {
            $tpNota = $this->serviceBoletim()->getRegra()->get('tipoNota');
            $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;

            if ($tpNota != $cnsNota::NENHUM) {
                $tabela = $this->serviceBoletim()->getRegra()->tabelaArredondamentoConceitual->findTabelaValor();

                foreach ($tabela as $index => $item) {
                    if ($tpNota == $cnsNota::NUMERICA) {
                        $nota = str_replace(',', '.', (string) $item->nome);
                        $opcoes[$nota] = $nota;
                    } else {
                        // $nota                   = str_replace(',', '.', (string) $item->valorMaximo);
                        $opcoes[$index] = array('valor_minimo' => str_replace(',', '.', (string) $item->valorMinimo),
                            'valor_maximo' => str_replace(',', '.', (string) $item->valorMaximo),
                            'descricao' => $this->safeString($item->nome . ' (' . $item->descricao . ')'));

                    }
                }
            }
        }

        return $opcoes;
    }

    protected function getNavegacaoTab()
    {
        return $this->getRequest()->navegacao_tab;
    }

    protected function canGetRegraAvaliacao()
    {
        return true;
    }

    protected function getRegraAvaliacao()
    {
        $itensRegra = array();

        if ($this->canGetRegraAvaliacao()) {
            $regra = $this->serviceBoletim()->getRegra();
            $itensRegra['id'] = $regra->get('id');
            $itensRegra['nome'] = $this->safeString($regra->get('nome'));
            $itensRegra['nota_maxima_geral'] = $regra->get('notaMaximaGeral');
            $itensRegra['nota_minima_geral'] = $regra->get('notaMinimaGeral');
            $itensRegra['nota_maxima_exame_final'] = $regra->get('notaMaximaExameFinal');
            $itensRegra['qtd_casas_decimais'] = $regra->get('qtdCasasDecimais');
            $itensRegra['regra_diferenciada_id'] = $regra->get('regraDiferenciada');

            // tipo presença
            $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
            $tpPresenca = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

            if ($tpPresenca == $cnsPresenca::GERAL) {
                $itensRegra['tipo_presenca'] = 'geral';
            } elseif ($tpPresenca == $cnsPresenca::POR_COMPONENTE) {
                $itensRegra['tipo_presenca'] = 'por_componente';
            } else {
                $itensRegra['tipo_presenca'] = $tpPresenca;
            }

            // tipo nota
            $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;
            $tpNota = $this->serviceBoletim()->getRegra()->get('tipoNota');

            if ($tpNota == $cnsNota::NENHUM) {
                $itensRegra['tipo_nota'] = 'nenhum';
            } elseif ($tpNota == $cnsNota::NUMERICA) {
                $itensRegra['tipo_nota'] = 'numerica';
            } elseif ($tpNota == $cnsNota::CONCEITUAL) {
                $itensRegra['tipo_nota'] = 'conceitual';
                //incluido opcoes notas, pois notas conceituais requer isto para visualizar os nomes
            } elseif ($tpNota == $cnsNota::NUMERICACONCEITUAL) {
                $itensRegra['tipo_nota'] = 'numericaconceitual';
            } else {
                $itensRegra['tipo_nota'] = $tpNota;
            }

            //Lançamento de nota manual
            $tpProgressao = $this->serviceBoletim()->getRegra()->get('tipoProgressao');
            $itensRegra['progressao_manual'] = ($tpProgressao == RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);
            $itensRegra['progressao_continuada'] = ($tpProgressao == RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

            // tipo parecer
            $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;
            $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');

            if ($tpParecer == $cnsParecer::NENHUM) {
                $itensRegra['tipo_parecer_descritivo'] = 'nenhum';
            } elseif ($tpParecer == $cnsParecer::ETAPA_COMPONENTE) {
                $itensRegra['tipo_parecer_descritivo'] = 'etapa_componente';
            } elseif ($tpParecer == $cnsParecer::ETAPA_GERAL) {
                $itensRegra['tipo_parecer_descritivo'] = 'etapa_geral';
            } elseif ($tpParecer == $cnsParecer::ANUAL_COMPONENTE) {
                $itensRegra['tipo_parecer_descritivo'] = 'anual_componente';
            } elseif ($tpParecer == $cnsParecer::ANUAL_GERAL) {
                $itensRegra['tipo_parecer_descritivo'] = 'anual_geral';
            } else {
                $itensRegra['tipo_parecer_descritivo'] = $tpParecer;
            }

            // opcoes notas
            $itensRegra['opcoes_notas'] = $this->getOpcoesNotas();
            if ($tpNota == $cnsNota::NUMERICACONCEITUAL) {
                $itensRegra['opcoes_notas_conceituais'] = $this->getOpcoesNotasConceituais();
            }

            // etapas
            $itensRegra['quantidade_etapas'] = $this->serviceBoletim()->getOption('etapas');
        }

        $itensRegra['nomenclatura_exame'] = ($GLOBALS['coreExt']['Config']->app->diario->nomenclatura_exame == 0 ? 'exame' : 'conselho');

        //tipo de recuperação paralela
        $tipoRecuperacaoParalela = $regra->get('tipoRecuperacaoParalela');

        if ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::NAO_USAR) {
            $itensRegra['tipo_recuperacao_paralela'] = 'nao_utiliza';
        } elseif ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA) {
            $itensRegra['tipo_recuperacao_paralela'] = 'por_etapa';
            $itensRegra['media_recuperacao_paralela'] = $this->serviceBoletim()->getRegra()->get('mediaRecuperacaoParalela');
        } elseif ($tipoRecuperacaoParalela == RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS) {
            $itensRegra['tipo_recuperacao_paralela'] = 'etapas_especificas';

            $etapa = $this->getRequest()->etapa;
            if ($regraRecuperacao = $regra->getRegraRecuperacaoByEtapa($etapa)) {
                $itensRegra['habilita_campo_etapa_especifica'] = $regraRecuperacao->getLastEtapa() == $etapa;
                $itensRegra['tipo_recuperacao_paralela_nome'] = $regraRecuperacao->get('descricao');
                $itensRegra['tipo_recuperacao_paralela_nota_maxima'] = $regraRecuperacao->get('notaMaxima');
            } else {
                $itensRegra['habilita_campo_etapa_especifica'] = false;
                $itensRegra['tipo_recuperacao_paralela_nome'] = '';
                $itensRegra['tipo_recuperacao_paralela_nota_maxima'] = 0;
            }

        }
        if ($regra->get('notaGeralPorEtapa') == '1') {
            $itensRegra['nota_geral_por_etapa'] = "SIM";
        } else {
            $itensRegra['nota_geral_por_etapa'] = "NAO UTILIZA";
        }

        $itensRegra['definir_componente_por_etapa'] = $regra->get('definirComponentePorEtapa') == 1;

        return $itensRegra;
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
        return ($GLOBALS['coreExt']['Config']->app->auditoria->notas == "1");
    }

    public function canChange()
    {
        $user = $this->getSession()->id_pessoa;
        $processoAp = $this->_processoAp;
        $obj_permissao = new clsPermissoes();

        return $obj_permissao->permissao_cadastra($processoAp, $user, 7);
    }

    public function postSituacao()
    {
        if ($this->canPostSituacaoAndNota()) {
            $novaSituacao = $this->getRequest()->att_value;
            $matriculaId = $this->getRequest()->matricula_id;

            $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);

            $this->serviceBoletim()->alterarSituacao($novaSituacao, $matriculaId);
            $this->messenger->append('Situação da matrícula ' . $this->getRequest()->matricula_id . ' alterada com sucesso.', 'success');
        } else {
            $this->messenger->append('Usuário não possui permissão para alterar a situação da matrícula.', 'error');
        }
    }

    public function canPostSituacaoAndNota()
    {

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        $acesso = new clsPermissoes();
        session_write_close();
        return $acesso->permissao_cadastra(630, $this->pessoa_logada, 7, null, true);

    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'matriculas')) {
            $this->appendResponse('matriculas', $this->getMatriculas());
            $this->appendResponse('navegacao_tab', $this->getNavegacaoTab());
            $this->appendResponse('can_change', $this->canChange());
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
        } elseif ($this->isRequestFor('delete', 'media')) {
            $this->deleteMedia();
        } elseif ($this->isRequestFor('post', 'situacao')) {
            $this->postSituacao();
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
