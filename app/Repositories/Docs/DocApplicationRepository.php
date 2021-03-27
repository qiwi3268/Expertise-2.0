<?php


namespace App\Repositories\Docs;

use App\Models\Docs\DocApplication;


final class DocApplicationRepository extends DocRepository
{
    protected string $modelClassName = DocApplication::class;
}
