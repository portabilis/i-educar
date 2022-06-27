<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

    public function up()
    {
        //Atualiza cidades do cities seeder original, com nomes diferentes sem o ibge code.
        $this->createOrUpdate('MA', 'Pindaré-Mirim', '2108504', 'Pindare Mirim');
        $this->createOrUpdate('PI', 'Aroeiras do Itaim', '2200954', 'Aroeira do Itaim');
        $this->createOrUpdate('PE', 'Belém do São Francisco', '2601607', 'Belem de Sao Francisco');
        $this->createOrUpdate('PE', 'Lagoa de Itaenga', '2608503', 'Lagoa do Itaenga');
        $this->createOrUpdate('MG', 'Brazópolis', '3108909', 'Brasopolis');
        $this->createOrUpdate('MG', "Pingo-d'Água", '3150539', 'Pingo D Agua');
        $this->createOrUpdate('MG', "Sem-Peixe", '3165560', 'Sem Peixe');
        $this->createOrUpdate('MG', "Tocos do Moji", '3169059', 'Tocos do Mogi');
        $this->createOrUpdate('RJ', "Paraty", '3303807', 'Parati');
        $this->createOrUpdate('RJ', "Trajano de Moraes", '3305901', 'Trajano de Morais');
        $this->createOrUpdate('PR', "Bela Vista da Caroba", '4102752', 'Bela Vista do Caroba');
        $this->createOrUpdate('MS', "Batayporã", '5002001', 'Bataipora');

        //cria tabela temporária para cidades 2022
        Schema::create('temp_cities_2022', function (Blueprint $table) {
            $table->temporary();
            $table->string('state_abbreviation');
            $table->string('name');
            $table->integer('ibge_code');
        });

        //copia valores da tabela de cidades 2022
        $filename = database_path('csv/cities/cities_2022.csv');

        DB::statement("COPY temp_cities_2022 FROM '{$filename}' DELIMITER ',' CSV HEADER");

        //obtem somente as cidades a serem atualizadas
        $cities = DB::table('temp_cities_2022 as tc')->whereNotExists(function ($query) {
            $query->selectRaw('1')
                ->from('cities as c')
                ->join('states as s', 'c.state_id', 's.id')
                ->where(function ($q) {
                    //com ibge_code
                    $q->where(function ($q) {
                        $q->whereNotNull('c.ibge_code');
                        $q->whereRaw('c.ibge_code  = tc.ibge_code');
                    });

                    //sem ibge_code, pesquisa por nome e estado
                    $q->orWhere(function ($q) {
                        $q->whereNull('c.ibge_code');
                        $q->whereRaw("unaccent(c.name) ILIKE unaccent(tc.name)");
                        $q->where('s.abbreviation', 'tc.state_abbreviation');
                    });
                });
        })->get();

        //cria ou atualiza as cidades
        if ($cities->isNotEmpty()) {
            foreach ($cities as $city) {
                $this->createOrUpdate($city->state_abbreviation, $city->name, $city->ibge_code);
            }
        }

    }


    public function createOrUpdate($state_abbreviation, $name, $ibge_code, $old_name = null)
    {
        //codigo ibge unico
        if (City::where('ibge_code', $ibge_code)->exists()) {
            return;
        }

        //atualiza ibge_code e nome, se a cidade estiver cadastrada sem o ibge_code
        $city = City::whereRaw("unaccent(name) ILIKE unaccent(?)", $old_name ?? $name)->whereHas('state', fn($q) => $q->where('abbreviation', $state_abbreviation))->whereNull('ibge_code')->first();

        if ($city) {
            $city->update(['ibge_code' => $ibge_code, 'name' => $name]);
            return;
        }

        //cria a cidade, se o ibge_code não estiver cadastrada
        if ($state_id = State::where('abbreviation', $state_abbreviation)->value('id')) {
            City::create(compact('state_id', 'name', 'ibge_code'));
        }
    }

    public function down()
    {

    }
};
