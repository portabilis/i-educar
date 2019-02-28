<?php

namespace App\Repositories;

use App\Models\Configuration;
use iEducar\Support\Repositories\ConfigurationRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class MenuRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ConfigurationRepositoryEloquent extends BaseRepository implements ConfigurationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Configuration::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get default configuration
     *
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->all()->first();
    }
}
