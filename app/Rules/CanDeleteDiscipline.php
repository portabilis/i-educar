<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CanDeleteDiscipline implements Rule
{
    public const FOREIGN_KEY_MESSAGE = 'Este componente não pode ser excluído para preservar o histórico, pois possui vínculo com turmas, professores ou servidores.';

    private const LINKS = [
        'series' => [
            'label' => 'vínculo com séries',
            'tables' => [
                'modules.componente_curricular_ano_escolar' => 'componente_curricular_id',
            ],
        ],
        'scores' => [
            'label' => 'notas lançadas',
            'tables' => [
                'modules.nota_componente_curricular' => 'componente_curricular_id',
                'modules.nota_componente_curricular_media' => 'componente_curricular_id',
                'modules.nota_exame' => 'ref_cod_componente_curricular',
            ],
        ],
        'absences' => [
            'label' => 'faltas lançadas',
            'tables' => [
                'modules.falta_componente_curricular' => 'componente_curricular_id',
            ],
        ],
        'opinions' => [
            'label' => 'pareceres descritivos',
            'tables' => [
                'modules.parecer_componente_curricular' => 'componente_curricular_id',
            ],
        ],
        'exemptions' => [
            'label' => 'dispensas de alunos',
            'tables' => [
                'pmieducar.dispensa_disciplina' => 'ref_cod_disciplina',
            ],
        ],
        'schedules' => [
            'label' => 'horários no quadro de horários',
            'tables' => [
                'pmieducar.quadro_horario_horarios' => 'ref_cod_disciplina',
            ],
        ],
    ];

    private array $links = [];

    public static function linksByDiscipline(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $found = [];

        foreach (self::LINKS as $link => $config) {
            foreach ($config['tables'] as $table => $column) {
                $linkedIds = DB::table('modules.componente_curricular')
                    ->whereIn('id', $ids)
                    ->whereExists(fn ($query) => $query->from($table)->whereColumn("{$table}.{$column}", 'modules.componente_curricular.id'))
                    ->pluck('id');

                foreach ($linkedIds as $id) {
                    $found[$id][$link] = true;
                }
            }
        }

        return array_map(
            fn (array $links) => array_values(array_intersect(array_keys(self::LINKS), array_keys($links))),
            $found
        );
    }

    public static function messageFor(array $links): string
    {
        if ($links === ['series']) {
            return 'Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico.';
        }

        $labels = array_map(fn (string $link) => self::LINKS[$link]['label'], $links);

        return 'Este componente não pode ser excluído para preservar o histórico, pois possui: ' . Arr::join($labels, ', ', ' e ') . '.';
    }

    public function passes($attribute, $value)
    {
        $this->links = self::linksByDiscipline([$value->getKey()])[$value->getKey()] ?? [];

        return empty($this->links);
    }

    public function message()
    {
        return self::messageFor($this->links);
    }
}
