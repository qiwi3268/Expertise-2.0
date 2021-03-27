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
 * Предназначен для сохранения анкеты процесса государственной экспертизы
 *
 */
final class ApplicationSaveController extends ApiController
{
    private array $TODO;


    private FormDocApplication $form;


    /**
     * Конструктор класса
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
        // Валидация общих входных параметров
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
     * Основной метод сохранения анкеты
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

        // Существуют изменения в источниках финансирования
        if ($req->gr('financingSources')['isChanged']) {
            $this->handleFinancingSources();
        }


        // Существуют изменения в подписанте
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
     * Сведения о цели обращения
     *
     * @return ItemsBag
     * @throws InvalidFormUnitException
     * @throws RequiredManagerException
     * @throws DisplayBlockException
     */
    public function handlePurposeDetails(): ItemsBag
    {
        $p = new Prefix('Сведения о цели обращения. ');

        [$purpose, $subjects, $additional] = arr_unpack($this->req->all(), ['expertisePurpose', 'expertiseSubject', 'additionalInfo']);

        $expertisePurpose = new SingleMisc($purpose, $p('Цель экспертизы'), 'expertisePurpose');
        $expertiseSubjects = MultipleMisc::createSubMisc($subjects, $p('Предметы экспертизы'), 'expertiseSubject', $expertisePurpose);
        $additionalInfo = new Text($additional, $p('Дополнительная информация'), 1000, false);

        $bag = RequiredManager::create()->addFormUnits([$expertisePurpose, $expertiseSubjects, $additionalInfo])
            ->getUnfilledRequiredUnitsBag();

        $this->form->misc_expertise_purpose_id = $expertisePurpose->getValue();
        $expertiseSubjects->handleManyToManyRelation($this->form->miscExpertiseSubjects());
        $this->form->additional_information = $additionalInfo->getValue();

        return $bag;
    }


