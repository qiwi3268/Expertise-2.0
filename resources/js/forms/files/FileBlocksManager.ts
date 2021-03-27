import { FileBlock } from './FileBlock';
import {
   isNotTemplate,
   mClosest,
   mQS,
   safeDataAttrGetter,
   safeJSONParse,
   safeMapGetter,
   safeWeakMapGetter, safeWeakMapSetter
} from '../../lib/main';
import { GeFile } from '../../lib/GeFile';
import { FormFilesManager } from './FormFilesManager';

/**
 * Описывает форму файлов на странице
 */
export type FilesForm = {
   signed: FileBox,
   notRequiredSign: {
      [mappings: string]: {
         result: true | null,
         starPaths: string[]
      }
   },
}

/**
 * Описывает форму файловых блоков с обязательным подписанием
 */
export type FileBox = {
   [mappings: string]: FileBlockJSON
}

/**
 * Описывает файловый блок с обязательным подписанием
 */
export type FileBlockJSON = {
   result: boolean | null,
   starPaths: string[]
}

/**
 * Предназначен для работы с файловыми блоками страницы
 */
export class FileBlocksManager {

   /**
    * Контейнер с объектами файловых полей
    */
   private fileBlocks: WeakMap<HTMLElement, FileBlock> = new WeakMap<HTMLElement, FileBlock>();

   /**
    * Элементы файловых блоков
    */
   private fileBlockElements: HTMLElement[] = [];

   private static instance: FileBlocksManager;

   private constructor() {
   }

   public static getInstance(): FileBlocksManager {

      if (!FileBlocksManager.instance) {
         FileBlocksManager.instance = new FileBlocksManager();
      }

      return FileBlocksManager.instance;
   }

   /**
    * Инициализирует файловые блоки на странице
    */
   public initPageFileBlocks(): void {
      const fileContainers: HTMLElement[] = Array.from(document.querySelectorAll('[data-files-container]'));
      fileContainers.filter(isNotTemplate).forEach(container => this.initFileBlock(container));

      FormFilesManager.getInstance().initPageFileFields();
   }

   /**
    * Инициализирует файловый блок
    * @param filesContainer - элемент файлового блока
    */
   private initFileBlock(filesContainer: HTMLElement): void {
      const fileBlock: FileBlock = new FileBlock(filesContainer);
      // this.fileBlocks.set(filesContainer, fileBlock);
      safeWeakMapSetter(this.fileBlocks, filesContainer, fileBlock);
      this.fileBlockElements.push(filesContainer);
   }

   /**
    * Инициализирует файловые блоки внутри области действия
    *
    * @param scope - область действия
    */
   public initNewElementWithFileBlocks(scope: HTMLElement): void {
      scope.querySelectorAll<HTMLElement>('[data-files-container]')
         .forEach(filesContainer => this.initFileBlock(filesContainer));

      FormFilesManager.getInstance().initNewElementWithFileFields(scope);
   }

   /**
    * Получает объект файлового блока по элементу родительского поля
    *
    * @param parentFieldElement - элемент родительского блока
    */
   public getByParentField(parentFieldElement: HTMLElement): FileBlock {
      const filesContainer: HTMLElement = mQS('[data-files-container]', parentFieldElement);
      return safeWeakMapGetter(this.fileBlocks, filesContainer);
   }

   /**
    * Получает объект файлового блока по дочернему файлу
    *
    * @param geFile - дочерний файл
    */
   public getByGeFile(geFile: GeFile): FileBlock {

      const filesContainer: HTMLElement = mClosest(
         '[data-files-container]',
         geFile.getFileBlock().getFilesContainer()
      );

      return safeWeakMapGetter(this.fileBlocks, filesContainer);
   }

   /**
    * Получает форму файловых блоков страницы
    */
   public getFilesForm(): FileBox {
      return this.getSignedFileBox();
   }

   /**
    * Получает форму файловых блоков страницы с обязательным подписанием
    */
   private getSignedFileBox(): FileBox {
      const fileBox: FileBox = {};

      this.fileBlockElements.forEach((blockElement: HTMLElement) => {

         const block: FileBlock = safeWeakMapGetter(this.fileBlocks, blockElement);

         const blockJSON: FileBlockJSON = block.toJSON();

         if (fileBox.hasOwnProperty(block.getMappings())) {

            const mainBlock: FileBlockJSON = fileBox[block.getMappings()];
            if ((mainBlock.result === null || mainBlock.result) && blockJSON.result !== null) {
               mainBlock.result = blockJSON.result;
            }
            mainBlock.starPaths = mainBlock.starPaths.concat(blockJSON.starPaths);


         } else {
            fileBox[block.getMappings()] = blockJSON;
         }

      });

      return fileBox;
   }


}
