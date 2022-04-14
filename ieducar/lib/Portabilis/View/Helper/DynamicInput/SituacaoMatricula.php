<?php

use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;

class Portabilis_View_Helper_DynamicInput_SituacaoMatricula extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = EnrollmentStatusFilter::getDescriptiveValues();

        return $this->insertOption(10, 'Todas', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Situação']];
    }

    public function situacaoMatricula($options = [])
    {
        parent::select($options);
    }
}
