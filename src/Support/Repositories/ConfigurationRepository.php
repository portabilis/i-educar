<?php

namespace iEducar\Support\Repositories;

use App\Models\User;
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
