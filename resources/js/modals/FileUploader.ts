import {
   clearDefaultDropEvents,
   mClosest,
   mGetByID,
   mQS,
} from '../lib/main';
import { ErrorModal } from './ErrorModal';
import { GeFile } from '../lib/GeFile';
import { Api } from '../api/Api';
import { UploadedFile } from '../api/response-handlers/files/UploadResponseHandler';
import { FileChecker } from '../forms/files/FileChecker';
import { FileBlock } from '../forms/files/FileBlock';
import { FileBlocksManager } from '../forms/files/FileBlocksManager';

export const SIG_EXTENSIONS: string[] = ['sig', 'p7z'];

/**
 * Представляет собой файловый загрузчик
 */
export class FileUploader {

   /**
    * Инстанс файлового загрузчика
    */
   private static instance: FileUploader;

   /**
    * Модальное окно файлового загрузчика
    */
   private modal: HTMLElement;

   /**
    * Фон модального окна
    */
   private overlay: HTMLElement;

   /**
    * Инпут, в который загружаются файлы
    */
   private fileInput: HTMLInputElement;

   /**
    * Блок, в который можно перенести файлы
    */
   private dropArea: HTMLElement;

   /**
    * Блок являющийся контейнером для файлов
    */
   private modalBody: HTMLElement;

   /**
    * Блок с заголовком модального окна
    */
   private modalTitle: HTMLElement;

   /**
    * Индикатор процента загрузки файлов
    */
   private progressBar: HTMLElement;

   /**
    * Файловый блок, для которого открыт загрузчик
    */
   private fileBlock: FileBlock;

   /**
    * Доступные расширения для файлового поля
    */
   private allowableExtensions: string[];

   /**
    * Запрещенные символы в названии загружаемых файлов
    */
   private forbiddenSymbols: string[];


   private maxFileSize: number | null;

   /**
    * Флаг, указывающий происходит ли загрузка файлов
    */
   private isUploading = false;

   /**
    * Флаг, указывающий открыт ли файловый загрузчик
    */
   private isOpened = false;

   /**
    * Файловое поле, для которого открывается файловый загрузчик
    */
   private parentField: HTMLElement;

   /**
    * Возвращает инстанс файлового загрузчика
    */
   public static getInstance(): FileUploader {

      if (!this.instance) {
         this.instance = new FileUploader();
      }

      return this.instance;
   }

   /**
    * Создает объект файлового загрузчика
    */
   private constructor() {
      this.initModalElements();
      this.initActions();
   }

   /**
    * Инициализирует элементы модального окна файлового загрузчика
    */
   private initModalElements(): void {
      this.fileInput = mGetByID('fileUploaderInput');

      this.modal = mGetByID('fileModal');
      this.overlay = mGetByID('fileOverlay');

      this.dropArea = mGetByID('filesDropArea');
      this.modalBody = mGetByID('fileUploaderBody');

      this.modalTitle = mGetByID('fileUploaderTitle');
      this.progressBar = mGetByID('fileUploaderProgressBar');

   }

   /**
    * Обрабатывает действия файлового загрузчика
    */
   private initActions(): void {
      clearDefaultDropEvents();
      this.handleDropArea();
      this.handleFileUploadButton();
      this.handleSubmitButton();
      this.handleDeleteButton();

      const closeButton = mQS('[data-modal-close]', this.modal);
      closeButton.addEventListener('click', () => this.closeModal());

      this.overlay.addEventListener('click', () => this.closeModal());
   }

   /**
    * Обрабатывает перенос файлов в файловый загрузчик
    */
   private handleDropArea(): void {
      ['dragenter', 'dragover'].forEach(eventName => {
         this.dropArea.addEventListener(eventName, () => {
            this.dropArea.setAttribute('data-active', 'true');
         });
      })

      ;['dragleave', 'drop'].forEach(eventName => {
         this.dropArea.addEventListener(eventName, () => {
            this.dropArea.setAttribute('data-active', 'false');
         });
      });

      this.dropArea.addEventListener('drop', event => {

         const transfer = event.dataTransfer;
         if (transfer) {
            this.dropFiles(transfer);
         }

      });
   }

   /**
    * Добавляет перенесенные файлы в модальное окно
    *
    * @param transfer - объект, в котором хранятся перенесенные файлы
    */
   private dropFiles(transfer: DataTransfer): void {
      if (
         this.dropArea.hasAttribute('data-multiple')
         || transfer.files.length === 1
      ) {
         this.clearModal();
         const files = transfer.files;
         this.fileInput.files = files;
         this.addFilesToModal(files);
      } else {
         ErrorModal.open('Ошибка при загрузке файлов', 'Загрузить можно только 1 файл');
      }
   }

   /**
    * Очищает модальное окно файлового загрузчика
    */
   private clearModal(): void {
      this.modalBody.textContent = '';
      this.fileInput.value = '';
   }

   /**
    * Добавляет переброшенные или выбранные файлы в модальное окно файлового загрузчика
    *
    * @param files - выбранные или переброшенные файлы
    */
   private addFilesToModal(files: FileList): void {
      for (const fileData of Array.from(files)) {
         this.modalBody.appendChild(FileUploader.createFileModalItem(fileData));
      }
   }

