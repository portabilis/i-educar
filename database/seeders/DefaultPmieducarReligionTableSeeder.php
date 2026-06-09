<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Seeder;

class DefaultPmieducarReligionTableSeeder extends Seeder
{
    public function run()
    {
        $religions = [
            'Adventista',
            'Ateísmo',
            'Budista',
            'Candomblé',
            'Católica',
            'Espírita',
            'Evangélica',
            'Hinduísta',
            'Judaica',
            'Messiânica',
            'Mormon',
            'Muçulmano',
            'Nenhuma',
            'Outras(os)',
            'Seicho-no-ie',
            'Testemunha de Jeová',
            'Tradições Indígenas',
            'Umbanda',
        ];

        foreach ($religions as $religion) {
            Religion::updateOrCreate([
                'name' => $religion,
            ]);
        }
    }
}
