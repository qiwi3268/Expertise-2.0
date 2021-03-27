import { safeDataAttrGetter, safeJSONParse, safeMapGetter } from '../../lib/main';
import { GeFile } from '../../lib/GeFile';
import { ValidationResult } from '../../api/response-handlers/files/InternalSignatureValidationResponseHandler';
import { UploadedFile } from '../../api/response-handlers/files/UploadResponseHandler';

/**
 * Вспомогательный класс для работы с файлами
 */
export class FilesUtils {

   /**
    * Хранилище с результатами валидации подписей
    * файлов загруженных на страницу
    */
   private static validationResults: Map<string, ValidationResult> = new Map();

   /**
    * Инициализирует файлы загруженные на страницу
    */
   public static initFiles(): void {
      const geFiles: GeFile[] = [];
      const fileBlocks: NodeListOf<HTMLElement> = document.querySelectorAll<HTMLElement>('[data-files-container]');
      fileBlocks.forEach(fileBlock => {

         const files: NodeListOf<HTMLElement> = fileBlock.querySelectorAll<HTMLElement>('[data-file]');
         files.forEach(fileElement => {

            // Потом сделать инициализацию
            const fileData: UploadedFile = {
               originalName: safeDataAttrGetter('originalName', fileElement),
               starPath: safeDataAttrGetter('starPath', fileElement),
               humanFileSize: safeDataAttrGetter('humanFileSize', fileElement),
            }
            fileElement.removeAttribute('[data-original-name]');
            fileElement.removeAttribute('[data-star-path]');
            fileElement.removeAttribute('[data-human-file-size]');
            //--------------------------

            const geFile = new GeFile(fileElement, fileData);
            geFile.handleActionButtons();

            if (geFile.getElement().hasAttribute('data-validation-result')) {
               const validationResult: string = safeDataAttrGetter('validationResult', geFile.getElement());
               if (validationResult !== '') {
                  this.setValidationResult(safeJSONParse<ValidationResult>(validationResult), geFile);
               }
               geFile.getElement().removeAttribute('data-validation-result');
            }

            geFiles.push(geFile);
         });

      });

      geFiles.forEach(geFile => geFile.setParentFieldSignState());
   }

   /**
    * Возвращает результаты валидации подписей для переданного файла
    *
    * @param geFile - файл, для которого получаются результаты валидации
    * @return Объект с результатами валидации подписей или null, если
    * файл не подписан
    */
   public static getValidationResult(geFile: GeFile): ValidationResult | null {
      let validationResult = null;

      if (this.validationResults.has(geFile.getStarPath())) {
         validationResult = safeMapGetter(this.validationResults, geFile.getStarPath());
      }

      return validationResult;
   }

   /**
    * Записывает результаты валидации подписей для переданного файла
    *
    * @param validationResults - результаты валидации подписей
    * @param geFile - файл, к которому относятся результаты валидации
    */
   public static setValidationResult(validationResults: ValidationResult, geFile: GeFile): void {
      if (!this.validationResults.has(geFile.getStarPath())) {
         this.validationResults.set(geFile.getStarPath(), validationResults);
      }
   }

   /**
    * Удаляет результаты валидации подписей для переданного файла
    *
    * @param geFile - файл, у которого удаляются результаты валидации
    */
   public static removeValidationResult(geFile: GeFile): void {
      this.validationResults.delete(geFile.getStarPath());
   }

}
