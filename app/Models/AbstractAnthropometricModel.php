<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\InterpolatesAnthropometricData;

abstract class AbstractAnthropometricModel extends Model
{
    use InterpolatesAnthropometricData;

    /** Common fillable fields for all anthropometric models */
    protected array $commonFillable = [
        'age_months',
        'gender', 
        'l_value',
        'm_value',
        's_value',
        'source',
    ];

    /** Common casts for all anthropometric models */
    protected array $commonCasts = [
        'age_months' => 'integer',
        'l_value' => 'decimal:6',
        'm_value' => 'decimal:6', 
        's_value' => 'decimal:6',
    ];

    /**
     * Get specific fillable fields for each model type
     */
    abstract protected function getSpecificFillable(): array;

    /**
     * Get specific casts for each model type  
     */
    abstract protected function getSpecificCasts(): array;

    /**
     * Initialize fillable and casts arrays
     */
    public function __construct(array $attributes = [])
    {
        $this->fillable = array_merge($this->commonFillable, $this->getSpecificFillable());
        $this->casts = array_merge($this->commonCasts, $this->getSpecificCasts());
        
        parent::__construct($attributes);
    }

    /**
     * Common cache method with specific data formatting
     */
    public static function getAllGroupedForCache(string $source = 'WHO_2007'): array
    {
        return static::getCachedData('grouped_data', $source, function () use ($source) {
            $data = static::where('source', $source)
                ->orderBy('age_months')
                ->get();

            $grouped = [];
            foreach ($data as $item) {
                $lmsData = static::convertToFloatArray($item, ['l_value' => 'L', 'm_value' => 'M', 's_value' => 'S']);
                $specificData = $item->getSpecificDataForCache();
                
                $grouped[$item->age_months][$item->gender] = array_merge($lmsData, $specificData);
            }

            return $grouped;
        });
    }

    /**
     * Get model-specific data for cache
     */
    abstract public function getSpecificDataForCache(): array;
}