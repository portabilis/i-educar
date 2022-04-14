<?php

namespace App;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use DateSerializer;

    public const TYPE_STRING = 'string';
    public const TYPE_FLOAT = 'float';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';

    /**
     * @var array
     */
    protected $fillable = [
        'key', 'value', 'type', 'description', 'setting_category_id', 'hint'
    ];

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case self::TYPE_STRING:
                return (string) $value;

            case self::TYPE_INTEGER:
                return (int) $value;

            case self::TYPE_FLOAT:
                return (float) $value;

            case self::TYPE_BOOLEAN:
                if (in_array($value, ['false', 'null'], true)) {
                    return false;
                }

                return (bool) $value;
        }

        return $value;
    }

    public function category()
    {
        return $this->belongsTo(SettingCategory::class, 'setting_category_id');
    }
}
