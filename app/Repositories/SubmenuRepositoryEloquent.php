<?php

namespace App\Repositories;

use iEducar\Support\Repositories\SubmenuRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\LegacySubmenu;
use Prettus\Repository\Eloquent\BaseRepository;

class SubmenuRepositoryEloquent extends BaseRepository implements SubmenuRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LegacySubmenu::class;
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
