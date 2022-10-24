<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyIndividualPicture extends Model
{
    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    /**
     * @var string
     */
    protected $table = 'cadastro.fisica_foto';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    public $fillable = [
        'idpes',
        'caminho',
    ];

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->caminho;
    }

    /**
     * @return void
     */
    public function setUrlAttribute($url)
    {
        $this->caminho = $url;
    }
}
