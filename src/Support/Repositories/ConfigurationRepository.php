<?php

namespace iEducar\Support\Repositories;

use App\Entities\User;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface MenuRepository.
 *
 * @package namespace App\Repositories;
 */
interface ConfigurationRepository extends RepositoryInterface
{
    public function getConfiguration();
}
