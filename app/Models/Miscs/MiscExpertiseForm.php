<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * Форма экспертизы
 *
 */
final class MiscExpertiseForm extends MiscModel
{

    /**
     * @return BelongsToMany
     */
    public function miscExpertisePurposes(): BelongsToMany
    {
        return $this->belongsToMany(MiscExpertisePurpose::class);
    }
}
