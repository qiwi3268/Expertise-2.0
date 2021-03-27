import { FileBlock } from '../forms/files/FileBlock';
import { GeFile } from './GeFile';
import { GeSign } from './GeSign';
import { LogicError } from './LogicError';

/**
 * Предназначен для проставления меток сохранения и удаления к файлам
 */
export class FileNeedsManager {

   private static instance: FileNeedsManager;

   public static getInstance(): FileNeedsManager {

      if (!this.instance) {
         this.instance = new FileNeedsManager();
      }

      return this.instance;
   }

   /**
    * Все файлы, которые были загружены на страницу
    */
   private pageFiles: Set<GeFile> = new Set<GeFile>();

   /**
    * Все подписи, которые были загружены на
    */
   private pageSigns: Set<GeSign> = new Set<GeSign>();


   /**
    * Массив с файлами для сохранения
    */
   private filesToSave: Set<GeFile> = new Set<GeFile>();

   /**
    * Массив с файлами для удаления
    */
   private filesToDelete: Set<GeFile> = new Set<GeFile>();

   /**
    * Контейнер с файлами для сохранения
    */
      // private signsToSave: Map<number, FileMapping> = new Map();
   private signsToSave: Set<GeSign> = new Set<GeSign>();

   /**
    * Контейнер с файлами для удаления
    */
      // private signsToDelete: Map<number, FileMapping> = new Map();
   private signsToDelete: Set<GeSign> = new Set<GeSign>();

   /**
    * Добавляет файлы на странице в массивы для сохранения или удаления
    */
   public putFilesToFileNeeds(): FileNeedsManager {

      // console.log(this.pageFiles);
      // console.log(this.pageSigns);

      // let toSave: Set<string> = new Set<string>();

      this.pageFiles.forEach(geFile => {

         if (geFile.isDeleted()) {
            this.deleteFile(geFile);
         } else if (geFile.isDisplayed()) {
            this.putFileToSave(geFile)
         }

      });


      /*    Array.from(this.pageFiles).forEach(file => {
             console.log('=====');
             console.log(file.isExistsOnPage());
             console.log(file.isSaved());
             console.log(!this.filesToDelete.has(file));
             console.log('=====');
          })
    */

      const unsavedFiles: GeFile[] = Array.from(this.pageFiles)
         // .filter(file => !this.filesToSave.has(file) && !file.isSaved())
         .filter(geFile => {
            // console.log(geFile.isSaved());
            // console.log(geFile.isExistsOnPage());
            // console.log(!this.filesToDelete.has(geFile));

            return geFile.isSaved() && !geFile.isExistsOnPage() && !this.filesToDelete.has(geFile);
         })
      // .filter(file => !file.isExistsOnPage() && file.isSaved())
      // .filter(file => !file.isSaved())
      // .filter(file => !this.filesToDelete.has(file));


      if (unsavedFiles.length > 0) {
         new LogicError('Потерянные файлы');
         console.log(unsavedFiles);
      }

      unsavedFiles.forEach(file => {
         console.log(file);
         console.log(file.getFileBlock().isDisplayed());
         // this.deleteFile(file)
      });


      // unsavedFiles.forEach(geFile => this.deleteFile(geFile));


      /*    FileBlock.fileBlocks.forEach((fileBlock: FileBlock) => {

             // console.log(fileBlock.parentField);
             // console.log(fileBlock.isActive());

             // Если блок скрыт, удаляем файлы, иначе сохраняем
             if (fileBlock.isDisplayed()) {
                this.saveFiles(fileBlock);
             } else {
                this.deleteFiles(fileBlock);
             }

          });*/

      /*    let unsavedFiles: GeFile[] = Array.from(this.pageFiles)
             .filter(file => !this.filesToSave.has(file))
             .filter(file => !this.filesToDelete.has(file))

          unsavedFiles.forEach(geFile => this.deleteFile(geFile));*/


      this.addSigns();

      console.log('===');
      console.log(this.pageFiles);
      console.log('---');

      // this.pageFiles.forEach(file => {
      //    console.log(file.element.style);
      // })

      console.log(this.filesToSave);
      console.log(this.filesToDelete);
      console.log(this.signsToSave);
      console.log(this.signsToDelete);

      const savedFiles: GeFile[] = Array.from(this.pageFiles)
         .filter(geFile => geFile.isSaved() && !geFile.isDeleted());

      this.pageFiles = new Set<GeFile>(savedFiles);
      this.filesToSave = new Set<GeFile>();
      this.filesToDelete = new Set<GeFile>();

      console.log('===');
      console.log(this.pageFiles);
      console.log('---');

      return this;
   }


