<?php

namespace App\Services;

use App\Models\LogUnificationOldData;

class UnificationService
{
    public function storeLogOldData($unificationId, $table, $keys, $oldData)
    {
        $logData = new LogUnificationOldData();
        $logData->unification_id = $unificationId;
        $logData->table = $table;
        $logData->keys = json_encode([$keys]);
        $logData->old_data = json_encode($oldData);
        $logData->save();

        return $logData;
    }
}
