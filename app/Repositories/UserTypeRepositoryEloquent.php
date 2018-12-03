<?php

namespace App\Repositories;

use iEducar\Support\Repositories\UserTypeRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\UserType;
use App\Validators\UserTypeValidator;

/**
 * Class UserTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTypeRepositoryEloquent extends BaseRepository implements UserTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserType::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
