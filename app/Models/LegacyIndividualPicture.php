<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyIndividualPicture extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.fisica_foto';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var bool
     */
    public $timestamps = false;

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
