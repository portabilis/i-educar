<?php

namespace iEducar\Support\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ResponsavelRepository
{
    public function list(array $params): Collection;
}
