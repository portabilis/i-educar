<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolManager extends Model
{
    public function individual()
    {
        return $this->belongsTo(Individual::class, 'individual_id', 'id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'cod_escola');
    }

    public function role()
    {
        return $this->belongsTo(ManagerRole::class, 'role_id', 'id');
    }

    public function accessCriteria()
    {
        return $this->belongsTo(ManagerAccessCriteria::class, 'access_criteria_id', 'id');
    }

    public function linkType()
    {
        return $this->belongsTo(ManagerLinkType::class, 'link_type_id', 'id');
    }
}
