<?php

namespace Tests\Browser\Support;

use App\Menu;
use App\Services\MenuCacheService;
use InvalidArgumentException;

/**
 * Árvore de menus usada pelos testes de browser.
 *
 * O banco `testing` roda com LEGACY_SEED_DATA=false, então as migrations de
 * database/migrations/data/ não rodam e public.menus fica quase vazia. Não dá
 * para reaproveitar o DefaultMenusTableSeeder: database/sqls/inserts/public.menus.sql
 * insere IDs explícitos (1, 2, 3...) que colidem com os menus criados por
 * migrations de fora de data/.
 *
 * Esta é uma réplica reduzida do menu real — mesmos títulos, links e códigos de
 * processo, só com as telas que a suíte percorre. A hierarquia importa: o
 * ProcessPolicy::view() libera qualquer processo que tenha um menu **raiz**,
 * então um item precisa de parent_id para que "visualiza" seja de fato conferido.
 */
final class Menus
{
    public const INDIVIDUALS = 43;

    public const USER_TYPES = 554;

    public const USERS = 555;

    public const INSTITUTION = 559;

    public const SCHOOLS = 561;

    public const COURSES = 566;

    public const STUDENTS = 578;

    public const GRADES = 583;

    public const SCHOOL_GRADES = 585;

    public const SCHOOL_CLASSES = 586;

    public const CALENDARS = 620;

    public const EMPLOYEES = 635;

    public const KNOWLEDGE_AREAS = 945;

    public const DISCIPLINES = 946;

    public const EVALUATION_RULES = 947;

    public const GRADE_DISCIPLINES = 9998859;

    /**
     * Endereçamento > Cadastros.
     */
    public const COUNTRIES = 753;

    public const STATES = 754;

    public const CITIES = 755;

    public const DISTRICTS = 759;

    /**
     * Pessoas > Cadastros > Tipos.
     */
    public const RELIGIONS = 579;

    public const DEFICIENCIES = 631;

    public const RACES = 678;

    public const DOCUMENT_ABSENCE_REASONS = 5842;

    /**
     * Escola > Cadastros > Tipos.
     */
    public const TRANSFER_TYPES = 575;

    public const CALENDAR_DAY_REASONS = 576;

    public const EXEMPTION_TYPES = 577;

    public const DISCIPLINARY_OCCURRENCE_TYPES = 580;

    public const BENEFITS = 581;

    public const EDUCATION_TYPES = 558;

    public const SCHOOL_CLASS_TYPES = 570;

    public const EDUCATION_LEVELS = 571;

    public const STAGE_TYPES = 584;

    public const ENROLLMENT_SEQUENCES = 587;

    public const REGIME_TYPES = 568;

    public const AVERAGE_FORMULAS = 948;

    public const ROUNDING_TABLES = 949;

    public const ABANDONMENT_TYPES = 950;

    public const PROJECTS = 21250;

    /**
     * Servidores > Cadastros > Tipos.
     */
    public const BOND_TYPES = 190;

    public const SCHOOLING_DEGREES = 632;

    public const WITHDRAWAL_REASONS = 633;

    public const ROLES = 634;

