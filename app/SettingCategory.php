<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingCategory extends Model
{
    protected $table = 'public.settings_categories';

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function settings()
    {
        return $this->hasMany(Setting::class, 'setting_category_id');
    }
}
