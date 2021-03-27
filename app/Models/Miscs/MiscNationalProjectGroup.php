<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Национальный проект. Группа
 *
 * @property-read Collection miscNationalProjectSubsectors
 */
final class MiscNationalProjectGroup extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscNationalProjectSubsectors(): BelongsToMany
    {
        return $this->BelongsToMany(MiscNationalProjectSubsector::class);
    }
}
