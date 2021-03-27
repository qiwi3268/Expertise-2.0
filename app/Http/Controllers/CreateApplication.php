<?php


namespace App\Http\Controllers;


use App\Lib\Singles\NodeStructure;
use App\Repositories\StructureDocumentation\StructureDocumentation1Repository;
use App\Services\Application\ApplicationService;

use App\Models\UsersData\User;
use App\Models\ApplicantAccessGroupType;
use App\Lib\Responsible\Responsible;


class CreateApplication extends Controller
{

    /**
     * Конструктор класса
     *
     * @param ApplicationService $applicationService
     */
    public function __construct(
        private ApplicationService $applicationService
    ) {
    }


    public function create()
    {
        $user = User::find(1);

        $application = $user->docApplications()->create();

        $this->applicationService->createDirectory($application->id);
        $tmp = $this->applicationService->createCounter($application->id);

        $user->applicantAccessGroups()->save($application, [
            'applicant_access_group_type_id' => ApplicantAccessGroupType::FULL_ACCESS
        ]);

        /*
        (new Responsible($application->id, doc()->application))
            ->createType2([ApplicantAccessGroupType::FULL_ACCESS]);

*/

        $a = true;

        jst('targetDocumentId', $application->id);

        return view('pages.application.create');
    }
}
