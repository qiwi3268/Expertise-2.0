<?php

declare(strict_types=1);

namespace App\Models;


/**
 * Виды групп доступа заявителей к заявлению
 *
 */
final class ApplicantAccessGroupType extends AppModel
{

    public $timestamps = false;

    public const FULL_ACCESS = 1;
    public const SIGNING_FINANCIAL_DOCUMENTS = 2;
    public const WORK_WITH_COMMENTS = 3;
    public const ONLY_VIEW = 4;
}
