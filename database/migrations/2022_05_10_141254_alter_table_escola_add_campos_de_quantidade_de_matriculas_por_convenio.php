<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola', static function (Blueprint $table) {
            $table->integer('qtd_matriculas_atividade_complementar')->nullable();
            $table->integer('qtd_atendimento_educacional_especializado')->nullable();
            $table->integer('qtd_ensino_regular_creche_par')->nullable();
            $table->integer('qtd_ensino_regular_creche_int')->nullable();
            $table->integer('qtd_ensino_regular_pre_escola_par')->nullable();
            $table->integer('qtd_ensino_regular_pre_escola_int')->nullable();
            $table->integer('qtd_ensino_regular_ensino_fund_anos_iniciais_par')->nullable();
            $table->integer('qtd_ensino_regular_ensino_fund_anos_iniciais_int')->nullable();
            $table->integer('qtd_ensino_regular_ensino_fund_anos_finais_par')->nullable();
            $table->integer('qtd_ensino_regular_ensino_fund_anos_finais_int')->nullable();
            $table->integer('qtd_ensino_regular_ensino_med_anos_iniciais_par')->nullable();
            $table->integer('qtd_ensino_regular_ensino_med_anos_iniciais_int')->nullable();
            $table->integer('qtd_edu_especial_classe_especial_par')->nullable();
            $table->integer('qtd_edu_especial_classe_especial_int')->nullable();
            $table->integer('qtd_edu_eja_ensino_fund')->nullable();
            $table->integer('qtd_edu_eja_ensino_med')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_int')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_int')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_int')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_int')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_inte_ensino_med_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tecinte_ensino_med_int')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_ensino_med_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_ensino_med_int')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_par')->nullable();
            $table->integer('qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_int')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_par')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_int')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_par')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_int')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_par')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_int')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_par')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_int')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_par')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_subsequente_ensino_med')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_int')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_par')->nullable();
            $table->integer('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_int')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola', static function (Blueprint $table) {
            $table->dropColumn('qtd_matriculas_atividade_complementar');
            $table->dropColumn('qtd_atendimento_educacional_especializado');
            $table->dropColumn('qtd_ensino_regular_creche_par');
            $table->dropColumn('qtd_ensino_regular_creche_int');
            $table->dropColumn('qtd_ensino_regular_pre_escola_par');
            $table->dropColumn('qtd_ensino_regular_pre_escola_int');
            $table->dropColumn('qtd_ensino_regular_ensino_fund_anos_iniciais_par');
            $table->dropColumn('qtd_ensino_regular_ensino_fund_anos_iniciais_int');
            $table->dropColumn('qtd_ensino_regular_ensino_fund_anos_finais_par');
            $table->dropColumn('qtd_ensino_regular_ensino_fund_anos_finais_int');
            $table->dropColumn('qtd_ensino_regular_ensino_med_anos_iniciais_par');
            $table->dropColumn('qtd_ensino_regular_ensino_med_anos_iniciais_int');
            $table->dropColumn('qtd_edu_especial_classe_especial_par');
            $table->dropColumn('qtd_edu_especial_classe_especial_int');
            $table->dropColumn('qtd_edu_eja_ensino_fund');
            $table->dropColumn('qtd_edu_eja_ensino_med');
            $table->dropColumn('qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_int');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_int');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_int');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_int');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_inte_ensino_med_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_tecinte_ensino_med_int');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_ensino_med_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_ensino_med_int');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_par');
            $table->dropColumn('qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_int');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_par');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_int');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_par');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_int');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_par');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_int');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_par');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_int');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_par');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_subsequente_ensino_med');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_int');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_par');
            $table->dropColumn('qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_int');
        });
    }
};
