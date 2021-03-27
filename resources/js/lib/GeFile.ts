import { FileBlock } from '../forms/files/FileBlock';
import { mQS, safeDataAttrGetter, safeMapSetter } from './main';
import { Api } from '../api/Api';
import { SignView } from '../modals/signs/SignView';
import { SignCreate } from '../modals/signs/SignCreate';
import { ErrorResponse } from '../api/response-handlers/ApiResponseHandler';
import { ErrorModal } from '../modals/ErrorModal';
import { FilesUtils } from '../forms/files/FilesUtils';
import { LogicError } from './LogicError';
import { GeSign } from './GeSign';
import { FileBlocksManager } from '../forms/files/FileBlocksManager';
import { UploadedFile } from '../api/response-handlers/files/UploadResponseHandler';
import {
   SignValidationResponse,
   ValidationResult
} from '../api/response-handlers/files/InternalSignatureValidationResponseHandler';

document.addEventListener('DOMContentLoaded', () => {
   FilesUtils.initFiles();
});

/**
 * Состояния подписания файла
 */
export enum SignState {
   Checking = 'checking',
   Valid = 'green',
   Invalid = 'red',
   Warning = 'orange',
   NotSigned = 'notSigned',
}

/**
 * Состояния результатов проверки подписей
 */
export enum ValidationState {
   Green = 'green',
   Red = 'red',
   Orange = 'orange'
}

/**
 * Коды ошибок при валидации встроенной подписи
 */
enum InternalValidationState {
   UnsignedFile = 'finisec',
   ExternalSign = 'fiesec',
}

/**
 * Представляет собой загруженный на страницу файл
 */
export class GeFile {

   /**
    * Элемент файла на странице
    */
   private readonly element: HTMLElement;

   /**
    * Блок с кнопками действий файла
    */
   private actions: HTMLElement;

   /**
    * Кнопка для скачивания файла
    */
   private unloadButton: HTMLElement;

   /**
    * Кнопка для удаления
    */
   private deleteButton: HTMLElement;

   /**
    * Кнопка для перехода в окно просмотра или создания подписи,
    * содержит статус подписания файла
    */
   private signButton: HTMLElement;

   /**
    * Поле, к которому относится файл
    */
   private readonly fileBlock: FileBlock;

   /**
    * Путь в файловой системе сервера в виде star path
    */
   private readonly starPath: string;

   /**
    * Названиеи файла
    */
   private readonly originalName: string;

   /**
    * Строка с размером файла
    */
   private readonly humanFileSize: string;

   /**
    * Является ли файл встроенной подписью
    */
   private isInternalSignature = false;

   /**
    * Результат валидации подписей
    */
   private validationState: ValidationState;

   /**
    * Сохранен ли файл в file needs
    */
   private saved: boolean;

   /**
    * Удален ли файл со страницы
    */
   private deleted: boolean;

   /**
    * Подписи к файлу
    */
   private signs: Map<string, GeSign> = new Map<string, GeSign>();

   /**
    * Создает объект загруженного на страницу файла
    *
    * @param fileElement - элемент файла на странице
    * @param fileData - данные о файле
    */
   public constructor(fileElement: HTMLElement, fileData: UploadedFile) {

      this.element = fileElement;

      this.starPath = fileData.starPath;
      this.originalName = fileData.originalName;
      this.humanFileSize = fileData.humanFileSize;

      this.fileBlock = FileBlocksManager.getInstance().getByGeFile(this);
      this.fileBlock.addFile(this);
   }

   /**
    * Добавляет обработчики кнопок действий с файлом
    */
   public handleActionButtons(): void {
      this.actions = mQS('[data-file-actions]', this.element);

      this.unloadButton = mQS('[data-file-unload]', this.actions);
      if (this.unloadButton) {
         this.handleUnloadButton();
      }

      this.deleteButton = mQS('[data-file-delete]', this.actions);
      if (this.deleteButton) {
         this.handleDeleteButton();
      }

      this.handleSignButton();
   }

   /**
    * Обрабатывает действие скачивания файла
    */
   private handleUnloadButton(): void {

      this.unloadButton.addEventListener('click', () => {

      });
   }

