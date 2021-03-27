<?php

declare(strict_types=1);

namespace App\Models\Files;

use App\Exceptions\Lib\Csp\CspInvalidArgumentException;

use App\Models\AppModel;

use App\Lib\ValueObjects\Fio;
use App\Models\Traits\Mutators\HasFio;
use App\Lib\Csp\Validation\ValueObjects\Public\Signer;
use App\Lib\Csp\Certification\ValueObjects\Certificate;
use App\Casts\JsonUnescapedUnicodeCast;


/**
 * Результаты валидации подписей
 *
 * @property Fio fio
 * @property Signer signer
 */
final class FileValidationResult extends AppModel
{
    use HasFio;

    protected $fillable = [
        'signer',
    ];

    protected $casts = [
        'signature_result'   => 'bool',
        'certificate'        => JsonUnescapedUnicodeCast::class,
        'certificate_result' => 'bool',
    ];


    /**
     * Мутатор подписанта
     *
     * @param Signer $signer
     */
    public function setSignerAttribute(Signer $signer): void
    {
        $this->fio = $signer->getFio();
        $this->signature_result    = $signer->getSignatureResult();
        $this->signature_message   = $signer->getSignatureMessage();
        $this->certificate         = $signer->getCertificate()->getArray();
        $this->certificate_result  = $signer->getCertificateResult();
        $this->certificate_message = $signer->getCertificateMessage();
    }


    /**
     * Аксессор подписанта
     *
     * @return Signer
     * @throws CspInvalidArgumentException
     */
    public function getSignerAttribute(): Signer
    {
        $this->existsAttributes([
            'signature_result',
            'signature_message',
            'certificate',
            'certificate_message',
            'certificate_result'
        ]);

        return new Signer(
            $this->fio,
            $this->signature_result,
            $this->signature_message,
            new Certificate($this->certificate),
            $this->certificate_result,
            $this->certificate_message
        );
    }
}
