<?php

declare(strict_types=1);

namespace App\Repositories\Files;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Repositories\Repository;
use App\Models\Files\FileInternalSign;


final class FileInternalSignRepository extends Repository
{
    protected string $modelClassName = FileInternalSign::class;


    /**
     * Возвращает коллекцию встроенных подписей
     *
     * @param array $ids
     * @return Collection
     */
    public function getByInternalSignatureFileIds(array $ids): Collection
    {
        return $this->m()->with(['validationResult' => function (BelongsTo $q) {
            $q->select(
                'id',
                'last_name',
                'first_name',
                'middle_name',
                'signature_result',
                'signature_message',
                'certificate',
                'certificate_result',
                'certificate_message',
            );
        }])->whereIn('internal_signature_file_id', $ids)->get([
            'id',
            'validation_result_id',
            'internal_signature_file_id'
        ]);
    }
}
