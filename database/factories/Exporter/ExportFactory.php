<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Enrollment;
use App\Models\Exporter\Export;
use Database\Factories\LegacyUserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExportFactory extends Factory
{
    protected $model = Export::class;

    public function definition(): array
    {
        return [
            'user_id' => fn () => LegacyUserFactory::new()->current(),
            'model' => Enrollment::class,
            'fields' => ['registration_id'],
            'hash' => md5(time()),
            'filename' => str_replace(' ', '_', 'matriculas' . '_'. \Carbon\Carbon::now()->toDateTimeString() .  '.csv'),
            'url' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'filters' => [],
        ];
    }
}
