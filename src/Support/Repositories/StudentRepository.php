<?php

namespace iEducar\Support\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface StudentRepository
{
    public function list(array $params): Collection;
}
