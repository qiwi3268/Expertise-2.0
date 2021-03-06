<?php


namespace App\Http\ApiControllers\Forms\Expertise;

use App\ApiServices\FormBlocks\FormTemplater;
use App\Exceptions\Requests\RequiredRequestParameterDoesNotExist;
use App\Exceptions\Lib\FormHandling\InvalidFormUnitException;
use App\Exceptions\Lib\FormHandling\DisplayManagerException;
use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Exceptions\Lib\FormHandling\RequiredManagerException;
use App\Exceptions\Lib\FormHandling\DisplayBlockException;

use App\Http\Requests\AppRequest;
use App\Lib\FormHandling\Display\DisplayBlock;
use App\Lib\FormHandling\Display\DisplayManager;
use App\Lib\FormHandling\Items\Dates\FutureDate;
use App\Lib\FormHandling\Items\Dates\PastDate;
use App\Lib\FormHandling\Items\Texts\Email;
use App\Lib\FormHandling\Items\Texts\Name;
use App\Lib\FormHandling\Items\Texts\Ogrnip;
use App\Lib\FormHandling\Items\Texts\OrgInn;
use App\Lib\FormHandling\Items\Texts\Kpp;
use App\Lib\FormHandling\Items\Texts\Ogrn;
use App\Lib\FormHandling\Items\Texts\Percent;
use App\Lib\FormHandling\Items\Texts\PersInn;
use App\Lib\FormHandling\Items\Texts\Phone;
use App\Lib\FormHandling\Items\Texts\Snils;
use App\Lib\FormHandling\ItemsAssoc;
use App\Lib\FormHandling\ItemsBag;
use App\Lib\FormHandling\Utils\FileBox;
use App\Lib\Singles\Arrays\ObjectAccess;
use App\Lib\Singles\ComparisonRule;
use App\Lib\Singles\Strings\Prefix;
use App\Lib\FormHandling\Items\Toggle;

use App\Models\Forms\Applicants\FormApplicantType1;
use App\Models\Forms\Applicants\FormApplicantType2;
use App\Models\Forms\Applicants\FormApplicantType3;
use App\Models\Forms\FinancingSources\FormFinancingSourceType1;
use App\Models\Forms\FinancingSources\FormFinancingSourceType2;
use App\Models\Forms\FinancingSources\FormFinancingSourceType3;
use Illuminate\Http\JsonResponse;

use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\Forms\Expertise\ApplicationSaveValidator as SelfValidator;

use App\Repositories\Forms\FormDocApplicationRepository;
use App\Models\Forms\FormDocApplication;

use App\Lib\FormHandling\Items\Miscs\SingleMisc;
use App\Lib\FormHandling\Items\Miscs\MultipleMisc;
use App\Lib\FormHandling\Items\Texts\Text;

use App\Lib\FormHandling\Required\RequiredManager;


/*
 * ???????????????????????? ?????? ???????????????????? ???????????? ???????????????? ?????????????????????????????? ????????????????????
 *
 */
final class ApplicationSaveController extends ApiController
{
    private array $TODO;


    private FormDocApplication $form;


    /**
     * ?????????????????????? ????????????
     *
     * @param SelfValidator $selfValidator
     * @param AppRequest $req
     * @param FormDocApplicationRepository $formRep
     */
    public function __construct(
        private SelfValidator $selfValidator,
        private AppRequest $req,
        FormDocApplicationRepository $formRep
    ) {
        // ?????????????????? ?????????? ?????????????? ????????????????????
        $selfValidator->commonInputParametersValidation();

        $applicationId = $req->applicationId;

        /** @var FormDocApplication $form */
        $form = $formRep->getByDocApplicationId($applicationId);

        if (is_null($form)) {

            $form = FormDocApplication::create([
                'doc_application_id' => $applicationId
            ]);
        }
        $this->form = $form;
    }


    /**
     * ???????????????? ?????????? ???????????????????? ????????????
     *
     * @param AppRequest $req
     * @param FormDocApplicationRepository $formRep
     * @return JsonResponse
     * @throws InvalidFormUnitException
     * @throws RequiredRequestParameterDoesNotExist
     * @throws \App\Exceptions\Lib\FormHandling\DisplayManagerException
     */
    public function save(AppRequest $req, FormDocApplicationRepository $formRep): JsonResponse
    {
        $this->TODO = $req->all();

        $purposeDetailsBag = $this->handlePurposeDetails();

        $tmp = new FileBox($req->fileBox);

        $naturalConditionsBag = $this->handleNaturalConditions();

        // ???????????????????? ?????????????????? ?? ???????????????????? ????????????????????????????
        if ($req->gr('financingSources')['isChanged']) {
            $this->handleFinancingSources();
        }


        // ???????????????????? ?????????????????? ?? ????????????????????
        if ($req->gr('applicant')['isChanged']) {
            $this->handleApplicant();
        }







        $this->form->unfilled_required_item_names = [
            ...$purposeDetailsBag->getNames()
        ];
        $this->form->save();

        return $this->makeSuccessfulResponse();
    }


