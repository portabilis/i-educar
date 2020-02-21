<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    const TRANSFERENCIA = 1;
    const OUTROS = 2;

    /**
     * @var string
     */
    protected $table = 'public.notification_type';

    protected $fillable = [
        'name',
    ];
}
