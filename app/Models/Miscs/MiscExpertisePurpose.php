<?php


namespace App\Models\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * Цель обращения
 *
 * @property-read Collection miscExpertiseSubjects
 */
final class MiscExpertisePurpose extends MiscModel
{

    /**
     * Аксессор заголовка для отображения в html
     *
     * @return string
     */
    public function getLabelAttribute(): string
    {
        $this->existsAttributes(['name', 'form']);
        return $this->form . '. ' . $this->name;
    }


    /**
     * @return BelongsToMany
     */
    public function miscExpertiseForms(): BelongsToMany
    {
        return $this->belongsToMany(MiscExpertiseForm::class);
    }



    /**
     * @return BelongsToMany
     */
    public function miscExpertiseSubjects(): BelongsToMany
    {
        return $this->belongsToMany(MiscExpertiseSubject::class);
    }
}
