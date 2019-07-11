<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    const TYPE_STRING = 'string';
    const TYPE_FLOAT = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';

    /**
     * @var array
     */
    protected $fillable = [
        'key', 'value', 'type', 'description',
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

                return (boolean) $value;
        }

        return $value;
    }
}
