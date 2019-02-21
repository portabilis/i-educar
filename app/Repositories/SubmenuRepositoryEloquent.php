<?php

namespace App\Repositories;

use iEducar\Support\Repositories\SubmenuRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Submenu;
use App\Validators\SubmenuValidator;

/**
 * Class SubmenuRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SubmenuRepositoryEloquent extends BaseRepository implements SubmenuRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Submenu::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
