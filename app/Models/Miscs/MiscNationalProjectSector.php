<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Национальный проект. Отрасль
 *
 * @property-read Collection miscNationalProjectSubsectors
 */
final class MiscNationalProjectSector extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscNationalProjectSubsectors(): BelongsToMany
    {
        return $this->BelongsToMany(MiscNationalProjectSubsector::class);
    }
}
