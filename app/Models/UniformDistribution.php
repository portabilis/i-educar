<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property array<int, string> $fillable
 */
class UniformDistribution extends Model
{
    use SoftDeletes;

    protected $table = 'public.uniform_distributions';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'complete_kit' => 'boolean',
        'distribution_date' => 'date:d/m/Y',
    ];

    protected $fillable = [
        'student_id',
        'year',
        'distribution_date',
        'complete_kit',
        'coat_pants_qty',
        'shirt_short_qty',
        'shirt_long_qty',
        'socks_qty',
        'shorts_tactel_qty',
        'shorts_coton_qty',
        'sneakers_qty',
        'coat_pants_tm',
        'shirt_short_tm',
        'shirt_long_tm',
        'socks_tm',
        'shorts_tactel_tm',
        'shorts_coton_tm',
        'sneakers_tm',
        'school_id',
        'kids_shirt_qty',
        'kids_shirt_tm',
        'pants_jeans_qty',
        'pants_jeans_tm',
        'skirt_qty',
        'skirt_tm',
        'coat_jacket_qty',
        'coat_jacket_tm',
        'type',
        'pants_fem_qty',
        'pants_fem_tm',
        'pants_mas_qty',
        'pants_mas_tm',
        'shorts_skirt_qty',
        'shorts_skirt_tm',
    ];

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'student_id');
    }

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'school_id');
    }

    protected function distributionDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::createFromFormat('d/m/Y', $value) : null,
        );
    }

    /**
     * Retorna as labels das opções de Uniformes
     *
     * @return array<string, string>
     */
    public function labels(): array
    {
        return [
            'coat_pants' => 'Agasalhos (jaqueta)',
            'shirt_short' => 'Camisetas (manga curta)',
            'shirt_long' => 'Camisetas (manga longa)',
            'socks' => 'Meias',
            'shorts_tactel' => 'Bermuda masculina (tecidos diversos)',
            'shorts_coton' => 'Bermuda feminina (tecidos diversos)',
            'sneakers' => 'Tênis',
            'kids_shirt' => 'Camisetas infantis (sem manga)',
            'pants_jeans' => 'Calça jeans',
            'skirt' => 'Saias',
            'coat_jacket' => 'Agasalhos (calça)',
            'pants_fem' => 'Calça feminina (tecidos diversos)',
            'pants_mas' => 'Calça masculina (tecidos diversos)',
            'shorts_skirt' => 'Shorts saias',
        ];
    }
}