   /**
    * Обрабатывает действие удаления
    */
   private handleDeleteButton(): void {
      this.deleteButton.addEventListener('click', () => this.remove());
   }

   /**
    * Определяет существует ли файл на странице
    */
   public isDisplayed(): boolean {
      return this.fileBlock.isDisplayed();
   }

   /**
    * Удаляет файл и подписи к нему со страницы
    */
   public remove(): void {
      // FileNeedsManager.getInstance().putFileToDelete(this);

      // console.log('set is deleted');

      this.setIsDeleted(true);


      // if (this.signStarPaths.size > 0) {
      this.removeSigns();
      // }

      this.fileBlock.removeFile(this);
      this.removeElement();
   }

   /**
    * Удаляет открепленную подпись файла
    */
   public removeSigns(): void {

      if (this.signs.size > 0) {
         this.signs.forEach(sign => sign.setIsDeleted(true));
         FilesUtils.removeValidationResult(this);
         this.setSignState(SignState.NotSigned);
      }


      /*if (this.signStarPaths.size > 0) {

         FileNeedsManager.getInstance().putSignToDelete(this);

         FilesUtils.removeValidationResult(this);
         this.signStarPaths.clear();

         this.setSignState(SignState.NotSigned);
      }*/

   }

   /**
    * Удаляет файл со страницы
    */
   public removeElement(): void {
      this.element.remove();
   }

   /**
    * Обрабатывает кнопку подписания файла
    * Открывает при нажатии в зависимости от типа страницы
    * модальное окно в режиме просмотра или в режиме создания подписи
    */
   private handleSignButton(): void {
      this.signButton = mQS('[data-sign-state]', this.element);
      if (this.signButton) {
         this.signButton.addEventListener('click', () => {
            const signState: string = safeDataAttrGetter('state', this.element);

            if ((this.fileBlock.isReadOnly() || this.isInternalSignature) && signState !== SignState.NotSigned) {
               SignView.getInstance().open(this);
            } else if (!this.fileBlock.isReadOnly() && signState !== SignState.Checking) {
               SignCreate.getInstance().open(this);
            }

         });
      }
   }

   /**
    * Создает элемент и объект файла на странице,
    * добавляет элемент в файловое поле
    *
    * @param fileData - данные файла, полученные с API file_uploader
    * @param filesBlock - файловый блок, в который добавляется файл
    * @returns Объект загруженного файла
    */
   public static create(fileData: UploadedFile, filesBlock: HTMLElement): GeFile {
      const fileItem: HTMLElement = document.createElement('DIV');
      fileItem.classList.add('files__item');
      fileItem.setAttribute('data-file', '');
      filesBlock.appendChild(fileItem);

      const geFile: GeFile = new GeFile(fileItem, fileData);

      geFile.setIsSaved(false);
      geFile.setIsDeleted(false);
      geFile.addInfo();
      geFile.addState();
      geFile.addActions();

      return geFile;
   }

   /**
    * Определяет, сохранен ли файл
    */
   public isSaved(): boolean {
      if (this.saved === undefined) {
         new LogicError('Файл не проинициализирован');
      }

      return this.saved;
   }

   /**
    * Устанавливает флаг сохранения
    *
    * @param saved - сохранен ли файл
    */
   public setIsSaved(saved: boolean): void {
      this.saved = saved;
   }

   /**
    * Возвращает элемент файла
    */
   public getElement(): HTMLElement {
      return this.element;
   }

   /**
    * Возвращает наименование файла
    */
   public getName(): string {
      return this.originalName;
   }

   /**
    * Возвращает строку с размером файла
    */
   public getSizeString(): string {
      return this.humanFileSize;
   }

   /**
    * Возвращает объект родительского файлового блока
    */
   public getFileBlock(): FileBlock {
      return this.fileBlock;
   }

   /**
    * Возвращает star path файла
    */
   public getStarPath(): string {
      return this.starPath;
   }

   /**
    * Устанавливает флаг удаления файла
    *
    * @param deleted - удален ли файл
    */
   public setIsDeleted(deleted: boolean): void {
      this.deleted = deleted;
   }

