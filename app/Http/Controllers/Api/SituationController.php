<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;

class SituationController extends ResourceController
{
    public function index(): array
    {
        return [
            'data' => EnrollmentStatusFilter::getDescriptiveValues(),
        ];
    }
}
