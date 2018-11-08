<?php

require_once 'include/pmieducar/geral.inc.php';

class CopyOrRemoveClassSteps
{
    private $curseCode = null;
    private $standerdSchoolYear = null;
    private $currentYear = null;

    public function  updateClassStepsForCurse()
    {
        $classStepsObject = new ClsPmieducarTurmaModulo();
        
        if ($this->standerdSchoolYear == 0) {
            $classStepsObject->copySchoolStepsIntoClassesForCurseAndYear($this->curseCode, $this->currentYear);
        } else {
            $classStepsObject->removeStepsOfClassesForCurseAndYear($this->curseCode, $this->currentYear);
        }
    }

    /**
     * @param integer $curseCode
     */
    public function setCurseCode($curseCode)
    {
        $this->curseCode = $curseCode;
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