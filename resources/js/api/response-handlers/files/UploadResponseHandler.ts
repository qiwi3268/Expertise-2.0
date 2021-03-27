import { ApiClientErrorCodes, ApiResponseHandler, ErrorResponse } from '../ApiResponseHandler';
import { ErrorModal } from '../../../modals/ErrorModal';

/**
 * Представляет собой данные о загруженном на сервер файле
 */
export type UploadedFile = {

   /**
    * Наименование файла
    */
   originalName: string

   /**
    * id, путь и маппинги файла в виде строки
    */
   starPath: string

   /**
    * Строка с размером для отображения на странице
    */
   humanFileSize: string

   /**
    * Версия файла
    */
   version?: number
}

/**
 * Представляет собой обработчик запроса на api загрузки файлов
 */
export class UploadResponseHandler extends ApiResponseHandler<UploadedFile[], void> {

   /**
    * Обрабатывает клиентскую ошибку при загрузке файлов
    */
   protected clientInvalidArgumentError(): void {

      console.error(this.errorResponse);

      const concatErrors = (accumulator: string[], value: string[]) => accumulator.concat(value);
      const allErrors = this.errorResponse.errors.reduce(concatErrors, []);

      ErrorModal.open(
         'Ошибка при загрузке файла',
         allErrors
      );

   }

   protected getHandledErrorCodes(): string[] {
      return [
         ApiClientErrorCodes.ClientInvalidInput
      ];
   }


}
