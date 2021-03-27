import { ApiClientErrorCodes, ApiResponseHandler } from '../ApiResponseHandler';
import { ErrorModal } from '../../../modals/ErrorModal';
import { SignValidationResponse } from './InternalSignatureValidationResponseHandler';

export class ExternalSignatureValidationResponseHandler extends ApiResponseHandler<SignValidationResponse, void> {

   /**
    * Обрабатывает клиентскую ошибку при загрузке файлов
    */
   protected clientInvalidArgumentError(): void {
      console.error(this.errorResponse);

      ErrorModal.open(
         'Ошибка при загрузке открепленной подписи',
         this.errorResponse.message
      );

   }

   protected getHandledErrorCodes(): string[] {
      return [ApiClientErrorCodes.FileIsNotExternalSign];
   }
}
