<?php

namespace App\Models\Responsible\Type2;

use Illuminate\Database\Eloquent\Builder;
use App\Models\AppModel;
use App\Models\Traits\NumericalSnakeName;


final class RespApplicationType2 extends AppModel
{
    use NumericalSnakeName;

    public const DOC_COLUMN = 'doc_application_id';

    protected $fillable = [
        'doc_application_id',
        'applicant_access_grout_type_id'
    ];


    public function deleteByMainDocumentId(int $id): void
    {
        $a = $this;
        $test = $this->where(self::DOC_COLUMN, $id)->delete();
        $a = 1;
    }
}
