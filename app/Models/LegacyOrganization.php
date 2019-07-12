<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyOrganization extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.juridica';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
