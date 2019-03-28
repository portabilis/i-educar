<?php

namespace App\Repositories;

use iEducar\Support\Repositories\UserTypeRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\LegacyUserType;
use Prettus\Repository\Eloquent\BaseRepository;

class UserTypeRepositoryEloquent extends BaseRepository implements UserTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LegacyUserType::class;
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
