<?php

namespace iEducar\Support\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ResponsaveisTurmaRepository
{
    public function list(array $params): Collection;
}
