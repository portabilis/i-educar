<?php

namespace App\Models;

use App\Models\Enums\FileExportStatus;
use App\Services\UrlPresigner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

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
            $studentFileExport->hash = md5(time());
            $studentFileExport->status_id = FileExportStatus::WAITING;
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
