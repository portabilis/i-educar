<?php

namespace Database\Seeders;

use App\Support\Database\IncrementSequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use IncrementSequence;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(DistrictsTableSeeder::class);

        $this->incrementSequence('countries');
        $this->incrementSequence('states');
        $this->incrementSequence('cities');
        $this->incrementSequence('districts');

        $this->call(DefaultCadastroRacaTableSeeder::class);
        $this->call(DefaultPmieducarReligionTableSeeder::class);
        $this->call(DefaultCadastroDeficienciaTableSeeder::class);
        $this->call(DefaultPmieducarProjetoTableSeeder::class);
        $this->call(DefaultPmieducarAlunoBeneficioTableSeeder::class);
        $this->call(DefaultPmieducarTipoOcorrenciaDisciplinarTableSeeder::class);
        $this->call(DefaultPmieducarAbandonoTipoTableSeeder::class);
        $this->call(DefaultPmieducarTransferenciaTipoTableSeeder::class);
        $this->call(DefaultPmieducarTipoDispensaTableSeeder::class);
        $this->call(DefaultPmieducarTipoRegimeTableSeeder::class);
        $this->call(DefaultPmieducarNivelEnsinoTableSeeder::class);
        $this->call(DefaultPmieducarTipoEnsinoTableSeeder::class);
        $this->call(DefaultPmieducarModuloTableSeeder::class);
        $this->call(DefaultPmieducarTurmaTipoTableSeeder::class);
        $this->call(DefaultPmieducarFuncaoTableSeeder::class);
        $this->call(DefaultCadastroEscolaridadeTableSeeder::class);
        $this->call(DefaultPmieducarMotivoAfastamentoTableSeeder::class);
        $this->call(DefaultPortalFuncionarioVinculoTableSeeder::class);
    }
}
