<?php

namespace App\Http\Controllers;

use App\ApiServices\FormBlocks\FormTemplater;
use App\ApiServices\Miscs\ItemsStorage;
use App\Http\Requests\AppRequest;
use App\Lib\Assertions\Assert;
use App\Lib\Authentication\AuthenticatedUser;
use App\Lib\Cache\KeyNaming;
use App\Lib\Cache\RepositoryCacher;
use App\Lib\Csp\Certification\Commands\CertificateInfoCommander;
use App\Lib\Csp\Certification\ValueObjects\Certificate;
use App\Lib\Date\DateFormatter;
use App\Lib\Date\DateHelper;
use App\Lib\Filesystem\StarPath;
use App\Lib\FormHandling\Display\Display;
use App\Lib\FormHandling\Display\DisplayManager;
use App\Lib\FormHandling\Files\FileBox;
use App\Lib\FormHandling\FormStorage;
use App\Lib\FormHandling\Items\Dates\FutureDate;
use App\Lib\FormHandling\Items\Dates\PastDate;
use App\Lib\FormHandling\Items\Miscs\MultipleMisc;
use App\Lib\FormHandling\Items\Texts\Name;
use App\Lib\FormHandling\Items\Texts\OrgInn;
use App\Lib\FormHandling\Items\Texts\PersInn;
use App\Lib\FormHandling\Items\Texts\Text;
use App\Lib\FormHandling\Items\Toggle;
use App\Lib\FormHandling\ItemsAssoc;
use App\Lib\FormHandling\Required\RequiredManager;
use App\Lib\FormHandling\Items\EmailItem;
use App\Lib\FormHandling\Items\OrgInnItem;
use App\Lib\FormHandling\Items\IntegerItem;
use App\Lib\FormHandling\Items\Texts\Kpp;
use App\Lib\FormHandling\Items\Miscs\SingleMisc;
use App\Lib\FormHandling\Items\NumericItem;
use App\Lib\FormHandling\Items\Texts\Ogrn;
use App\Lib\FormHandling\Units\Items\Texts\Snils;
use App\Lib\Images\SignatureStampMaker;
use App\Lib\Images\SignatureStamps\StampManager;
use App\Lib\Navigation\Managers\DefaultBlocksManager;
use App\Lib\Navigation\Managers\SelectedBlocksManager;
use App\Lib\Navigation\NavigationEngine;
use App\Lib\PhpOffice\PhpWord\PhpWordFacade;
use App\Lib\Settings\Miscs\DependentMiscsManager;
use App\Lib\Settings\Miscs\SingleMiscsManager;
use App\Lib\Settings\Navigation;
use App\Lib\Settings\SystemDatabaseSync\DocumentTypesDatabaseSync;
use App\Lib\Settings\SystemDatabaseSync\SystemDatabaseSync;
use App\Lib\Cache\CacheArray;
use App\Lib\Singles\Arrays\FakeArray;
use App\Lib\Singles\Arrays\HashArray;
use App\Lib\Singles\Arrays\ObjectAccess;
use App\Lib\Singles\DadataFacade;
use App\Lib\Singles\NodeStructure;
use App\Lib\Singles\PatternLibrary;
use App\Lib\Singles\Roles;
use App\Lib\Singles\Strings\Prefix;
use App\Lib\Singles\TypeOfObjectBridge;
use App\Models\Files\FileInternalSign;
use App\Models\FinancingSources\FinancingSourceType1;
use App\Models\Forms\FormDocApplication;
use App\Models\Miscs\MiscExpertiseSubject;
use App\Models\Miscs\MiscFederalProjectName;
use App\Models\Miscs\MiscModel;
use App\Models\Miscs\MiscMunicipalDistrict;
use App\Models\Miscs\MiscNationalProjectSector;
use App\Models\StructureDocumentation\StructureDocumentationType1;
use App\Repositories\Application\ApplicationCounterRepository;
use App\Repositories\Calendars\CalendarWorkdayRepository;
use App\Repositories\Miscs\MiscRegionCodeRepository;
use App\Repositories\Miscs\MiscRepository;
use App\Repositories\StructureDocumentation\StructureDocumentation1Repository;
use App\Repositories\StructureDocumentation\StructureDocumentationType1Repository;
use App\Repositories\Sys\SysModelClassNameRepository;
use App\Repositories\System\SysDocumentTypeRepository;
use App\Repositories\System\SysFileMappingsRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\CarbonTimeZone;
use Carbon\Doctrine\CarbonImmutableType;
use DateTimeImmutable;
use DateTimeZone;
use Faker\Calculator\Inn;
use Illuminate\Database\Eloquent\Model;
use App\Lib\Csp\Validation\Commands\InternalSignatureCommander;
use App\Lib\Files\Initialization\FileInitializer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Http\FileHelpers;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use App\Lib\Settings\FileMappingsManager;
use App\Lib\Responsible\Responsible;

