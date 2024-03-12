<?php

namespace App\Services;

use App\Models\Enums\FileExportStatus;
use App\Models\FileExport;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Models\NotificationType;
use Exception;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;
use iEducar\Reports\Contracts\StudentRecordReport;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FileExportService
{
    private string $mainPath;

    private string $destinyMainPath;

    private string $destinyZipFilePath;

    private string $folderStudentsPath;

    private string $folderStudentsName;

    private string $connection;

    private string $zipFilePath;

    private Collection $students;

    private string $tempDisk = 'local';

    private string $compressType = 'zip';

    private int $student_folders_count = 0;

    private bool $issueStudentRecordReport = true;

    public function __construct(
        private FileExport $fileExport,
        private array $args,
        private ?string $disk = null
    ) {
        $this->connection = $fileExport->getConnectionName();

        //temp
        $this->mainPath = $this->getMainPath();
        $this->folderStudentsName = $this->getFolderStudentsName();
        $this->folderStudentsPath = $this->getFolderStudentsPath();
        $this->zipFilePath = $this->getZipFilePath();
        //destiny
        $this->disk = $disk ?? config('filesystems.default');
        $this->destinyMainPath = $this->getDestinyMainPath();
        $this->destinyZipFilePath = $this->getDestinyZipFilePath();
    }

    private function getMainPath(): string
    {
        return "{$this->connection}/{$this->compressType}_temp/{$this->fileExport->hash}/";
    }

    private function getFolderStudentsName(): string
    {
        return $this->fileExport->filename;
    }

    private function getFolderStudentsPath(): string
    {
        return $this->mainPath . $this->folderStudentsName . '/';
    }

    private function getZipFilePath(): string
    {
        return $this->mainPath . $this->folderStudentsName . '.' . $this->compressType;
    }

    private function getDestinyMainPath(): string
    {
        return "{$this->connection}/{$this->compressType}/{$this->fileExport->hash}/";
    }

    public function getDestinyZipFilePath(): string
    {
        return $this->destinyMainPath . $this->folderStudentsName . '.' . $this->compressType;
    }

    /**
     * @throws Exception
     */
    public function execute(): void
    {
        $this->createStudentsFolder();
        $this->compressStudentsFolder();
        $this->copyToFinallyDisk();
        $this->updateExporter();
        $this->notifyUser();
    }

    /**
     * @throws Exception
     */
    private function createStudentsFolder(): void
    {
        $this->deleteMainFolder(); //limpa a pasta principal em caso de falhas anteriores
        $this->students = $this->getStudents();
        foreach ($this->students as $student) {
            $studentPath = $this->folderStudentsPath . $this->getStudentPath($student);
            $createStudentRecordReport = $this->createStudentRecordReport(
                studentPath: $studentPath,
                registration: $student['registration']
            );
            $createStudentFiles = $this->createStudentFiles(
                studentPath: $studentPath,
                files: $student['files']
            );
            //conta somente os alunos que tem arquivo
            if ($createStudentRecordReport || $createStudentFiles) {
                $this->student_folders_count++;
            }
        }
    }

    public function deleteMainFolder(): void
    {
        if ($this->getStorageTemp()->directoryExists($this->mainPath)) {
            $this->getStorageTemp()->deleteDirectory($this->mainPath);
        }
    }

    private function getStorageTemp(): Filesystem
    {
        return Storage::disk($this->tempDisk);
    }

    private function getStudents()
    {
        return LegacyStudent::query()
            ->select([
                'cod_aluno',
                'ref_idpes',
                'url_documento',
                'url_laudo_medico',
            ])
            ->active()
            ->with([
                'person:idpes,nome',
                'picture',
                'registrations' => function ($q) {
                    $q->with(['school:cod_escola,ref_cod_instituicao']);
                    $q->filter([
                        'yearEq' => $this->getarg('year'),
                        'school' => $this->getarg('school'),
                        'course' => $this->getarg('course'),
                        'grade' => $this->getarg('grade'),
                        'registration' => $this->getarg('registration'),
                    ]);
                    $q->active();
                    $q->whereHas('lastEnrollment', function ($q) {
                        $q->whereValid();
                        $q->whereSchoolClass($this->getarg('schoolClass'));
                    });
                },
            ])
            ->whereHas('registrations', function ($q) {
                $q->filter([
                    'yearEq' => $this->getarg('year'),
                    'school' => $this->getarg('school'),
                    'course' => $this->getarg('course'),
                    'grade' => $this->getarg('grade'),
                    'registration' => $this->getarg('registration'),
                ]);
                $q->active();
                $q->whereHas('lastEnrollment', function ($q) {
                    $q->whereValid();
                    $q->whereSchoolClass($this->getarg('schoolClass'));
                });
            })
            ->get()
            ->map(function (LegacyStudent $student) {
                //documentos
                $files = collect(json_decode($student->url_documento, false))
                    ->map(function ($file, $index) {
                        $number = sprintf('%02d', $index + 1);

                        return [
                            'filename' => "Documento-{$number}" . '.' . $this->getExtension($file->url),
                            'url' => $file->url,
                        ];
                    });
                //laudos
                $files = $files->merge(collect(json_decode($student->url_laudo_medico, false))
                    ->map(function ($file, $index) {
                        $number = sprintf('%02d', $index + 1);

                        return [
                            'filename' => "Laudo-{$number}" . '.' . $this->getExtension($file->url),
                            'url' => $file->url,
                        ];
                    }));
                //foto
                if ($student->picture) {
                    $files->push([
                        'filename' => 'Foto.' . $this->getExtension($student->picture->caminho),
                        'url' => $student->picture->caminho,
                    ]);
                }

                return [
                    'id' => $student->getKey(),
                    'name' => $student->person->name,
                    'registration' => $student->registrations->first(),
                    'files' => $files->filter(fn ($file) => !empty($file['url'])),
                ];
            });
    }

    private function getArg(string $key, mixed $default = null)
    {
        return Arr::get($this->args, $key, $default);
    }

    private function getExtension(?string $url)
    {
        if (empty($url)) {
            return null;
        }
        $path_parts = pathinfo(parse_url($url, PHP_URL_PATH));

        return $path_parts['extension'];
    }

    private function getStudentPath(array $student): string
    {
        //remove caracteres especiais e transforma em maiúsculo
        return preg_replace('/[^\w\s]/u', '', mb_strtoupper($student['name'])) . " ({$student['id']})" . '/';
    }

    private function createStudentRecordReport(string $studentPath, LegacyRegistration $registration): bool
    {
        if (!$this->issueStudentRecordReport || !app()->bound(StudentRecordReport::class)) {
            return false;
        }
        $studentRecord = app(StudentRecordReport::class);
        $studentRecord->args = [
            'ano' => $registration->ano,
            'instituicao' => $registration->school->ref_cod_instituicao,
            'escola' => $registration->ref_ref_cod_escola,
            'modelo' => 1,
            'curso' => $registration->ref_cod_curso,
            'serie' => $registration->ref_ref_cod_serie,
            'turma' => $registration->lastEnrollment->ref_cod_turma,
            'situacao' => EnrollmentStatusFilter::ALL,
            'matricula' => $registration->getKey(),
            'database' => $this->connection,
            'termo_declaracao' => config('legacy.report.ficha_do_aluno.termo_declaracao'),
            'SUBREPORT_DIR' => config('legacy.report.source_path'),
            'data_emissao' => 0,
        ];

        //precisa ignorar os erros devido o legado
        $encoded = @$studentRecord->dumps([
            'options' => [
                'encoding' => 'base64',
            ],
        ]);
        $pdfFilePath = $studentPath . 'Ficha_do_aluno.pdf';
        $this->getStorageTemp()->put($pdfFilePath, $encoded);
        if (!$this->getStorageTemp()->fileExists($pdfFilePath)) {
            throw new Exception('Não foi possível criar a ficha do aluno.');
        }

        return true;
    }

    private function createStudentFiles(string $studentPath, Collection $files): bool
    {
        $countFiles = 0;
        foreach ($files as $file) {
            $filePath = $this->getPathFromUrl($file['url']);
            if (Storage::fileExists($filePath)) {
                $stream = Storage::readStream($filePath);
                $filePath = $studentPath . $file['filename'];
                $this->getStorageTemp()->writeStream($filePath, $stream);
                $countFiles++;
            }
        }

        return $countFiles > 0;
    }

    private function getPathFromUrl($url): string
    {
        // Remove o domínio e o esquema da URL
        $path = parse_url($url, PHP_URL_PATH);
        // Remove a barra inicial, se houver
        $path = ltrim($path, '/');
        // Encontrar a posição de "storage/" e pegar a parte após isso
        $positionStorage = strpos($path, 'storage/');
        if ($positionStorage !== false) {
            $path = substr($path, $positionStorage + strlen('storage/'));
        }

        return $path;
    }

    private function compressStudentsFolder(): void
    {
        //se nao tiver documentos, deve-se colocar algum conteúdo para poder criar o zip
        if ($this->student_folders_count === 0) {
            $this->getStorageTemp()->put($this->folderStudentsPath . 'nenhum_documento_exportado.txt', '');
        }
        $zip = new ZipArchive;
        $zipFileFullPath = $this->getStorageTemp()->path($this->zipFilePath);
        if ($zip->open($zipFileFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = $this->getStorageTemp()->allFiles($this->folderStudentsPath);
            foreach ($files as $file) {
                $filePath = $this->getStorageTemp()->path($file);
                $zip->addFile(
                    filepath: $filePath,
                    entryname: $this->getShortPathToCompress($filePath)
                );
            }
            $zip->close();
        }
        if (!$this->getStorageTemp()->fileExists($this->zipFilePath)) {
            throw new Exception('Não foi possível criar o arquivo compactado.');
        }
        $this->deleteStudentsFolder();
    }

    private function getShortPathToCompress(string $fullPath): string
    {
        $nomeArquivo = basename($fullPath);
        $data = explode('/', $fullPath);
        $ultimaPasta = $data[count($data) - 2];

        return $this->getArg('registration') ? $nomeArquivo : $ultimaPasta . '/' . $nomeArquivo;
    }

    public function deleteStudentsFolder(): void
    {
        if ($this->getStorageTemp()->directoryExists($this->folderStudentsPath)) {
            $this->getStorageTemp()->deleteDirectory($this->folderStudentsPath);
        }
    }

    private function copyToFinallyDisk(): void
    {
        $stream = $this->getStorageTemp()->readStream($this->zipFilePath);
        $this->getDestinyStorage()->writeStream($this->destinyZipFilePath, $stream);
        if (!$this->getDestinyStorage()->fileExists($this->destinyZipFilePath)) {
            throw new Exception('Não foi possível copia o arquivo compactado para o disco de destino.');
        }
        $this->deleteMainFolder();
    }

    private function getDestinyStorage(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    private function updateExporter(): void
    {
        $this->fileExport->update([
            'url' => $this->getDestinyStorage()->url($this->destinyZipFilePath),
            'status_id' => FileExportStatus::SUCCESS,
            'size' => $this->getDestinyStorage()->size($this->destinyZipFilePath),
        ]);
    }

    private function notifyUser(): void
    {
        (new NotificationService())->createByUser(
            userId: $this->fileExport->user_id,
            text: $this->getMessage(),
            link: $this->getDestinyStorage()->url($this->destinyZipFilePath),
            type: NotificationType::OTHER
        );
    }

    private function getMessage(): string
    {
        return "Foram exportados os documentos de {$this->student_folders_count} alunos. Clique aqui para fazer download do arquivo {$this->fileExport->filename}.";
    }

    public function failed(): void
    {
        $this->deleteMainFolder();
        $this->fileExport->update([
            'status_id' => FileExportStatus::ERROR,
        ]);
    }

    public function setIssueStudentRecordReport(bool $issueStudentRecordReport): self
    {
        $this->issueStudentRecordReport = $issueStudentRecordReport;

        return $this;
    }
}
