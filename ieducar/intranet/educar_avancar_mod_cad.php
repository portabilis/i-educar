<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolAcademicYear;
use Illuminate\Support\Facades\Session;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $data_matricula;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(int_processo_ap: 845, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_index.php');

        $this->breadcrumb(currentPage: 'Rematrícula automática', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $anoLetivoHelperOptions = ['situacoes' => ['em_andamento', 'nao_iniciado']];

        $this->inputsHelper()->dynamic(helperNames: 'ano');
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola', 'curso', 'serie']);
        $this->inputsHelper()->dynamic(helperNames: 'turma', inputOptions: ['label' => 'Selecione a turma do ano anterior', 'required' => false]);
        $this->inputsHelper()->dynamic(helperNames: 'anoLetivo', inputOptions: ['label' => 'Ano destino'], helperOptions: $anoLetivoHelperOptions);
        $this->inputsHelper()->date(attrName: 'data_matricula', inputOptions: ['label' => 'Data da matricula', 'placeholder' => 'dd/mm/yyyy']);

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Cadastro/Assets/Javascripts/RematriculaAutomaticaModal.js',
        ]);
    }

    public function Novo()
    {
        $anoLetivo = request('ano_letivo');
        $ano = request('ano');

        $anoLetivos = LegacySchoolAcademicYear::query()
            ->whereSchool($this->ref_cod_escola)
            ->whereYearEq($anoLetivo)
            ->inProgress()
            ->active()
            ->get(['id']);

        if ($anoLetivo < $ano) {
            Session::now('notice', "O ano de destino ({$anoLetivo}) deve ser maior ou igual que o atual ({$ano}).");

            return false;
        }

        if ($anoLetivos->isEmpty()) {
            Session::now('notice', "Nenhum aluno letivo aberto para {$anoLetivo}.");

            return false;
        }

        $this->data_matricula = Portabilis_Date_Utils::brToPgSQL(date: $this->data_matricula);

        if ($anoLetivos->count() > 1) {
            Session::now('notice', 'Nenhum aluno rematriculado. Certifique-se que somente um ano letivo encontra-se em aberto.');

            return false;
        }

        // Valida se a data da matrícula não é menor que a data do início do
        // ano letivo.
        $anoLetivo = $anoLetivos->first();
        $dataInicio = $anoLetivo->academicYearStages()->orderBySequencial()->value('data_inicio');

        $inicioAnoLetivo = $dataInicio->format('Y-m-d');

        /** @var LegacyInstitution $instituicao */
        $instituicao = app(abstract: LegacyInstitution::class);

        if ($this->data_matricula < $inicioAnoLetivo) {
            if (!$instituicao->allowRegistrationOutAcademicYear) {
                Session::now('notice', 'A data da matrícula deve ser posterior ao dia ' . Portabilis_Date_Utils::pgSQLToBr(timestamp: $inicioAnoLetivo) . '.');

                return false;
            }
        }

        $this->db = new clsBanco();
        $this->db2 = new clsBanco();
        $this->db3 = new clsBanco();

        return $this->rematricularAlunos(
            escolaId: $this->ref_cod_escola,
            cursoId: $this->ref_cod_curso,
            serieId: $this->ref_cod_serie,
            turmaId: $this->ref_cod_turma,
            ano: request('ano')
        );
    }

    public function Editar()
    {
        return true;
    }

    protected function rematricularAlunos($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        try {
            $result = $this->selectMatriculas(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $this->ano_letivo);
            $alunosSemInep = $this->getAlunosSemInep(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $ano);
            $alunosComSaidaDaEscola = $this->getAlunosComSaidaDaEscola(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $ano);
            $count = 0;
            $nomesAlunos = [];

            if (count(value: $alunosSemInep) === 0) {
                while ($result && $this->db->ProximoRegistro()) {
                    [$matriculaId, $alunoId, $situacao, $nomeAluno] = $this->db->Tupla();

                    $this->db2->Consulta(
                        "
                            UPDATE pmieducar.matricula
                            SET ultima_matricula = '0'
                            WHERE cod_matricula = $matriculaId
                        "
                    );

                    if ($result && $situacao == 1 || $situacao == 12 || $situacao == 13) {
                        $result = $this->rematricularAlunoAprovado(escolaId: $escolaId, serieId: $serieId, ano: $this->ano_letivo, alunoId: $alunoId);
                    } elseif ($result && $situacao == 2 || $situacao == 14) {
                        $result = $this->rematricularAlunoReprovado(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, ano: $this->ano_letivo, alunoId: $alunoId);
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
                $mensagem .= "O(s) aluno(s) foram rematriculados com sucesso em $this->ano_letivo. Clique <a href='#' onclick='ModalAlunos.init(\"alunos_rematriculados\");'>aqui</a> para conferir os alunos rematrículados</br>";
                $mensagem .= '</br> As enturmações podem ser realizadas em: Movimentação > Enturmação.';
                if (count(value: $alunosComSaidaDaEscola) > 0) {
                    $mensagem .= '</br></br>Alguns alunos não foram rematriculados, pois possuem saída na escola. Clique <a href=\'#\' onclick=\'ModalAlunos.init("alunos_com_saida");\'>aqui</a> para ver esses alunos</br>';
                }

                $this->inputsHelper()->hidden(attrName: 'alunos_rematriculados', inputOptions: ['value' => implode(separator: ',', array: $nomesAlunos)]);
                $this->inputsHelper()->hidden(attrName: 'alunos_com_saida', inputOptions: ['value' => implode(separator: ',', array: $alunosComSaidaDaEscola)]);
                Session::now('success', $mensagem);
            } elseif (count(value: $alunosSemInep) > 0) {
                $mensagem = 'Não foi possível realizar a rematrícula, pois alguns alunos não possuem o INEP cadastrado. Clique <a href=\'#\' onclick=\'ModalAlunos.init("alunos_sem_inep");\'>aqui</a> para ver esses alunos.</br>';

                $mensagem .= '</br>Por favor, cadastre o INEP do(s) aluno(s) em: Cadastros > Alunos > Campo: Código INEP.';

                $this->inputsHelper()->hidden(attrName: 'alunos_sem_inep', inputOptions: ['value' => implode(separator: ',', array: $alunosSemInep)]);
                Session::now('error', $mensagem);
            } elseif ($this->existeMatriculasAprovadasReprovadas(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $this->ano_letivo)) {
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
        $objMatricula->setOrderby(strNomeCampo: 'nome');
        $lstMatricula = $objMatricula->lista4(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $ano);
        //Verifica o parametro na série pra exigir inep
        $objSerie = new clsPmieducarSerie(cod_serie: $serieId);
        $serieDet = $objSerie->detalhe();
        $exigeInep = $serieDet['exigir_inep'];
        //Retorna alunos sem inep
        $alunosSemInep = [];
        $objAluno = new clsPmieducarAluno();

        foreach ($lstMatricula as $matricula) {
            $alunoInep = $objAluno->verificaInep(cod_aluno: $matricula['ref_cod_aluno']);
            if (!$alunoInep && $exigeInep) {
                $alunosSemInep[] = mb_strtoupper(string: $matricula['nome']);
            }
        }

        return $alunosSemInep;
    }

    protected function getAlunosComSaidaDaEscola($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        $objMatricula = new clsPmieducarMatriculaTurma();
        $objMatricula->setOrderby(strNomeCampo: 'nome');
        $alunosComSaidaDaEscola = $objMatricula->lista4(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $ano, saida_escola: true);
        $alunos = [];

        foreach ($alunosComSaidaDaEscola as $a) {
            $alunos[] = mb_strtoupper(string: $a['nome']);
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
            error_log(message: 'Erro ao selecionar matrículas ano anterior, no processo rematrícula automática:' . $e->getMessage());

            throw new Exception(message: "Erro ao selecionar matrículas ano anterior: $anoAnterior");
        }

        return true;
    }

    protected function existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $turmaId, $ano)
    {
        $objMatricula = new clsPmieducarMatriculaTurma();
        $objMatricula->setOrderby(strNomeCampo: 'nome');
        $anoAnterior = $this->ano_letivo - 1;
        $matriculas = $objMatricula->lista4(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, turmaId: $turmaId, ano: $anoAnterior);
        $qtdMatriculasAprovadasReprovadas = 0;

        foreach ($matriculas as $m) {
            if (in_array(needle: $m['aprovado'], haystack: [1, 2, 12, 13, 14])) {
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

        if (is_numeric(value: $nextSerieId)) {
            $nextCursoId = $this->db2->CampoUnico(
                "
                    SELECT ref_cod_curso FROM pmieducar.serie
                    WHERE cod_serie = $nextSerieId
                "
            );

            if ($this->escolaSerieConfigurada(escolaId: $escolaId, serieId: $nextSerieId)) {
                return $this->matricularAluno(escolaId: $escolaId, cursoId: $nextCursoId, serieId: $nextSerieId, ano: $this->ano_letivo, alunoId: $alunoId);
            } else {
                throw new Exception(message: 'A série de destino não está configurada na escola. Favor efetuar o cadastro em Cadastro > Série > Escola-Série');
            }
        } else {
            throw new Exception(message: 'Não foi possível obter a próxima série da sequência de enturmação');
        }
    }

    protected function rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $ano, $alunoId)
    {
        return $this->matricularAluno(escolaId: $escolaId, cursoId: $cursoId, serieId: $serieId, ano: $this->ano_letivo, alunoId: $alunoId);
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
            error_log(message: "Erro durante a matrícula do aluno $alunoId, no processo de rematrícula automática:" . $e->getMessage());

            throw new Exception(message: "Erro durante matrícula do aluno: $alunoId");
        }

        return true;
    }

    protected function escolaSerieConfigurada($escolaId, $serieId)
    {
        $escolaSerie = new clsPmieducarEscolaSerie(ref_cod_escola: $escolaId, ref_cod_serie: $serieId);

        $escolaSerie = $escolaSerie->detalhe();

        if (is_array(value: $escolaSerie) && count(value: $escolaSerie) > 0) {
            if ($escolaSerie['ativo'] == '1') {
                return true;
            }
        }

        return false;
    }

    public function Formular()
    {
        $this->titulo = 'Rematrícula automática';
        $this->processoAp = '845';
    }
};
