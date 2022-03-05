<?php

use App\Models\LegacySchoolClass;

return new class extends clsCadastro {
    public $pessoa_logada;

    public $observacao;

    public $ref_cod_matricula;
    public $ref_cod_turma;
    public $ref_cod_serie;
    public $ref_cod_disciplina;
    public $ref_sequencial;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            578,
            $this->pessoa_logada,
            7,
            'educar_disciplina_dependencia_lst.php?ref_ref_cod_matricula=' . $this->ref_cod_matricula
        );

        if (is_numeric($this->ref_cod_matricula)) {
            $obj_matricula = new clsPmieducarMatricula(
                $this->ref_cod_matricula,
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

            $det_matricula = $obj_matricula->detalhe();

            if (!$det_matricula) {
                $this->simpleRedirect('educar_matricula_lst.php');
            }

            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        } else {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $obj = new clsPmieducarDisciplinaDependencia(
                $this->ref_cod_matricula,
                $this->ref_cod_serie,
                $this->ref_cod_escola,
                $this->ref_cod_disciplina
            );

            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
            sprintf(
                'educar_disciplina_dependencia_det.php?ref_cod_matricula=%d&ref_cod_serie=%d&ref_cod_escola=%d&ref_cod_disciplina=%d',
                $registro['ref_cod_matricula'],
                $registro['ref_cod_serie'],
                $registro['ref_cod_escola'],
                $registro['ref_cod_disciplina']
            ) :
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Disciplinas de dependência', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        /**
         * Busca dados da matricula
         */
        $obj_ref_cod_matricula = (new clsPmieducarMatricula())->lista($this->ref_cod_matricula);
        $detalhe_aluno = array_shift($obj_ref_cod_matricula);

        $obj_aluno = new clsPmieducarAluno();
        $det_aluno = $obj_aluno->lista(
            $detalhe_aluno['ref_cod_aluno'],
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

        $det_aluno = array_shift($det_aluno);

        $obj_escola = new clsPmieducarEscola(
            $this->ref_cod_escola,
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

        $det_escola = $obj_escola->detalhe();
        $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

        $obj_matricula_turma = new clsPmieducarMatriculaTurma();
        $lst_matricula_turma = $obj_matricula_turma->lista(
            $this->ref_cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_serie,
            null,
            $this->ref_cod_escola
        );

        if (is_array($lst_matricula_turma)) {
            $det = array_shift($lst_matricula_turma);
            $this->ref_cod_turma = $det['ref_cod_turma'];
            $this->ref_sequencial = $det['sequencial'];
        }

        $this->campoRotulo('nm_aluno', 'Nome do Aluno', $det_aluno['nome_aluno']);

        if (!isset($this->ref_cod_turma)) {
            $this->mensagem = 'Para cadastrar uma disciplina de dependência de um aluno, é necessário que este esteja enturmado.';

            return;
        }

        // primary keys
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
        $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $opcoes = ['' => 'Selecione'];

        $ano = LegacySchoolClass::find($this->ref_cod_turma)->ano;
        // Seleciona os componentes curriculares da turma

        try {
            $componentes = App_Model_IedFinder::getComponentesTurma(
                serieId: $this->ref_cod_serie,
                escola: $this->ref_cod_escola,
                turma: $this->ref_cod_turma,
                ano: $ano
            );
        } catch (App_Model_Exception $e) {
            $this->mensagem = $e->getMessage();

            return;
        }

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        if ($this->ref_cod_disciplina) {
            $this->campoRotulo('nm_disciplina', 'Disciplina', $opcoes[$this->ref_cod_disciplina]);
            $this->campoOculto('ref_cod_disciplina', $this->ref_cod_disciplina);
        } else {
            $this->campoLista(
                'ref_cod_disciplina',
                'Disciplina',
                $opcoes,
                $this->ref_cod_disciplina
            );
        }

        $this->campoMemo('observacao', 'Observação', $this->observacao, 60, 10, false);
    }

    public function existeComponenteSerie()
    {
        $db = new clsBanco();
        $sql = "SELECT  EXISTS (SELECT 1
                                FROM pmieducar.escola_serie_disciplina
                               WHERE ref_ref_cod_serie = {$this->ref_cod_serie}
                                 AND ref_ref_cod_escola = {$this->ref_cod_escola}
                                 AND ref_cod_disciplina = {$this->ref_cod_disciplina}
                                 AND escola_serie_disciplina.ativo = 1)";

        return dbBool($db->campoUnico($sql));
    }

    public function validaQuantidadeDisciplinasDependencia()
    {
        $query = <<<'SQL'
            SELECT t.ano
            FROM pmieducar.matricula AS m
            INNER JOIN pmieducar.matricula_turma AS mt ON mt.ref_cod_matricula = m.cod_matricula
            INNER JOIN pmieducar.turma AS t ON t.cod_turma = mt.ref_cod_turma
            WHERE m.cod_matricula = $1
SQL;
        $ano = Portabilis_Utils_Database::selectField($query, [$this->ref_cod_matricula]);

        $db = new clsBanco();
        $db->consulta("SELECT (CASE
                               WHEN escola.utiliza_regra_diferenciada AND rasa.regra_avaliacao_diferenciada_id IS NOT NULL
                               THEN regra_avaliacao_diferenciada.qtd_disciplinas_dependencia
                               ELSE regra_avaliacao.qtd_disciplinas_dependencia
                                END) AS qtd_disciplinas_dependencia
                         FROM pmieducar.escola,
                              pmieducar.serie
                    LEFT JOIN modules.regra_avaliacao_serie_ano AS rasa ON (rasa.serie_id = serie.cod_serie AND rasa.ano_letivo = {$ano})
                    LEFT JOIN modules.regra_avaliacao ON (rasa.regra_avaliacao_id = regra_avaliacao.id)
                    LEFT JOIN modules.regra_avaliacao AS regra_avaliacao_diferenciada ON (rasa.regra_avaliacao_diferenciada_id = regra_avaliacao_diferenciada.id)
                        WHERE serie.cod_serie = {$this->ref_cod_serie}
                          AND escola.cod_escola = {$this->ref_cod_escola}");

        $db->ProximoRegistro();
        $m = $db->Tupla();
        $qtdDisciplinasLimite = $m['qtd_disciplinas_dependencia'];

        $db->consulta("SELECT COUNT(1) as qtd
                    FROM pmieducar.disciplina_dependencia
                    WHERE ref_cod_matricula = {$this->ref_cod_matricula} ");
        $db->ProximoRegistro();
        $m = $db->Tupla();
        $qtdDisciplinas = $m['qtd'];

        $valid = $qtdDisciplinas < $qtdDisciplinasLimite;

        if (!$valid) {
            $this->mensagem .= "A regra desta série limita a quantidade de disciplinas de dependência para {$qtdDisciplinasLimite}. <br/>";
        }

        return $valid;
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            578,
            $this->pessoa_logada,
            7,
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula
        );

        if (!$this->validaQuantidadeDisciplinasDependencia()) {
            return false;
        }

        if (!$this->existeComponenteSerie()) {
            $this->mensagem = 'O componente não está habilitado na série da escola.';
            $this->url_cancelar = 'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->nome_url_cancelar = 'Cancelar';

            return false;
        }

        $sql = 'SELECT MAX(cod_disciplina_dependencia) + 1 FROM pmieducar.disciplina_dependencia';
        $db = new clsBanco();
        $max_cod_disciplina_dependencia = $db->CampoUnico($sql);

        // Caso não exista nenhuma dispensa, atribui o cÃ³digo 1, tabela não utiliza sequences
        $max_cod_disciplina_dependencia = $max_cod_disciplina_dependencia > 0 ? $max_cod_disciplina_dependencia : 1;

        $obj = new clsPmieducarDisciplinaDependencia(
            $this->ref_cod_matricula,
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->ref_cod_disciplina,
            $this->observacao,
            $max_cod_disciplina_dependencia
        );

        if ($obj->existe()) {
            $obj = new clsPmieducarDisciplinaDependencia(
                $this->ref_cod_matricula,
                $this->ref_cod_serie,
                $this->ref_cod_escola,
                $this->ref_cod_disciplina,
                $this->observacao
            );

            $obj->edita();
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Cadastro não realizado.<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            578,
            $this->pessoa_logada,
            7,
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula
        );

        $obj = new clsPmieducarDisciplinaDependencia(
            $this->ref_cod_matricula,
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->ref_cod_disciplina,
            $this->observacao
        );

        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            578,
            $this->pessoa_logada,
            7,
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula
        );

        $obj = new clsPmieducarDisciplinaDependencia(
            $this->ref_cod_matricula,
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->ref_cod_disciplina,
            $this->observacao
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Dispensa Componente Curricular';
        $this->processoAp = 578;
    }
};
