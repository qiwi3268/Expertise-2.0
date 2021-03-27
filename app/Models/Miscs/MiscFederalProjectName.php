<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Название федерального проекта
 *
 * @property-read Collection miscNationalProjectNames
 */
final class MiscFederalProjectName extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscNationalProjectNames(): BelongsToMany
    {
        return $this->BelongsToMany(MiscNationalProjectName::class);
    }
}
