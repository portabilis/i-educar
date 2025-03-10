<?php

namespace App\Services;

use App\Repositories\AtribRepository;
use Illuminate\Support\Collection;

class AtribService
{
    public function getRecentStudentsData(int $schoolCode): Collection
    {
        $repository = new AtribRepository();

        try {
            return $repository($schoolCode);
        } catch (\Throwable $th) {
            return collect(["error" => $th->getMessage()]);
        }
    }
}