   private addSigns(): void {

      this.pageSigns.forEach(sign => {

         if (sign.isDeleted() && sign.isSaved()) {
            this.signsToDelete.add(sign);
         } else {
            this.signsToSave.add(sign);
            sign.setIsSaved(true);
         }

      });

      const lostSigns: GeSign[] = Array.from(this.pageSigns)
         .filter(sign => !this.signsToSave.has(sign))
         .filter(sign => !this.signsToDelete.has(sign))

      if (lostSigns.length > 0) {
         const signStarPaths: string[] = lostSigns.map(sign => sign.getStarPath());
         new LogicError('Ошибка при добавлении file needs. Нераспознанные подписи: ' + signStarPaths);
      }

   }

   public addFile(geFile: GeFile): void {
      this.pageFiles.add(geFile);
   }

   public addSign(geSign: GeSign): void {
      this.pageSigns.add(geSign);
   }

   /**
    * Добавляет файлы в массив для сохранения
    *
    * @param fileBlock - блок с файлами
    */
   private saveFiles(fileBlock: FileBlock): void {
      fileBlock.getFiles().forEach((geFile: GeFile) => this.putFileToSave(geFile));
   }

   /**
    * Добавляет файл в массив для сохранения
    *
    * @param geFile - сохраняемый файл
    */
   private putFileToSave(geFile: GeFile): void {
      if (!geFile.isSaved() && !geFile.isDeleted()) {
         this.filesToSave.add(geFile);
         geFile.setIsSaved(true);
         // this.putSignsToSave(geFile);
      }
   }

   /**
    * Добавляет открепленную подпись в массив для сохранения
    *
    * @param geFile - файл, к которому относится открепленная подпись
    */
   public putSignsToSave(geFile: GeFile): void {
      // this.signsToSave.push(...geFile.signStarPaths);
      // geFile.signStarPaths.forEach(sign => this.signsToSave.add(sign));
   }

   /**
    * Добавляет файлы в массив для удаления
    *
    * @param fileBlock - блок с файлами
    */
   private deleteFiles(fileBlock: FileBlock): void {
      fileBlock.getFiles().forEach(geFile => this.deleteFile(geFile));
   }

   private deleteFile(geFile: GeFile): void {
      geFile.remove();
      this.putFileToDelete(geFile);
   }

   /**
    * Добавляет файл в массив для удаления
    *
    * @param geFile - удаляемый файл
    */
   public putFileToDelete(geFile: GeFile): void {
      this.filesToSave.delete(geFile);

      if (geFile.isSaved()) {
         this.filesToDelete.add(geFile);
      }

   }

   /**
    * Добавляет открепленную подпись в массив для удаления
    *
    * @param geFile - файл, к которому относится открепленная подпись
    */
   /*public putSignToDelete (geFile: GeFile): void {
      geFile.signStarPaths.forEach(sign => {
         this.signsToSave.delete(sign)
      });
      // this.signsToSave = new Set(Array.from(this.signsToSave).filter(sign => !geFile.signStarPaths.has(sign)));
      if (geFile.isSaved()) {
         console.log('ge file saved');
         // this.signsToDelete.push(...geFile.signStarPaths);
         geFile.signStarPaths.forEach(sign => this.signsToDelete.add(sign));
      }
   }*/

   /**
    * Добавляет массив с подписями к массиву с файлами для отправки на API file_needs_setter
    */

   /*   private addSigns (): void {
         // this.filesToSave = this.filesToSave.concat(Array.from(this.signsToSave.values()));

         // this.signsToSave.forEach(sign => this.filesToSave.add(sign));
         // this.signsToDelete.forEach(sign => this.filesToDelete.add(sign));

         // this.filesToDelete = this.filesToDelete.concat(Array.from(this.signsToDelete.values()));
      }*/

   /**
    * Получает json с файлами для сохранения и удаления
    * для отправки на API file_needs_setter
    *
    * @returns json c файлами
    */
   public getFileNeeds(): any {
      //todo

      /* return {
          toSave: Array.from(this.filesToSave),
          toDelete: Array.from(this.filesToDelete)
       }*/
   }

   /**
    * Очищает массивы с файлами и подписями после
    * отправки на API file_needs_setter
    */
   public clear(): void {
      // todo здесь поставить, что GeFile сохранены
      // this.filesToSave = new Set<string>();
      // this.filesToDelete = new Set<string>();
      // this.signsToSave = new Set<string>();
      // this.signsToDelete = new Set<string>();
   }

   /**
    * Определяет наличие файлов и подписей для сохранения и удаления
    *
    * @returns есть ли файлы для сохранения или удаления
    */
   private hasFiles(): boolean {
      return (
         this.filesToSave.size !== 0
         || this.filesToDelete.size !== 0
         || this.signsToSave.size !== 0
         || this.signsToDelete.size !== 0
      );
   }

}


