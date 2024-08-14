<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'repeat_on_login',
        'show_confirmation',
        'show_vacancy',
        'created_by_user_id',
    ];

    public function userTypes(): BelongsToMany
    {
        return $this->belongsToMany(LegacyUserType::class, 'announcement_user_types', 'announcement_id', 'user_type_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(LegacyUser::class, 'announcement_users', 'announcement_id', 'user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class);
    }
}
