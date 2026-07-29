<?php

use App\Models\File;
use App\Models\FileRelation;
use App\Models\LegacyStudent;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $students = LegacyStudent::query()
            ->select([
                'cod_aluno',
                'url_laudo_medico',
            ])
            ->whereNotNull('url_laudo_medico')
            ->orderBy('cod_aluno')
            ->chunk(100, function ($students) {
                foreach ($students as $student) {
                    $laudos = json_decode($student->url_laudo_medico);

                    if (empty($laudos)) {
                        continue;
                    }

                    foreach ($laudos as $laudo) {
                        if (empty($laudo->url)) {
                            continue;
                        }

                        $date = Carbon::createFromFormat('d/m/Y', $laudo->data ?? now());
                        $file = File::query()->create([
                            'url' => $laudo->url,
                            'size' => 0,
                            'original_name' => pathinfo($laudo->url, PATHINFO_BASENAME),
                            'extension' => pathinfo($laudo->url, PATHINFO_EXTENSION),
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);

                        FileRelation::query()->create([
                            'relation_type' => LegacyStudent::class,
                            'relation_id' => $student->getKey(),
                            'file_id' => $file->getKey(),
                            'type' => 'laudo',
                        ]);
                    }
                }
            });
    }
};