   /**
    * Определяет, удален ли файл
    */
   public isDeleted(): boolean {
      if (this.deleted === undefined) {
         new LogicError('Файл не проинициализирован');
      }

      return this.deleted;
   }

   /**
    * Определяет существует ли файл на странице
    */
   public isExistsOnPage(): boolean {
      return document.body.contains(this.element);
   }

   /**
    * Проверяет подписи файла и отображает статус подписания
    */
   public handleInternalSigns(): void {

      Api.checkFile(this.starPath)
         .then(() => {
            return Api.internalSignatureValidate(this.starPath)
         })
         .then((verifyResponse: SignValidationResponse) => {

            FilesUtils.setValidationResult(verifyResponse.validationResult, this);
            this.isInternalSignature = true;
            this.setParentFieldSignState();

         })
         .catch((error: ErrorResponse) => {
            this.setSignState(SignState.NotSigned);
            this.handleInvalidInternalSignValidationResult(error);
         });
   }

   /**
    * Обрабатывает ошибку при валидации встроенной подписи
    *
    * @param error - объект ошибки с апи
    */
   private handleInvalidInternalSignValidationResult(error: ErrorResponse): void {

      switch (error.code) {
         case InternalValidationState.UnsignedFile:
            // Файл без подписи
            break;
         case InternalValidationState.ExternalSign:
            ErrorModal.open('Ошибка при загрузке файла', error.message);
            this.remove();
            break;
         default:
            this.remove();
            break;
      }

   }

   /**
    * Добавляет имя, размер и иконку файла для отображения на странице
    */
   private addInfo(): void {
      const fileInfo: HTMLElement = document.createElement('DIV');
      fileInfo.classList.add('files__info');
      this.element.appendChild(fileInfo);

      const fileIcon: HTMLElement = document.createElement('I');
      fileIcon.classList.add('files__icon', 'fas', GeFile.getFileIconClass(this.originalName));
      fileInfo.appendChild(fileIcon);

      const fileDescription: HTMLElement = document.createElement('DIV');
      fileDescription.classList.add('files__description');
      fileInfo.appendChild(fileDescription);


      const fileName: HTMLElement = document.createElement('DIV');
      fileName.classList.add('files__name');
      fileName.textContent = this.originalName;
      fileDescription.appendChild(fileName);

      const fileSize: HTMLElement = document.createElement('DIV');
      fileSize.classList.add('files__size');
      fileSize.textContent = this.humanFileSize;
      fileDescription.appendChild(fileSize);
   }

   /**
    * Добавляет блок со статусом подписи файла
    */
   private addState(): void {
      const signState: HTMLElement = document.createElement('DIV');
      signState.classList.add('files__state');
      signState.setAttribute('data-sign-state', '');
      this.element.appendChild(signState);
      this.setSignState(SignState.Checking);
      this.spinStateIcon();
   }

   /**
    * Крутит иконку статуса подписи во время проверки
    */
   private spinStateIcon(): void {
      const stateIcon: HTMLElement = mQS('[data-state-icon]', this.element);
      let degrees = 0;

      const spin: NodeJS.Timer = setInterval(() => {
         degrees++;
         stateIcon.style.transform = 'rotate(' + degrees + 'deg)';

         if (this.element.dataset.state !== SignState.Checking) {
            clearInterval(spin);
         }

      }, 5);
   }

