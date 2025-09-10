<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App\Models\RegistrationStatus;
use Avaliacao_Model_NotaComponenteMediaDataMapper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinalStatusImportService
{
    protected array $expectedColumns = [
        'registration_id' => 'ID da Matrícula',
        'final_status' => 'Situação Final',
        'exit_date' => 'Data de Saída',
    ];

    protected array $requiredColumns = [
        'registration_id',
        'final_status',
        'exit_date',
    ];

    public function getStatus(): array
    {
        return (new RegistrationStatus)->getRegistrationStatus();
    }

    public function getStatusMapping(): array
    {
        $registrationStatus = new RegistrationStatus;
        $statusDescriptions = $registrationStatus->getRegistrationStatus();

        $mapping = [];
        foreach ($statusDescriptions as $code => $description) {
            $mapping[mb_strtolower($description, 'UTF-8')] = $code;
        }

        return $mapping;
    }

    public function getExpectedColumns(): array
    {
        return $this->expectedColumns;
    }

    public function getRequiredColumns(): array
    {
        return $this->requiredColumns;
    }

    public function getRequiredColumnsTranslations(): array
    {
        $translations = [];
        foreach ($this->requiredColumns as $column) {
            $translations[$column] = $this->expectedColumns[$column] ?? $column;
        }

        return $translations;
    }

    public function getStatusRequiringExitDate(): array
    {
        return [
            RegistrationStatus::ABANDONED,
            RegistrationStatus::TRANSFERRED,
            RegistrationStatus::DECEASED,
            RegistrationStatus::RECLASSIFIED,
        ];
    }

    public function analyzeUploadedFile($uploadedFile): array
    {
        $content = file_get_contents($uploadedFile->getRealPath());

        $separators = [',', ';'];
        $bestSeparator = ',';
        $maxColumns = 0;

        foreach ($separators as $separator) {
            $lines = explode("\n", $content);
            if (!empty($lines)) {
                $firstLine = $lines[0];
                $columns = str_getcsv($firstLine, $separator);
                if (count($columns) > $maxColumns) {
                    $maxColumns = count($columns);
                    $bestSeparator = $separator;
                }
            }
        }

        $lines = explode("\n", $content);
        $rows = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $rows[] = str_getcsv($line, $bestSeparator);
            }
        }

        if (empty($rows)) {
            throw new \Exception('Arquivo vazio ou formato inválido.');
        }

        $headers = array_shift($rows);

        $rows = array_filter($rows, function ($row) {
            return !empty(array_filter($row, function ($cell) {
                return !empty(trim($cell));
            }));
        });

        return [
            'total_rows' => count($rows),
            'headers' => $headers,
            'sample_data' => array_slice($rows, 0, 5),
            'file_data' => $rows,
        ];
    }

    public function processImport(array $analysis, array $columnMapping, $user, bool $ignoreApproved = false): array
    {
        $rows = $analysis['file_data'];
        $headers = $analysis['headers'];

        $columnIndexes = [];
        foreach ($columnMapping as $expectedColumn => $selectedHeader) {
            $index = array_search($selectedHeader, $headers);
            if ($index !== false) {
                $columnIndexes[$expectedColumn] = $index;
            }
        }

        $ignored = 0;
        if ($ignoreApproved && isset($columnIndexes['final_status'])) {
            $originalCount = count($rows);
            $rows = array_filter($rows, function ($row) use ($columnIndexes) {
                $value = strtolower(trim($row[$columnIndexes['final_status']] ?? ''));

                return $value !== 'aprovado';
            });
            $rows = array_values($rows);
            $ignored = $originalCount - count($rows);
        }

        $total = count($rows);
        $errors = [];
        $warnings = [];
        $processed = 0;

        $validatedData = [];
        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2;

            $rowData = $this->extractRowData($row, $columnIndexes);
            $registrationId = $rowData['registration_id'];
            $finalStatus = $rowData['final_status'];
            $exitDate = $rowData['exit_date'];

            $registration = null;
            if (is_numeric($registrationId) && (int) $registrationId == $registrationId && (int) $registrationId > 0) {
                $registration = LegacyRegistration::find($registrationId);
            }

            $validationResult = $this->validateRowWithStrictRules(
                $registrationId,
                $finalStatus,
                $exitDate,
                $rowNumber,
                $errors,
                $warnings,
                $registration
            );

            if ($validationResult !== null) {
                $validatedData[] = $validationResult;
            }
        }

        if (!empty($errors)) {
            return [
                'status' => 'failed',
                'total' => $total,
                'processed' => 0,
                'ignored' => $ignored,
                'errors' => $errors,
                'warnings' => $warnings,
            ];
        }

        $batchSize = 500;
        $batches = array_chunk($validatedData, $batchSize);

        foreach ($batches as $batch) {
            DB::beginTransaction();

            try {
                foreach ($batch as $validatedRow) {
                    $this->updateDatabase($validatedRow, $user);
                    $processed++;
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();

                return [
                    'status' => 'failed',
                    'total' => $total,
                    'processed' => $processed,
                    'ignored' => $ignored,
                    'errors' => [
                        ['row' => 0, 'error' => 'Erro crítico no processamento: ' . $e->getMessage()],
                    ],
                    'warnings' => $warnings,
                ];
            }
        }

        return [
            'status' => 'completed',
            'total' => $total,
            'processed' => $processed,
            'ignored' => $ignored,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    public function autoMapColumns(array $headers): array
    {
        $mapping = [
            'registration_id' => -1,
            'final_status' => -1,
            'exit_date' => -1,
        ];

        $normalizedHeaders = array_map(function ($header) {
            return mb_strtolower(trim($header), 'UTF-8');
        }, $headers);

        $patterns = [
            'registration_id' => [
                'id matrícula', 'id matricula', 'código', 'codigo', 'matrícula', 'matricula',
                'id da matrícula', 'id da matricula', 'código da matrícula', 'codigo da matricula',
            ],
            'final_status' => [
                'situação final', 'situacao final', 'situação', 'situacao', 'status final', 'status',
                'situação da matrícula', 'situacao da matricula', 'situação da matricula',
            ],
            'exit_date' => [
                'data de saída', 'data de saida', 'data saída', 'data saida',
                'data de saída da matrícula', 'data de saida da matricula', 'data de saída',
            ],
        ];

        foreach ($patterns as $expectedColumn => $possibleNames) {
            foreach ($normalizedHeaders as $index => $normalizedHeader) {
                if (in_array($normalizedHeader, $possibleNames)) {
                    $mapping[$expectedColumn] = $index;
                    break;
                }
            }
        }

        return $mapping;
    }

    public function validateData(array $data, array $columnMapping): array
    {
        $errors = [];
        $warnings = [];
        $validatedData = [];

        // Pré-carrega todas as matrículas para otimização
        $registrationIds = [];
        foreach ($data as $row) {
            if (isset($row['registration_id'])) {
                $registrationId = trim($row['registration_id'] ?? '');
            } else {
                $registrationId = isset($columnMapping['registration_id']) && $columnMapping['registration_id'] >= 0 ?
                    trim($row[$columnMapping['registration_id']] ?? '') : '';
            }

            if ($registrationId !== '' && is_numeric($registrationId) && (int) $registrationId == $registrationId && (int) $registrationId > 0) {
                $registrationIds[] = (int) $registrationId;
            }
        }
        $registrationIds = array_unique($registrationIds);
        $registrations = LegacyRegistration::query()
            ->with([
                'enrollments' => function ($query) {
                    $query->orderBy('sequencial', 'DESC');
                },
            ])->whereIn('cod_matricula', $registrationIds)
            ->get()
            ->keyBy('cod_matricula');

        foreach ($data as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;

            $rowData = $this->extractRowData($row, $columnMapping);
            $registrationId = $rowData['registration_id'];
            $finalStatus = $rowData['final_status'];
            $exitDate = $rowData['exit_date'];

            $registration = null;
            if (is_numeric($registrationId) && (int) $registrationId == $registrationId && (int) $registrationId > 0) {
                $registration = $registrations->get((int) $registrationId);
            }

            $validationResult = $this->validateRowWithStrictRules(
                $registrationId,
                $finalStatus,
                $exitDate,
                $rowNumber,
                $errors,
                $warnings,
                $registration
            );

            if ($validationResult !== null) {
                $validatedData[] = $validationResult;
            }

            if (!empty($errors)) {
                break;
            }
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'validated_data' => $validatedData,
        ];
    }

    private function extractRowData($row, $columnMapping, $columnIndexes = null): array
    {
        if (isset($row['registration_id'])) {
            // Dados já estão no formato correto (usado pelos testes)
            return [
                'registration_id' => trim($row['registration_id'] ?? ''),
                'final_status' => trim($row['final_status'] ?? ''),
                'exit_date' => trim($row['exit_date'] ?? ''),
            ];
        } else {
            // Dados vindos do CSV (usado em produção)
            $registrationId = isset($columnMapping['registration_id']) && $columnMapping['registration_id'] >= 0 ?
                trim($row[$columnMapping['registration_id']] ?? '') : '';
            $finalStatus = isset($columnMapping['final_status']) && $columnMapping['final_status'] >= 0 ?
                trim($row[$columnMapping['final_status']] ?? '') : '';
            $exitDate = isset($columnMapping['exit_date']) && $columnMapping['exit_date'] >= 0 ?
                trim($row[$columnMapping['exit_date']] ?? '') : '';

            return [
                'registration_id' => $registrationId,
                'final_status' => $finalStatus,
                'exit_date' => $exitDate,
            ];
        }
    }

    private function validateRowWithStrictRules(string $registrationId, string $finalStatus, string $exitDate, int $rowNumber, array &$errors, array &$warnings, $registration = null): ?array
    {
        try {
            // Validação do ID da matrícula
            $idValidation = $this->isValidRegistrationId($registrationId);
            if ($idValidation !== true) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => $idValidation,
                ];

                return null;
            }

            // Validação da situação final
            if (empty($finalStatus)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => 'Situação final é obrigatória',
                ];

                return null;
            }

            $normalizedFinalStatus = strtolower(trim($finalStatus));
            $statusCode = $this->getStatusMapping()[$normalizedFinalStatus] ?? null;

            if ($statusCode === null) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => "Matrícula {$registrationId} com situação final inválida: '{$finalStatus}'",
                ];

                return null;
            }

            // Validação da data de saída
            $exitDateValidation = $this->validateExitDate($exitDate, $registrationId, $rowNumber, $statusCode, $finalStatus);
            if ($exitDateValidation !== true) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => $exitDateValidation,
                ];

                return null;
            }
            $processedExitDate = !empty($exitDate) ? Carbon::createFromFormat('d/m/Y', $exitDate)->format('Y-m-d') : null;

            // Validação da matrícula
            if (!$registration) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => "Matrícula não encontrada: {$registrationId}",
                ];

                return null;
            }

            // Warning para matrícula inativa
            if (!$registration->ativo) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => "Matrícula {$registrationId} está inativa, mas será atualizada",
                ];
            }

            $enrollment = null;
            if (in_array($statusCode, $this->getStatusRequiringExitDate())) {
                $enrollments = $registration->enrollments;

                // Verifica múltiplas enturmações ativas
                $activeCount = $enrollments->where('ativo', 1)->count();
                if ($activeCount > 1) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => "Matrícula {$registrationId} possui {$activeCount} enturmações ativas. Não é possível alterar a situação final para '{$finalStatus}' enquanto houver mais de uma enturmação ativa.",
                    ];

                    return null;
                }

                // Verifica se existe pelo menos uma enturmação
                if ($enrollments->count() === 0) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => "Matrícula {$registrationId} com situação '{$finalStatus}' não possui enturmação. É necessário ter uma enturmação para atualizar a situação.",
                    ];

                    return null;
                }

                // Pega a enturmação mais recente
                $latestEnrollment = $enrollments->first();

                // Valida se a enturmação pode ser atualizada
                if (!$this->canUpdateEnrollment($latestEnrollment, $statusCode)) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => "Matrícula {$registrationId} com situação '{$finalStatus}' possui enturmação inativa com status incompatível. Não é possível atualizar.",
                    ];

                    return null;
                }

                $enrollment = $latestEnrollment;
            }

            return [
                'row_number' => $rowNumber,
                'registration_id' => $registrationId,
                'registration' => $registration,
                'normalized_final_status' => $normalizedFinalStatus,
                'status_code' => $statusCode,
                'processed_exit_date' => $processedExitDate,
                'enrollment' => $enrollment,
            ];

        } catch (\Exception $e) {
            $msg = strpos($e->getMessage(), 'sintaxe de entrada é inválida para tipo integer') !== false
                ? "Matrícula ID tem valor inválido para matrícula: '{$registrationId}'"
                : 'Erro ao validar linha: ' . $e->getMessage();
            $errors[] = [
                'row' => $rowNumber,
                'error' => $msg,
            ];

            return null;
        }
    }

    /**
     * Valida o ID da matrícula. Retorna true se válido, ou mensagem de erro se inválido.
     */
    private function isValidRegistrationId($registrationId)
    {
        $trimmed = trim($registrationId);
        if ($trimmed === '' || $trimmed === null) {
            return 'ID da matrícula é obrigatório';
        }
        if (!is_numeric($trimmed) || (int) $trimmed != $trimmed || (int) $trimmed <= 0) {
            return "Matrícula ID tem valor inválido para matrícula: '{$registrationId}'";
        }

        return true;
    }

    /**
     * Valida a data de saída. Retorna true se válida, ou mensagem de erro se inválida.
     */
    private function validateExitDate($exitDate, $registrationId, $rowNumber, $statusCode, $finalStatus)
    {
        if (in_array($statusCode, $this->getStatusRequiringExitDate()) && empty($exitDate)) {
            return "Matrícula {$registrationId} com situação '{$finalStatus}': Data de saída é obrigatória";
        }
        if (!empty($exitDate)) {
            $validator = Validator::make([
                'exit_date' => $exitDate,
            ], [
                'exit_date' => ['date_format:d/m/Y'],
            ]);
            if ($validator->fails()) {
                return "Matrícula {$registrationId} com data de saída inválida: '{$exitDate}'. Use formato DD/MM/AAAA";
            }
        }

        return true;
    }

    private function updateDatabase(array $validatedRow, $user): void
    {
        $registration = $validatedRow['registration'];
        $statusCode = $validatedRow['status_code'];
        $processedExitDate = $validatedRow['processed_exit_date'];
        $enrollment = $validatedRow['enrollment'];

        $registration->aprovado = $statusCode;

        if (in_array($statusCode, $this->getStatusRequiringExitDate()) && $processedExitDate) {
            $registration->data_cancel = $processedExitDate;
        } else {
            $registration->data_cancel = null;
        }

        $registration->updated_at = now();
        $registration->save();

        if (in_array($statusCode, $this->getStatusRequiringExitDate()) && $enrollment) {
            $enrollment->transferido = false;
            $enrollment->remanejado = false;
            $enrollment->reclassificado = false;
            $enrollment->abandono = false;
            $enrollment->falecido = false;

            switch ($statusCode) {
                case RegistrationStatus::TRANSFERRED:
                    $enrollment->transferido = true;
                    break;
                case RegistrationStatus::ABANDONED:
                    $enrollment->abandono = true;
                    break;
                case RegistrationStatus::DECEASED:
                    $enrollment->falecido = true;
                    break;
                case RegistrationStatus::RECLASSIFIED:
                    $enrollment->reclassificado = true;
                    break;
            }

            $enrollment->ativo = 0;
            $enrollment->updated_at = now();

            if ($processedExitDate) {
                $enrollment->data_exclusao = $processedExitDate;
            }

            $enrollment->ref_usuario_exc = $user->id;
            $enrollment->save();

            $this->processDisciplineScoreSituation($registration, $statusCode);
        }
    }

    private function processDisciplineScoreSituation(LegacyRegistration $registration, int $newStatus): void
    {
        $registrationScoreId = $registration->registrationStores()->value('id');

        if ($registrationScoreId) {
            (new Avaliacao_Model_NotaComponenteMediaDataMapper)->updateSituation($registrationScoreId, $newStatus);
        }
    }

    private function canUpdateEnrollment($enrollment, int $newStatusCode): bool
    {
        // Se ativa, sempre pode ser atualizada
        if ($enrollment->ativo) {
            return true;
        }

        // Não pode ser remanejado
        if (!empty($enrollment->remanejado)) {
            return false;
        }

        // Se inativa, só pode se não tiver status ou se for o mesmo status
        $currentStatus = $this->getEnrollmentCurrentStatus($enrollment);

        return $currentStatus === null || $currentStatus === $newStatusCode;
    }

    /**
     * Obtém o status atual da enturmação baseado nos campos booleanos
     */
    private function getEnrollmentCurrentStatus($enrollment): ?int
    {
        if ($enrollment->abandono) {
            return RegistrationStatus::ABANDONED;
        }

        if ($enrollment->transferido) {
            return RegistrationStatus::TRANSFERRED;
        }

        if ($enrollment->falecido) {
            return RegistrationStatus::DECEASED;
        }

        if ($enrollment->reclassificado) {
            return RegistrationStatus::RECLASSIFIED;
        }

        return null;
    }
}
