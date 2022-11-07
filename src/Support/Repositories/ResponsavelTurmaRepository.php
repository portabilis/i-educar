<?php
namespace iEducar\Support\Repositories;
use Illuminate\Database\Eloquent\Collection;

interface ResponsavelTurmaRepository{
    
    public function list(array $params): collection;

}