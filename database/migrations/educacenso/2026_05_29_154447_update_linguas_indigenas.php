<?php

use App\Models\EducacensoIndigenousLanguage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $languages = [
            390 => 'Bahetá / Baetá',
            106 => 'Mawé/Sateré-Mawé',
            391 => 'Guarasugwe/Guarasug\'wé/Warazu',
            157 => 'Tikúna/Magüta',
            324 => 'Charrúa/Charrua/Ypi',
            392 => 'Kipeá / Kariri-Kipeá',
            393 => 'Otxukayana',
            394 => 'Dzubukuá / Dzibukuá / Kariri-Dzubukuá',
        ];

        foreach ($languages as $id => $lingua) {
            EducacensoIndigenousLanguage::updateOrCreate([
                'id' => $id,
            ], [
                'lingua' => $lingua,
            ]);
        }
    }
};
