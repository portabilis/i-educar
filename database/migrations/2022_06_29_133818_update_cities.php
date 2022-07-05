<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up()
    {
        //Comparação realizada com o seeder original

        //Atualização. Não possuem ibge_code e os nomes estão diferentes.
        $this->createOrUpdate('MA', 'Pindaré-Mirim', '2108504', 'Pindare Mirim');
        $this->createOrUpdate('PI', 'Aroeiras do Itaim', '2200954', 'Aroeira do Itaim');
        $this->createOrUpdate('PE', 'Belém do São Francisco', '2601607', 'Belem de Sao Francisco');
        $this->createOrUpdate('PE', 'Lagoa de Itaenga', '2608503', 'Lagoa do Itaenga');
        $this->createOrUpdate('MG', 'Brazópolis', '3108909', 'Brasopolis');
        $this->createOrUpdate('MG', 'Pingo-d\'Água', '3150539', 'Pingo D Agua');
        $this->createOrUpdate('MG', 'Sem-Peixe', '3165560', 'Sem Peixe');
        $this->createOrUpdate('MG', 'Tocos do Moji', '3169059', 'Tocos do Mogi');
        $this->createOrUpdate('RJ', 'Paraty', '3303807', 'Parati');
        $this->createOrUpdate('RJ', 'Trajano de Moraes', '3305901', 'Trajano de Morais');
        $this->createOrUpdate('PR', 'Bela Vista da Caroba', '4102752', 'Bela Vista do Caroba');
        $this->createOrUpdate('MS', 'Batayporã', '5002001', 'Bataipora');

        //Novos
        $this->createOrUpdate('AM', 'Itacoatiara', '1301902');
        $this->createOrUpdate('BA', 'Barro Preto', '2903300');
        $this->createOrUpdate('CE', 'Itapajé', '2306306');
        $this->createOrUpdate('MS', 'Paraíso das Águas', '5006275');
        $this->createOrUpdate('MT', 'Curvelândia', '5103437');
        $this->createOrUpdate('PA', 'Mojuí dos Campos', '1504752');
        $this->createOrUpdate('PA', 'Santa Izabel do Pará', '1506500');
        $this->createOrUpdate('PB', 'Joca Claudino', '2513653');
        $this->createOrUpdate('PB', 'São Domingos', '2513968');
        $this->createOrUpdate('PB', 'Tacima', '2516409');
        $this->createOrUpdate('PE', 'Ilha de Itamaracá', '2607604');
        $this->createOrUpdate('PR', 'Goioerê', '4108601');
        $this->createOrUpdate('RN', 'Serra Caiada', '2410306');
        $this->createOrUpdate('RS', 'Pinto Bandeira', '4314548');
        $this->createOrUpdate('SC', 'Garopaba', '4205704');
        $this->createOrUpdate('SC', 'Pescaria Brava', '4212650');
        $this->createOrUpdate('SC', 'Balneário Rincão', '4220000');
        $this->createOrUpdate('SP', 'Embu das Artes', '3515004');
        $this->createOrUpdate('TO', 'Couto Magalhães', '1706001');
        $this->createOrUpdate('TO', 'São Valério', '1720499');
    }

    public function createOrUpdate($state_abbreviation, $name, $ibge_code, $old_name = null)
    {
        //verifica se o codigo ibge já existe
        if (City::where('ibge_code', $ibge_code)->exists()) {
            return;
        }

        //atualiza ibge_code e nome, se a cidade estiver cadastrada sem o ibge_code
        $city = City::whereRaw('unaccent(name) ILIKE unaccent(?)', $old_name ?? $name)->whereHas('state', fn ($q) => $q->where('abbreviation', $state_abbreviation))->whereNull('ibge_code')->first();

        if ($city) {
            $city->update(['ibge_code' => $ibge_code, 'name' => $name]);

            return;
        }

        //cria a cidade
        if ($state_id = State::where('abbreviation', $state_abbreviation)->value('id')) {
            City::create(compact('state_id', 'name', 'ibge_code'));
        }
    }

    public function down()
    {
    }
};
