<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Builder;
use App\Models\AppModel;


/**
 * Модель справочника
 *
 * Все таблицы справочников имею структуру:
 * - id
 * - name
 * - is_active
 * - sort
 *
 * @method Builder active()
 * @method Builder sorting()
 * @property-read string label
 */
abstract class MiscModel extends AppModel
{
    public $timestamps = false;

    protected $casts = [
        'is_active' => 'bool'
    ];


    /**
     * Аксессор заголовка для отображения в html
     *
     * @return string
     */
    public function getLabelAttribute(): string
    {
        $this->existsAttributes(['name']);
        return $this->name;
    }


    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }


    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSorting(Builder $query): Builder
    {
        return $query->orderBy('sort');
    }
}
