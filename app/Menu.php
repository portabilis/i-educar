<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'link',
        'icon',
        'order',
        'type',
        'process',
        'active',
    ];

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function root()
    {
        $root = $this;

        while ($root->parent) {
            $root = $root->parent;
        }

        return $root;
    }
}