    /**
     * ???????????????? ?? ???????? ??????????????????
     *
     * @return ItemsBag
     * @throws InvalidFormUnitException
     * @throws RequiredManagerException
     * @throws DisplayBlockException
     */
    public function handlePurposeDetails(): ItemsBag
    {
        $p = new Prefix('???????????????? ?? ???????? ??????????????????. ');

        [$purpose, $subjects, $additional] = arr_unpack($this->req->all(), ['expertisePurpose', 'expertiseSubject', 'additionalInfo']);

        $expertisePurpose = new SingleMisc($purpose, $p('???????? ????????????????????'), 'expertisePurpose');
        $expertiseSubjects = MultipleMisc::createSubMisc($subjects, $p('???????????????? ????????????????????'), 'expertiseSubject', $expertisePurpose);
        $additionalInfo = new Text($additional, $p('???????????????????????????? ????????????????????'), 1000, false);

        $bag = RequiredManager::create()->addFormUnits([$expertisePurpose, $expertiseSubjects, $additionalInfo])
            ->getUnfilledRequiredUnitsBag();

        $this->form->misc_expertise_purpose_id = $expertisePurpose->getValue();
        $expertiseSubjects->handleManyToManyRelation($this->form->miscExpertiseSubjects());
        $this->form->additional_information = $additionalInfo->getValue();

        return $bag;
    }


    /**
     * ???????????????? ?? ?????????????????? ????????????????
     *
     * @return ItemsBag
     * @throws InvalidFormUnitException
     * @throws DisplayManagerException
     * @throws DisplayBlockException
     */
    public function handleNaturalConditions(): ItemsBag
    {
        $p = new Prefix('???????????????? ?? ?????????????????? ????????????????. ');

        [$wind, $snow, $seismic, $climatic, $engineering] =
            arr_unpack($this->req->all(), ['windRegion', 'snowRegion', 'seismicIntensity', 'climaticRegionAndSubRegion', 'engineeringGeologicalCondition']);

        $bag = new ItemsBag([

            $windRegions = new MultipleMisc($wind, $p('???????????????? ??????????'), 'windRegion'),
            $snowRegions = new MultipleMisc($snow, $p('???????????????? ??????????'), 'snowRegion'),
            $seismicIntensities = new MultipleMisc($seismic, $p('???????????????????????? ??????????????????????????'), 'seismicIntensity'),
            $climaticRegionAndSubRegions = new MultipleMisc($climatic, $p('???????????????????????? ?????????? ?? ????????????????'), 'climaticRegionAndSubRegion'),
            $engineeringGeologicalConditions = new MultipleMisc($engineering, $p('?????????????????? ?????????????????? ??????????????????-?????????????????????????? ??????????????'), 'engineeringGeologicalCondition')
        ]);

        $block = DisplayBlock::createFromTransportable('???????????????? ?? ?????????????????? ????????????????', $bag);

        $main = new MultipleMisc($this->req->expertiseSubject, 'tmp', 'expertiseSubject');

        DisplayManager::create($main)->addAndProcess(ComparisonRule::inverseSomethingOtherFromHtmlArray('3'), $block);

        // ----------------------------------------

        $windRegions->handleManyToManyRelation($this->form->miscWindRegions());
        $snowRegions->handleManyToManyRelation($this->form->miscSnowRegions());
        $seismicIntensities->handleManyToManyRelation($this->form->miscSeismicIntensities());
        $climaticRegionAndSubRegions->handleManyToManyRelation($this->form->miscClimaticRegionAndSubRegions());
        $engineeringGeologicalConditions->handleManyToManyRelation($this->form->miscEngineeringGeologicalConditions());

        return RequiredManager::createAndImportFormUnits([$block])->getUnfilledRequiredUnitsBag();
    }


