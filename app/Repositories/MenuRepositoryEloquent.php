<?php

namespace App\Repositories;

use App\Models\User;
use iEducar\Support\Repositories\MenuRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Menu;
use App\Validators\MenuValidator;

/**
 * Class MenuRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MenuRepositoryEloquent extends BaseRepository implements MenuRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Menu::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
