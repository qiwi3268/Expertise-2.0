import { FileUploader } from '../../modals/FileUploader';

/**
 * Предназначен для работы с файловыми полями
 */
export class FormFilesManager {

   private static instance: FormFilesManager;

   public static getInstance(): FormFilesManager {

      if (!FormFilesManager.instance) {
         FormFilesManager.instance = new FormFilesManager();
      }

      return FormFilesManager.instance;
   }

   private constructor() {
   }

   /**
    * Инициализирует файловые поля на странице
    */
   public initPageFileFields(): void {
      const fileSelects = document.querySelectorAll<HTMLElement>('[data-modal-select="file"]');
      fileSelects.forEach(select => this.initFileField(select));
   }

   /**
    * Инициализирует файловое поле
    *
    * @param select - элемент, по клику на который вызывается файловый загрузчик
    */
   private initFileField(select: HTMLElement): void {
      select.addEventListener('click', () => FileUploader.getInstance().open(select));
   }

   /**
    * Инициализирует файловые поля в области действия
    *
    * @param scope - область действия
    */
   public initNewElementWithFileFields(scope: HTMLElement): void {
      scope.querySelectorAll<HTMLElement>('[data-modal-select="file"]')
         .forEach(select => this.initFileField(select));
   }


}
