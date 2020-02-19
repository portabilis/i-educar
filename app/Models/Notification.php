<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.notifications';
    
    protected $fillable = [
        'text',
        'link',
        'read_at',
        'user_id'
    ];
}
