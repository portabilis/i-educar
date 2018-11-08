<?php

require_once 'include/pmieducar/geral.inc.php';

class CopyOrRemoveClassSteps
{
    private $courseCode = null;
    private $standerdSchoolYear = null;
    private $currentYear = null;

    public function  updateClassStepsForCourse()
    {
        $classStepsObject = new ClsPmieducarTurmaModulo();

        $classStepsObject->removeStepsOfClassesForCourseAndYear($this->courseCode, $this->currentYear);

        if ($this->standerdSchoolYear == 0) {
            $classStepsObject->copySchoolStepsIntoClassesForCourseAndYear($this->courseCode, $this->currentYear);
        }
    }

    /**
     * @param integer $courseCode
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;
    }

    /**
     * @param integer $standerdSchoolYear
     */
    public function setStanderdSchoolYear($standerdSchoolYear)
    {
        $this->standerdSchoolYear = $standerdSchoolYear;
    }

    /**
     * @param integer $currentYear
     */
    public function setCurrentYear($currentYear)
    {
        $this->currentYear = $currentYear;
    }
}