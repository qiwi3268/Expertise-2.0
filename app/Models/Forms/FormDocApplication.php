<?php


namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use Illuminate\Database\Eloquent\Collection;
use App\Models\AppModel;

use App\Models\Miscs\MiscExpertiseSubject;
use App\Models\Miscs\MiscSnowRegion;
use App\Models\Miscs\MiscWindRegion;
use App\Models\Miscs\MiscSeismicIntensity;
use App\Models\Miscs\MiscClimaticRegionAndSubRegion;
use App\Models\Miscs\MiscEngineeringGeologicalCondition;

use App\Models\Forms\FinancingSources\FormFinancingSourceType1;
use App\Models\Forms\FinancingSources\FormFinancingSourceType2;
use App\Models\Forms\FinancingSources\FormFinancingSourceType3;

use App\Models\Forms\Applicants\FormApplicantType1;
use App\Models\Forms\Applicants\FormApplicantType2;
use App\Models\Forms\Applicants\FormApplicantType3;


/**
 * Форма заявления экспертизы
 *
 * @property-read Collection miscExpertiseSubjects
 * @property-read Collection miscSnowRegions
 * @property-read Collection miscWindRegions
 * @property-read Collection miscSeismicIntensities
 * @property-read Collection miscClimaticRegionAndSubRegions
 * @property-read Collection miscEngineeringGeologicalConditions
 * @property-read Collection financingSourcesType1
 * @property-read Collection financingSourcesType2
 * @property-read Collection financingSourcesType3
 */
final class FormDocApplication extends AppModel
{
    protected $fillable = [
        'doc_application_id'
    ];

    protected $casts = [
        'unfilled_required_item_names' => 'array'
    ];


    /**
     * Полиморфное отношение N к N
     *
     * @return MorphToMany
     */
    public function miscExpertiseSubjects(): MorphToMany
    {
        return $this->morphToMany(
            MiscExpertiseSubject::class,
            'form',
            'form_misc_expertise_subject'
        );
    }


    /**
     * Полиморфное отношение N к N
     *
     * @return MorphToMany
     */
    public function miscSnowRegions(): MorphToMany
    {
        return $this->morphToMany(
            MiscSnowRegion::class,
            'form',
            'form_misc_snow_region'
        );
    }


    /**
     * Полиморфное отношение N к N
     *
     * @return MorphToMany
     */
    public function miscWindRegions(): MorphToMany
    {
        return $this->morphToMany(
            MiscWindRegion::class,
            'form',
            'form_misc_wind_region'
        );
    }


    /**
     * Полиморфное отношение N к N
     *
     * @return MorphToMany
     */
    public function miscSeismicIntensities(): MorphToMany
    {
        return $this->morphToMany(
            MiscSeismicIntensity::class,
            'form',
            'form_misc_seismic_intensity'
        );
    }


    /**
     * Полиморфное отношение N к N
     *
     * @return MorphToMany
     */
    public function miscClimaticRegionAndSubRegions(): MorphToMany
    {
        return $this->morphToMany(
            MiscClimaticRegionAndSubRegion::class,
            'form',
            'form_misc_climatic_region_and_sub_region'
        );
    }


    /**
     * Полиморфное отношение N к N
     *
     * @return MorphToMany
     */
    public function miscEngineeringGeologicalConditions(): MorphToMany
    {
        return $this->morphToMany(
            MiscEngineeringGeologicalCondition::class,
            'form',
            'form_misc_engineering_geological_condition'
        );
    }


    /**
     * Полиморфное отношение 1 к N
     *
     * @return MorphMany
     */
    public function financingSourcesType1(): MorphMany
    {
        return $this->morphMany(
            FormFinancingSourceType1::class,
            'form'
        );
    }


    /**
     * Полиморфное отношение 1 к N
     *
     * @return MorphMany
     */
    public function financingSourcesType2(): MorphMany
    {
        return $this->morphMany(
            FormFinancingSourceType2::class,
            'form'
        );
    }


    /**
     * Полиморфное отношение 1 к N
     *
     * @return MorphMany
     */
    public function financingSourcesType3(): MorphMany
    {
        return $this->morphMany(
            FormFinancingSourceType3::class,
            'form'
        );
    }


    /**
     * Полиморфное отношение 1 к 1
     *
     * @return MorphTo
     */
    public function applicant(): MorphTo
    {
        return $this->morphTo('applicant');
    }

}
