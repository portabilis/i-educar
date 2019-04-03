<?php

namespace App\Repositories;

use App\Models\LegacySystemMenu;
use iEducar\Support\Repositories\SystemMenuRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class SystemMenuRepositoryEloquent extends BaseRepository implements SystemMenuRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LegacySystemMenu::class;
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

    /**
     * @param array $submenuIdArray
     * @param int   $tutorMenuId
     * @param int   $level
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
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
            })
            ->orderBy('level', 'ASC')
            ->orderBy('tipo_menu', 'ASC')
            ->orderBy('ord_menu', 'ASC')
            ->orderBy('tt_menu', 'ASC');

        return $query->get();
    }

    /**
     * @param array $parents
     * @param array $submenuIdArray
     * @param int   $tutorMenuId
     * @param int   $level
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
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
            ->whereNotIn('cod_menu', $parents)
            ->orderBy('level', 'ASC')
            ->orderBy('tipo_menu', 'ASC')
            ->orderBy('ord_menu', 'ASC')
            ->orderBy('tt_menu', 'ASC');

        return $query->get();
    }
}
