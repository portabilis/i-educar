<?php

namespace App\Repositories;

use iEducar\Support\Repositories\MenuRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\LegacyMenu;
use Prettus\Repository\Eloquent\BaseRepository;

class MenuRepositoryEloquent extends BaseRepository implements MenuRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LegacyMenu::class;
    }

    /**
     * Boot up the repository, pushing criteria
     *
     * @return void
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
