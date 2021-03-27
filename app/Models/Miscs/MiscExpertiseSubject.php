<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Предмет экспертизы
 *
 * @property-read Collection miscExpertisePurposes
 */
final class MiscExpertiseSubject extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscExpertisePurposes(): BelongsToMany
    {
        return $this->belongsToMany(MiscExpertisePurpose::class);
    }
}
