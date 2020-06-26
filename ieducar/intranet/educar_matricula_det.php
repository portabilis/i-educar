<?php

use App\Process;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/Utils/CustomLabel.php';
require_once 'Portabilis/String/Utils.php';
require_once 'lib/App/Model/Educacenso.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'Portabilis/View/Helper/Application.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Matrícula');
        $this->processoAp = 578;
    }
}

class indice extends clsDetalhe
{
    public $titulo;

    public $ref_cod_matricula;

    public $ref_cod_reserva_vaga;

    public $ref_ref_cod_escola;

    public $ref_ref_cod_serie;

    public $ref_cod_abandono_tipo;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_aluno;

    public $aprovado;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function getDescription($description)
    {
        if (empty($description)) {
            return $description;
        }

        $lessDescription = substr($description, 0, strpos($description, ' ', 200)) . '...';

        return "<div align='justify'> <span class='desc-red'>{$lessDescription}</span> <span class='descricao' style='display: none'>{$description}</span><a href='javascript:void(0)' class='ver-mais'>Mostrar mais</a><a href='javascript:void(0)' style='display: none' class='ver-menos'>Mostrar menos</a></div>";
    }

    public function Gerar()
    {
        // carrega estilo para feedback messages, exibindo msgs da api.
        $style = '/modules/Portabilis/Assets/Stylesheets/Frontend.css';
        Portabilis_View_Helper_Application::loadStylesheet($this, $style);

        $this->titulo = 'Matrícula - Detalhe';
        $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

        $this->ref_cod_matricula = $_GET['cod_matricula'];

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);

        if ($lst_matricula) {
            $registro = array_shift($lst_matricula);
        }

        if (!$registro) {
            $this->simpleRedirect('educar_aluno_det.php?cod_aluno=' . $registro['ref_cod_aluno']);
        }

        $verificaMatriculaUltimoAno = $obj_matricula->verificaMatriculaUltimoAno($registro['ref_cod_aluno'], $registro['cod_matricula']);

        $existeSaidaEscola = $obj_matricula->existeSaidaEscola($registro['cod_matricula']);

        // Curso
        $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $curso_id = $registro['ref_cod_curso'];
        $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

        // Série
        $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $serie_id = $registro['ref_ref_cod_serie'];
        $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

        // Nome da instituição
        $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

        // Escola
        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $escola_id = $registro['ref_ref_cod_escola'];
        $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

        // Nome do aluno
        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista(
            $registro['ref_cod_aluno'],
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $nm_aluno = $det_aluno['nome_aluno'];
        }

        if ($registro['cod_matricula']) {
            $this->addDetalhe(['Número Matrícula', $registro['cod_matricula']]);
        }