use App\Models\ApplicantAccessGroupType;
use App\Models\Application\ApplicationCounter;
use App\Models\Docs\DocApplication;
use App\Models\UsersData\User;
use App\Services\Application\ApplicationService;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\Files\File;
use App\Lib\Settings\YamlSettingsInitializer;
use Illuminate\Support\Facades\Validator;

use App\Lib\Settings\DocumentsManager;
use App\Lib\Settings\MiscsManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Lib\Singles\FileUtils;
use App\Lib\Filesystem\StarPathHandler;
use App\Lib\Filesystem\StorageParameters;
use App\Lib\Singles\SettingsChecker;
use App\Exceptions\Api\ClientException;
use App\Exceptions\Api\ServerException;
use App\Exceptions\Api\ClientExceptionWithoutReport;
use App\Exceptions\Api\ExceptionContext;
use App\Lib\Formats\ApiResponse\ApiResponseFormatter;
use App\Rules\Files\FileServerUploadRule;
use App\Rules\Files\UploadedFileRule;
use App\Lib\Csp\AlgorithmsManager;


use Intervention\Image\AbstractFont;
use LogicException;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Process\Process;
use App\Lib\Stream\StreamPartsMaker;

use App\Lib\Csp\Validation\SignatureValidator;
use App\Lib\Settings\SystemDatabaseSync\FileMappingsDatabaseSync;
use App\Lib\Settings\SystemDatabaseSync\FilesystemDisksDatabaseSync;
use App\Repositories\Files\FileRepository;
use App\Lib\Filesystem\SimpleStorageParameters;
use App\Models\PeopleName;
use App\Lib\Csp\MessageParser;
use App\Lib\Csp\Validation\ValidationMessageParser;
use App\Repositories\PeopleNameRepository;
use App\Lib\ValueObjects\Fio;
use App\ApiServices\FileUploading\ValueObjects\UploadedFilesStorage;
use Exception;
use SplEnum;
use App\Models\Files\FileValidationResult;
use App\Lib\Csp\Validation\ValueObjects\Public\Signer;
use App\Lib\Files\SignedFiles;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Lib\Csp\Validation\ValueObjects\Public\ValidationResult;
use Illuminate\Support\Facades\Auth;

use App\Models\Miscs\MiscOksGroup;
use App\Models\Miscs\MiscOksTypeOfObject;
use App\View\Utils\Miscs\DateCollection;
use App\Repositories\Repository;
use App\Models\Miscs\MiscExpertisePurpose;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Lib\Date\DaysDater;
use DateTime;
use App\Lib\Singles\Money;
use App\Lib\Settings\NavigationManager;
use App\Lib\FormHandling\Items\PercentItem;
use Throwable;
use Whoops\Util\Misc;
use App\Lib\Singles\ComparisonRule;
use App\Lib\FormHandling\Display\DisplayBlock;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Models\Utils\RelationHandler;
use App\Lib\FormHandling\Units\Utils\DependentMiscRelation;
use App\Lib\Singles\Strings\Sprintf;
use App\Lib\FormHandling\ItemsBag;
use App\Lib\Singles\PriorityTree;
use function Symfony\Component\Translation\t;
use App\Models\Miscs\MiscRegionCode;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Illuminate\Contracts\View\View;
use App\Mixins\CarbonImmutableMixin;


class Test extends Controller
{
    public function __construct(private AppRequest $req)
    {}

    /**
     * @param Request $req
     * @return string
     * @throws Exception
     */
    public function test(Request $req)
    {


        $now = CarbonImmutable::now();


        $now2 = $now->toAppTimeZone();

        $c1 = $now->toDateTimeString('microsecond');
        $c2 = $now->toDateString();
        $c3 = $now->toDayDateTimeString();

        $a1 = CarbonImmutable::shiftBusinessDays(-1);

        $now = $now->subDay();
        $now = $now->subDay();















        $roles = new Roles(['ZAM', 'REPORT']);

        $input = $this->req->unpackInput(['b', 'v']);

        $manager = ($input === false)
            ? new DefaultBlocksManager($roles)
            : new SelectedBlocksManager($roles, ...$input);


        $blocks = $manager->getBlocks();


        //foreach ($blocks as $block)


        //return '';
        throw new Exception();
    }





    private function filesStatistic()
    {
        $files = File::select()->get(['file_size']);

        $count = $files->count();

        $size = FileUtils::getHumanFileSize($files->sum('file_size'));

    }


    public function test3()
    {
        $a = File::all();

        $b = new SignedFiles($a);
        $b->calculate();

        foreach ($a as $file) {

            $has = $file->hasSigners();

            dump($has);

            if ($has) {
                foreach ($file->getSigners() as $signer) {
                    dump($signer->getFio()->getShortFio());
                }
            }
        }
    }

}

