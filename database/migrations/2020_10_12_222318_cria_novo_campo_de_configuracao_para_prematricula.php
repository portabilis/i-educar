<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CriaNovoCampoDeConfiguracaoParaPrematricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prematriculaAtivo = Setting::query()->where('key', 'prematricula.active')->where('value', '1')->exists();

        if ($prematriculaAtivo) {
            DB::table('settings')->insert([
                'key' => 'prematricula.logo',
                'value' => '',
                'type' => 'string',
                'description' => 'URL referente à logo do Pré-matrícula Digital',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'setting_category_id' => 11,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->where('key', 'prematricula.logo')->delete();
    }
}
