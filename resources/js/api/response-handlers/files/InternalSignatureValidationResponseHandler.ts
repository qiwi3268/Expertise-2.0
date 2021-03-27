import { ApiClientErrorCodes, ApiResponseHandler, ErrorResponse } from '../ApiResponseHandler';
import { ErrorModal } from '../../../modals/ErrorModal';

export type SignValidationResponse = {
   validationResult: ValidationResult
}

export type ValidationResult = {
   result: string,
   signers: SignInfo[]
}

/**
 * Описывает результат валидации подписей файла
 */
export type SignInfo = {
   /**
    * ФИО подписанта
    */
   fio: string,

   /**
    * Информация о сертификате
    */
   certificate: CertificateInfo,

   certificateMessage: string
   certificateResult: boolean,

   signatureMessage: string
   signatureResult: boolean,
}

/**
 * Информация о сертификате
 */
export type CertificateInfo = {
   /**
    * Серийный номер
    */
   serial: string,
   /**
    * Издатель
    */
   issuer: string,
   /**
    * Владелец
    */
   subject: string,

   /**
    * Диапазон дат, в котором сертификат действителен
    */
   validRange: string;
}


export class InternalSignatureValidationResponseHandler extends ApiResponseHandler<SignValidationResponse> {

   /**
    * Обрабатывает клиентскую ошибку при валидации встроенной подписи
    */
   protected clientInvalidArgumentError(): ErrorResponse {

      if (this.errorResponse.code === ApiClientErrorCodes.FileIsIncorrect) {
         console.error(this.errorResponse);

         ErrorModal.open(
            'Ошибка при валидации встроенной подписи',
            this.errorResponse.message
         );
      }

      return this.errorResponse;

   }

   protected getHandledErrorCodes(): string[] {
      return [
         ApiClientErrorCodes.FileIsNotInternalSign,
         ApiClientErrorCodes.FileIsExternalSign,
         ApiClientErrorCodes.FileIsIncorrect
      ];
   }

}
