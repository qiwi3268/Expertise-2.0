<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Классификатор ОКС группа
 *
 * @property-read Collection miscOksTypeOfObjects
 */
final class MiscOksGroup extends MiscModel
{


    /**
     * @return BelongsToMany
     */
    public function miscOksTypeOfObjects(): BelongsToMany
    {
        return $this->belongsToMany(MiscOksTypeOfObject::class);
    }
}