        if ($nm_aluno) {
            $this->addDetalhe(['Aluno', $nm_aluno]);
        }

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(['Instituição', $registro['ref_cod_instituicao']]);
        }

        if ($registro['ref_ref_cod_escola']) {
            $this->addDetalhe(['Escola', $registro['ref_ref_cod_escola']]);
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Curso', $registro['ref_cod_curso']]);
        }

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(['Série', $registro['ref_ref_cod_serie']]);
        }

        // Nome da turma
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            $this->ref_cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $existeTurma = false;
        $existeTurmaMulti = false;
        $existeTurmaTurnoIntegral = false;
        $existeAtendimentoEspecializado = false;
        $nomesTurmas = [];
        $datasEnturmacoes = [];

        foreach ($enturmacoes as $enturmacao) {
            $turma = new clsPmieducarTurma($enturmacao['ref_cod_turma']);
            $turma = $turma->detalhe();
            $turma_id = $enturmacao['ref_cod_turma'];
            $nomesTurmas[] = $turma['nm_turma'];
            $datasEnturmacoes[] = Portabilis_Date_Utils::pgSQLToBr($enturmacao['data_enturmacao']);

            if (in_array($turma['etapa_educacenso'], App_Model_Educacenso::etapas_multisseriadas())) {
                $existeTurmaMulti = true;
            }

            if ($enturmacao['ativo'] == 0) {
                continue;
            }

            if ($turma['turma_turno_id'] == clsPmieducarTurma::TURNO_INTEGRAL) {
                $existeTurmaTurnoIntegral = true;
            }

            if ($turma['tipo_atendimento'] == TipoAtendimentoTurma::AEE) {
                $existeAtendimentoEspecializado = true;
            }
        }
        $nomesTurmas = implode('<br />', $nomesTurmas);
        $datasEnturmacoes = implode('<br />', $datasEnturmacoes);

        if ($nomesTurmas) {
            $this->addDetalhe(['Turma', $nomesTurmas]);
            $this->addDetalhe(['Data Enturmação', $datasEnturmacoes]);
            $existeTurma = true;
        } else {
            $this->addDetalhe(['Turma', '']);
            $this->addDetalhe(['Data Enturmação', '']);
        }

        if ($registro['ref_cod_reserva_vaga']) {
            $this->addDetalhe(['Número Reserva Vaga', $registro['ref_cod_reserva_vaga']]);
        }

        $campoObs = false;

        $situacao = App_Model_MatriculaSituacao::getSituacao($registro['aprovado']);
        $this->addDetalhe(['Situação', $situacao]);

        if ($registro[aprovado] == 4) {
            $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

            $lst_transferencia = $obj_transferencia->lista(null, null, null, null, null, $registro['cod_matricula'], null, null, null, null, null, 1, null, null, $registro['ref_cod_aluno'], false);

            if (is_array($lst_transferencia)) {
                $det_transferencia = array_shift($lst_transferencia);
            }
            if (!$det_transferencia['ref_cod_escola_destino'] == '0') {
                $tmp_obj = new clsPmieducarEscola($det_transferencia['ref_cod_escola_destino']);
                $tmp_det = $tmp_obj->detalhe();
                $this->addDetalhe(['Escola destino', $tmp_det['nome']]);
            } else {
                $this->addDetalhe(['Escola destino', $det_transferencia['escola_destino_externa']]);
                $this->addDetalhe(['Estado escola destino', $det_transferencia['estado_escola_destino_externa']]);
                $this->addDetalhe(['Município escola destino', $det_transferencia['municipio_escola_destino_externa']]);
            }
        }

        if ($registro['aprovado'] == App_Model_MatriculaSituacao::FALECIDO) {
            $this->addDetalhe(['Observação', Portabilis_String_Utils::toLatin1($registro['observacao'])]);
        }

        if ($existeSaidaEscola) {
            $this->addDetalhe(['Saída da escola', 'Sim']);
            $this->addDetalhe(['Data de saída da escola', Portabilis_Date_Utils::pgSQLToBr($registro['data_saida_escola'])]);
            $this->addDetalhe(['Observação', Portabilis_String_Utils::toLatin1($registro['observacao'])]);
        }

        if ($campoObs) {
            $tipoAbandono = new clsPmieducarAbandonoTipo($registro['ref_cod_abandono_tipo']);
            $tipoAbandono = $tipoAbandono->detalhe();

            $observacaoAbandono = Portabilis_String_Utils::toLatin1($registro['observacao']);

            $this->addDetalhe(['Motivo do Abandono', $tipoAbandono['nome']]);
            $this->addDetalhe(['Observação', $observacaoAbandono]);
        }

        if ($registro[aprovado] == App_Model_MatriculaSituacao::RECLASSIFICADO){
            $this->addDetalhe(['Descrição', $this->getDescription($registro['descricao_reclassificacao'])]);
        }

        $this->addDetalhe(['Formando', $registro['formando'] == 0 ? 'N&atilde;o' : 'Sim']);

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            // verifica se existe transferencia
            if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
                $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

                $lst_transferencia = $obj_transferencia->lista(
                    null,
                    null,
                    null,
                    null,
                    null,
                    $registro['cod_matricula'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    1,
                    null,
                    null,
                    $registro['ref_cod_aluno'],
                    false
                );

                // verifica se existe uma solicitacao de transferencia INTERNA
                if (is_array($lst_transferencia)) {
                    $det_transferencia = array_shift($lst_transferencia);
                }

                $data_transferencia = $det_transferencia['data_transferencia'];
            }

            if ($registro['aprovado'] == 3 &&
                (!is_array($lst_transferencia) && !isset($data_transferencia))
            ) {

                // Verificar se tem permissao para executar cancelamento de matricula
                if ($this->permissao_cancelar()) {
                    $this->array_botao[] = 'Cancelar matrícula';
                    $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente cancelar esta matrícula?\"))go(\"educar_matricula_cad.php?cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
                }

                $this->array_botao[] = 'Ocorrências disciplinares';
                $this->array_botao_url_script[] = "go(\"educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";

                // Apenas libera a dispensa de disciplina quando o aluno estiver enturmado
                if ($registro['ref_ref_cod_serie'] && $existeTurma) {
                    $this->array_botao[] = 'Dispensa de componentes curriculares';
                    $this->array_botao_url_script[] = "go(\"educar_dispensa_disciplina_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
                }

                $dependencia = $registro['dependencia'];

                if ($registro['ref_ref_cod_serie'] && $existeTurma && $dependencia) {
                    $this->array_botao[] = 'Disciplinas de depend&ecirc;ncia';
                    $this->array_botao_url_script[] = "go(\"educar_disciplina_dependencia_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
                }

                $this->array_botao[] = _cl('matricula.detalhe.enturmar');
                $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$registro['cod_matricula']}&ano_letivo={$registro['ano']}\")";

                $this->array_botao[] = 'Abandono';
                $this->array_botao_url_script[] = "go(\"educar_abandono_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

                $this->array_botao[] = 'Falecido';
                $this->array_botao_url_script[] = "go(\"educar_falecido_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

                if ($registro['ref_ref_cod_serie'] && $this->permissaoReclassificar()) {
                    $this->array_botao[] = 'Reclassificar';
                    $this->array_botao_url_script[] = "go(\"educar_matricula_reclassificar_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
                }
            }

            if ($existeTurmaMulti) {
                $this->array_botao[] = 'Etapa do aluno';
                $this->array_botao_url_script[] = "go(\"educar_matricula_etapa_turma_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
            }

            if ($existeAtendimentoEspecializado) {
                $this->array_botao[] = 'Tipo do AEE do aluno';
                $this->array_botao_url_script[] = "go(\"educar_matricula_turma_tipo_aee_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
            }

            if ($existeTurmaTurnoIntegral) {
                $this->array_botao[] = 'Turno';
                $this->array_botao_url_script[] = "go(\"educar_matricula_turma_turno_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
            }

            if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
                if (is_array($lst_transferencia) && isset($data_transferencia)) {
                    $this->array_botao[] = 'Cancelar solicitação transferência';
                    $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
                } elseif ($registro['ref_ref_cod_serie']) {
                    $this->array_botao[] = _cl('matricula.detalhe.solicitar_transferencia');
                    $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
                }

                if ($registro['aprovado'] == 3 &&
                    (!is_array($lst_transferencia) && !isset($data_transferencia))
                ) {
                    if ($registro['formando'] == 0) {
                        $this->array_botao[] = 'Formando';
                        $this->array_botao_url_script[] = "if(confirm(\"Deseja marcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=1\")";
                    } else {
                        $this->array_botao[] = 'Desmarcar como formando';
                        $this->array_botao_url_script[] = "if(confirm(\"Deseja desmarcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=0\")";
                    }
                }
            }

            $ultimaMatricula = $obj_matricula->getEndMatricula($registro['ref_cod_aluno']);
            if ($registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO && $this->canCancelTransferencia($registro['cod_matricula'])) {
                $this->array_botao[] = 'Cancelar transferência';

                # TODO ver se código, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
                $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
            } elseif ($registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO && $ultimaMatricula == 4) {
                $this->array_botao[] = 'Cancelar transferência';

                # TODO ver se código, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
                $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
            }

            if ($registro['aprovado'] == App_Model_MatriculaSituacao::ABANDONO) {
                $this->array_botao[] = 'Desfazer abandono';
                $this->array_botao_url_script[] = "deleteAbandono({$registro['cod_matricula']})";
            }

            if (!$existeSaidaEscola &&
                $verificaMatriculaUltimoAno &&
                ($registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::REPROVADO ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS)) {
                $this->array_botao[] = 'Saída da escola';
                $this->array_botao_url_script[] = "go(\"educar_saida_escola_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&escola={$registro['ref_ref_cod_escola']}\");";
            }

            if ($existeSaidaEscola && $verificaMatriculaUltimoAno) {
                $this->array_botao[] = 'Cancelar saída da escola';
                $this->array_botao_url_script[] = "desfazerSaidaEscola({$registro['cod_matricula']})";
            }

            if ($registro['aprovado'] == App_Model_MatriculaSituacao::RECLASSIFICADO) {
                $this->array_botao[] = 'Desfazer reclassificação';
                $this->array_botao_url_script[] = "deleteReclassificacao({$registro['cod_matricula']})";
            }
        }

        if ($this->user()->can('view', Process::ENROLLMENT_HISTORY)) {
            $this->array_botao[] = 'Histórico de enturmações';
            $link = route('enrollments.enrollment-history', ['id' => $registro['cod_matricula']]);
            $this->array_botao_url_script[] = "go(\"{$link}\")";
        }

        $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $registro['ref_cod_aluno'];
        $this->largura = '100%';

        $this->breadcrumb('Matrícula', [
            'educar_index.php' => 'Escola',
        ]);

        // js
        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/MatriculaShow.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    // Verificar se pode cancelar matricula
    public function permissao_cancelar()
    {
        $acesso = new clsPermissoes();

        return $acesso->permissao_excluir(627, $this->pessoa_logada, 7, null, true);
    }

    public function permissaoReclassificar()
    {
        $acesso = new clsPermissoes();

        return $acesso->permissao_cadastra(Process::RECLASSIFY_REGISTRATION, $this->pessoa_logada, 7, null, true);
    }

    public function canCancelTransferencia($matriculaId)
    {
        $sql = "SELECT transferencia_solicitacao.cod_transferencia_solicitacao
              FROM pmieducar.transferencia_solicitacao
             WHERE ref_cod_matricula_saida = $matriculaId
               AND ativo = 1";

        $db = new clsBanco();

        return $db->CampoUnico($sql);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
