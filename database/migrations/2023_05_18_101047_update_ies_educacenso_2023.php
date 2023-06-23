<?php

use App\Models\EducacensoInstitution;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up(): void
    {
        $file = file(database_path('csv/censo/2023/update_or_create_ies_2023.csv'));

        $admin = LegacyUser::query()
            ->where('ativo', 1)
            ->where('ref_cod_tipo_usuario', LegacyUserType::LEVEL_ADMIN)
            ->first();

        if ($admin) {
            foreach ($file as $line) {
                $data = str_getcsv(
                    string: $line,
                    separator: ';'
                );

                EducacensoInstitution::updateOrCreate([
                    'ies_id' => $data[0],
                ], [
                    'nome' => $data[1],
                    'dependencia_administrativa_id' => $data[2],
                    'tipo_instituicao_id' => $data[3],
                    'user_id' => $admin->getKey(),
                ]);
            }
        }
    }
};
