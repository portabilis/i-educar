<?php

namespace App\Observers;

use App\Models\SchoolManager;

class SchoolManagerObserver
{
    /**
     * Handle the school manager "created" event.
     *
     *
     * @return void
     */
    public function created(SchoolManager $schoolManager)
    {
        if (!$schoolManager->isChief()) {
            return;
        }

        $this->updateManagerDataSchool($schoolManager, $schoolManager->employee_id, $schoolManager->role_id, $schoolManager->individual->person->email);
    }

    /**
     * Handle the school manager "deleted" event.
     *
     *
     * @return void
     */
    public function deleted(SchoolManager $schoolManager)
    {
        if (!$schoolManager->isChief()) {
            return;
        }

        $this->updateManagerDataSchool($schoolManager, null, null, null);
    }

    private function updateManagerDataSchool(SchoolManager $schoolManager, $individualId, $roleId, $managerEmail)
    {
        $school = $schoolManager->school;
        $school->ref_idpes_gestor = $individualId;
        $school->cargo_gestor = $roleId;
        $school->email_gestor = $managerEmail;
        $school->save();
    }
}
