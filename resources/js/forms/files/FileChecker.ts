import { htmlArrDecode, isNumeric, safeDataAttrGetter } from '../../lib/main';
import { ErrorModal } from '../../modals/ErrorModal';
import { SIG_EXTENSIONS } from '../../modals/FileUploader';

/**
 * Предназначен для валидации файлов
 */
export class FileChecker {
   private static instance: FileChecker;

   public static getInstance(): FileChecker {

      if (!this.instance) {
         this.instance = new FileChecker();
      }

      return this.instance;
   }

   /**
    * Получает доступные расширения файлов для загрузки по элементу поля
    *
    * @param fileField - элемент файлового поля
    */
   public static getFileFieldAllowableExtensions(fileField: HTMLElement): string[] {
      let allowableExtensions: string[] = [];
      const extensionsAttr: string = safeDataAttrGetter('allowableExtensions', fileField);
      if (extensionsAttr !== '') {
         allowableExtensions = htmlArrDecode(extensionsAttr);
      }

      return allowableExtensions;
   }

   /**
    * Получает запрещенные символы в названии файлов по элементу поля
    *
    * @param fileField - элемент файлового поля
    */
   public static getFileFieldForbiddenSymbols(fileField: HTMLElement): string[] {
      const forbiddenSymbolsAttr: string = safeDataAttrGetter('forbiddenSymbols', fileField);
      return forbiddenSymbolsAttr ? htmlArrDecode(forbiddenSymbolsAttr) : [];
   }

   /**
    * Получает максимальный размер загружаемого файла по элементу поля
    *
    * @param fileField - элемент файлового поля
    */
   public static getFileFieldMaxFileSize(fileField: HTMLElement): number | null {
      const sizeString: string = safeDataAttrGetter('maxFileSize', fileField);
      return isNumeric(sizeString) ? parseInt(sizeString) : null;
   }

   /**
    * Валидирует добавленные на страницу файлы
    *
    * @param uploadedFiles - файлы, добавленные на страницу
    * @param allowableExtensions - допустимые расширения
    * @param forbiddenSymbols - запрещенные символы в названии
    * @param maxFileSize - максимальный размер
    */
   public checkFiles(
      uploadedFiles: File[] | null,
      allowableExtensions: string[],
      forbiddenSymbols: string[],
      maxFileSize: number | null
   ): boolean {

      if (!uploadedFiles || uploadedFiles.length === 0) {
         ErrorModal.open(
            'Ошибка при загрузке файлов',
            'Не выбраны файлы для загрузки'
         );
         return false;
      }
      if (!this.isValidExtensions(uploadedFiles, allowableExtensions)) {
         ErrorModal.open(
            'Ошибка при загрузке файлов',
            'Файл содержит недопустимое расширение'
         );
         return false;
      } else if (!this.isValidSizes(uploadedFiles, maxFileSize)) {
         ErrorModal.open(
            'Ошибка при загрузке файлов',
            `Максимальный размер файлов для загрузки: ${maxFileSize} МБ`
         );
         return false;
      } else if (this.containsForbiddenSymbols(uploadedFiles, forbiddenSymbols)) {
         ErrorModal.open(
            'Ошибка при загрузке файлов',
            `Название файла содержит запрещенные символы`
         );
         return false;
      }

      return true;
   }

   /**
    * Определяет, содержат ли файлы допустимые расширения
    *
    * @param files - файлы для проверки
    * @param allowableExtensions - допустимые расширения
    */
   private isValidExtensions(files: File[], allowableExtensions: string[]): boolean {
      if (allowableExtensions.length === 0) {
         return true;
      }

      return files.some((file: File) => {

         const nameParts: string[] = file.name.split('.');
         return nameParts
            .filter(this.isExtension)
            .some((namePart: string) => allowableExtensions.includes(namePart));

      });
   }

   /**
    * Определяет, является ли строка расширением файла
    *
    * @param fileNamePart - строка для проверки
    */
   private isExtension(fileNamePart: string): boolean {
      return !isNumeric(fileNamePart)
         && fileNamePart.length > 2
         && !SIG_EXTENSIONS.includes(fileNamePart);
   }

   /**
    * Определяет, не превышает ли размер файлов максимально доступный
    *
    * @param files - файлы для проверки
    * @param maxFileSize - максимальный размер
    */
   private isValidSizes(files: File[], maxFileSize: number | null): boolean {
      if (maxFileSize === null) {
         return true;
      }

      return !files.some((file: File) => file.size / 1024 / 1024 > maxFileSize);
   }

   /**
    * Определяет, не содержат ли названия файлов запрещенные символы
    *
    * @param files - файлы для проверки
    * @param forbiddenSymbols - запрещенные символы
    */
   private containsForbiddenSymbols(files: File[], forbiddenSymbols: string[]): boolean {
      if (forbiddenSymbols.length === 0) {
         return true;
      }

      return files.some((file: File) => {
         return file.name.split('').some((character: string) => {
            return forbiddenSymbols.includes(character);
         });
      });
   }

}
