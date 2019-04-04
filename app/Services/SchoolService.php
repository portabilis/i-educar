<?php

namespace App\Services;

use App\Models\SchoolManager;

class SchoolService
{
    /**
     * @param $schoolId
     * @return SchoolManager[]
     */
    public function getSchoolManagers($schoolId)
    {
        return SchoolManager::whereSchoolId($schoolId)->get();
    }

    /**
     * @param $schoolId
     */
    public function deleteAllManagers($schoolId)
    {
        SchoolManager::whereSchoolId($schoolId)->delete();
    }

    /**
     * @param integer $individualId
     * @param integer $schoolId
     * @param integer $roleId
     * @param integer $accessCriteriaId
     * @param string $accessCriteriaDescription
     * @param integer $linkTypeId
     * @param boolean $isChief
     */
    public function storeManager($individualId, $schoolId, $roleId, $accessCriteriaId, $accessCriteriaDescription, $linkTypeId, $isChief)
    {
        $schoolManager = new SchoolManager();
        $schoolManager->individual_id = $individualId;
        $schoolManager->school_id = $schoolId;
        $schoolManager->role_id = $roleId;
        $schoolManager->access_criteria_id = $accessCriteriaId;
        $schoolManager->access_criteria_description = $accessCriteriaDescription;
        $schoolManager->link_type_id = $linkTypeId;
        $schoolManager->chief = (bool)$isChief;
        $schoolManager->save();
    }
}
