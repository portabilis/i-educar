<?php

namespace App\Repositories;

use App\Models\LegacyConfiguration;
use iEducar\Support\Repositories\ConfigurationRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class ConfigurationRepositoryEloquent extends BaseRepository implements ConfigurationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LegacyConfiguration::class;
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
     * Get default configuration
     *
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->all()->first();
    }
}
