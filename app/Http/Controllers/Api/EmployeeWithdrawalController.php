<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeWithdrawal;
use Exception;
use Illuminate\Http\JsonResponse;

class EmployeeWithdrawalController extends Controller
{
    public function remove($id): JsonResponse
    {
        try {
            $employeeWithdrawal = EmployeeWithdrawal::query()->findOrFail($id);
            $employeeWithdrawal->update(['data_exclusao' => now(), 'ativo' => 0]);
        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => false,
                    'message' => 'Erro ao atualizar o afastamento do servidor'
                ]
            );
        }

        return response()->json(['success' => true]);
    }
}
