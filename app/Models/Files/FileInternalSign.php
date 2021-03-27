<?php


namespace App\Models\Files;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AppModel;


/**
 * Встроенные подписи к файлам
 *
 * @property FileValidationResult validationResult
 */
final class FileInternalSign extends AppModel
{
    public $timestamps = false;

    protected $fillable = [
        'validation_result_id',
        'internal_signature_file_id'
    ];


    /**
     * Отношение 1 к 1
     *
     * @return BelongsTo
     */
    public function validationResult(): BelongsTo
    {
        return $this->belongsTo(FileValidationResult::class);
    }
}