    /**
     * Сведения о природных условиях
     *
     * @return ItemsBag
     * @throws InvalidFormUnitException
     * @throws DisplayManagerException
     * @throws DisplayBlockException
     */
    public function handleNaturalConditions(): ItemsBag
    {
        $p = new Prefix('Сведения о природных условиях. ');

        [$wind, $snow, $seismic, $climatic, $engineering] =
            arr_unpack($this->req->all(), ['windRegion', 'snowRegion', 'seismicIntensity', 'climaticRegionAndSubRegion', 'engineeringGeologicalCondition']);

        $bag = new ItemsBag([

            $windRegions = new MultipleMisc($wind, $p('Ветровой район'), 'windRegion'),
            $snowRegions = new MultipleMisc($snow, $p('Снеговой район'), 'snowRegion'),
            $seismicIntensities = new MultipleMisc($seismic, $p('Сейсмическая интенсивность'), 'seismicIntensity'),
            $climaticRegionAndSubRegions = new MultipleMisc($climatic, $p('Климатическй район и подрайон'), 'climaticRegionAndSubRegion'),
            $engineeringGeologicalConditions = new MultipleMisc($engineering, $p('Категория сложности инженерно-геологических условий'), 'engineeringGeologicalCondition')
        ]);

        $block = DisplayBlock::createFromTransportable('Сведения о природных условиях', $bag);

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
     * Сведения об источниках финансирования
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

            $p = new Prefix('Сведения об источниках финансирования. ');
            $p1 = Prefix::merge('Бюджетные средства. ', $p);
            $p2 = Prefix::merge('Средства Юр.лиц, перечисленных в части 2 статьи 48.2 Градостроительного кодекса Российской Фередации. ', $p);
            $p3 = Prefix::merge('Средства Юр.лиц, не входящие в перечень, указанный в части 2 статьи 8.3 Градостроительного кодекса Российской Фередации. ', $p);


            $block1_assoc = new ItemsAssoc([
                'misc_budget_level_id' => new SingleMisc($obj1->budgetLevel, $p1('Уровень бюджета'), 'budgetLevel'),
                'percent'              => new Percent($obj1->percent, $p1('Процент финансирования'))
            ]);

            $block2_assoc = new ItemsAssoc([
                'percent' => new Percent($obj2->percent, $p2('Процент финансирования')),
                FormTemplater::organization($obj2, $p2),
                FormTemplater::address($obj2, $p2)
            ]);

            $block3_assoc = new ItemsAssoc([
                'percent' => new Percent($obj3->percent, $p3('Процент финансирования'), false)
            ]);

            $block1 = DisplayBlock::createFromTransportable('1й источник финансирования', $block1_assoc);
            $block2 = DisplayBlock::createFromTransportable('2й источник финансирования', $block2_assoc);
            $block3 = DisplayBlock::createFromTransportable('3й источник финансирования', $block3_assoc);

            $financingSource = new SingleMisc($financingSource, $p('Тип источника финансирования'), 'financingSource');

            $visibleBlock = DisplayManager::create($financingSource)
                ->add(ComparisonRule::equal('1'), $block1)
                ->add(ComparisonRule::equal('2'), $block2)
                ->add(ComparisonRule::equal('3'), $block3)
                ->process(true)->getVisibleDisplayBlock();

            RequiredManager::createAndImportFormUnits([$block1, $block2, $block3])
                ->handleUnitsBagWithErrors();

            // Определение заполенного блока
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
        // Сюда заходить если изменено
        // Удалить всё

        $data = $this->req->gr('applicant')['data'];

        [$tmp1, $tmp2, $tmp3, $legalSubject] = arr_unpack($data, [1, 2, 3, 'legalSubject']);

        $obj1 = new ObjectAccess($tmp1); // Юридическое лицо
        $obj2 = new ObjectAccess($tmp2); // Физическое лицо
        $obj3 = new ObjectAccess($tmp3); // Индивидуальный предприниматель

        $p = new Prefix('Сведения о Заявителе. ');
            $p1 = Prefix::merge('Юридическое лицо. ', $p);
                $p1_1 = Prefix::merge('Сведения о юридическом лице. ', $p1);
                $p1_2 = Prefix::merge('Адрес юридического лица. ', $p1);
                $p1_3 = Prefix::merge('Руководитель. ', $p1);
                $p1_4 = Prefix::merge('Подписант. ', $p1);
                $p1_5 = Prefix::merge('Сведения о доверенности. ', $p1);
                $p1_6 = Prefix::merge('Лицо, выдавшее доверенность. ', $p1);
            $p2 = Prefix::merge('Физическое лицо. ', $p);
                $p2_1 = Prefix::merge('Сведения о физическом лице. ', $p2);
                $p2_2 = Prefix::merge('Адрес физического лица. ', $p2);
                $p2_3 = Prefix::merge('Документы, подтверждающие полномочия. ', $p2);
                $p2_4 = Prefix::merge('Сведения о доверенности. ', $p2);
                $p2_5 = Prefix::merge('Лицо, выдавшее доверенность. ', $p2);
            $p3 = Prefix::merge('Индивидуальный предприниматель. ', $p);
                $p3_1 = Prefix::merge('Сведения об индивидуальном предпринимателе. ', $p3);
                $p3_2 = Prefix::merge('Адрес индивидуального предпринимателя. ', $p3);
                $p3_3 = Prefix::merge('Документы, подтверждающие полномочия. ', $p3);
                $p3_4 = Prefix::merge('Сведения о доверенности. ', $p3);
                $p3_5 = Prefix::merge('Лицо, выдавшее доверенность. ', $p3);


        /*
        |--------------------------------------------------------------------------
        | Юридическое лицо
        |--------------------------------------------------------------------------
        */
        $block1_assoc = new ItemsAssoc([

            // Сведения о юридическом лице
            FormTemplater::organization($obj1, $p1_1),

            // Адрес юридического лица
            FormTemplater::address($obj1, $p1_2),

            // Руководитель
            FormTemplater::fioWithPost($obj1, $p1_3, 'director'),

            // Подписант
            'is_signer_equals_director' => new Toggle($obj1->isSignerEqualsDirector, $p1_4('Руководитель Да/Нет')),
            $block1_1_assoc = new ItemsAssoc(FormTemplater::fioWithPost($obj1, $p1_4, 'signer')),
            FormTemplater::credentials($obj1, $p1_4),

            $block1_2_assoc = new ItemsAssoc([

                // Сведения о доверенности
                FormTemplater::legalBasisDetails($obj1, $p1_5),

                // Лицо, выдавшее доверенность
                'is_legal_basis_issuer_equals_director' => new Toggle($obj1->isLegalBasisIssuerEqualsDirector, $p1_6('Руководитель Да/Нет')),
                $block1_2_1_assoc = new ItemsAssoc(FormTemplater::fio($obj1, $p1_6, 'legalBasisIssuer')),
            ]),
        ]);

        $block1_1 = DisplayBlock::createFromTransportable('Заявитель. Юр.лицо. Фио подписанта', $block1_1_assoc);
        $block1_2 = DisplayBlock::createFromTransportable('Заявитель. Юр.лицо. Доверенность', $block1_2_assoc);
        $block1_2_1 = DisplayBlock::createFromTransportable('Заявитель. Юр.лицо. Фио лица, выдавшего доверенность', $block1_2_1_assoc);

        // Фио подписанта, если совпадает с руководителем
        DisplayManager::create($block1_assoc->is_signer_equals_director)
            ->addAndProcess(ComparisonRule::off(), $block1_1);

        // Сведения о доверенности и лицо, выдавшее доверенность
        DisplayManager::create($block1_assoc->misc_legal_basis_id)
            ->addAndProcess(ComparisonRule::equal('1'), $block1_2);

        // Фио лица, выдавшего доверенность, если совпадает с руководителем
        DisplayManager::create($block1_2_assoc->is_legal_basis_issuer_equals_director)
            ->addAndProcess(ComparisonRule::off(), $block1_2_1);

        /*
        |--------------------------------------------------------------------------
        | Физическое лицо
        |--------------------------------------------------------------------------
        */
        $block2_assoc = new ItemsAssoc([

            // Сведения о физическом лице
            FormTemplater::fio($obj2, $p2_1),
            FormTemplater::passport($obj2, $p2_1),
            'snils' => new Snils($obj2->snils, $p2_1('СНИЛС')),
            FormTemplater::contact($obj2, $p2_1),

            // Адрес физического лица
            FormTemplater::address($obj2, $p2_2),

            // Документы, подтверждающие полномочия
            FormTemplater::credentials($obj2, $p2_3),

            $block2_1_assoc = new ItemsAssoc([

                // Сведения о доверенности
                FormTemplater::legalBasisDetails($obj2, $p2_4),

                // Лицо, выдавшее доверенность
                FormTemplater::fio($obj2, $p2_5, 'legalBasisIssuer'),
            ]),
        ]);

        $block2_1 = DisplayBlock::createFromTransportable('Заявитель. Физ.лицо. Доверенность', $block2_1_assoc);

        // Сведения о доверенности и лицо, выдавшее доверенность
        DisplayManager::create($block2_assoc->misc_legal_basis_id)
            ->addAndProcess(ComparisonRule::equal('1'), $block2_1);


        /*
        |--------------------------------------------------------------------------
        | Индивидуальный предприниматель
        |--------------------------------------------------------------------------
        */
        $block3_assoc = new ItemsAssoc([

            // Сведения об индивидуальном предпринимателе
            FormTemplater::fio($obj3, $p3_1),
            FormTemplater::passport($obj3, $p3_1),
            'snils'    => new Snils($obj3->snils, $p3_1('СНИЛС')),
            'pers_inn' => new PersInn($obj3->persInn, $p3_1('ИНН')),
            'ogrnip'   => new Ogrnip($obj3->ogrnip, $p3_1('ОГРНИП')),
            FormTemplater::contact($obj3, $p3_1),

            // Адрес индивидуального предпринимателя
            FormTemplater::address($obj3, $p3_2),

            // Документы, подтверждающие полномочия
            FormTemplater::credentials($obj3, $p3_3),

            $block3_1_assoc = new ItemsAssoc([

                // Сведения о доверенности
                FormTemplater::legalBasisDetails($obj3, $p3_4),

                // Лицо, выдавшее доверенность
                FormTemplater::fio($obj3, $p3_5, 'legalBasisIssuer'),
            ]),
        ]);

        $block3_1 = DisplayBlock::createFromTransportable('Заявитель. ИП. Доверенность', $block3_1_assoc);

        // Сведения о доверенности и лицо, выдавшее доверенность
        DisplayManager::create($block3_assoc->misc_legal_basis_id)
            ->addAndProcess(ComparisonRule::equal('1'), $block3_1);

        /*
        |--------------------------------------------------------------------------
        | Валидация всех данных
        |--------------------------------------------------------------------------
        */
        $block1 = DisplayBlock::createFromTransportable('Заявитель. Юр.лицо', $block1_assoc);
        $block2 = DisplayBlock::createFromTransportable('Заявитель. Физ.лицо', $block2_assoc);
        $block3 = DisplayBlock::createFromTransportable('Заявитель. ИП', $block3_assoc);

        $legalSubject = new SingleMisc($legalSubject, $p('Вид заявителя'), 'legalSubject');

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


        // Определение заполенного блока
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