    /**
     * ???????????????? ???? ???????????????????? ????????????????????????????
     *
     * @throws InvalidFormUnitException
     * @throws RequiredRequestParameterDoesNotExist
     * @throws DisplayManagerException
     * @throws RequiredManagerException
     */
    public function handleFinancingSources(): void
    {
        $this->form->financingSourcesType1()->delete();
        $this->form->financingSourcesType2()->delete();
        $this->form->financingSourcesType3()->delete();

        foreach ($this->req->gr('financingSources')['data'] as $data) {

            [$tmp1, $tmp2, $tmp3, $financingSource] = arr_unpack($data, [1, 2, 3, 'financingSource']);

            $obj1 = new ObjectAccess($tmp1);
            $obj2 = new ObjectAccess($tmp2);
            $obj3 = new ObjectAccess($tmp3);

            $p = new Prefix('???????????????? ???? ???????????????????? ????????????????????????????. ');
            $p1 = Prefix::merge('?????????????????? ????????????????. ', $p);
            $p2 = Prefix::merge('???????????????? ????.??????, ?????????????????????????? ?? ?????????? 2 ???????????? 48.2 ???????????????????????????????????? ?????????????? ???????????????????? ??????????????????. ', $p);
            $p3 = Prefix::merge('???????????????? ????.??????, ???? ???????????????? ?? ????????????????, ?????????????????? ?? ?????????? 2 ???????????? 8.3 ???????????????????????????????????? ?????????????? ???????????????????? ??????????????????. ', $p);


            $block1_assoc = new ItemsAssoc([
                'misc_budget_level_id' => new SingleMisc($obj1->budgetLevel, $p1('?????????????? ??????????????'), 'budgetLevel'),
                'percent'              => new Percent($obj1->percent, $p1('?????????????? ????????????????????????????'))
            ]);

            $block2_assoc = new ItemsAssoc([
                'percent' => new Percent($obj2->percent, $p2('?????????????? ????????????????????????????')),
                FormTemplater::organization($obj2, $p2),
                FormTemplater::address($obj2, $p2)
            ]);

            $block3_assoc = new ItemsAssoc([
                'percent' => new Percent($obj3->percent, $p3('?????????????? ????????????????????????????'), false)
            ]);

            $block1 = DisplayBlock::createFromTransportable('1?? ???????????????? ????????????????????????????', $block1_assoc);
            $block2 = DisplayBlock::createFromTransportable('2?? ???????????????? ????????????????????????????', $block2_assoc);
            $block3 = DisplayBlock::createFromTransportable('3?? ???????????????? ????????????????????????????', $block3_assoc);

            $financingSource = new SingleMisc($financingSource, $p('?????? ?????????????????? ????????????????????????????'), 'financingSource');

            $visibleBlock = DisplayManager::create($financingSource)
                ->add(ComparisonRule::equal('1'), $block1)
                ->add(ComparisonRule::equal('2'), $block2)
                ->add(ComparisonRule::equal('3'), $block3)
                ->process(true)->getVisibleDisplayBlock();

            RequiredManager::createAndImportFormUnits([$block1, $block2, $block3])
                ->handleUnitsBagWithErrors();

            // ?????????????????????? ?????????????????????? ??????????
            if ($visibleBlock->is($block1)) {

                $this->form->financingSourcesType1()->save(
                    $block1_assoc->injectIntoModel(new FormFinancingSourceType1)
                );
            } elseif ($visibleBlock->is($block2)) {

                $this->form->financingSourcesType2()->save(
                    $block2_assoc->injectIntoModel(new FormFinancingSourceType2)
                );
            } else { // $block3

                $this->form->financingSourcesType3()->save(
                    $block3_assoc->injectIntoModel(new FormFinancingSourceType3)
                );
            }
        }
    }

