<?php

namespace App\Services\Educacenso\Version2022;

use App\Models\City;
use App\Models\District;
use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacySchool;
use App\Services\Educacenso\Version2019\Registro00Import as Registro00Import2019;
use App\Services\Educacenso\Version2022\Models\Registro00Model;

class Registro00Import extends Registro00Import2019
{
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->model = $model;
        $this->year = $year;
        $this->user = $user;
        parent::import($model, $year, $user);
    }

    protected function getOrCreateSchool()
    {
        parent::getOrCreateSchool();

        $schoolInep = parent::getSchool();

        if (empty($schoolInep)) {
            return;
        }

        /** @var LegacySchool $school */
        $school = $schoolInep->school;
        $model = $this->model;

        if (!$school->iddis) {
            $ibge_code = explode($model->codigoIbgeMunicipio, $model->codigoIbgeDistrito)[1];
            $city = City::where('ibge_code', $model->codigoIbgeMunicipio)->first();

            $district = District::where('city_id', $city->id)
                ->where('ibge_code', $ibge_code)
                ->first();

            $school->iddis = $district->getKey();
        }

        $school->poder_publico_parceria_convenio = transformDBArrayInString($model->poderPublicoConveniado) ?: null;
        $school->qtd_matriculas_atividade_complementar = $model->qtdMatAtividadesComplentar ?: null;
        $school->qtd_atendimento_educacional_especializado = $model->qtdMatAee ?: null;
        $school->qtd_ensino_regular_creche_par = $model->qtdMatCrecheParcial ?: null;
        $school->qtd_ensino_regular_creche_int = $model->qtdMatCrecheIntegral ?: null;
        $school->qtd_ensino_regular_pre_escola_par = $model->qtdMatPreEscolaParcial ?: null;
        $school->qtd_ensino_regular_pre_escola_int = $model->qtdMatPreEscolaIntegral ?: null;
        $school->qtd_ensino_regular_ensino_fund_anos_iniciais_par = $model->qtdMatFundamentalIniciaisParcial ?: null;
        $school->qtd_ensino_regular_ensino_fund_anos_iniciais_int = $model->qtdMatFundamentalIniciaisIntegral ?: null;
        $school->qtd_ensino_regular_ensino_fund_anos_finais_par = $model->qtdMatFundamentalFinaisParcial ?: null;
        $school->qtd_ensino_regular_ensino_fund_anos_finais_int = $model->qtdMatFundamentalFinaisIntegral ?: null;
        $school->qtd_ensino_regular_ensino_med_anos_iniciais_par = $model->qtdMatEnsinoMedioParcial ?: null;
        $school->qtd_ensino_regular_ensino_med_anos_iniciais_int = $model->qtdMatEnsinoMedioIntegral ?: null;
        $school->qtd_edu_especial_classe_especial_par = $model->qdtMatClasseEspecialParcial ?: null;
        $school->qtd_edu_especial_classe_especial_int = $model->qdtMatClasseEspecialIntegral ?: null;
        $school->qtd_edu_eja_ensino_fund = $model->qdtMatEjaFundamental ?: null;
        $school->qtd_edu_eja_ensino_med = $model->qtdMatEjaEnsinoMedio ?: null;
        $school->qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_par = $model->qtdMatEdProfIntegradaEjaFundamentalParcial ?: null;
        $school->qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_int = $model->qtdMatEdProfIntegradaEjaFundamentalIntegral ?: null;
        $school->qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_par = $model->qtdMatEdProfIntegradaEjaNivelMedioParcial ?: null;
        $school->qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_int = $model->qtdMatEdProfIntegradaEjaNivelMedioIntegral ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_par = $model->qtdMatEdProfConcomitanteEjaNivelMedioParcial ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_int = $model->qtdMatEdProfConcomitanteEjaNivelMedioIntegral ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_par = $model->qtdMatEdProfIntercomentarEjaNivelMedioParcial ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_int = $model->qtdMatEdProfIntercomentarEjaNivelMedioIntegral ?: null;
        $school->qtd_edu_prof_quali_prof_tec_inte_ensino_med_par = $model->qtdMatEdProfIntegradaEnsinoMedioParcial ?: null;
        $school->qtd_edu_prof_quali_prof_tecinte_ensino_med_int = $model->qtdMatEdProfIntegradaEnsinoMedioIntegral ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_ensino_med_par = $model->qtdMatEdProfConcomitenteEnsinoMedioParcial ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_ensino_med_int = $model->qtdMatEdProfConcomitenteEnsinoMedioIntegral ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_par = $model->qtdMatEdProfIntercomplementarEnsinoMedioParcial ?: null;
        $school->qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_int = $model->qtdMatEdProfIntercomplementarEnsinoMedioIntegral ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_par = $model->qtdMatEdProfTecnicaIntegradaEnsinoMedioParcial ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_int = $model->qtdMatEdProfTecnicaIntegradaEnsinoMedioIntegral ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_par = $model->qtdMatEdProfTecnicaConcomitanteEnsinoMedioParcial ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_int = $model->qtdMatEdProfTecnicaConcomitanteEnsinoMedioIntegral ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_par = $model->qtdMatEdProfTecnicaIntercomplementarEnsinoMedioParcial ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_int = $model->qtdMatEdProfTecnicaIntercomplementarEnsinoMedioItegral ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_subsequente_ensino_med = $model->qtdMatEdProfTecnicaSubsequenteEnsinoMedio ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_par = $model->qtdMatEdProfTecnicaIntegradaEjaNivelMedioParcial ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_int = $model->qtdMatEdProfTecnicaIntegradaEjaNivelMedioIntegral ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_par = $model->qtdMatEdProfTecnicaConcomitanteEjaNivelMedioParcial ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_int = $model->qtdMatEdProfTecnicaConcomitanteEjaNivelMedioIntegral ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_par = $model->qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioParcial ?: null;
        $school->qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_int = $model->qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioIntegral ?: null;

        $school->save();
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro00|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro00Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }
}
