<?php

namespace App\Rules;

use App\Models\LegacyDisciplineExemption;
use iEducar\Modules\School\Model\ExemptionType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class CanCreateActiveSearchExemption implements Rule
{
    public $msg;

    public function passes($attribute, $value)
    {
        $legacyRegistration = $value['registration'];
        $registrationDate = $legacyRegistration->data_matricula;
        $startDate = Carbon::createFromFormat('Y-m-d', $value['start_date']);
        $endDate = $value['end_date'] ? Carbon::createFromFormat('Y-m-d', $value['start_date']) : null;
        $exemptionTypeId = $value['exemption_type_id'];
        $stages = $value['stages'];

        if ($startDate->lt($registrationDate)) {
            $this->msg = 'Data de início não pode ser menor que a data matrícula.';
            return false;
        }

        if ($endDate) {
            if ($endDate->lt($startDate)) {
                $this->msg = 'Data de retorno não pode ser menor que a data de início';
                return false;
            }
            if ($endDate->lt($registrationDate)) {
                $this->msg = 'Data de retorno não pode ser menor que a data da matrícula';
                return false;
            }
            if ($endDate->gt($startDate)){
                $this->msg = 'Data de retorno não pode ser maior que a data de início';
                return false;
            }
        }

        $legacyDisciplineExemption = LegacyDisciplineExemption::query()
            ->select('dispensa_disciplina.*')
            ->distinct()
            ->join('pmieducar.tipo_dispensa', 'ref_cod_tipo_dispensa', '=', 'cod_tipo_dispensa')
            ->join('pmieducar.dispensa_etapa', 'dispensa_etapa.ref_cod_dispensa', '=', 'cod_dispensa')
            ->where('ref_cod_matricula', $legacyRegistration->cod_matricula)
            ->where('ref_cod_tipo_dispensa', $exemptionTypeId)
            ->where('tipo', ExemptionType::DISPENSA_BUSCA_ATIVA)
            ->whereIn('dispensa_etapa.etapa', $stages);

        $activeSearchExemptions = $legacyDisciplineExemption->first();

        if($activeSearchExemptions){
            $this->msg = 'Já existe uma busca ativa lançada para as etapas selecionadas.';
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->msg;
    }
}
