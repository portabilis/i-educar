<?php
 
namespace App\Repositories;

use App\Models\RegistrationStatus;
use App\Models\Responsavel;
use iEducar\Support\Repositories\ResponsavelRepository;
use Illuminate\Database\Eloquent\Collection;

class ResponsavelRepositoryEloquent implements ResponsavelRepository
{
    public function list(array $params): Collection
    {
        $query = Responsavel::select();

        if ($id = $this->param($params, 'id')) {
            $query->where('id', $id);
        }

       

        return $query->get();
    }

    protected function param(array $params, string $key, $default = null)
    {
        return $params[$key] ?? $default;
    }
}
