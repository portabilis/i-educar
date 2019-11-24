<?php

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar');
        $this->processoAp = '845';
    }
}

use App\Models\LegacyInstitution;
use Illuminate\Support\Facades\Session;

class indice extends clsCadastro
{
    public $pessoa_logada;

    public $data_matricula;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(845, $this->pessoa_logada, 7, 'educar_index.php');

        $this->breadcrumb('Rematrícula automática', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $anoLetivoHelperOptions = ['situacoes' => ['em_andamento', 'nao_iniciado']];

        $this->inputsHelper()->dynamic('ano');
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie']);
        $this->inputsHelper()->dynamic('turma', ['label' => 'Selecione a turma do ano anterior', 'required' => false]);
        $this->inputsHelper()->dynamic('anoLetivo', ['label' => 'Ano destino'], $anoLetivoHelperOptions);
        $this->inputsHelper()->date('data_matricula', ['label' => 'Data da matricula', 'placeholder' => 'dd/mm/yyyy']);

        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Cadastro/Assets/Javascripts/RematriculaAutomatica.js',
            '/modules/Cadastro/Assets/Javascripts/RematriculaAutomaticaModal.js'
        ]);
    }

    public function Novo()
    {
        $anoLetivo = new clsPmieducarEscolaAnoLetivo();
        $anoLetivo = $anoLetivo->lista($this->ref_cod_escola, null, null, null, 1);

        $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);

        if (count($anoLetivo) > 1) {
            Session::now('notice', 'Nenhum aluno rematriculado. Certifique-se que somente um ano letivo encontra-se em aberto.');

            return false;
        }

        // Valida se a data da matrícula não é menor que a data do início do
        // ano letivo.

        $obj = new clsPmieducarAnoLetivoModulo();
        $obj->setOrderBy('sequencial ASC');
        $registros = $obj->lista($anoLetivo[0]['ano'], $this->ref_cod_escola);

        $inicioAnoLetivo = $registros[0]['data_inicio'];

        /** @var LegacyInstitution $instituicao */
        $instituicao = app(LegacyInstitution::class);

        if ($this->data_matricula < $inicioAnoLetivo) {
            if (!$instituicao->allowRegistrationOutAcademicYear) {
                Session::now('notice', 'A data da matrícula deve ser posterior ao dia ' . Portabilis_Date_Utils::pgSQLToBr($inicioAnoLetivo) . '.');

                return false;
            }
        }

        $this->db = new clsBanco();
        $this->db2 = new clsBanco();
        $this->db3 = new clsBanco();

        $result = $this->rematricularAlunos(
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_serie,
            $this->ref_cod_turma,
            $_POST['ano']
        );

        return $result;
    }

    public function Editar()
    {
        return true;
    }

    protected function rematricularAlunos($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        try {
            $result = $this->selectMatriculas($escolaId, $cursoId, $serieId, $turmaId, $this->ano_letivo);
            $alunosSemInep = $this->getAlunosSemInep($escolaId, $cursoId, $serieId, $turmaId, $ano);
            $alunosComSaidaDaEscola = $this->getAlunosComSaidaDaEscola($escolaId, $cursoId, $serieId, $turmaId, $ano);
            $count = 0;
            $nomesAlunos = [];

            if (count($alunosSemInep) === 0) {
                while ($result && $this->db->ProximoRegistro()) {
                    list($matriculaId, $alunoId, $situacao, $nomeAluno) = $this->db->Tupla();

                    $this->db2->Consulta(
                        "
                            UPDATE pmieducar.matricula
                            SET ultima_matricula = '0'
                            WHERE cod_matricula = $matriculaId
                        "
                    );

                    if ($result && $situacao == 1 || $situacao == 12 || $situacao == 13) {
                        $result = $this->rematricularAlunoAprovado($escolaId, $serieId, $this->ano_letivo, $alunoId);
                    } elseif ($result && $situacao == 2 || $situacao == 14) {
                        $result = $this->rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $this->ano_letivo, $alunoId);
                    }

                    $nomesAlunos[] = $nomeAluno;
                    $count += 1;

                    if (!$result) {
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            Session::now('error', $e->getMessage());

            return false;
        }

        if ($result) {
            if ($count > 0) {
                $mensagem = '';

                if ($count > 0) {
                    $mensagem .= "O(s) aluno(s) foram rematriculados com sucesso em $this->ano_letivo. Clique <a href='#' onclick='ModalAlunos.init(\"alunos_rematriculados\");'>aqui</a> para conferir os alunos rematrículados</br>";

                    $mensagem .= '</br> As enturmações podem ser realizadas em: Movimentação > Enturmação.';
                    if (count($alunosComSaidaDaEscola) > 0) {
                        $mensagem .= '</br></br>Alguns alunos não foram rematriculados, pois possuem saída na escola. Clique <a href=\'#\' onclick=\'ModalAlunos.init("alunos_com_saida");\'>aqui</a> para ver esses alunos</br>';
                    }
                }

                $this->inputsHelper()->hidden('alunos_rematriculados', ['value' => implode(',', $nomesAlunos)]);
                $this->inputsHelper()->hidden('alunos_com_saida', ['value' => implode(',', $alunosComSaidaDaEscola)]);
                Session::now('success', $mensagem);
            } elseif (count($alunosSemInep) > 0) {
                $mensagem = 'Não foi possível realizar a rematrícula, pois alguns alunos não possuem o INEP cadastrado. Clique <a href=\'#\' onclick=\'ModalAlunos.init("alunos_sem_inep");\'>aqui</a> para ver esses alunos.</br>';

                $mensagem .= '</br>Por favor, cadastre o INEP do(s) aluno(s) em: Cadastros > Alunos > Campo: Código INEP.';

                $this->inputsHelper()->hidden('alunos_sem_inep', ['value' => implode(',', $alunosSemInep)]);
                Session::now('error', $mensagem);
            } elseif ($this->existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $turmaId, $this->ano_letivo)) {
                Session::now('error', 'Nenhum aluno rematriculado. Certifique-se que a turma possui alunos aprovados ou reprovados em ' . ($this->ano_letivo - 1) . '.');
            } else {
                Session::now('notice', 'Os alunos desta série já encontram-se rematriculados, sendo assim, favor verificar se as enturmações já foram efetuadas em Movimentação > Enturmação.');
            }
        } else {
            Session::now('error', 'Ocorreu algum erro inesperado durante as rematrículas, por favor, tente novamente.');
        }

        return $result;
    }

    protected function getAlunosSemInep($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        //Pega todas as matriculas
        $objMatricula = new clsPmieducarMatriculaTurma();
        $objMatricula->setOrderby('nome');
        $lstMatricula = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $ano);
        //Verifica o parametro na série pra exigir inep
        $objSerie = new clsPmieducarSerie($serieId);
        $serieDet = $objSerie->detalhe();
        $exigeInep = $serieDet['exigir_inep'];
        //Retorna alunos sem inep
        $alunosSemInep = [];
        $objAluno = new clsPmieducarAluno();

        foreach ($lstMatricula as $matricula) {
            $alunoInep = $objAluno->verificaInep($matricula['ref_cod_aluno']);
            if (!$alunoInep && $exigeInep) {
                $alunosSemInep[] = strtoupper($matricula['nome']);
            }
        }

        return $alunosSemInep;
    }

    protected function getAlunosComSaidaDaEscola($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        $objMatricula = new clsPmieducarMatriculaTurma();
        $objMatricula->setOrderby('nome');
        $alunosComSaidaDaEscola = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $ano, true);
        $alunos = [];

        foreach ($alunosComSaidaDaEscola as $a) {
            $alunos[] = strtoupper($a['nome']);
        }

        return $alunos;
    }

    protected function selectMatriculas($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        $anoAnterior = $this->ano_letivo - 1;

        $sql = "
            SELECT
                cod_matricula,
                ref_cod_aluno,
                aprovado,
                (
                    SELECT upper(nome)
                    FROM cadastro.pessoa, pmieducar.aluno
                    WHERE pessoa.idpes = aluno.ref_idpes
                    AND aluno.cod_aluno = ref_cod_aluno
                ) as nome
            FROM pmieducar.matricula m, pmieducar.matricula_turma
            WHERE aprovado in (1, 2, 12, 13, 14)
            AND m.ativo = 1
            AND ref_ref_cod_escola = $escolaId
            AND ref_ref_cod_serie = $serieId
            AND ref_cod_curso = $cursoId
            AND cod_matricula = ref_cod_matricula
            AND matricula_turma.ativo = 1
            AND ano  = $anoAnterior
            AND m.dependencia = FALSE
            AND m.saida_escola = FALSE
            AND NOT EXISTS(
                SELECT 1
                FROM pmieducar.matricula m2
                WHERE m2.ref_cod_aluno = m.ref_cod_aluno
                AND m2.ano = $this->ano_letivo
                AND m2.ativo = 1
                AND m2.ref_ref_cod_escola = m.ref_ref_cod_escola
            )
            AND NOT EXISTS(
                SELECT 1
                FROM pmieducar.matricula m2
                WHERE m2.ref_cod_aluno = m.ref_cod_aluno
                AND m2.ano = $this->ano_letivo
                AND m2.ativo = 1
                AND m2.ref_ref_cod_serie = (
                    SELECT ref_serie_destino
                    FROM pmieducar.sequencia_serie
                    WHERE ref_serie_origem = $serieId
                    AND ativo = 1
                )
            )
        ";

        if ($turmaId) {
            $sql .= "AND ref_cod_turma = $turmaId ORDER BY nome";
        }

        try {
            $this->db->Consulta($sql);
        } catch (Exception $e) {
            error_log('Erro ao selecionar matrículas ano anterior, no processo rematrícula automática:' . $e->getMessage());

            throw new Exception("Erro ao selecionar matrículas ano anterior: $anoAnterior");
        }

        return true;
    }

    protected function existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        $objMatricula = new clsPmieducarMatriculaTurma();
        $objMatricula->setOrderby('nome');
        $anoAnterior = $this->ano_letivo - 1;
        $matriculas = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $anoAnterior);
        $qtdMatriculasAprovadasReprovadas = 0;

        foreach ($matriculas as $m) {
            if (in_array($m['aprovado'], [1, 2, 12, 13])) {
                $qtdMatriculasAprovadasReprovadas++;
            }
        }

        return ($qtdMatriculasAprovadasReprovadas == 0) ? true : false;
    }

    protected function rematricularAlunoAprovado($escolaId, $serieId, $ano, $alunoId)
    {
        $nextSerieId = $this->db2->campoUnico(
            "
                SELECT ref_serie_destino FROM pmieducar.sequencia_serie
                WHERE ref_serie_origem = $serieId AND ativo = 1
            "
        );

        if (is_numeric($nextSerieId)) {
            $nextCursoId = $this->db2->CampoUnico(
                "
                    SELECT ref_cod_curso FROM pmieducar.serie
                    WHERE cod_serie = $nextSerieId
                "
            );

            if ($this->escolaSerieConfigurada($escolaId, $nextSerieId)) {
                return $this->matricularAluno($escolaId, $nextCursoId, $nextSerieId, $this->ano_letivo, $alunoId);
            } else {
                throw new Exception('A série de destino não está configurada na escola. Favor efetuar o cadastro em Cadastro > Série > Escola-Série');
            }
        } else {
            throw new Exception('Não foi possível obter a próxima série da sequência de enturmação');
        }

        return false;
    }

    protected function rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $ano, $alunoId)
    {
        return $this->matricularAluno($escolaId, $cursoId, $serieId, $this->ano_letivo, $alunoId);
    }

    protected function matricularAluno($escolaId, $cursoId, $serieId, $ano, $alunoId)
    {
        $sql = "
            INSERT INTO pmieducar.matricula (ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula, data_matricula)
            VALUES ('%d', '%d', '%d', '%d', '3', 'NOW()', '%d', '%d', '1','{$this->data_matricula}')
        ";

        try {
            $this->db2->Consulta(sprintf(
                $sql,
                $escolaId,
                $serieId,
                $this->pessoa_logada,
                $alunoId,
                $this->ano_letivo,
                $cursoId
            ));
        } catch (Exception $e) {
            error_log("Erro durante a matrícula do aluno $alunoId, no processo de rematrícula automática:" . $e->getMessage());

            throw new Exception("Erro durante matrícula do aluno: $alunoId");
        }

        $this->auditarMatriculas($escolaId, $cursoId, $serieId, $ano, $alunoId);

        return true;
    }

    protected function auditarMatriculas($escolaId, $cursoId, $serieId, $ano, $alunoId)
    {
        $objMatricula = new clsPmieducarMatricula();
        $matricula = $objMatricula->lista(null, null, $escolaId, $serieId, null, null, $alunoId, null, null, null, null, null, 1, $ano, null, null, null, null, null, null, null, null, null, null, $cursoId);

        $matriculaId = $matricula[0]['cod_matricula'];
        $objMatricula->cod_matricula = $matriculaId;

        $detalhe = $objMatricula->detalhe();

        $auditoria = new clsModulesAuditoriaGeral('matricula', $this->pessoa_logada, $matriculaId);
        $auditoria->inclusao($detalhe);

        return true;
    }

    protected function escolaSerieConfigurada($escolaId, $serieId)
    {
        $escolaSerie = new clsPmieducarEscolaSerie($escolaId, $serieId);

        $escolaSerie = $escolaSerie->detalhe();

        if (count($escolaSerie) > 0) {
            if ($escolaSerie['ativo'] == '1') {
                return true;
            }
        }

        return false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
