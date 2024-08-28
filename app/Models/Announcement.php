<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function description(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                return strip_tags($value, '<p><b><i><u><strong><em><a><ul><ol><li><br><span><h1><h2><h3><h4><h5><h6><img><table><tr><th><td><link><video><source>');
            },
        );
    }
}
