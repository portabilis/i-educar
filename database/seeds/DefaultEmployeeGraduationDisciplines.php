<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultEmployeeGraduationDisciplines extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            6 => 'Língua /Literatura Portuguesa',
            7 => 'Língua /Literatura estrangeira - Inglês',
            8 => 'Língua /Literatura estrangeira - Espanhol',
            30 => 'Língua/Literatura estrangeira - Francês',
            9 => 'Língua /Literatura estrangeira - outra',
            27 => 'Língua indígena',
            23 => 'Libras',
            31 => 'Língua Portuguesa como Segunda Língua',
            10 => 'Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)',
            11 => 'Educação Física',
            3 => 'Matemática',
            1 => 'Química',
            2 => 'Física',
            4 => 'Biologia',
            5 => 'Ciências',
            12 => 'História',
            13 => 'Geografia',
            14 => 'Filosofia',
            28 => 'Estudos Sociais',
            29 => 'Sociologia',
            16 => 'Informática/Computação',
            17 => 'Disciplinas Áreas do conhecimento profissionalizantes',
            25 => 'Disciplinas Áreas do conhecimento pedagógicas',
            26 => 'Ensino religioso',
            32 => 'Estágio curricular supervisionado',
            99 => 'Outras Disciplinas Áreas do conhecimento',
        ];

        foreach ($data as $id => $name) {
            DB::table('employee_graduation_disciplines')->updateOrInsert(
                ['id' => $id],
                ['name' => $name]
            );
        }
    }
}
