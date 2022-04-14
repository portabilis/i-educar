<?php

namespace App\Services;

use App\Models\SchoolManager;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;

class SchoolManagerService
{
    /**
     * @param $schoolId
     *
     * @return SchoolManager[]
     */
    public function getSchoolManagers($schoolId)
    {
        return SchoolManager::ofSchool($schoolId)->get();
    }

    /**
     * @param $schoolId
     */
    public function deleteAllManagers($schoolId)
    {
        $managers = SchoolManager::ofSchool($schoolId)->get();
        foreach ($managers as $schoolManager) {
            $schoolManager->delete();
        }
    }

    /**
     * @param SchoolManagerValueObject $valueObject
     */
    public function storeManager(SchoolManagerValueObject $valueObject)
    {
        $schoolManager = new SchoolManager();
        $schoolManager->employee_id = $valueObject->employeeId;
        $schoolManager->school_id = $valueObject->schoolId;
        $schoolManager->role_id = $valueObject->roleId;
        $schoolManager->access_criteria_id = $valueObject->accessCriteriaId;
        $schoolManager->access_criteria_description = $valueObject->accessCriteriaDescription;
        $schoolManager->link_type_id = $valueObject->linkTypeId;
        $schoolManager->chief = (bool) $valueObject->isChief;
        $schoolManager->save();
    }
}
