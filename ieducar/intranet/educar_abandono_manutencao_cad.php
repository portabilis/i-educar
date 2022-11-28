<?php
use App\Models\Frequencia;
use App\Models\FrequenciaAluno;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_transferencia_solicitacao;
    public $ref_cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_matricula_entrada;
    public $ref_cod_matricula_saida;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $data_transferencia;
    public $data_cancel;
    public $ref_cod_instituicao;
    public $abandono_tipo;

    public $ref_cod_matricula;
    public $transferencia_tipo;
    public $ref_cod_aluno;
    public $nm_aluno;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $this->url_cancelar = "educar_manutencao_matricula.php";

        $this->breadcrumb('Registro do abandono de matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, false, false, false, '', '', '', '', true);
        }

        $this->ref_cod_instituicao = $det_aluno['ref_cod_abandono_tipo'];

        $tiposAbandono  = new clsPmieducarAbandonoTipo();
        $tiposAbandono  = $tiposAbandono->lista(null, null, null, null, null, null, null, null, 1, $ref_cod_instituicao);

        foreach ($tiposAbandono as $tipoAbandono) {
            $selectOptions[$tipoAbandono['cod_abandono_tipo']] = $tipoAbandono['nome'];
        }

        $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

        $options = ['label' => 'Motivo do abandono', 'resources' => $selectOptions, 'value' => ''];

        $this->inputsHelper()->select('abandono_tipo', $options);

        $this->inputsHelper()->date('data_cancel', ['label' => 'Data do abandono', 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y')]);
        // text
        $this->campoMemo('observacao', 'Observa&ccedil;&atilde;o', $this->observacao, 60, 5, false);
    }

    public function Novo()
    {
        $db = new clsBanco();
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");


        $tamanhoObs = strlen($this->observacao);
        if ($tamanhoObs > 300) {
            $this->mensagem = 'O campo observação deve conter no máximo 300 caracteres.<br>';

            return false;
        }

        $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, $this->pessoa_logada, null, null, 6);
        $obj_matricula->data_cancel = Portabilis_Date_Utils::brToPgSQL($this->data_cancel);

        $det_matricula = $obj_matricula->detalhe();

        if (is_null($det_matricula['data_matricula'])) {
            if (substr($det_matricula['data_cadastro'], 0, 10) > $obj_matricula->data_cancel) {
                $this->mensagem = 'Data de abandono não pode ser inferior a data da matrícula.<br>';

                return false;
            }
        } else {
            if (substr($det_matricula['data_matricula'], 0, 10) > $obj_matricula->data_cancel) {
                $this->mensagem = 'Data de abandono não pode ser inferior a data da matrícula.<br>';

                return false;
            }
        }

        $turma = new clsPmieducarTurma($_GET['turma']);
        $tipoTurma = $turma->getTipoTurma();

        if ($tipoTurma == 1) {

            $atendimento = new clsModulesComponenteMinistradoAee();
            $dataAtendimento = $atendimento->selectDataAtendimentoByMatricula($_GET['ref_cod_matricula']);

            $frequencia = Frequencia::where('ref_cod_turma', $_GET['turma'])->where('data', '>=', $obj_matricula->data_cancel)->orderBy('id', 'DESC')->get();
            foreach($frequencia as $list) {
                FrequenciaAluno::where('ref_frequencia',$list['id'])->where('ref_cod_matricula',$_GET['cod_matricula'])->delete();
            }
        }

        if ($tipoTurma == 0) {

            $frequencia = new clsModulesFrequencia();
            $dataFrequencia = $frequencia->selectDataFrequenciaByTurma($_GET['turma']);

            $frequencia = Frequencia::where('ref_cod_turma', $_GET['turma'])->where('data', '>=', $obj_matricula->data_cancel)->orderBy('id', 'DESC')->get();
            foreach($frequencia as $list) {
                FrequenciaAluno::where('ref_frequencia',$list['id'])->where('ref_cod_matricula',$_GET['cod_matricula'])->delete();
            }
        }

        if ($obj_matricula->edita()) {
            if ($obj_matricula->cadastraObs($this->observacao, $this->abandono_tipo)) {
                $enturmacoes = new clsPmieducarMatriculaTurma();
                $enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, 1);

                foreach ($enturmacoes as $enturmacao) {
                    $enturmacao = new clsPmieducarMatriculaTurma($this->ref_cod_matricula, $enturmacao['ref_cod_turma'], $this->pessoa_logada, null, null, null, 0, null, $enturmacao['sequencial']);
                    $detEnturmacao = $enturmacao->detalhe();
                    $detEnturmacao = $detEnturmacao['data_enturmacao'];
                    $enturmacao->data_enturmacao = $detEnturmacao;

                    if (!$enturmacao->edita()) {
                        $this->mensagem = 'N&atilde;o foi poss&iacute;vel desativar as enturma&ccedil;&otilde;es da matr&iacute;cula.';

                        return false;
                    } else {
                        $enturmacao->marcaAlunoAbandono($this->data_cancel);
                    }
                }

                $notaAluno = (new Avaliacao_Model_NotaAlunoDataMapper())
                    ->findAll(['id'], ['matricula_id' => $obj_matricula->cod_matricula])[0];

                if (!is_null($notaAluno)) {
                    (new Avaliacao_Model_NotaComponenteMediaDataMapper())
                        ->updateSituation($notaAluno->get('id'), App_Model_MatriculaSituacao::ABANDONO);
                }

                $this->mensagem .= 'Abandono realizado com sucesso.<br>';
                $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
            }

            $this->mensagem = 'Observação não pode ser salva.<br>';

            return false;
        }
        $this->mensagem = 'Abandono não pode ser realizado.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
    }

    public function Formular()
    {
        $this->title = 'Transfer&ecirc;ncia Solicita&ccedil;&atilde;o';
        $this->processoAp = '578';
    }
    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-confirmar-exclusao.js');
    }
};
