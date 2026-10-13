<?php

namespace App\Models\Enums;

use Illuminate\Support\Collection;

enum PersonLinkType: string
{
    case STUDENT = 'student';
    case EMPLOYEE = 'employee';
    case MOTHER = 'mother';
    case FATHER = 'father';
    case RESPONSIBLE = 'responsible';

    public function name(): string
    {
        return match ($this) {
            self::STUDENT => 'Aluno',
            self::EMPLOYEE => 'Servidor',
            self::MOTHER => 'Mãe da Pessoa Física',
            self::FATHER => 'Pai da Pessoa Física',
            self::RESPONSIBLE => 'Responsável da Pessoa Física',
        };
    }

    /**
     * @return Collection<string, string>
     */
    public static function getDescriptiveValues(): Collection
    {
        return collect(self::cases())->mapWithKeys(
            fn (PersonLinkType $type) => [$type->value => $type->name()]
        );
    }
}
