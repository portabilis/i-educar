<?php

namespace Tests\Browser\Support;

use App\Menu;
use App\Services\MenuCacheService;

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
     * Raiz => [processo da raiz, link da raiz, ícone, grupos].
     * Grupo => lista de [título, processo, link].
     *
     * Os processos das raízes (7, 25, 55, 71) são os mesmos do menu real, e são
     * eles que fazem as telas de índice ficarem sempre visíveis.
     */
    private const TREE = [
        'Pessoas' => [7, '/intranet/educar_pessoas_index.php', 'fa-user', [
            'Cadastros' => [
                ['Pessoas físicas', self::INDIVIDUALS, '/intranet/atendidos_lst.php'],
            ],
        ]],
        'Escola' => [55, '/intranet/educar_index.php', 'fa-leanpub', [
            'Cadastros' => [
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
            'Séries' => [
                ['Componentes da série', self::GRADE_DISCIPLINES, '/intranet/educar_componentes_serie_lst.php'],
            ],
            'Componentes curriculares' => [
                ['Áreas de conhecimento', self::KNOWLEDGE_AREAS, '/module/AreaConhecimento/index'],
            ],
        ]],
        'Servidores' => [71, '/intranet/educar_servidores_index.php', 'fa-users', [
            'Cadastros' => [
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

        foreach (self::TREE as $title => [$process, $link, $icon, $groups]) {
            $root = self::menu($title, $process, $link, null, type: 1, order: $order++, icon: $icon);

            $groupOrder = 1;

            foreach ($groups as $groupTitle => $items) {
                $group = self::menu($groupTitle, null, null, $root->getKey(), type: 2, order: $groupOrder++);

                $itemOrder = 1;

                foreach ($items as [$itemTitle, $itemProcess, $itemLink]) {
                    self::menu($itemTitle, $itemProcess, $itemLink, $group->getKey(), type: 3, order: $itemOrder++);
                }
            }
        }

        app(MenuCacheService::class)->flushAll();

        return self::processes();
    }

    /**
     * Processos de todos os itens da árvore (sem as raízes, que são sempre visíveis).
     *
     * @return array<int, int>
     */
    public static function processes(): array
    {
        $processes = [];

        foreach (self::TREE as [, , , $groups]) {
            foreach ($groups as $items) {
                foreach ($items as [, $process]) {
                    $processes[] = $process;
                }
            }
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
        return Menu::query()->where('process', $process)->value('id');
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