    /**
     * Raiz => [processo da raiz, link da raiz, ícone, filhos].
     *
     * Nos filhos, uma chave **string** é um agrupamento sem link (o "Cadastros",
     * o "Tipos" e os subgrupos do menu real) e a lista sob ela é recursiva; uma
     * chave numérica é um item, no formato [título, processo, link].
     *
     * Os processos das raízes (7, 25, 55, 71) são os mesmos do menu real, e são
     * eles que fazem as telas de índice ficarem sempre visíveis.
     */
    private const TREE = [
        'Endereçamento' => [68, '/intranet/educar_enderecamento_index.php', 'fa-map', [
            'Cadastros' => [
                ['Países', self::COUNTRIES, '/intranet/public_pais_lst.php'],
                ['Estados', self::STATES, '/intranet/public_uf_lst.php'],
                ['Municípios', self::CITIES, '/intranet/public_municipio_lst.php'],
                ['Distritos', self::DISTRICTS, '/intranet/public_distrito_lst.php'],
            ],
        ]],
        'Pessoas' => [7, '/intranet/educar_pessoas_index.php', 'fa-user', [
            'Cadastros' => [
                'Tipos' => [
                    ['Tipos de cor ou raça', self::RACES, '/intranet/educar_raca_lst.php'],
                    ['Tipos de deficiência', self::DEFICIENCIES, '/intranet/educar_deficiencia_lst.php'],
                    ['Tipos de religião', self::RELIGIONS, '/intranet/educar_religiao_lst.php'],
                    ['Motivos de ausência de documentação', self::DOCUMENT_ABSENCE_REASONS, '/intranet/educar_motivo_ausencia_documentacao_lst.php'],
                ],
                ['Pessoas físicas', self::INDIVIDUALS, '/intranet/atendidos_lst.php'],
            ],
        ]],
        'Escola' => [55, '/intranet/educar_index.php', 'fa-leanpub', [
            'Cadastros' => [
                'Tipos' => [
                    'Alunos' => [
                        ['Tipos de benefício', self::BENEFITS, '/intranet/educar_aluno_beneficio_lst.php'],
                        ['Tipos de projeto', self::PROJECTS, '/intranet/educar_projeto_lst.php'],
                    ],
                    'Matrículas' => [
                        ['Tipos de abandono', self::ABANDONMENT_TYPES, '/intranet/educar_abandono_tipo_lst.php'],
                        ['Tipos de ocorrência disciplinar', self::DISCIPLINARY_OCCURRENCE_TYPES, '/intranet/educar_tipo_ocorrencia_disciplinar_lst.php'],
                        ['Tipos de transferência', self::TRANSFER_TYPES, '/intranet/educar_transferencia_tipo_lst.php'],
                    ],
                    'Regras de avaliação' => [
                        ['Fórmulas de cálculo de média', self::AVERAGE_FORMULAS, '/module/FormulaMedia/index'],
                        ['Tabelas de arredondamento', self::ROUNDING_TABLES, '/module/TabelaArredondamento/index'],
                    ],
                    'Componentes curriculares' => [
                        ['Tipos de dispensa', self::EXEMPTION_TYPES, '/intranet/educar_tipo_dispensa_lst.php'],
                        ['Áreas de conhecimento', self::KNOWLEDGE_AREAS, '/module/AreaConhecimento/index'],
                    ],
                    'Cursos' => [
                        ['Tipos de ensino', self::EDUCATION_TYPES, '/intranet/educar_tipo_ensino_lst.php'],
                        ['Tipos de nível ensino', self::EDUCATION_LEVELS, '/intranet/educar_nivel_ensino_lst.php'],
                        ['Tipos de regime', self::REGIME_TYPES, '/intranet/educar_tipo_regime_lst.php'],
                    ],
                    'Escolas' => [
                        ['Tipos de etapas', self::STAGE_TYPES, '/intranet/educar_modulo_lst.php'],
                    ],
                    'Séries' => [
                        ['Componentes da série', self::GRADE_DISCIPLINES, '/intranet/educar_componentes_serie_lst.php'],
                        ['Sequências de enturmação', self::ENROLLMENT_SEQUENCES, '/intranet/educar_sequencia_serie_lst.php'],
                    ],
                    'Turmas' => [
                        ['Tipos de turma', self::SCHOOL_CLASS_TYPES, '/intranet/educar_turma_tipo_lst.php'],
                    ],
                    'Calendários' => [
                        ['Tipos de evento do calendário', self::CALENDAR_DAY_REASONS, '/intranet/educar_calendario_dia_motivo_lst.php'],
                    ],
                ],
                ['Alunos', self::STUDENTS, '/intranet/educar_aluno_lst.php'],
                ['Escolas', self::SCHOOLS, '/intranet/educar_escola_lst.php'],
                ['Instituição', self::INSTITUTION, '/intranet/educar_instituicao_lst.php'],
                ['Calendários', self::CALENDARS, '/intranet/educar_calendario_ano_letivo_lst.php'],
                ['Regras de avaliação', self::EVALUATION_RULES, '/module/RegraAvaliacao/index'],
                ['Componentes curriculares', self::DISCIPLINES, '/intranet/educar_componente_curricular_lst.php'],
                ['Cursos', self::COURSES, '/intranet/educar_curso_lst.php'],
                ['Séries', self::GRADES, '/intranet/educar_serie_lst.php'],
                ['Séries da escola', self::SCHOOL_GRADES, '/intranet/educar_escola_serie_lst.php'],
                ['Turmas', self::SCHOOL_CLASSES, '/intranet/educar_turma_lst.php'],
            ],
        ]],
        'Servidores' => [71, '/intranet/educar_servidores_index.php', 'fa-users', [
            'Cadastros' => [
                'Tipos' => [
                    ['Funções', self::ROLES, '/intranet/educar_funcao_lst.php'],
                    ['Escolaridade', self::SCHOOLING_DEGREES, '/intranet/educar_escolaridade_lst.php'],
                    ['Motivos de afastamento', self::WITHDRAWAL_REASONS, '/intranet/educar_motivo_afastamento_lst.php'],
                    ['Vínculos da alocação', self::BOND_TYPES, '/intranet/funcionario_vinculo_lst.php'],
                ],
                ['Servidores', self::EMPLOYEES, '/intranet/educar_servidor_lst.php'],
            ],
        ]],
        'Configurações' => [25, '/intranet/educar_configuracoes_index.php', 'fa-gear', [
            'Permissões' => [
                ['Tipos de usuário', self::USER_TYPES, '/intranet/educar_tipo_usuario_lst.php'],
                ['Usuários', self::USERS, '/intranet/educar_usuario_lst.php'],
            ],
        ]],
    ];

