<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Название национального проекта
 *
 * @property-read Collection miscFederalProjectNames
 */
final class MiscNationalProjectName extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscFederalProjectNames(): BelongsToMany
    {
        return $this->BelongsToMany(MiscFederalProjectName::class);
    }
}
