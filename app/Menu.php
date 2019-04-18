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
     * Indica se o menu é um link ou tem ao menos um link em seus submenus.
     *
     * @return bool
     */
    public function hasLink()
    {
        return $this->isLink() || $this->hasLinkInSubmenu();
    }

    /**
     * Indica se o menu é um link.
     *
     * @return bool
     */
    public function isLink()
    {
        return boolval($this->link);
    }

    /**
     * Indica se o menu tem links em seus submenus.
     *
     * @return bool
     */
    public function hasLinkInSubmenu()
    {
        foreach ($this->children as $menu) {

            if ($menu->isLink()) {
                return true;
            }

            if ($menu->hasLinkInSubmenu()) {
                return true;
            }
        }

        return false;
    }

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
     * Retorna o menu raiz.
     *
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
     * Retorna os menus disponíveis para um determinado usuário.
     *
     * @param User $user
     *
     * @return Collection
     */
    public static function user(User $user)
    {
        if ($user->isAdmin()) {
            return static::query()
                ->with('children.children.children.children.children')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();
        }

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
