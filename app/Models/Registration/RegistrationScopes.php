<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Builder;

trait RegistrationScopes
{
    /**
     * @return $this
     */
    public function doesntHaveFather()
    {
        return $this->whereHas('student.individual', function (Builder $query) {
            $query->whereNull('father_individual_id');
        });
    }

    /**
     * @return $this
     */
    public function studentIsActive()
    {
        return $this->whereHas('student', function (Builder $query) {
            // Soft deletes já faz o filtro
        });
    }

    /**
     * @param int $school
     *
     * @return $this
     */
    public function school($school)
    {
        return $this->where('school_id', $school);
    }

    /**
     * @param int $year
     *
     * @return $this
     */
    public function year($year)
    {
        return $this->where('year', $year);
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * @return $this
     */
    public function inProgress()
    {
        return $this->status(3);
    }
}
