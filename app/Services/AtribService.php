<?php

namespace App\Services;

use App\Repositories\AtribRepository;
use Illuminate\Support\Collection;

class AtribService
{
    public function getRecentStudentsData(): Collection
    {
        $repository = new AtribRepository();

        try {
            return $repository();
        } catch (\Throwable $th) {
            return collect(["error" => $th->getMessage()]);
        }
    }
}
