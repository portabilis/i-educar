<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Phone
 *
 * @property int $person_id
 * @property int $type_id
 * @property int $area_code
 * @property int $number
 * @property int $created_by
 * @property int $updated_by
 */
class Phone extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'person_id' => 'integer',
        'type_id' => 'integer',
        'area_code' => 'integer',
        'number' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * @return string|null
     */
    public function getFormattedNumberAttribute()
    {
        $areaCode = $this->area_code;
        $number = $this->number;

        if (empty($number)) {
            return null;
        }

        $number = preg_replace('/(\d{4,5})(\d{4})/', '$1-$2', $number);

        if ($areaCode) {
            return "({$areaCode}) {$number}";
        }

        return $number;
    }
}