    /**
     * Cria a árvore de menus e devolve os processos criados.
     *
     * @return array<int, int>
     */
    public static function build(): array
    {
        $order = 1;

        foreach (self::TREE as $title => [$process, $link, $icon, $children]) {
            $root = self::menu($title, $process, $link, null, type: 1, order: $order++, icon: $icon);

            self::children($children, $root->getKey(), depth: 2);
        }

        app(MenuCacheService::class)->flushAll();

        return self::processes();
    }

    /**
     * Cria os filhos de um menu, recursivamente.
     *
     * O `type` é a profundidade, como no menu real: raiz 1, "Cadastros" 2,
     * "Tipos" 3, os subgrupos de "Tipos" 4 e as telas sob eles 5.
     *
     * @param  array<int|string, mixed>  $children
     */
    private static function children(array $children, int $parent, int $depth): void
    {
        $order = 1;

        foreach ($children as $key => $child) {
            if (is_string($key)) {
                $group = self::menu($key, null, null, $parent, type: $depth, order: $order++);

                self::children($child, $group->getKey(), $depth + 1);

                continue;
            }

            [$title, $process, $link] = $child;

            self::menu($title, $process, $link, $parent, type: $depth, order: $order++);
        }
    }

    /**
     * Processos de todos os itens da árvore (sem as raízes, que são sempre visíveis).
     *
     * @return array<int, int>
     */
    public static function processes(): array
    {
        $processes = [];

        foreach (self::TREE as [, , , $children]) {
            $processes = array_merge($processes, self::leaves($children));
        }

        return $processes;
    }

    /**
     * Processos dos itens de um ramo, recursivamente.
     *
     * @param  array<int|string, mixed>  $children
     * @return array<int, int>
     */
    private static function leaves(array $children): array
    {
        $processes = [];

        foreach ($children as $key => $child) {
            if (is_string($key)) {
                $processes = array_merge($processes, self::leaves($child));

                continue;
            }

            $processes[] = $child[1];
        }

        return $processes;
    }

    /**
     * Quantos itens a tela de permissões oferece.
     *
     * Menu::processes() só considera menus que têm processo **e** pai, então o
     * total é o desta árvore mais os menus que já existem no banco `testing`
     * (migrations de fora de database/migrations/data/ criam a árvore de
     * "Movimentações de Matrícula"). Contar pelo banco evita fixar um número que
     * muda a cada migration nova.
     */
    public static function count(): int
    {
        return Menu::query()
            ->whereNotNull('parent_id')
            ->whereNotNull('process')
            ->count();
    }

    /**
     * ID do menu de um processo, que é o que a tela de permissões envia em
     * `processes[<menu_id>]` e o que pmieducar.menu_tipo_usuario referencia.
     */
    public static function id(int $process): int
    {
        $id = Menu::query()->where('process', $process)->value('id');

        // Processo fora da árvore reduzida não tem menu, e devolver nulo daqui
        // estouraria um TypeError sem dizer qual processo faltou.
        if ($id === null) {
            throw new InvalidArgumentException(
                "O processo [{$process}] não está na árvore de menus dos testes de browser."
            );
        }

        return $id;
    }

    private static function menu(
        string $title,
        ?int $process,
        ?string $link,
        ?int $parent,
        int $type,
        int $order,
        ?string $icon = null,
    ): Menu {
        return Menu::query()->firstOrCreate([
            'title' => $title,
            'parent_id' => $parent,
        ], [
            'process' => $process,
            'link' => $link,
            'icon' => $icon,
            'type' => $type,
            'order' => $order,
            'active' => true,
        ]);
    }
}