   /**
    * Устанавливает статус подписи файла
    *
    * @param state - строковое значение статуса подписи
    */
   public setSignState(state: string): void {
      const fileState: HTMLElement = mQS('[data-sign-state]', this.element);
      fileState.textContent = '';

      const stateIcon: HTMLElement = document.createElement('I');
      stateIcon.classList.add('files__state-icon', 'fas');
      stateIcon.setAttribute('data-state-icon', '');
      fileState.appendChild(stateIcon);

      this.element.dataset.state = state;

      if (fileState.dataset.type !== 'short') {
         const stateText: HTMLElement = document.createElement('SPAN');
         stateText.classList.add('files__state-text');
         fileState.appendChild(stateText);

         switch (state) {
            case SignState.Checking:
               stateIcon.classList.add('fa-spinner');
               stateText.textContent = 'Проверка';
               this.validationState = ValidationState.Red
               break;
            case SignState.Valid:
               stateIcon.classList.add('fa-pen-alt');
               stateText.textContent = 'Подписано';
               this.validationState = ValidationState.Green
               break;
            case SignState.Invalid:
               stateIcon.classList.add('fa-times');
               stateText.textContent = 'Подпись недействительна';
               this.validationState = ValidationState.Red
               break;
            case SignState.Warning:
               stateIcon.classList.add('fa-exclamation');
               stateText.textContent = 'Ошибка сертификата';
               this.validationState = ValidationState.Orange
               break;
            case SignState.NotSigned:
            default:
               stateIcon.classList.add('fa-times');
               stateText.textContent = 'Не подписано';
               this.validationState = ValidationState.Red
               break;
         }
      } else {
         stateIcon.classList.add('fa-pen-alt');
      }

   }

   /**
    * Добавляет файлу блок с кнопками действий
    */
   private addActions(): void {
      this.actions = document.createElement('DIV');
      this.actions.classList.add('files__actions');
      this.actions.setAttribute('data-file-actions', '');
      this.element.appendChild(this.actions);

      this.unloadButton = document.createElement('I');
      this.unloadButton.classList.add('files__action', 'unload', 'fas', 'fa-angle-double-down');
      this.unloadButton.setAttribute('data-file-unload', '');
      this.actions.appendChild(this.unloadButton);
      this.handleUnloadButton();

      this.deleteButton = document.createElement('I');
      this.deleteButton.classList.add('files__action', 'delete', 'fas', 'fa-minus');
      this.deleteButton.setAttribute('data-file-delete', '');
      this.actions.appendChild(this.deleteButton);
      this.handleDeleteButton();
      this.handleSignButton();
   }

   /**
    * Отображает состояние проверки подписи в поле с файлом
    */
   public setParentFieldSignState(): void {
      const validationResult: ValidationResult | null = FilesUtils.getValidationResult(this);

      let signState: string = SignState.NotSigned;

      if (validationResult) {
         signState = validationResult.result;
      }

      this.setSignState(signState);
   }

   /**
    * Возвращает результат валидации подписей
    */
   public getValidationState(): string {
      return this.validationState;
   }

   /**
    * Определяет, содержит ли файл встроенную подпись
    */
   public isInternalSign(): boolean {
      return this.isInternalSignature;
   }

   /**
    * При добавлении подписи, старые удаляются
    *
    * @param signStarPath
    * @param validationResult
    */
   public addExternalSign(signStarPath: string, validationResult: ValidationResult): void {

      // this.removeSigns();

      this.signs.forEach(sign => sign.setIsDeleted(true));
      safeMapSetter(this.signs, signStarPath, new GeSign(signStarPath));


      // this.signStarPaths.add(signStarPath);

      FilesUtils.setValidationResult(validationResult, this);

      // Добавляем статус подписания в поле с файлом
      this.setParentFieldSignState();

      // FileNeedsManager.getInstance().putSignsToSave(this);
   }

   public getParentField(): HTMLElement {
      return this.fileBlock.getField();
   }

   /**
    * Получает строку с размером файла по размеру в байтах
    *
    * @param fileSize - размер файла в байтах
    * @returns Размер файла с единицой измерения
    */
   public static getFileSizeString(fileSize: number): string {
      let size: string;
      const kb: number = fileSize / 1024;

      if (kb > 1024) {
         size = Math.round(kb / 1024) + ' МБ'
      } else {
         size = Math.round(kb) + ' КБ'
      }

      return size;
   }

   /**
    * Возвращает строку с классом иконки файла в зависимости от его типа
    *
    * @param fileName - имя файла
    * @returns Класс иконки файла
    */
   public static getFileIconClass(fileName: string): string {
      let iconClass = 'fa-file-alt';

      if (fileName.includes('.pdf')) {
         iconClass = 'fa-file-pdf';
      } else if (fileName.includes('.docx')) {
         iconClass = 'fa-file-word';
      } else if (fileName.includes('.xlsx')) {
         iconClass = 'fa-file-excel';
      }

      return iconClass;
   }

}
