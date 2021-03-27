<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Национальный проект. Подотрасль
 *
 * @property-read Collection miscNationalProjectSectors
 * @property-read Collection miscNationalProjectGroups
 */
final class MiscNationalProjectSubsector extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscNationalProjectSectors(): BelongsToMany
    {
        return $this->BelongsToMany(MiscNationalProjectSector::class);
    }

    /**
     * @return BelongsToMany
     */
    public function miscNationalProjectGroups(): BelongsToMany
    {
        return $this->BelongsToMany(MiscNationalProjectGroup::class);
    }
}
