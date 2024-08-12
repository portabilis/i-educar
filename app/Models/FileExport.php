<?php

namespace App\Models;

use App\Models\Enums\FileExportStatus;
use App\Services\UrlPresigner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, string> $fillable
 * @property int $user_id
 * @property string $url
 * @property string $hash
 * @property string $filename
 * @property int $status_id
 * @property int $size
 * @property Carbon $created_at
 */
class FileExport extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'hash',
        'filename',
        'status_id',
        'size',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $studentFileExport) {
            $studentFileExport->hash = md5((string) time());
            $studentFileExport->status_id = FileExportStatus::WAITING->value;
        });
    }

    protected function presignedUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => (new UrlPresigner)->getPresignedUrl($this->url)
        );
    }

    protected function sizeFormat(): Attribute
    {
        return Attribute::make(
            get: function () {
                $units = [
                    'B',
                    'KB',
                    'MB',
                    'GB',
                    'TB',
                ];
                $bytes = max($this->size, 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);

                return number_format($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
            }
        );
    }

    public function statusIsError(): bool
    {
        return $this->status_id === FileExportStatus::ERROR->value || ($this->statusIsWaiting() && $this->created_at < now()->subMinutes(5));
    }

    public function statusIsWaiting(): bool
    {
        return $this->status_id === FileExportStatus::WAITING->value;
    }

    public function statusIsSuccess(): bool
    {
        return $this->status_id === FileExportStatus::SUCCESS->value;
    }
}
