<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $keyType = 'string';

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'person_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'updated_by');
    }

    protected function formattedNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
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
            },
        );
    }
}