   /**
    * Создает файловый элемент для отображения в модальном окне файлового загрузчика
    *
    * @param fileData - данные о файле
    * @return файловый элемент
    */
   private static createFileModalItem(fileData: File): HTMLElement {
      const fileItem = document.createElement('DIV');
      fileItem.classList.add('file-modal__item');

      const fileIcon = document.createElement('I');
      fileIcon.classList.add('file-modal__icon', 'fas', GeFile.getFileIconClass(fileData.name));

      const fileInfo = document.createElement('DIV');
      fileInfo.classList.add('file-modal__info');

      const fileName = document.createElement('DIV');
      fileName.classList.add('file-modal__name');
      fileName.textContent = fileData.name;

      const fileSize = document.createElement('DIV');
      fileSize.classList.add('file-modal__size');
      fileSize.textContent = GeFile.getFileSizeString(fileData.size);

      fileItem.appendChild(fileIcon);
      fileInfo.appendChild(fileName);
      fileInfo.appendChild(fileSize);
      fileItem.appendChild(fileInfo);

      return fileItem;
   }

   /**
    * Обрабатывает кнопку выбора файла
    */
   private handleFileUploadButton(): void {
      const uploadButton = mGetByID('fileUploaderUpload');
      uploadButton.addEventListener('click', () => {
         if (!this.isUploading && this.isOpened) {
            // Вызываем событие для выбора файла у стандартного инпута
            this.clearModal();
            this.fileInput.click();
         }
      });

      this.fileInput.addEventListener('change', () => {
         const files: FileList | null = this.fileInput.files;
         if (files) {
            this.addFilesToModal(files);
         }
      });
   }

   /**
    * Обрабатывает кнопку загрузки выбранных файлов на сервер
    */
   private handleSubmitButton(): void {
      const submitButton = mGetByID('fileUploaderSubmit');
      submitButton.addEventListener('click', () => {

         if (this.fileInput.files !== null) {

            const isValidFiles: boolean = FileChecker.getInstance().checkFiles(
               Array.from(this.fileInput.files),
               this.allowableExtensions,
               this.forbiddenSymbols,
               this.maxFileSize
            );

            if (isValidFiles) {
               this.sendFiles();
            }
         }
      });
   }

   /**
    * Загружает файлы на сервер
    */
   private sendFiles(): void {

      this.isUploading = true;
      const files = Array.from(this.fileInput.files!);

      const fieldInfo = this.fileBlock.getFieldInfo();

      Api.uploadFiles(fieldInfo, files, this.uploadProgressCallback.bind(this))
         .then((uploadedFiles: UploadedFile[]) => {

            this.putFilesToRow(uploadedFiles);
            this.isUploading = false;
            this.closeModal();

         })
         .catch(() => {
            this.isUploading = false;
            this.closeModal();
         });
   }

   /**
    * Анимирует индикатор степени загрузки файлов
    *
    * @param event - объект, содержащий информацию о состоянии загрузки
    */
   private uploadProgressCallback(event: ProgressEvent): void {
      const downloadPercent = Math.round(100 * event.loaded / event.total);
      this.modalTitle.textContent = `Загрузка ${downloadPercent}%`;
      this.progressBar.style.width = downloadPercent + '%';
   }

   /**
    * Добавляет загруженные файлы в файловое поле
    *
    * @param files - загруженные на сервер файлы
    */
   private putFilesToRow(files: UploadedFile[]): void {

      files.forEach(file => {
         const geFile = GeFile.create(file, this.fileBlock.getFilesContainer());
         geFile.handleInternalSigns();
      });

   }

   /**
    * Закрывает модальное окно файлового загрузчика
    */
   private closeModal(): void {
      if (!this.isUploading) {
         this.modal.setAttribute('data-opened', 'false');
         this.overlay.setAttribute('data-opened', 'false');

         this.isOpened = false;
         this.clearModal();
      }
   }

   /**
    * Обрабатывает кнопку удаления выбранных файлов
    */
   private handleDeleteButton(): void {
      const deleteButton = mGetByID('fileUploaderDelete');
      deleteButton.addEventListener('click', () => {
         if (!this.isUploading) {
            this.clearModal();
         }
      });
   }

   /**
    * Добавляет в файловый загрузчик данные о файловом поле
    *
    * @param select - файловое поле
    */
   private putFieldData(select: HTMLElement): void {
      this.parentField = mClosest('[data-field]', select);
      this.fileBlock = FileBlocksManager.getInstance().getByParentField(this.parentField);

      this.putValidateRules();
   }

   private putValidateRules(): void {
      this.allowableExtensions = FileChecker.getFileFieldAllowableExtensions(this.parentField);
      this.forbiddenSymbols = FileChecker.getFileFieldForbiddenSymbols(this.parentField);
      this.maxFileSize = FileChecker.getFileFieldMaxFileSize(this.parentField);

      if (this.parentField.dataset.multiple !== 'false') {
         this.fileInput.setAttribute('multiple', '');
      } else {
         this.fileInput.removeAttribute('multiple');
      }
   }

   /**
    * Очищает заголовок модального окна файлового загрузчика
    */
   private clearModalTitle(): void {
      this.modalTitle.textContent = 'Выберите или перетащите файлы';
      this.progressBar.style.removeProperty('width');
   }

   /**
    * Открывает файловый загрузчик
    *
    * @param select - файловое поле
    */
   public open(select: HTMLElement): void {
      this.putFieldData(select);
      this.clearModalTitle();
      this.modal.setAttribute('data-opened', 'true');
      this.overlay.setAttribute('data-opened', 'true');
      this.isOpened = true;
   }

}


