import { AxiosError, AxiosRequestConfig, AxiosResponse } from 'axios';
import { LogicError } from '../../lib/LogicError';
import { ApiError } from '../ApiError';
import { ErrorModal } from '../../modals/ErrorModal';

/**
 * Коды ошибок, которые могут прийти с апи
 */
export enum ApiClientErrorCodes {
   FileIsNotExternalSign = 'finesec',
   FileIsNotInternalSign = 'finisec',
   FileIsExternalSign = 'fiesec',
   FileIsIncorrect = 'fiiec',
   ClientInvalidInput = 'ciiec'
}

/**
 * Описывает объект, полученный в результате успешного запроса
 */
type SuccessResponse<T> = {

   /**
    * Сообщение с результатом запроса
    */
   message: string,

   /**
    * Данные с api
    */
   data: T,

   /**
    * Массив с информационными данными
    */
   meta: unknown[];
}

/**
 * Описывает объект, полученный в результате запроса с ошибкой
 */
export type ErrorResponse = {

   /**
    * Сообщение с результатом запроса
    */
   message: string,

   /**
    * Массив с ошибками
    */
   errors: string[][],

   /**
    * Массив с информационными данными
    */
   meta: unknown[];

   /**
    * Код ошибки
    */
   code: string;
}

/**
 * Описывает параметры гет запроса
 */
export type GetParams = {
   [param: string]: string
}

/**
 * Представляет собой обработчик апи
 */
export abstract class ApiResponseHandler<T = unknown, E = ErrorResponse> {

   /**
    * Ответ, полученный в результате успешного запроса
    */
   protected response: AxiosResponse<SuccessResponse<T>>;

   /**
    * Ошибка, полученная в результате запроса
    */
   protected error: AxiosError<ErrorResponse>;

   /**
    * Данные об ошибке, полученные с сервера
    */
   protected errorResponse: ErrorResponse;

   /**
    * Конфиг для запроса на апи
    */
   protected config: AxiosRequestConfig;

   /**
    * Конфигурирует гет запрос
    *
    * @param params - параметры гет запроса
    * @param url - путь для запроса
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildGetConfig(
      url: string,
      params: GetParams,
      uploadCallback: Function | null = null
   ): ApiResponseHandler<T, E> {

      this.config = {
         method: 'get',
         url: url,
         params: params,
      };

      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      return this;
   }

   /**
    * Конфигурирует пост запрос с форм датой
    *
    * @param url - путь для запроса
    * @param formData - данные для отправки
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildPostConfig(
      url: string,
      formData: FormData,
      uploadCallback: Function | null = null
   ): ApiResponseHandler<T, E> {

      this.config = {
         method: 'post',
         url: url,
         data: formData,
         headers: {
            'Content-Type': 'multipart/form-data'
         }
      };

      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      return this;
   }

   /**
    * Конфигурирует пост запрос с json
    *
    * @param params
    * @param url - путь для запроса
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildPostJSONConfig(
      url: string,
      // todo добавить тип
      params: unknown,
      uploadCallback: Function | null = null
   ): ApiResponseHandler<T, E> {
      this.config = {
         method: 'post',
         url: url,
         data: params,
         headers: {
            'Content-Type': 'application/json'
         }
      };


      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      return this;
   }

   /**
    * Отправляет и обрабатывает запрос на api
    *
    * @returns Промис с данными полученными с api
    */
   public send(): Promise<T> {

      return new Promise<T>((resolve, reject) => {

         (window as any).axios.request(this.config)
            .then((response: AxiosResponse) => {

               if (ApiResponseHandler.isValidSuccessResponse(response)) {
                  this.response = response as AxiosResponse<SuccessResponse<T>>;
                  resolve(this.successResponseHandling());
               } else {
                  new LogicError(`Ошибка при выполнении запроса: ${response.status}`);
                  console.error(response);
                  reject();
               }

            })
            .catch((error: AxiosError) => {

               if (ApiResponseHandler.isValidErrorResponse(error)) {
                  this.error = error as AxiosError<ErrorResponse>;
                  this.errorResponse = this.error.response!.data;

                  if (this.error.response!.status.toString().startsWith('4')) {
                     reject(this.clientFatalError());
                  } else {
                     reject(this.serverFatalError());
                  }

               } else if (error.response) {
                  if (error.response.status === 401) {
                     ErrorModal.open('Ошибка при выполнении запроса', 'Вы не авторизованы')
                  } else {
                     new ApiError(`Непредвиденная ошибка при выполнении запроса: ${error.message}`, error);
                  }
                  reject();
               } else if (error.request) {
                  new ApiError('Ошибка при выполнении запроса: Не получен ответ от сервера', error);
                  reject();
               } else {
                  new ApiError(`Error: ${error.message}`, error);
                  reject();
               }
            });
      });

   }

   /**
    * Проверяет содержит ли успешный ответ с api ожидаемые данные
    *
    * @param response - ответ с api
    */
   private static isValidSuccessResponse(response: AxiosResponse): boolean {
      return (
         response.status === 200
         && response.data.hasOwnProperty('message')
         && response.data.hasOwnProperty('data')
         && response.data.hasOwnProperty('meta')
      );
   }

   /**
    * Проверяет содержит ли объект ошибки с api ожидаемые данные
    *
    * @param error - ответ с api
    */
   private static isValidErrorResponse(error: AxiosError): boolean {
      return (
         !!error.response
         && !!error.response.data
         && error.response.data.hasOwnProperty('message')
         && error.response.data.hasOwnProperty('errors')
         && error.response.data.hasOwnProperty('meta')
         && error.response.data.hasOwnProperty('code')
      );
   }

   /**
    * Обработка успешного результата запроса по умолчанию
    *
    * @return данные полученные с api
    */
   protected successResponseHandling(): T {
      return this.response.data.data;
   }

   /**
    * Обработка клиентской ошибки запроса по умолчанию
    */
   protected clientFatalError(): E | ErrorResponse {
      const errorCode: string = this.error.response!.data.code;

      if (
         this.error.response!.status === 422
         && errorCode !== ''
         && this.getHandledErrorCodes().includes(errorCode)
      ) {
         return this.clientInvalidArgumentError();
      } else {
         return this.defaultError();
      }
   }

   /**
    * Получает массив обрабатываемых ошибок для апи
    */
   protected getHandledErrorCodes(): string[] {
      return [];
   }

   /**
    * Обрабатывает случай, когда входные параметры со стороны Js невалидны.
    * Это может быть связано с ошибкой при входной валидации или ошибкой в логике
    */
   protected clientInvalidArgumentError(): E | ErrorResponse {
      return this.defaultError();
   }

   /**
    * Обработка серверной ошибки запроса по умолчанию
    */
   protected serverFatalError(): E | ErrorResponse {
      return this.defaultError();
   }

   /**
    * Вывод ошибки по умолчанию
    */
   private defaultError(): ErrorResponse {
      new ApiError(this.errorResponse.message + '. Обратитесь к администратору', this.error);
      return this.errorResponse;

   }

}
