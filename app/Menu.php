<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * @return Menu
     */
    public function root()
    {
        $root = $this;

        while ($root->parent) {
            $root = $root->parent;
        }

        return $root;
    }

    /**
     * @param User $user
     *
     * @return Collection
     */
    public static function user(User $user)
    {
        $ids = $user->menu()->pluck('process')->sortBy('process')->toArray();

        return static::query()
            ->with([
                'children' => function ($query) use ($ids) {
                    /** @var Builder $query */
                    $query->whereNull('process');
                    $query->orWhereIn('process', $ids);
                    $query->orderBy('order');
                    $query->with([
                        'children' => function ($query) use ($ids) {
                            /** @var Builder $query */
                            $query->whereNull('process');
                            $query->orWhereIn('process', $ids);
                            $query->orderBy('order');
                            $query->with([
                                'children' => function ($query) use ($ids) {
                                    /** @var Builder $query */
                                    $query->whereNull('process');
                                    $query->orWhereIn('process', $ids);
                                    $query->orderBy('order');
                                    $query->with([
                                        'children' => function ($query) use ($ids) {
                                            /** @var Builder $query */
                                            $query->whereNull('process');
                                            $query->orWhereIn('process', $ids);
                                            $query->orderBy('order');
                                            $query->with([
                                                'children' => function ($query) use ($ids) {
                                                    /** @var Builder $query */
                                                    $query->whereNull('process');
                                                    $query->orWhereIn('process', $ids);
                                                    $query->orderBy('order');
                                                }
                                            ]);
                                        }
                                    ]);
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }
}
