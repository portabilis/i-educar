<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bnccSeries extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'bncc_series';
    protected $primaryKey = 'id';
    protected $fillable = ['id_bncc', 'id_serie', 'id'];
    
    public function getRouteKeyName()
	{
		return 'id';
	}
}
