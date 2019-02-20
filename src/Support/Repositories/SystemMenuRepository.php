<?php

namespace iEducar\Support\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface MenuRepository.
 *
 * @package namespace App\Repositories;
 */
interface SystemMenuRepository extends RepositoryInterface
{
    /**
     * @param array $submenuIdArray
     * @param integer $tutorMenuId
     * @param integer $level
     * @return Collection
     */
    public function getNoParentBySubMenusAndTutorMenu(array $submenuIdArray, $tutorMenuId, $level);

    /**
     * @param array $parents
     * @param array $submenuIdArray
     * @param integer $tutorMenuId
     * @param integer $level
     * @return Collection
     */
    public function getByParentsAndSubMenusAndTutorMenu($parents, array $submenuIdArray, $tutorMenuId, $level);
}
