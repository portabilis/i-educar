<?php

namespace App\Services;

class ValidationDataService
{
    public function verifyQuantityByKey($data, $key, $quantity): bool
    {
        $dataFilter = array_filter($data, fn ($item) => $item[$key] === true);

        return count($dataFilter) > $quantity;
    }

    public function verifyDataContainsDuplicatesByKey($data, $key): bool
    {
        $ids = array_map(fn ($item) => $item[$key], $data);
        $ids = array_unique($ids);

        return count($data) === count($ids);
    }
}