    /**
     * @throws DisplayManagerException
     * @throws InvalidFormUnitException
     * @throws RequiredRequestParameterDoesNotExist
     * @throws DisplayBlockException
     * @throws RequiredManagerException
     */
    private function handleApplicant()
    {
        // ???????? ???????????????? ???????? ????????????????
        // ?????????????? ??????

        $data = $this->req->gr('applicant')['data'];

        [$tmp1, $tmp2, $tmp3, $legalSubject] = arr_unpack($data, [1, 2, 3, 'legalSubject']);

        $obj1 = new ObjectAccess($tmp1); // ?????????????????????? ????????
        $obj2 = new ObjectAccess($tmp2); // ???????????????????? ????????
        $obj3 = new ObjectAccess($tmp3); // ???????????????????????????? ??????????????????????????????

        $p = new Prefix('???????????????? ?? ??????????????????. ');
            $p1 = Prefix::merge('?????????????????????? ????????. ', $p);
                $p1_1 = Prefix::merge('???????????????? ?? ?????????????????????? ????????. ', $p1);
                $p1_2 = Prefix::merge('?????????? ???????????????????????? ????????. ', $p1);
                $p1_3 = Prefix::merge('????????????????????????. ', $p1);
                $p1_4 = Prefix::merge('??????????????????. ', $p1);
                $p1_5 = Prefix::merge('???????????????? ?? ????????????????????????. ', $p1);
                $p1_6 = Prefix::merge('????????, ???????????????? ????????????????????????. ', $p1);
            $p2 = Prefix::merge('???????????????????? ????????. ', $p);
                $p2_1 = Prefix::merge('???????????????? ?? ???????????????????? ????????. ', $p2);
                $p2_2 = Prefix::merge('?????????? ?????????????????????? ????????. ', $p2);
                $p2_3 = Prefix::merge('??????????????????, ???????????????????????????? ????????????????????. ', $p2);
                $p2_4 = Prefix::merge('???????????????? ?? ????????????????????????. ', $p2);
                $p2_5 = Prefix::merge('????????, ???????????????? ????????????????????????. ', $p2);
            $p3 = Prefix::merge('???????????????????????????? ??????????????????????????????. ', $p);
                $p3_1 = Prefix::merge('???????????????? ???? ???????????????????????????? ??????????????????????????????. ', $p3);
                $p3_2 = Prefix::merge('?????????? ?????????????????????????????? ??????????????????????????????. ', $p3);
                $p3_3 = Prefix::merge('??????????????????, ???????????????????????????? ????????????????????. ', $p3);
                $p3_4 = Prefix::merge('???????????????? ?? ????????????????????????. ', $p3);
                $p3_5 = Prefix::merge('????????, ???????????????? ????????????????????????. ', $p3);


        /*
        |--------------------------------------------------------------------------
        | ?????????????????????? ????????
        |--------------------------------------------------------------------------
        */
        $block1_assoc = new ItemsAssoc([

            // ???????????????? ?? ?????????????????????? ????????
            FormTemplater::organization($obj1, $p1_1),

            // ?????????? ???????????????????????? ????????
            FormTemplater::address($obj1, $p1_2),

            // ????????????????????????
            FormTemplater::fioWithPost($obj1, $p1_3, 'director'),

            // ??????????????????
            'is_signer_equals_director' => new Toggle($obj1->isSignerEqualsDirector, $p1_4('???????????????????????? ????/??????')),
            $block1_1_assoc = new ItemsAssoc(FormTemplater::fioWithPost($obj1, $p1_4, 'signer')),
            FormTemplater::credentials($obj1, $p1_4),

            $block1_2_assoc = new ItemsAssoc([

                // ???????????????? ?? ????????????????????????
                FormTemplater::legalBasisDetails($obj1, $p1_5),

                // ????????, ???????????????? ????????????????????????
                'is_legal_basis_issuer_equals_director' => new Toggle($obj1->isLegalBasisIssuerEqualsDirector, $p1_6('???????????????????????? ????/??????')),
                $block1_2_1_assoc = new ItemsAssoc(FormTemplater::fio($obj1, $p1_6, 'legalBasisIssuer')),
            ]),
        ]);

        $block1_1 = DisplayBlock::createFromTransportable('??????????????????. ????.????????. ?????? ????????????????????', $block1_1_assoc);
        $block1_2 = DisplayBlock::createFromTransportable('??????????????????. ????.????????. ????????????????????????', $block1_2_assoc);
        $block1_2_1 = DisplayBlock::createFromTransportable('??????????????????. ????.????????. ?????? ????????, ?????????????????? ????????????????????????', $block1_2_1_assoc);

        // ?????? ????????????????????, ???????? ?????????????????? ?? ??????????????????????????
        DisplayManager::create($block1_assoc->is_signer_equals_director)
            ->addAndProcess(ComparisonRule::off(), $block1_1);

        // ???????????????? ?? ???????????????????????? ?? ????????, ???????????????? ????????????????????????
        DisplayManager::create($block1_assoc->misc_legal_basis_id)
            ->addAndProcess(ComparisonRule::equal('1'), $block1_2);

        // ?????? ????????, ?????????????????? ????????????????????????, ???????? ?????????????????? ?? ??????????????????????????
        DisplayManager::create($block1_2_assoc->is_legal_basis_issuer_equals_director)
            ->addAndProcess(ComparisonRule::off(), $block1_2_1);

        /*
        |--------------------------------------------------------------------------
        | ???????????????????? ????????
        |--------------------------------------------------------------------------
        */
        $block2_assoc = new ItemsAssoc([

            // ???????????????? ?? ???????????????????? ????????
            FormTemplater::fio($obj2, $p2_1),
            FormTemplater::passport($obj2, $p2_1),
            'snils' => new Snils($obj2->snils, $p2_1('??????????')),
            FormTemplater::contact($obj2, $p2_1),

            // ?????????? ?????????????????????? ????????
            FormTemplater::address($obj2, $p2_2),

            // ??????????????????, ???????????????????????????? ????????????????????
            FormTemplater::credentials($obj2, $p2_3),

            $block2_1_assoc = new ItemsAssoc([

                // ???????????????? ?? ????????????????????????
                FormTemplater::legalBasisDetails($obj2, $p2_4),

                // ????????, ???????????????? ????????????????????????
                FormTemplater::fio($obj2, $p2_5, 'legalBasisIssuer'),
            ]),
        ]);

        $block2_1 = DisplayBlock::createFromTransportable('??????????????????. ??????.????????. ????????????????????????', $block2_1_assoc);

        // ???????????????? ?? ???????????????????????? ?? ????????, ???????????????? ????????????????????????
        DisplayManager::create($block2_assoc->misc_legal_basis_id)
            ->addAndProcess(ComparisonRule::equal('1'), $block2_1);


        /*
        |--------------------------------------------------------------------------
        | ???????????????????????????? ??????????????????????????????
        |--------------------------------------------------------------------------
        */
        $block3_assoc = new ItemsAssoc([

            // ???????????????? ???? ???????????????????????????? ??????????????????????????????
            FormTemplater::fio($obj3, $p3_1),
            FormTemplater::passport($obj3, $p3_1),
            'snils'    => new Snils($obj3->snils, $p3_1('??????????')),
            'pers_inn' => new PersInn($obj3->persInn, $p3_1('??????')),
            'ogrnip'   => new Ogrnip($obj3->ogrnip, $p3_1('????????????')),
            FormTemplater::contact($obj3, $p3_1),

            // ?????????? ?????????????????????????????? ??????????????????????????????
            FormTemplater::address($obj3, $p3_2),

            // ??????????????????, ???????????????????????????? ????????????????????
            FormTemplater::credentials($obj3, $p3_3),

            $block3_1_assoc = new ItemsAssoc([

                // ???????????????? ?? ????????????????????????
                FormTemplater::legalBasisDetails($obj3, $p3_4),

                // ????????, ???????????????? ????????????????????????
                FormTemplater::fio($obj3, $p3_5, 'legalBasisIssuer'),
            ]),
        ]);

        $block3_1 = DisplayBlock::createFromTransportable('??????????????????. ????. ????????????????????????', $block3_1_assoc);

        // ???????????????? ?? ???????????????????????? ?? ????????, ???????????????? ????????????????????????
        DisplayManager::create($block3_assoc->misc_legal_basis_id)
            ->addAndProcess(ComparisonRule::equal('1'), $block3_1);

        /*
        |--------------------------------------------------------------------------
        | ?????????????????? ???????? ????????????
        |--------------------------------------------------------------------------
        */
        $block1 = DisplayBlock::createFromTransportable('??????????????????. ????.????????', $block1_assoc);
        $block2 = DisplayBlock::createFromTransportable('??????????????????. ??????.????????', $block2_assoc);
        $block3 = DisplayBlock::createFromTransportable('??????????????????. ????', $block3_assoc);

        $legalSubject = new SingleMisc($legalSubject, $p('?????? ??????????????????'), 'legalSubject');

        $visibleBlock = DisplayManager::create($legalSubject)
            ->add(ComparisonRule::equal('1'), $block1)
            ->add(ComparisonRule::equal('2'), $block2)
            ->add(ComparisonRule::equal('3'), $block3)
            ->process(true)->getVisibleDisplayBlock();

        RequiredManager::createAndImportFormUnits([
            $block1, $block1_2, $block1_2_1,
            $block2, $block2_1,
            $block3, $block3_1
        ])->handleUnitsBagWithErrors();


        // ?????????????????????? ?????????????????????? ??????????
        if ($visibleBlock->is($block1)) {

            $applicant = (new FormApplicantType1)->injectionFromUnitsAssocs([
                $block1_assoc, $block1_1_assoc, $block1_2_assoc, $block1_2_1_assoc
            ]);
        } elseif ($visibleBlock->is($block2)) {

            $applicant = (new FormApplicantType2)->injectionFromUnitsAssocs([
                $block2_assoc, $block2_1_assoc
            ]);
        } else { // $block3

            $applicant = (new FormApplicantType3)->injectionFromUnitsAssocs([
                $block3_assoc, $block3_1_assoc
            ]);
        }

        $applicant->save();
        $this->form->applicant()->associate($applicant);
        $a = 1;

    }



}
