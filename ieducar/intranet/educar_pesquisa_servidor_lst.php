<?php

use App\Models\Builders\EmployeeBuilder;
use App\Models\Employee;
use App\Models\LegacyEmployee;
use App\Models\LegacyInstitution;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $cod_servidor;

    public $ref_idesco;

    public $ref_cod_funcao;

    public $carga_horaria;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $horario;

    public $lst_matriculas;

    public $ref_cod_instituicao;

    public $professor;

    public $ref_cod_escola;

    public $nome_servidor;

    public $ref_cod_servidor;

    public $periodo;

    public $carga_horaria_usada;

    public $min_mat;

    public $min_ves;

    public $min_not;

    public $dia_semana;

    public $ref_cod_disciplina;

    public $ref_cod_curso;

    public $matutino = false;

    public $vespertino = false;

    public $noturno = false;

    public $identificador;

    public $ano_alocacao;

    public function Gerar()
    {
        Session::put(key: [
            'campo1' => $_GET['campo1'] ?? Session::get(key: 'campo1'),
            'campo2' => $_GET['campo2'] ?? Session::get(key: 'campo2'),
            'dia_semana' => $_GET['dia_semana'] ?? Session::get(key: 'dia_semana'),
            'hora_inicial' => $_GET['hora_inicial'] ?? Session::get(key: 'hora_inicial'),
            'hora_final' => $_GET['hora_final'] ?? Session::get(key: 'hora_final'),
            'professor' => $_GET['professor'] ?? Session::get(key: 'professor'),
            'horario' => $_GET['horario'] ?? Session::get(key: 'horario'),
            'ref_cod_escola' => $_GET['ref_cod_escola'] ?? Session::get(key: 'ref_cod_escola'),
            'min_mat' => $_GET['min_mat'] ?? Session::get(key: 'min_mat'),
            'min_ves' => $_GET['min_ves'] ?? Session::get(key: 'min_ves'),
            'min_not' => $_GET['min_not'] ?? Session::get(key: 'min_not'),
            'ref_cod_disciplina' => $_GET['ref_cod_disciplina'] ?? Session::get(key: 'ref_cod_disciplina'),
            'ref_cod_curso' => $_GET['ref_cod_curso'] ?? Session::get(key: 'ref_cod_curso'),
            'ano_alocacao' => $_GET['ano_alocacao'] ?? Session::get(key: 'ano_alocacao'),
            'identificador' => $_GET['identificador'] ?? Session::get(key: 'identificador'),
            'lst_matriculas' => $_GET['lst_matriculas'] ?? Session::get(key: 'lst_matriculas'),
            'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ? $_GET['ref_cod_instituicao'] : Session::get(key: 'ref_cod_instituicao'),
            'ref_cod_servidor' => $_GET['ref_cod_servidor'] ? $_GET['ref_cod_servidor'] : Session::get(key: 'ref_cod_servidor'),
        ]);

        if (!isset($_GET['tipo'])) {
            Session::forget(keys: [
                'setAllField1',
                'setAllField2',
                'tipo',
            ]);
        }

        $this->ref_cod_escola = Session::get(key: 'ref_cod_escola');
        $this->ref_cod_instituicao = Session::get(key: 'ref_cod_instituicao');
        $this->ref_cod_servidor = Session::get(key: 'ref_cod_servidor');
        $this->professor = Session::get(key: 'professor');
        $this->horario = Session::get(key: 'horario');
        $this->min_mat = Session::get(key: 'min_mat');
        $this->min_ves = Session::get(key: 'min_ves');
        $this->min_not = Session::get(key: 'min_not');
        $this->ref_cod_disciplina = Session::get(key: 'ref_cod_disciplina');
        $this->ref_cod_curso = Session::get(key: 'ref_cod_curso');
        $this->identificador = Session::get(key: 'identificador');
        $this->ano_alocacao = Session::get(key: 'ano_alocacao');
        $this->lst_matriculas = Session::get(key: 'lst_matriculas');

        Session::put(key: 'tipo', value: $_GET['tipo'] ?? Session::get(key: 'tipo') ?? '');

        $this->titulo = 'Servidores P&uacute;blicos - Listagem';
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = $val === '' ? null : $val;
        }
        if (isset($this->lst_matriculas)) {
            $this->lst_matriculas = urldecode(string: $this->lst_matriculas);
        }
        $string1 = ($this->min_mat - floor(num: $this->min_mat / 60) * 60);
        $string1 = str_repeat(string: 0, times: 2 - strlen(string: $string1)).$string1;
        $string2 = floor(num: $this->min_mat / 60);
        $string2 = str_repeat(string: 0, times: 2 - strlen(string: $string2)).$string2;
        $hr_mat = $string2.':'.$string1;
        $string1 = ($this->min_ves - floor(num: $this->min_ves / 60) * 60);
        $string1 = str_repeat(string: 0, times: 2 - strlen(string: $string1)).$string1;
        $string2 = floor(num: $this->min_ves / 60);
        $string2 = str_repeat(string: 0, times: 2 - strlen(string: $string2)).$string2;
        $hr_ves = $string2.':'.$string1;
        $string1 = ($this->min_not - floor(num: $this->min_not / 60) * 60);
        $string1 = str_repeat(string: 0, times: 2 - strlen(string: $string1)).$string1;
        $string2 = floor(num: $this->min_not / 60);
        $string2 = str_repeat(string: 0, times: 2 - strlen(string: $string2)).$string2;
        $hr_not = $string2.':'.$string1;
        $hora_inicial_ = explode(separator: ':', string: Session::get(key: 'hora_inicial'));
        $hora_final_ = explode(separator: ':', string: Session::get(key: 'hora_final'));
        $h_m_ini = ((int) $hora_inicial_[0] * 60) + $hora_inicial_[1];
        $h_m_fim = ((int) $hora_final_[0] * 60) + $hora_final_[1];
        if ($h_m_ini >= 480 && $h_m_ini <= 720) {
            $this->matutino = true;
            if ($h_m_fim >= 721 && $h_m_fim <= 1080) {
                $this->vespertino = true;
            } elseif (($h_m_fim >= 1801 && $h_m_fim <= 1439) || ($h_m_fim == 0)) {
                $this->noturno = true;
            }
        } elseif ($h_m_ini >= 721 && $h_m_ini <= 1080) {
            $this->vespertino = true;
            if (($h_m_fim >= 1081 && $h_m_fim <= 1439)) {
                $this->noturno = true;
            }
        } elseif (($h_m_ini >= 1081 && $h_m_ini <= 1439) || ($h_m_ini == 0)) {
            $this->noturno = true;
        }
        $this->addCabecalhos(coluna: [
            'Nome do Servidor',
            'Matrícula',
            'Instituição',
        ]);
        $this->campoTexto(nome: 'nome_servidor', campo: 'Nome Servidor', valor: $this->nome_servidor, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoOculto(nome: 'tipo', valor: $_GET['tipo']);
        // Paginador
        $this->limite = 20;
        $array_hora = null;

        if (Session::has(key: ['dia_semana', 'hora_inicial', 'hora_final'])) {
            $array_hora = [
                Session::get(key: 'dia_semana'),
                Session::get(key: 'hora_inicial'),
                Session::get(key: 'hora_final'),
            ];
        }
        // Marca a disciplina como NULL se não for informada, restringindo a busca
        // aos professores e não selecionar aqueles em que o curso não seja
        // globalizado e sem disciplinas cadastradas
        $this->ref_cod_disciplina = $this->ref_cod_disciplina ?
      $this->ref_cod_disciplina : null;
        // Passa NULL para $alocacao_escola_instituicao senão o seu filtro anula
        // um anterior (referente a selecionar somente servidores não alocados),
        // selecionando apenas servidores alocados na instituiÃ§Ã£o
        $query = $this->consultaServidores(
            arrayHorario: $array_hora,
            horaMatutino: $hr_mat,
            horaVespertino: $hr_ves,
            horaNoturno: $hr_not,
            diaSemana: Session::get(key: 'dia_semana')
        );

        $resultado = $query
            ->orderBy('pessoa.nome')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $resultado->getCollection();
        $total = $resultado->total();
        $nm_instituicao = null;
        $matriculasPorServidor = collect();

        if ($lista->isNotEmpty()) {
            $nm_instituicao = LegacyInstitution::query()
                ->whereKey($lista->first()->ref_cod_instituicao)
                ->value('nm_instituicao');

            $matriculasPorServidor = LegacyEmployee::query()
                ->whereIn('ref_cod_pessoa_fj', $lista->pluck('cod_servidor'))
                ->pluck('matricula', 'ref_cod_pessoa_fj');
        }

        foreach ($lista as $registro) {
            $registro['matricula'] = $matriculasPorServidor[$registro['cod_servidor']] ?? null;
            $campo1 = Session::get(key: 'campo1');
            $campo2 = Session::get(key: 'campo2');
            if (Session::get(key: 'tipo')) {
                if (is_string(value: $campo1) && is_string(value: $campo2)) {
                    if (is_string(value: Session::get(key: 'horario'))) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['nome']}','{$registro['cod_servidor']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                    } else {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); fecha();\"";
                    }
                } elseif (is_string(value: $campo1)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                }
            } else {
                if (is_string(value: $campo1) && is_string(value: $campo2)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}','{$registro['cod_servidor']}'); fecha();\"";
                } elseif (is_string(value: $campo2)) {
                    $script = " onclick=\"addVal1('{$campo2}','{$registro['nome']}','{$registro['cod_servidor']}'); fecha();\"";
                } elseif (is_string(value: $campo1)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                }
            }
            $this->addLinhas(linha: [
                "<a href=\"javascript:void(0);\" $script>{$registro['nome']}</a>",
                "<a href=\"javascript:void(0);\" $script>{$registro['matricula']}</a>",
                "<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>",
            ]);
        }
        $this->addPaginador2(
            strUrl: 'educar_pesquisa_servidor_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );
        $this->largura = '100%';
    }

    private function consultaServidores(
        ?array $arrayHorario,
        string $horaMatutino,
        string $horaVespertino,
        string $horaNoturno,
        $diaSemana
    ): EmployeeBuilder {
        $instituicao = $this->ref_cod_instituicao;
        $escola = $this->ref_cod_escola;
        $ano = $this->ano_alocacao;
        $porHorario = is_string(value: $this->horario) && $this->horario === 'S';

        $query = Employee::query()
            ->select(['servidor.cod_servidor', 'pessoa.nome', 'servidor.ref_cod_instituicao'])
            ->leftJoin('cadastro.pessoa', 'pessoa.idpes', 'servidor.cod_servidor')
            ->when(is_numeric(value: $this->ref_idesco), fn ($q) => $q->whereSchoolingDegree($this->ref_idesco))
            ->when(is_numeric(value: $this->carga_horaria), fn ($q) => $q->where('carga_horaria', $this->carga_horaria))
            ->when(is_numeric(value: $instituicao), fn ($q) => $q->whereInstitution($instituicao))
            ->when(is_string(value: $this->nome_servidor), fn ($q) => $q->whereName($this->nome_servidor))
            ->whereHas('employeeAllocations', function ($q) use ($instituicao, $escola, $ano) {
                $q->when(is_numeric(value: $instituicao), fn ($q) => $q->whereInstitution($instituicao));
                $q->when(is_numeric(value: $escola), fn ($q) => $q->whereSchool($escola));
                $q->when(is_numeric(value: $ano), fn ($q) => $q->whereYearEq($ano));
                $q->active();
            })
            ->active();

        if (is_array(value: $arrayHorario)) {
            $horaInicial = explode(separator: ':', string: $arrayHorario[1]);
            $horaFinal = explode(separator: ':', string: $arrayHorario[2]);
            $horas = abs(num: (int) $horaFinal[0] - (int) $horaInicial[0]);
            $minutos = abs(num: (int) $horaFinal[1] - (int) $horaInicial[1]);

            $matutino = $this->matutino;
            $vespertino = $this->vespertino;
            $noturno = $this->noturno;

            // Aula que atravessa o limite do período conta só no período em que começa,
            // senão o docente precisaria de carga disponível nos dois períodos
            $minutoInicial = (int) $horaInicial[0] * 60 + (int) $horaInicial[1];
            $minutoFinal = (int) $horaFinal[0] * 60 + (int) $horaFinal[1];

            if ($minutoInicial < 12 * 60 && $minutoFinal > 12 * 60) {
                $vespertino = false;
            }

            if ($minutoInicial < 18 * 60 && $minutoFinal > 18 * 60) {
                $noturno = false;
            }

            if ($matutino) {
                $query = $porHorario
                    ? $this->comCargaDisponivel($query, 1, '06:00', '12:00', $horaMatutino, $diaSemana)
                    : $this->semAlocacaoNoPeriodo($query, 1);
            }

            if ($vespertino) {
                $query = $porHorario
                    ? $this->comCargaDisponivel($query, 2, '12:00', '18:00', $horaVespertino, $diaSemana)
                    : $this->semAlocacaoNoPeriodo($query, 2);
            }

            if ($noturno) {
                $query = $porHorario
                    ? $this->comCargaDisponivel($query, 3, '18:00', '23:59', $horaNoturno, $diaSemana)
                    : $this->semAlocacaoNoPeriodo($query, 3);
            }

            if (!$porHorario) {
                $query->whereRaw(
                    "((servidor.carga_horaria >= COALESCE(
                        (SELECT sum(hora_final - qhh.hora_inicial) + ?
                           FROM pmieducar.servidor_alocacao sa
                          WHERE sa.ref_cod_servidor = servidor.cod_servidor
                            AND sa.ref_ref_cod_instituicao = ?),'00:00')) OR servidor.multi_seriado)",
                    [$horas . ':' . $minutos, $instituicao]
                );
            }
        }

        if ($this->ref_cod_servidor) {
            $query->whereNotIn('servidor.cod_servidor', $this->codigosServidores($this->ref_cod_servidor));
        }

        $query->whereHas('employeeRoles', function ($q) {
            $q->whereColumn('servidor_funcao.ref_ref_cod_instituicao', 'servidor.ref_cod_instituicao');
            $q->whereTeacherRole();
        });

        $query->whereExists(function ($q) {
            $this->consultaDisciplinas($q);
        });

        if ($porHorario) {
            $matriculas = $this->codigosServidores($this->lst_matriculas);

            $query->whereRaw(
                '(servidor.cod_servidor NOT IN
                  (SELECT DISTINCT qhh.ref_servidor
                     FROM pmieducar.quadro_horario_horarios qhh
                     INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhh.ref_cod_quadro_horario
                                                             AND quadro_horario.ativo = 1)
                     INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                                    AND turma.ativo = 1)
                    WHERE qhh.ref_servidor = servidor.cod_servidor
                      AND qhh.ref_cod_instituicao_servidor = servidor.ref_cod_instituicao
                      AND qhh.dia_semana = ?
                      AND (((? > qhh.hora_inicial AND ? < qhh.hora_final)
                            OR (? > qhh.hora_inicial AND ? < qhh.hora_final))
                           OR (? = qhh.hora_inicial AND ? = qhh.hora_final)
                           OR (? <= qhh.hora_inicial AND ? >= qhh.hora_final))
                      AND qhh.ativo = 1'
                . (is_numeric(value: $ano) ? ' AND quadro_horario.ano = ' . (int) $ano : '')
                . ($matriculas ? ' AND qhh.ref_servidor NOT IN (' . implode(',', $matriculas) . ')' : '')
                . ') OR servidor.multi_seriado)',
                [
                    $arrayHorario[0],
                    $arrayHorario[1], $arrayHorario[1],
                    $arrayHorario[2], $arrayHorario[2],
                    $arrayHorario[1], $arrayHorario[2],
                    $arrayHorario[1], $arrayHorario[2],
                ]
            );
        }

        return $query;
    }

    private function comCargaDisponivel(
        EmployeeBuilder $query,
        int $periodo,
        string $horaDe,
        string $horaAte,
        string $horaAula,
        $diaSemana
    ): EmployeeBuilder {
        $instituicao = $this->ref_cod_instituicao;
        $escola = $this->ref_cod_escola;
        $ano = $this->ano_alocacao;

        // No vespertino a soma considera o ano do quadro e ignora horários repetidos no mesmo intervalo
        $anoQuadro = $periodo === 2 && is_numeric(value: $ano) ? ' AND quadro_horario.ano = ' . (int) $ano : '';
        $desempateSequencial = $periodo === 2
            ? ' AND qhh.sequencial = (SELECT s_qhh.sequencial
                    FROM pmieducar.quadro_horario_horarios s_qhh
                   WHERE s_qhh.dia_semana = qhh.dia_semana
                     AND s_qhh.hora_inicial = qhh.hora_inicial
                     AND s_qhh.ref_cod_quadro_horario = quadro_horario.cod_quadro_horario
                     AND s_qhh.hora_final = qhh.hora_final
                   ORDER BY s_qhh.sequencial DESC
                   LIMIT 1)'
            : '';

        [$condicaoAlocacao, $bindingsAlocacao] = $this->condicaoAlocacao($instituicao, $escola);

        return $query->whereRaw(
            '(servidor.cod_servidor IN
              (SELECT a.ref_cod_servidor
                 FROM pmieducar.servidor_alocacao a
                WHERE ' . $condicaoAlocacao . '
                  AND a.periodo = ?'
            . (is_numeric(value: $ano) ? ' AND a.ano = ' . (int) $ano : '')
            . ' AND (a.data_saida > now() or a.data_saida is null)
                  AND a.carga_horaria >= COALESCE(
                    (SELECT SUM(qhh.hora_final - qhh.hora_inicial)
                       FROM pmieducar.quadro_horario_horarios qhh
                       INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhh.ref_cod_quadro_horario
                                                               AND quadro_horario.ativo = 1)
                       INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                                      AND turma.ativo = 1)
                      WHERE qhh.ref_cod_instituicao_servidor = ?
                        AND qhh.ref_cod_escola = ?
                        AND qhh.hora_inicial >= ?
                        AND qhh.hora_inicial <= ?
                        AND qhh.ativo = 1
                        AND qhh.dia_semana <> ?
                        AND qhh.ref_servidor = a.ref_cod_servidor'
            . $anoQuadro
            . $desempateSequencial
            . " GROUP BY qhh.ref_servidor),'00:00') + ? + COALESCE(
                    (SELECT SUM(qhha.hora_final - qhha.hora_inicial)
                       FROM pmieducar.quadro_horario_horarios_aux qhha
                       INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhha.ref_cod_quadro_horario
                                                               AND quadro_horario.ativo = 1)
                       INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                                      AND turma.ativo = 1)
                      WHERE qhha.ref_cod_instituicao_servidor = ?
                        AND qhha.ref_cod_escola = ?
                        AND qhha.hora_inicial >= ?
                        AND qhha.hora_inicial <= ?
                        AND qhha.ref_servidor = a.ref_cod_servidor"
            . $anoQuadro
            . " AND qhha.identificador = ?
                      GROUP BY qhha.ref_servidor),'00:00')) OR servidor.multi_seriado)",
            [
                ...$bindingsAlocacao,
                $periodo,
                $instituicao, $escola, $horaDe, $horaAte, $diaSemana,
                $horaAula,
                $instituicao, $escola, $horaDe, $horaAte, $this->identificador,
            ]
        );
    }

    private function semAlocacaoNoPeriodo(EmployeeBuilder $query, int $periodo): EmployeeBuilder
    {
        $ano = $this->ano_alocacao;

        [$condicaoAlocacao, $bindingsAlocacao] = $this->condicaoAlocacao($this->ref_cod_instituicao, $this->ref_cod_escola);

        return $query->whereRaw(
            '(servidor.cod_servidor NOT IN
              (SELECT a.ref_cod_servidor
                 FROM pmieducar.servidor_alocacao a
                WHERE ' . $condicaoAlocacao
            . (is_numeric(value: $ano) ? ' AND a.ano = ' . (int) $ano : '')
            . ' AND (a.data_saida > now() or a.data_saida is null)
                  AND a.periodo = ?) OR servidor.multi_seriado)',
            [...$bindingsAlocacao, $periodo]
        );
    }

    private function condicaoAlocacao($instituicao, $escola): array
    {
        $condicoes = [];
        $bindings = [];

        if (is_numeric(value: $instituicao)) {
            $condicoes[] = 'a.ref_ref_cod_instituicao = ?';
            $bindings[] = $instituicao;
        }

        if (is_numeric(value: $escola)) {
            $condicoes[] = 'a.ref_cod_escola = ?';
            $bindings[] = $escola;
        }

        $condicoes[] = 'a.ativo = 1';

        return [implode(' AND ', $condicoes), $bindings];
    }

    private function consultaDisciplinas(QueryBuilder $query): void
    {
        $query->selectRaw('1')
            ->from('pmieducar.servidor_disciplina')
            ->whereColumn('servidor_disciplina.ref_cod_servidor', 'servidor.cod_servidor')
            ->whereColumn('servidor_disciplina.ref_ref_cod_instituicao', 'servidor.ref_cod_instituicao');

        if ($this->ref_cod_disciplina || $this->ref_cod_curso) {
            $query->whereRaw(
                '(case when ? = 0 then servidor_disciplina.ref_cod_curso = ?
                    else (servidor_disciplina.ref_cod_disciplina = ? AND servidor_disciplina.ref_cod_curso = ?) end)',
                [
                    (int) $this->ref_cod_disciplina,
                    (int) $this->ref_cod_curso,
                    (int) $this->ref_cod_disciplina,
                    (int) $this->ref_cod_curso,
                ]
            );

            return;
        }

        $disciplinas = DB::table('pmieducar.servidor_disciplina')
            ->when(is_numeric(value: $this->ref_cod_servidor), fn ($q) => $q->where('ref_cod_servidor', $this->ref_cod_servidor))
            ->pluck('ref_cod_disciplina');

        if ($disciplinas->isEmpty()) {
            return;
        }

        $query->groupBy('servidor_disciplina.ref_cod_servidor')
            ->havingRaw('?::int[] <@ array_agg(servidor_disciplina.ref_cod_disciplina)', ['{' . $disciplinas->implode(',') . '}']);
    }

    private function codigosServidores($lista): array
    {
        if (!is_string(value: $lista) && !is_numeric(value: $lista)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($codigo) => (int) trim(string: $codigo),
            explode(separator: ',', string: (string) $lista)
        )));
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-pesquisa-servidor-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = '0';
        $this->renderMenu = true;
        $this->renderMenuSuspenso = false;
    }
};
