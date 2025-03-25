<?php

namespace App\Services;

use App\Repositories\AtribRepository;
use Illuminate\Support\Collection;

class AtribService
{
    public function getRecentStudentsData(int $schoolCode, string $serie): Collection
    {
        $repository = new AtribRepository();

        try {
            return $repository($schoolCode, $serie);
        } catch (\Throwable $th) {
            return collect(["error" => $th->getMessage()]);
        }
    }
}
