<?php

namespace App\Repositories;

use App\Models\SystemMenu;
use iEducar\Support\Repositories\SystemMenuRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class MenuRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SystemMenuRepositoryEloquent extends BaseRepository implements SystemMenuRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SystemMenu::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getNoParentBySubMenusAndTutorMenu(array $submenuIdArray, $tutorMenuId, $level)
    {
        $model = $this->makeModel();
        $query = $model
            ->select([DB::raw($level . ' as level'), '*'])
            ->where('ref_cod_menu_pai', null)
            ->where('ref_cod_tutormenu', $tutorMenuId)
            ->where(function ($query) use ($submenuIdArray) {
                $query->orWhere('ref_cod_menu_submenu', null)
                    ->orWhereIn('ref_cod_menu_submenu', $submenuIdArray);
            });

        return $query->get();
    }

    public function getByParentsAndSubMenusAndTutorMenu($parents, array $submenuIdArray, $tutorMenuId, $level)
    {
        $model = $this->makeModel();
        $query = $model
            ->select([DB::raw($level . ' as level'), '*'])
            ->whereIn('ref_cod_menu_pai', $parents)
            ->where(function ($query) use ($submenuIdArray) {
                $query->orWhere('ref_cod_menu_submenu', null)
                    ->orWhereIn('ref_cod_menu_submenu', $submenuIdArray);
            })
            ->whereNotIn('cod_menu', $parents);
        return $query->get();
    }
}
