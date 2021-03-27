<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Классификатор ОКС вид объекта строительства
 *
 * @property-read Collection miscOksGroups
 */
final class MiscOksTypeOfObject extends MiscModel
{

    /**
     * Аксессор заголовка для отображения в html
     *
     * @return string
     */
    public function getLabelAttribute(): string
    {
        $this->existsAttributes(['name', 'code']);
        return $this->code . ' ' . $this->name;
    }


    /**
     * @return BelongsToMany
     */
    public function miscOksGroups(): BelongsToMany
    {
        return $this->BelongsToMany(MiscOksGroup::class);
    }
}
