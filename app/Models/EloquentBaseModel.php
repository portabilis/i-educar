<?php

namespace App\Models;

use iEducar\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;

class EloquentBaseModel extends Model implements BaseModel
{
    /**
     * Get primary key
     *
     * @return string
     */
    public function id()
    {
        return $this->{$this->primaryKey};
    }
}
